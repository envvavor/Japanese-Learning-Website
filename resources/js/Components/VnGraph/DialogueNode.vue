<template>
  <div
    class="dialogue-node"
    :class="{ 'is-start': isStart, 'is-selected': selected }"
  >
    <Handle type="target" :position="Position.Top" class="handle-target" />

    <div class="node-header">
      <span class="node-id">#{{ data.dialogueId || 'NEW' }}</span>
      <span v-if="isStart" class="start-badge">START</span>
    </div>

    <div class="node-character">
      <svg class="node-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
      <span class="node-character-name">{{ data.characterName || 'Narrator' }}</span>
    </div>

    <div class="node-text">
      {{ truncate(data.originalText, 60) || 'Empty dialogue...' }}
    </div>

    <div v-if="data.backgroundName" class="node-bg-label">
      <svg class="node-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
      {{ data.backgroundName }}
    </div>

    <Handle
      type="source"
      :position="Position.Bottom"
      id="next"
      class="handle-next"
    />

    <div v-if="data.choices?.length" class="choice-handles">
      <div v-for="choice in data.choices" :key="choice.id" class="choice-handle-row">
        <span class="choice-label">{{ truncate(choice.choiceText, 20) || 'Empty Choice' }}</span>
        <Handle
          type="source"
          :position="Position.Right"
          :id="`choice-${choice.id}`"
          class="handle-choice"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Handle, Position } from '@vue-flow/core';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
});

const isStart = computed(() => !!props.data.isStart);

function truncate(text, len) {
  if (!text) return '';
  return text.length > len ? text.substring(0, len) + '…' : text;
}
</script>

<style scoped>
.dialogue-node { min-width: 220px; max-width: 280px; background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px; font-size: 13px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s ease; cursor: pointer; }
:root.dark .dialogue-node, .dark .dialogue-node { background: #1f2937; border-color: #374151; color: #e5e7eb; }
.dialogue-node:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.dialogue-node.is-selected { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); }
:root.dark .dialogue-node.is-selected, .dark .dialogue-node.is-selected { box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3); }
.dialogue-node.is-start { border-color: #10b981; }
.dialogue-node.is-start.is-selected { border-color: #6366f1; }
.node-header { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
.node-id { font-size: 11px; font-weight: 600; font-family: monospace; color: #9ca3af; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
:root.dark .node-id, .dark .node-id { background: #374151; color: #9ca3af; }
.start-badge { font-size: 10px; font-weight: 700; color: white; background: #10b981; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.05em; }
.node-character { display: flex; align-items: center; gap: 5px; margin-bottom: 6px; color: #6366f1; font-weight: 600; font-size: 12px; }
:root.dark .node-character, .dark .node-character { color: #818cf8; }
.node-icon { width: 14px; height: 14px; flex-shrink: 0; }
.node-text { color: #374151; line-height: 1.4; margin-bottom: 6px; word-break: break-word; }
:root.dark .node-text, .dark .node-text { color: #d1d5db; }
.node-bg-label { display: flex; align-items: center; gap: 4px; font-size: 11px; color: #9ca3af; padding-top: 6px; border-top: 1px solid #f3f4f6; }
:root.dark .node-bg-label, .dark .node-bg-label { border-color: #374151; }
.node-icon-sm { width: 12px; height: 12px; flex-shrink: 0; }
.choice-handles { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e5e7eb; display: flex; flex-direction: column; gap: 6px; }
:root.dark .choice-handles, .dark .choice-handles { border-color: #4b5563; }
.choice-handle-row { display: flex; align-items: center; justify-content: flex-end; gap: 6px; position: relative; }
.choice-label { font-size: 11px; color: #a855f7; font-weight: 500; text-align: right; }
.handle-target { width: 10px !important; height: 10px !important; background: #6366f1 !important; border: 2px solid white !important; border-radius: 50% !important; }
:root.dark .handle-target, .dark .handle-target { border-color: #1f2937 !important; }
.handle-next { width: 10px !important; height: 10px !important; background: #6366f1 !important; border: 2px solid white !important; border-radius: 50% !important; }
:root.dark .handle-next, .dark .handle-next { border-color: #1f2937 !important; }
.handle-choice { width: 8px !important; height: 8px !important; background: #a855f7 !important; border: 2px solid white !important; border-radius: 50% !important; position: relative !important; transform: none !important; right: -18px !important; top: auto !important; }
:root.dark .handle-choice, .dark .handle-choice { border-color: #1f2937 !important; }
</style>