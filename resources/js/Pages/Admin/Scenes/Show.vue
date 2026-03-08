<template>
  <VnAdminLayout>
    <div class="max-w-5xl mx-auto pb-12">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-gray-200 dark:border-gray-800 pb-6">
        <div>
          <Link href="/admin/vn/scenes" class="flex items-center text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors mb-3">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Scenes
          </Link>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ scene.title }}</h2>
          <p v-if="scene.description" class="text-gray-500 dark:text-gray-400 mt-2 text-sm">{{ scene.description }}</p>
        </div>
        <div class="flex items-center gap-3">
          <Link
            :href="`/admin/vn/scenes/${scene.id}/edit`"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
            Edit Scene
          </Link>
          <Link
            v-if="scene.first_dialogue_id"
            :href="`/vn/play/${scene.first_dialogue_id}`"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Play Scene
          </Link>
        </div>
      </div>

      <section class="mb-12">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Characters</h3>
            <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium">{{ scene.characters?.length || 0 }}</span>
          </div>
          <Link
            :href="`/admin/vn/scenes/${scene.id}/characters/create`"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            + Add Character
          </Link>
        </div>

        <div v-if="!scene.characters?.length" class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
          <p class="text-sm text-gray-500 dark:text-gray-400">No characters registered for this scene.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="char in scene.characters"
            :key="char.id"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 flex items-center justify-between hover:shadow-sm transition-shadow"
          >
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              </div>
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ char.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Voice ID: <span class="font-mono">{{ char.elevenlabs_voice_id || 'Unassigned' }}</span></p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <Link :href="`/admin/vn/scenes/${scene.id}/characters/${char.id}/edit`" class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <span class="sr-only">Edit</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
              </Link>
              <button @click="deleteItem('characters', char.id)" class="p-2 text-red-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                <span class="sr-only">Delete</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="mb-12">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Backgrounds</h3>
            <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium">{{ scene.backgrounds?.length || 0 }}</span>
          </div>
          <Link
            :href="`/admin/vn/scenes/${scene.id}/backgrounds/create`"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            + Add Background
          </Link>
        </div>

        <div v-if="!scene.backgrounds?.length" class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
          <p class="text-sm text-gray-500 dark:text-gray-400">No backgrounds added yet.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="bg in scene.backgrounds"
            :key="bg.id"
            class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-md transition-all"
          >
            <div class="aspect-video bg-gray-100 dark:bg-gray-900 relative overflow-hidden">
              <img :src="`/storage/${bg.image_path}`" :alt="bg.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
            </div>
            <div class="p-4 flex items-center justify-between">
              <p class="font-medium text-gray-900 dark:text-white text-sm truncate pr-4">{{ bg.name }}</p>
              <div class="flex gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                <Link :href="`/admin/vn/scenes/${scene.id}/backgrounds/${bg.id}/edit`" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </Link>
                <button @click="deleteItem('backgrounds', bg.id)" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section>
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Story Script</h3>
            <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium">{{ scene.dialogues?.length || 0 }}</span>
          </div>
          <Link
            :href="`/admin/vn/scenes/${scene.id}/dialogues/create`"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            + Add Dialogue
          </Link>
        </div>

        <div v-if="!scene.dialogues?.length" class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
          <p class="text-sm text-gray-500 dark:text-gray-400">Your script is empty. Start writing the story.</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="dialogue in scene.dialogues"
            :key="dialogue.id"
            class="relative bg-white dark:bg-gray-800 border rounded-xl p-5 hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
            :class="scene.first_dialogue_id === dialogue.id ? 'border-emerald-200 dark:border-emerald-900/50 shadow-sm' : 'border-gray-200 dark:border-gray-700'"
          >
            <div v-if="scene.first_dialogue_id === dialogue.id" class="absolute -left-1.5 top-5 w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-gray-900"></div>

            <div class="flex items-start justify-between gap-6 pl-2">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                  <span class="text-xs font-mono text-gray-400 dark:text-gray-500">ID: {{ dialogue.id }}</span>
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ dialogue.character?.name || 'Narration' }}</span>
                  <span v-if="dialogue.background" class="text-xs text-gray-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ dialogue.background.name }}
                  </span>
                </div>
                
                <p class="text-gray-900 dark:text-gray-200 text-base mb-1">{{ dialogue.original_text }}</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm italic mb-3">{{ dialogue.translated_text }}</p>
                
                <div v-if="dialogue.choices?.length" class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                  <span class="text-xs font-medium text-gray-400 uppercase tracking-wider mt-1 mr-1">Branches:</span>
                  <div v-for="choice in dialogue.choices" :key="choice.id" class="inline-flex items-center text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-md">
                    {{ choice.choice_text }}
                    <svg class="w-3 h-3 mx-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    ID: {{ choice.target_dialogue_id }}
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 shrink-0">
                <Link :href="`/admin/vn/scenes/${scene.id}/dialogues/${dialogue.id}/edit`" class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                  <span class="sr-only">Edit</span>
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </Link>
                <button @click="deleteItem('dialogues', dialogue.id)" class="p-2 text-red-400 hover:text-red-600 dark:hover:text-red-400 transition-colors bg-red-50 dark:bg-red-500/10 rounded-lg">
                  <span class="sr-only">Delete</span>
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'; // Import komponen Link agar perpindahan halamannya instan (SPA)
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

const isDark = document.documentElement.classList.contains('dark');

const props = defineProps({
  scene: Object,
});

function deleteItem(type, id) {
  const labels = { characters: 'character', backgrounds: 'background', dialogues: 'dialogue' };
  if (confirm(`Are you sure you want to delete this ${labels[type]}? This action cannot be undone.`)) {
    router.delete(`/admin/vn/scenes/${props.scene.id}/${type}/${id}`, {
      preserveScroll: true // Menjaga posisi scroll setelah menghapus data
    });
  }
}
</script>