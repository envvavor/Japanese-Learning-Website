<template>
  <VnAdminLayout>
    <div class="graph-wrapper">
      <div class="graph-toolbar">
        <div class="toolbar-left">
          <a :href="`/admin/vn/scenes/${scene.id}`" class="toolbar-btn back-btn">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Scene
          </a>
          <span class="toolbar-divider"></span>
          <span class="toolbar-title">{{ scene.title }} — Story Graph</span>
        </div>
        <div class="toolbar-right">
          <button @click="addNode" class="toolbar-btn add-btn">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Node
          </button>
          <button @click="saveGraph" :disabled="saving" class="toolbar-btn save-btn">
            <svg v-if="!saving" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
            {{ saving ? 'Saving...' : 'Save Graph' }}
          </button>
        </div>
      </div>

      <transition name="fade">
        <div v-if="saveMessage" class="save-feedback" :class="saveError ? 'error' : 'success'">
          {{ saveMessage }}
        </div>
      </transition>

      <div class="graph-canvas">
        <VueFlow
          v-model:nodes="nodes"
          v-model:edges="edges"
          :node-types="nodeTypes"
          :default-viewport="{ x: 50, y: 50, zoom: 0.8 }"
          :snap-to-grid="true"
          :snap-grid="[20, 20]"
          fit-view-on-init
          @node-click="onNodeClick"
          @pane-click="onPaneClick"
          @connect="onConnect"
          class="vue-flow-instance"
        >
          <Background :gap="20" :size="1" />
          <Controls :show-fit-view="true" :show-interactive="false" />
          <MiniMap :pannable="true" :zoomable="true" node-color="#6366f1" mask-color="rgba(0,0,0,0.1)" />
        </VueFlow>

        <NodeSidebar
          :node="selectedNode"
          :characters="characters"
          :backgrounds="backgrounds"
          :is-start="selectedNode ? String(currentFirstDialogueId) === String(selectedNode.id) : false"
          :is-generating-audio="generatingAudioFor === selectedNode?.id" 
          @close="selectedNode = null"
          @toggle-start="toggleStart"
          @delete-node="deleteNode"
          @update-labels="syncEdgeLabels" 
          @regenerate-audio="handleRegenerateAudio"
        />
      </div>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { ref, markRaw, onMounted } from 'vue';
import { VueFlow, MarkerType } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import { MiniMap } from '@vue-flow/minimap';
import axios from 'axios';

import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';
import DialogueNode from '../../../Components/VnGraph/DialogueNode.vue';
import NodeSidebar from '../../../Components/VnGraph/NodeSidebar.vue';

// Configure CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

const props = defineProps({
  scene: Object,
  graphData: Object,
  characters: Array,
  backgrounds: Array,
  firstDialogueId: [Number, String, null],
});

// Init Data - MENGAMBIL AUDIO PATH DARI DATABASE
const nodes = ref(props.graphData.nodes.map(n => ({
  ...n,
  data: {
    ...n.data,
    // Pastikan menangkap nama field yang benar dari Laravel
    audioFilePath: n.data?.audio_file_path || n.data?.audioFilePath || n.audio_file_path || null,
    isStart: String(props.firstDialogueId) === String(n.id),
    choices: (n.data.choices || []).map(c => ({
      id: c.id,
      choiceText: c.choice_text || c.choiceText || '' 
    }))
  },
})));

const edges = ref(props.graphData.edges.map(e => ({
  ...e,
  markerEnd: MarkerType.ArrowClosed,
})));

const nodeTypes = { dialogue: markRaw(DialogueNode) };

const selectedNode = ref(null);
const deletedNodeIds = ref([]);
const saving = ref(false);
const saveMessage = ref('');
const saveError = ref(false);
const currentFirstDialogueId = ref(props.firstDialogueId);
const generatingAudioFor = ref(null); // STATE LOADING AUDIO
let newNodeCounter = 0;

function syncEdgeLabels() {
  let hasChanges = false;
  const newEdges = edges.value.map(edge => {
    if (edge.sourceHandle && edge.sourceHandle.startsWith('choice-')) {
      const sourceNode = nodes.value.find(n => n.id === edge.source);
      if (sourceNode && sourceNode.data && sourceNode.data.choices) {
        const choiceId = edge.sourceHandle.replace('choice-', '');
        const choice = sourceNode.data.choices.find(c => String(c.id) === String(choiceId));
        const correctLabel = (choice && choice.choiceText && choice.choiceText.trim() !== '') ? choice.choiceText : 'Choice';
        
        if (edge.label !== correctLabel) {
          hasChanges = true;
          return { ...edge, label: correctLabel };
        }
      }
    }
    return edge;
  });

  if (hasChanges) edges.value = newEdges;
}

