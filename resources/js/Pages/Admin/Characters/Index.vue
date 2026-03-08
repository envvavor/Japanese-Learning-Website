<template>
  <VnAdminLayout>
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-white">Characters</h2>
      <a
        href="/admin/vn/characters/create"
        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40"
      >
        + New Character
      </a>
    </div>

    <div v-if="characters.length === 0" class="text-center py-20">
      <div class="text-6xl mb-4">👤</div>
      <p class="text-gray-400 text-lg">No characters yet. Create your first one!</p>
    </div>

    <div v-else class="grid gap-4">
      <div
        v-for="character in characters"
        :key="character.id"
        class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-5 flex items-center justify-between hover:border-gray-700/50 transition-all duration-200"
      >
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center text-2xl">
            👤
          </div>
          <div>
            <h3 class="font-semibold text-white text-lg">{{ character.name }}</h3>
            <p class="text-sm text-gray-400">
              Voice: {{ character.elevenlabs_voice_id || 'Not set' }}
              <span v-if="character.default_sprite_path" class="ml-3">🖼️ Has sprite</span>
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a
            :href="`/admin/vn/characters/${character.id}/edit`"
            class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
          >
            Edit
          </a>
          <button
            @click="deleteCharacter(character.id)"
            class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-sm transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

defineProps({
  characters: Array,
});

function deleteCharacter(id) {
  if (confirm('Are you sure you want to delete this character?')) {
    router.delete(`/admin/vn/characters/${id}`);
  }
}
</script>
