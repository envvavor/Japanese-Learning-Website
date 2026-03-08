<template>
  <VnAdminLayout>
    <div class="max-w-2xl mx-auto">
      <a :href="`/admin/vn/scenes/${scene.id}`" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-4 inline-block">← Back to {{ scene.title }}</a>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{{ character ? 'Edit Character' : 'New Character' }}</h2>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
          <input v-model="form.name" type="text" required class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" placeholder="e.g. Sakura, Narrator..." />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sprite Image</label>
          <input type="file" accept="image/*" @change="form.sprite_image = $event.target.files[0]" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-700 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-sm file:cursor-pointer" />
          <p v-if="form.errors.sprite_image" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.sprite_image }}</p>
          <div v-if="character?.default_sprite_path" class="mt-3">
            <img :src="`/storage/${character.default_sprite_path}`" class="rounded-xl max-h-48 object-contain" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ElevenLabs Voice</label>
          <select v-if="voices.length > 0" v-model="form.elevenlabs_voice_id" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
            <option value="">— No voice —</option>
            <option v-for="voice in voices" :key="voice.voice_id" :value="voice.voice_id">{{ voice.name }}</option>
          </select>
          <div v-else class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl">
            <p class="text-gray-500 text-sm">⚠️ ElevenLabs API key not configured.</p>
            <input v-model="form.elevenlabs_voice_id" type="text" class="mt-2 w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/50 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50" placeholder="Manually enter voice ID..." />
          </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
          <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25">
            {{ form.processing ? 'Saving...' : (character ? 'Update' : 'Create') }}
          </button>
          <a :href="`/admin/vn/scenes/${scene.id}`" class="px-6 py-3 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">Cancel</a>
        </div>
      </form>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

const props = defineProps({ scene: Object, character: Object, voices: Array });

const form = useForm({
  name: props.character?.name || '',
  sprite_image: null,
  elevenlabs_voice_id: props.character?.elevenlabs_voice_id || '',
});

function submit() {
  if (props.character) {
    router.post(`/admin/vn/scenes/${props.scene.id}/characters/${props.character.id}`, {
      _method: 'put', name: form.name, sprite_image: form.sprite_image, elevenlabs_voice_id: form.elevenlabs_voice_id,
    }, { forceFormData: true });
  } else {
    form.post(`/admin/vn/scenes/${props.scene.id}/characters`);
  }
}
</script>
