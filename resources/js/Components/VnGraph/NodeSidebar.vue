<template>
  <transition name="slide">
    <div v-if="node" class="sidebar">
      <div class="sidebar-header">
        <h3 class="sidebar-title">Edit Dialogue #{{ node.data.dialogueId || 'NEW' }}</h3>
        <button @click="$emit('close')" class="sidebar-close">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <div class="sidebar-body">
        <div class="field">
          <label class="field-label">Character</label>
          <select v-model="node.data.characterId" @change="updateCharacterName" class="field-input">
            <option :value="null">— Narrator —</option>
            <option v-for="c in characters" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div class="field">
          <label class="field-label">Background</label>
          <select v-model="node.data.backgroundId" @change="updateBackgroundName" class="field-input">
            <option :value="null">— None —</option>
            <option v-for="bg in backgrounds" :key="bg.id" :value="bg.id">{{ bg.name }}</option>
          </select>
        </div>

        <div class="field">
          <label class="field-label">Original Text (Japanese)</label>
          <textarea v-model="node.data.originalText" rows="3" class="field-input" placeholder="日本語のテキスト..."></textarea>
        </div>

        <div class="field">
          <label class="field-label">Translated Text</label>
          <textarea v-model="node.data.translatedText" rows="3" class="field-input" placeholder="Terjemahan..."></textarea>
        </div>

        <label class="start-toggle">
          <input type="checkbox" :checked="isStart" @change="$emit('toggle-start', node.id)" class="start-checkbox" />
          <span>Set as <strong class="text-emerald-600 dark:text-emerald-400">starting dialogue</strong></span>
        </label>

        <div class="audio-section pt-4 border-t border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Audio (Voice Over)</h4>

          <div v-if="node.data.audioFilePath" class="mb-3">
            <div class="flex items-center gap-1.5 mb-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Audio is ready
            </div>
            
            <button
              @click="togglePlay"
              class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium transition-colors border border-gray-200 dark:border-gray-600"
            >
              <svg v-if="!isPlaying" class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              <svg v-else class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
              {{ isPlaying ? 'Stop Playing' : 'Play Audio' }}
            </button>
          </div>

          <div v-else class="mb-3 text-xs text-gray-500 dark:text-gray-400">
            No audio generated yet.
          </div>

          <button 
            @click="$emit('regenerate-audio', node.id)" 
            :disabled="isGeneratingAudio || !node.data.characterId || !node.data.originalText"
            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium disabled:opacity-50 transition-colors"
          >
            <svg v-if="isGeneratingAudio" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
            {{ isGeneratingAudio ? 'Generating...' : (node.data.audioFilePath ? 'Regenerate Audio' : 'Generate Audio') }}
          </button>
        </div>

        <div class="choices-section mt-4">
          <div class="choices-header">
            <span class="field-label">Choices</span>
            <button @click="addChoice" class="add-choice-btn">+ Add</button>
          </div>
          <div v-for="(choice, i) in node.data.choices" :key="choice.id" class="choice-row">
            <input v-model="choice.choiceText" @input="$emit('update-labels')" class="field-input choice-input" placeholder="Choice text..." />
            <button @click="removeChoice(i)" class="remove-choice-btn">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
          </div>
          <p v-if="!node.data.choices?.length" class="no-choices">No branching choices. Connect nodes using the "Next" handle for linear flow.</p>
        </div>

        <button @click="$emit('delete-node', node.id)" class="delete-btn mt-4">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          Delete this Dialogue
        </button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps({
  node: Object,
  characters: Array,
  backgrounds: Array,
  isStart: Boolean,
  isGeneratingAudio: Boolean
});

const emit = defineEmits(['close', 'toggle-start', 'delete-node', 'update-labels', 'regenerate-audio']);

// 🔥 FUNGSI PEMUTAR AUDIO
const currentAudio = ref(null);
const isPlaying = ref(false);

