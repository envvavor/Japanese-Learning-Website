<template>
  <VnAdminLayout>
    <div class="max-w-3xl mx-auto">
      <a :href="`/admin/vn/scenes/${scene.id}`" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-4 inline-block">← Back to {{ scene.title }}</a>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{{ dialogue ? 'Edit Dialogue' : 'New Dialogue' }}</h2>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Character (leave empty for Narrator)</label>
          <select v-model="form.character_id" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
            <option :value="null">— Narrator —</option>
            <option v-for="c in characters" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Background</label>
          <select v-model="form.background_id" required class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
            <option value="">— Select —</option>
            <option v-for="bg in backgrounds" :key="bg.id" :value="bg.id">{{ bg.name }}</option>
          </select>
          <p v-if="form.errors.background_id" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.background_id }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Original Text (Japanese)</label>
          <textarea v-model="form.original_text" required rows="3" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all resize-none" placeholder="日本語のテキストを入力..." />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Translated Text (Indonesian)</label>
          <textarea v-model="form.translated_text" required rows="3" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all resize-none" placeholder="Terjemahan dalam Bahasa Indonesia..." />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Next Dialogue (linear flow)</label>
          <select v-model="form.next_dialogue_id" class="w-full px-4 py-3 bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
            <option :value="null">— End / Use Choices —</option>
            <option v-for="d in allDialogues" :key="d.id" :value="d.id">#{{ d.id }} — {{ d.original_text.substring(0, 60) }}</option>
          </select>
        </div>

        <!-- Is First Dialogue -->
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" v-model="form.is_first" class="w-5 h-5 rounded bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-emerald-500 focus:ring-emerald-500/50" />
          <span class="text-sm text-gray-700 dark:text-gray-300">Set as <strong class="text-emerald-600 dark:text-emerald-400">starting dialogue</strong> for this scene</span>
        </label>

        <!-- Choices -->
        <div class="border border-gray-300 dark:border-gray-700/50 rounded-xl p-5 space-y-4 bg-gray-50 dark:bg-transparent">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Branching Choices</h3>
            <button type="button" @click="addChoice" class="px-3 py-1.5 bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-500/30 rounded-lg text-sm transition-colors">+ Add Choice</button>
          </div>
          <div v-for="(choice, index) in form.choices" :key="index" class="flex gap-3 items-start">
            <div class="flex-1 space-y-2">
              <input v-model="choice.choice_text" type="text" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/50 rounded-lg text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" placeholder="Choice text..." />
              <select v-model="choice.target_dialogue_id" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/50 rounded-lg text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                <option value="">— Target Dialogue —</option>
                <option v-for="d in allDialogues" :key="d.id" :value="d.id">#{{ d.id }} — {{ d.original_text.substring(0, 50) }}</option>
              </select>
            </div>
            <button type="button" @click="form.choices.splice(index, 1)" class="mt-1 p-2 bg-red-100 dark:bg-red-500/10 hover:bg-red-200 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 rounded-lg transition-colors">✕</button>
          </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
          <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg shadow-indigo-500/25">
            {{ form.processing ? 'Saving...' : (dialogue ? 'Update' : 'Create') }}
          </button>
          <a :href="`/admin/vn/scenes/${scene.id}`" class="px-6 py-3 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">Cancel</a>
        </div>
      </form>
    </div>
  </VnAdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import VnAdminLayout from '../../../Layouts/VnAdminLayout.vue';

const props = defineProps({ scene: Object, dialogue: Object, characters: Array, backgrounds: Array, allDialogues: Array, isFirst: Boolean });

const form = useForm({
  character_id: props.dialogue?.character_id || null,
  background_id: props.dialogue?.background_id || '',
  original_text: props.dialogue?.original_text || '',
  translated_text: props.dialogue?.translated_text || '',
  next_dialogue_id: props.dialogue?.next_dialogue_id || null,
  is_first: props.isFirst || false,
  choices: props.dialogue?.choices?.map(c => ({ choice_text: c.choice_text, target_dialogue_id: c.target_dialogue_id })) || [],
});

function addChoice() {
  form.choices.push({ choice_text: '', target_dialogue_id: '' });
}

function submit() {
  if (props.dialogue) {
    form.put(`/admin/vn/scenes/${props.scene.id}/dialogues/${props.dialogue.id}`);
  } else {
    form.post(`/admin/vn/scenes/${props.scene.id}/dialogues`);
  }
}
</script>
