<template>
  <VnAdminLayout>
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-white">Backgrounds</h2>
      <a
        href="/admin/vn/backgrounds/create"
        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25"
      >
        + New Background
      </a>
    </div>

    <div v-if="backgrounds.length === 0" class="text-center py-20">
      <div class="text-6xl mb-4">🖼️</div>
      <p class="text-gray-400 text-lg">No backgrounds yet. Create your first one!</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="bg in backgrounds"
        :key="bg.id"
        class="bg-gray-900/50 border border-gray-800/50 rounded-2xl overflow-hidden hover:border-gray-700/50 transition-all duration-200 group"
      >
        <div class="aspect-video bg-gray-800 relative overflow-hidden">
          <img
            :src="`/storage/${bg.image_path}`"
            :alt="bg.name"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
        </div>
        <div class="p-4 flex items-center justify-between">
          <h3 class="font-semibold text-white">{{ bg.name }}</h3>
          <div class="flex items-center gap-2">
            <a
              :href="`/admin/vn/backgrounds/${bg.id}/edit`"
              class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
            >
              Edit
            </a>
            <button
              @click="deleteBackground(bg.id)"
              class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-sm transition-colors"
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
  backgrounds: Array,
});

function deleteBackground(id) {
  if (confirm('Delete this background?')) {
    router.delete(`/admin/vn/backgrounds/${id}`);
  }
}
</script>
