<template>
  <VnAdminLayout>
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Scenes</h2>
      <a
        href="/admin/vn/scenes/create"
        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25"
      >
        + New Scene
      </a>
    </div>

    <div v-if="scenes.length === 0" class="text-center py-20">
      <p class="text-gray-500 dark:text-gray-400 text-lg">No scenes yet. Create your first one!</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <a
        v-for="scene in scenes"
        :key="scene.id"
        :href="`/admin/vn/scenes/${scene.id}`"
        class="bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800/50 rounded-2xl overflow-hidden hover:border-indigo-400 dark:hover:border-indigo-500/50 transition-all duration-200 group block shadow-sm hover:shadow-md"
      >
        <div class="aspect-video bg-gray-100 dark:bg-gray-800 relative overflow-hidden">
          <img
            v-if="scene.thumbnail_path"
            :src="`/storage/${scene.thumbnail_path}`"
            :alt="scene.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-5xl bg-gradient-to-br from-indigo-100 dark:from-indigo-900/50 to-purple-100 dark:to-purple-900/50">🎬</div>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">{{ scene.title }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ scene.description || 'No description' }}</p>
          <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500 font-medium mt-2">
            <div class="flex items-center gap-1.5" title="Characters">
              <svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span>{{ scene.characters_count }}</span>
            </div>

            <div class="flex items-center gap-1.5" title="Backgrounds">
              <svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>{{ scene.backgrounds_count }}</span>
            </div>

            <div class="flex items-center gap-1.5" title="Dialogues">
              <svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <span>{{ scene.dialogues_count }}</span>
            </div>
          </div>
        </div>
      </a>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

defineProps({
  scenes: Array,
});
</script>
