<template>
  <VnAdminLayout>
    <div class="max-w-2xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">
        {{ scene ? 'Edit Scene' : 'Create Scene' }}
      </h2>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
          <input v-model="form.title" type="text" required
            class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all"
            placeholder="e.g. Chapter 1: The Beginning..." />
          <p v-if="form.errors.title" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
          <textarea v-model="form.description" rows="3"
            class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all resize-none"
            placeholder="Short description of this scene..." />
          <p v-if="form.errors.description" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thumbnail Image</label>
          <input type="file" accept="image/*" @change="form.thumbnail = $event.target.files[0]"
            class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-700 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-sm file:cursor-pointer" />
          <div v-if="scene?.thumbnail_path" class="mt-3">
            <img :src="`/storage/${scene.thumbnail_path}`" class="rounded-xl max-h-48 object-cover" />
          </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
          <button type="submit" :disabled="form.processing"
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25">
            {{ form.processing ? 'Saving...' : (scene ? 'Update' : 'Create') }}
          </button>
          <a href="/admin/vn/scenes" class="px-6 py-3 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

const props = defineProps({
  scene: Object,
});

const form = useForm({
  title: props.scene?.title || '',
  description: props.scene?.description || '',
  thumbnail: null,
});

function submit() {
  if (props.scene) {
    router.post(`/admin/vn/scenes/${props.scene.id}`, {
      _method: 'put',
      title: form.title,
      description: form.description,
      thumbnail: form.thumbnail,
    }, { forceFormData: true });
  } else {
    form.post('/admin/vn/scenes');
  }
}
</script>