onMounted(() => {
  syncEdgeLabels();
});

// --- AUDIO LOGIC (API CALL) ---
async function handleRegenerateAudio(nodeId) {
  if (String(nodeId).startsWith('new-')) {
    alert('Silakan klik "Save Graph" di pojok kanan atas terlebih dahulu sebelum men-generate audio untuk dialog baru!');
    return;
  }

  const nodeIndex = nodes.value.findIndex(n => n.id === nodeId);
  if (nodeIndex === -1) return;
  
  const node = nodes.value[nodeIndex];
  if (!node.data.characterId || !node.data.originalText) {
    alert('Pastikan karakter dan teks bahasa Jepang/Original sudah diisi!');
    return;
  }

  generatingAudioFor.value = nodeId;

  try {
    const response = await axios.post(`/admin/vn/dialogues/${nodeId}/generate-audio`, {
      text: node.data.originalText,
      character_id: node.data.characterId
    });

    // Update path audio di node yang bersangkutan
    nodes.value[nodeIndex].data.audioFilePath = response.data.audio_file_path;
    // Paksa reactivity
    nodes.value = [...nodes.value];
    
    saveMessage.value = 'Audio berhasil dibuat!';
    saveError.value = false;
  } catch (err) {
    console.error('Gagal generate audio:', err);
    saveMessage.value = err.response?.data?.message || 'Gagal men-generate audio.';
    saveError.value = true;
  } finally {
    generatingAudioFor.value = null;
    setTimeout(() => { saveMessage.value = ''; }, 4000);
  }
}

// --- Event Handlers ---
function onNodeClick({ node }) { selectedNode.value = node; }
function onPaneClick() { selectedNode.value = null; }

function onConnect(connection) {
  edges.value = edges.value.filter(e => !(e.source === connection.source && e.sourceHandle === connection.sourceHandle));
  const isChoice = connection.sourceHandle && connection.sourceHandle.startsWith('choice-');
  let labelText = isChoice ? 'Choice' : 'Next';
  
  if (isChoice) {
    const sourceNode = nodes.value.find(n => n.id === connection.source);
    if (sourceNode && sourceNode.data.choices) {
      const choiceId = connection.sourceHandle.replace('choice-', '');
      const choice = sourceNode.data.choices.find(c => String(c.id) === String(choiceId));
      if (choice && choice.choiceText) labelText = choice.choiceText;
    }
  }

  edges.value.push({
    id: `edge-${connection.source}-${connection.sourceHandle}-${connection.target}`,
    source: connection.source,
    target: connection.target,
    sourceHandle: connection.sourceHandle,
    targetHandle: connection.targetHandle,
    type: 'smoothstep',
    animated: !isChoice,
    style: isChoice ? { stroke: '#a855f7', strokeDasharray: '5 5' } : { stroke: '#6366f1' },
    label: labelText,
    markerEnd: MarkerType.ArrowClosed,
  });
  
  setTimeout(syncEdgeLabels, 50);
}

function addNode() {
  newNodeCounter++;
  const id = `new-${newNodeCounter}`;
  const defaultBg = props.backgrounds.length > 0 ? props.backgrounds[0] : null;

  nodes.value.push({
    id,
    type: 'dialogue',
    position: { x: 150, y: 150 },
    data: {
      dialogueId: null,
      characterId: null,
      characterName: 'Narrator',
      backgroundId: defaultBg?.id || null,
      backgroundName: defaultBg?.name || '',
      originalText: '',
      translatedText: '',
      audioFilePath: null, // Audio kosong untuk node baru
      choices: [],
      isStart: false,
    },
  });

  selectedNode.value = nodes.value[nodes.value.length - 1];
}

function deleteNode(nodeId) {
  if (!confirm('Delete this dialogue node?')) return;
  if (!String(nodeId).startsWith('new-')) deletedNodeIds.value.push(nodeId);
  edges.value = edges.value.filter(e => e.source !== nodeId && e.target !== nodeId);
  nodes.value = nodes.value.filter(n => n.id !== nodeId);
  if (selectedNode.value?.id === nodeId) selectedNode.value = null;
  if (String(currentFirstDialogueId.value) === String(nodeId)) currentFirstDialogueId.value = null;
}

function toggleStart(nodeId) {
  currentFirstDialogueId.value = String(currentFirstDialogueId.value) === String(nodeId) ? null : nodeId;
  nodes.value.forEach(n => { n.data.isStart = String(currentFirstDialogueId.value) === String(n.id); });
}

