<template>
  <VnAdminLayout>
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-white">Dialogues</h2>
      <a
        href="/admin/vn/dialogues/create"
        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25"
      >
        + New Dialogue
      </a>
    </div>

    <div v-if="dialogues.length === 0" class="text-center py-20">
      <div class="text-6xl mb-4">💬</div>
      <p class="text-gray-400 text-lg">No dialogues yet. Create your first one!</p>
    </div>

    <div v-else class="grid gap-4">
      <div
        v-for="dialogue in dialogues"
        :key="dialogue.id"
        class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-5 hover:border-gray-700/50 transition-all duration-200"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-xs font-mono bg-gray-800 px-2 py-1 rounded text-gray-400">#{{ dialogue.id }}</span>
              <span class="text-sm font-semibold text-indigo-300">
                {{ dialogue.character?.name || '(Narrator)' }}
              </span>
              <span class="text-xs text-gray-500">
                📍 {{ dialogue.background?.name || '—' }}
              </span>
            </div>
            <p class="text-white text-sm mb-1 truncate">{{ dialogue.original_text }}</p>
            <p class="text-gray-400 text-sm truncate">{{ dialogue.translated_text }}</p>
            <div v-if="dialogue.choices && dialogue.choices.length > 0" class="mt-2 flex gap-2 flex-wrap">
              <span
                v-for="choice in dialogue.choices"
                :key="choice.id"
                class="text-xs bg-purple-500/20 text-purple-300 px-2 py-1 rounded-lg"
              >
                {{ choice.choice_text }} → #{{ choice.target_dialogue_id }}
              </span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <a
              :href="`/admin/vn/dialogues/${dialogue.id}/edit`"
              class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
            >
              Edit
            </a>
            <button
              @click="deleteDialogue(dialogue.id)"
              class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-sm transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

defineProps({
  dialogues: Array,
});

function deleteDialogue(id) {
  if (confirm('Delete this dialogue? All its choices will also be deleted.')) {
    router.delete(`/admin/vn/dialogues/${id}`);
  }
}
</script>