function togglePlay() {
  if (!props.node.data.audioFilePath) return;

  if (isPlaying.value && currentAudio.value) {
    currentAudio.value.pause();
    currentAudio.value.currentTime = 0;
    isPlaying.value = false;
  } else {
    if (currentAudio.value) currentAudio.value.pause();
    
    // Trik nambah ?t=timestamp agar audio terbaru yg diputar, bukan cache lama!
    const timestamp = new Date().getTime();
    currentAudio.value = new Audio(`${props.node.data.audioFilePath}?t=${timestamp}`);
    
    currentAudio.value.play();
    isPlaying.value = true;

    currentAudio.value.onended = () => {
      isPlaying.value = false;
    };
  }
}

watch(() => props.node?.id, () => {
  if (currentAudio.value) {
    currentAudio.value.pause();
    isPlaying.value = false;
  }
});

onBeforeUnmount(() => {
  if (currentAudio.value) {
    currentAudio.value.pause();
    isPlaying.value = false;
  }
});

function updateCharacterName() {
  if (!props.node) return;
  const char = props.characters.find(c => c.id === props.node.data.characterId);
  props.node.data.characterName = char ? char.name : 'Narrator';
}

function updateBackgroundName() {
  if (!props.node) return;
  const bg = props.backgrounds.find(b => b.id === props.node.data.backgroundId);
  props.node.data.backgroundName = bg ? bg.name : '';
}

function addChoice() {
  if (!props.node) return;
  if (!props.node.data.choices) props.node.data.choices = [];
  props.node.data.choices.push({
    id: `temp-${Date.now()}`,
    choiceText: '',
    targetDialogueId: null,
  });
}

function removeChoice(index) {
  if (!props.node) return;
  props.node.data.choices.splice(index, 1);
  emit('update-labels'); 
}
</script>

<style scoped>
.sidebar { position: absolute; top: 0; right: 0; bottom: 0; width: 340px; background: white; border-left: 1px solid #e5e7eb; z-index: 100; display: flex; flex-direction: column; box-shadow: -4px 0 20px rgba(0,0,0,0.05); overflow: hidden; }
:root.dark .sidebar, .dark .sidebar { background: #111827; border-color: #1f2937; box-shadow: -4px 0 20px rgba(0,0,0,0.3); }
.sidebar-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
:root.dark .sidebar-header, .dark .sidebar-header { border-color: #1f2937; }
.sidebar-title { font-size: 15px; font-weight: 700; color: #111827; }
:root.dark .sidebar-title, .dark .sidebar-title { color: white; }
.sidebar-close { color: #9ca3af; transition: color 0.15s; padding: 4px; border-radius: 6px; }
.sidebar-close:hover { color: #374151; background: #f3f4f6; }
:root.dark .sidebar-close:hover, .dark .sidebar-close:hover { color: white; background: #1f2937; }
.sidebar-body { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
:root.dark .field-label, .dark .field-label { color: #9ca3af; }
.field-input { padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; background: white; color: #111827; transition: border-color 0.15s; outline: none; resize: none; }
.field-input:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.15); }
:root.dark .field-input, .dark .field-input { background: #1f2937; border-color: #374151; color: #e5e7eb; }
:root.dark .field-input:focus, .dark .field-input:focus { border-color: #6366f1; }
.start-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: #374151; }
:root.dark .start-toggle, .dark .start-toggle { color: #d1d5db; }
.start-checkbox { width: 18px; height: 18px; border-radius: 4px; accent-color: #10b981; }
.choices-section { padding-top: 8px; border-top: 1px solid #e5e7eb; }
:root.dark .choices-section, .dark .choices-section { border-color: #1f2937; }
.choices-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.add-choice-btn { font-size: 12px; font-weight: 600; color: #a855f7; padding: 4px 10px; border-radius: 6px; background: rgba(168,85,247,0.1); transition: all 0.15s; }
.add-choice-btn:hover { background: rgba(168,85,247,0.2); }
.choice-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
.choice-input { flex: 1; }
.remove-choice-btn { color: #ef4444; padding: 6px; border-radius: 6px; transition: all 0.15s; flex-shrink: 0; }
.remove-choice-btn:hover { background: rgba(239,68,68,0.1); }
.no-choices { font-size: 12px; color: #9ca3af; font-style: italic; }
.delete-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #ef4444; background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2); transition: all 0.15s; margin-top: auto; }
.delete-btn:hover { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.4); }
.slide-enter-active, .slide-leave-active { transition: transform 0.25s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
</style>