async function saveGraph() {
  saving.value = true;
  saveMessage.value = '';
  saveError.value = false;

  try {
    const payload = {
      nodes: nodes.value.map(n => ({
        id: n.id,
        position: n.position,
        data: {
          characterId: n.data.characterId,
          backgroundId: n.data.backgroundId,
          originalText: n.data.originalText || 'Empty',
          translatedText: n.data.translatedText || 'Empty',
          choices: (n.data.choices || []).map(c => ({
            id: String(c.id).startsWith('temp-') ? null : c.id,
            choice_text: c.choiceText
          }))
        },
      })),
      edges: edges.value.map(e => ({
        source: e.source,
        target: e.target,
        sourceHandle: e.sourceHandle || 'next',
        label: e.label || '',
      })),
      deletedNodeIds: deletedNodeIds.value,
      firstDialogueId: currentFirstDialogueId.value,
    };

    const response = await axios.post(`/admin/vn/scenes/${props.scene.id}/graph/save`, payload);
    saveMessage.value = response.data.message || 'Graph saved successfully!';
    saveError.value = false;
    deletedNodeIds.value = [];
    setTimeout(() => window.location.reload(), 800);
  } catch (err) {
    saveMessage.value = err.response?.data?.message || 'Failed to save graph.';
    saveError.value = true;
  } finally {
    saving.value = false;
    setTimeout(() => { saveMessage.value = ''; }, 4000);
  }
}
</script>

<style>
@import '@vue-flow/core/dist/style.css';
@import '@vue-flow/core/dist/theme-default.css';
@import '@vue-flow/controls/dist/style.css';
@import '@vue-flow/minimap/dist/style.css';
</style>

<style scoped>
.graph-wrapper { position: fixed; top: 64px; left: 0; right: 0; bottom: 0; z-index: 40; display: flex; flex-direction: column; background: #f9fafb; overflow: hidden; }
.graph-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; background: white; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; z-index: 20; }
:root.dark .graph-toolbar, .dark .graph-toolbar { background: #111827; border-color: #1f2937; }
.toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 10px; }
.toolbar-divider { width: 1px; height: 24px; background: #e5e7eb; }
:root.dark .toolbar-divider, .dark .toolbar-divider { background: #374151; }
.toolbar-title { font-size: 14px; font-weight: 600; color: #374151; }
:root.dark .toolbar-title, .dark .toolbar-title { color: #d1d5db; }
.toolbar-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; text-decoration: none; }
.back-btn { color: #6b7280; background: #f3f4f6; }
.back-btn:hover { background: #e5e7eb; color: #374151; }
:root.dark .back-btn, .dark .back-btn { color: #9ca3af; background: #1f2937; }
:root.dark .back-btn:hover, .dark .back-btn:hover { background: #374151; color: white; }
.add-btn { color: #6366f1; background: rgba(99,102,241,0.1); }
.add-btn:hover { background: rgba(99,102,241,0.2); }
.save-btn { color: white; background: #6366f1; }
.save-btn:hover:not(:disabled) { background: #4f46e5; }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.save-feedback { position: absolute; top: 70px; left: 50%; transform: translateX(-50%); padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 50; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.save-feedback.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.save-feedback.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
:root.dark .save-feedback.success, .dark .save-feedback.success { background: rgba(16,185,129,0.15); color: #6ee7b7; border-color: rgba(16,185,129,0.3); }
:root.dark .save-feedback.error, .dark .save-feedback.error { background: rgba(239,68,68,0.15); color: #fca5a5; border-color: rgba(239,68,68,0.3); }
.graph-canvas { flex: 1; position: relative; overflow: hidden; }
.vue-flow-instance { width: 100%; height: 100%; }
:root.dark .vue-flow__background, .dark .vue-flow__background { background: #0f172a !important; }
:root.dark .vue-flow__minimap, .dark .vue-flow__minimap { background: #1e293b !important; }
:root.dark .vue-flow__controls, .dark .vue-flow__controls { background: #1f2937 !important; border-color: #374151 !important; }
:root.dark .vue-flow__controls .vue-flow__controls-button, .dark .vue-flow__controls .vue-flow__controls-button { background: #1f2937 !important; border-color: #374151 !important; fill: #9ca3af !important; }
:root.dark .vue-flow__controls .vue-flow__controls-button:hover, .dark .vue-flow__controls .vue-flow__controls-button:hover { background: #374151 !important; }
:root.dark .vue-flow__edge-text, .dark .vue-flow__edge-text { fill: #d1d5db !important; }
:root.dark .vue-flow__edge-textbg, .dark .vue-flow__edge-textbg { fill: #1f2937 !important; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>