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
          <div class="flex gap-3 text-xs text-gray-400 dark:text-gray-500">
            <span>👤 {{ scene.characters_count }} chars</span>
            <span>🖼️ {{ scene.backgrounds_count }} bgs</span>
            <span>💬 {{ scene.dialogues_count }} dialogues</span>
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
