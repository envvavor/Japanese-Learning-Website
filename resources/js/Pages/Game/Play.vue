<template>
  <div class="fixed inset-0 w-full h-full bg-slate-950 overflow-hidden font-sans select-none">
    
    <div v-if="!isLoaded" class="absolute inset-0 z-50 bg-slate-900 flex flex-col items-center justify-center">
      <div class="text-6xl text-violet-500 animate-bounce mb-6">
        <i class="fas fa-theater-masks"></i>
      </div>
      <h2 class="text-2xl font-black text-white uppercase tracking-widest mb-2">Memuat Cerita...</h2>
      <p class="text-violet-400 font-black mb-6 text-xl">{{ Math.round(loadingProgress) }}%</p>
      
      <div class="w-64 h-6 bg-slate-800 border-4 border-slate-950 rounded-full overflow-hidden shadow-inner">
        <div class="h-full bg-violet-500 rounded-full transition-all duration-300" :style="{ width: `${loadingProgress}%` }"></div>
      </div>
    </div>

    <div v-else class="relative w-full h-full cursor-pointer" @click="handleClick">

      <transition name="fade">
        <div
          v-if="currentDialogue"
          :key="currentDialogue.background?.image_url"
          class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-1000 z-0"
          :style="{ backgroundImage: `url(${currentDialogue.background?.image_url || ''})` }"
        />
      </transition>

      <div class="absolute inset-0 bg-black/40 z-10 pointer-events-none" />
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.7)_100%)] z-10 pointer-events-none" />

      <transition name="slide-up">
        <div
          v-if="currentDialogue?.character?.default_sprite_path"
          :key="currentDialogue.character.name"
          class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none flex justify-center items-end h-[55vh] sm:h-[75vh]"
        >
          <img
            :src="currentDialogue.character.default_sprite_path"
            :alt="currentDialogue.character?.name"
            class="max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_30px_rgba(0,0,0,0.8)] scale-[1.7] sm:scale-125 origin-bottom transition-transform duration-500"
          />
        </div>
      </transition>

      <div v-if="!currentDialogue" class="absolute inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-gray-800 border-4 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 sm:p-10 text-center flex flex-col items-center shadow-2xl max-w-sm w-full">
          <i class="fas fa-flag-checkered text-5xl sm:text-6xl text-amber-500 mb-6"></i>
          <h1 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6">Tamat</h1>
          <div class="w-16 sm:w-20 h-2 bg-slate-200 dark:bg-gray-700 rounded-full mb-8"></div>
          
          <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
            <a href="/admin/vn/scenes" class="w-full flex items-center justify-center gap-2 bg-violet-500 border-2 border-b-[6px] border-violet-700 text-white font-black uppercase tracking-widest py-4 px-6 rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all text-sm sm:text-base" @click.stop>
              Kembali ke Admin
            </a>
          </template>
          <template v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
            <a href="/vn/scenes" class="w-full flex items-center justify-center gap-2 bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] text-white font-black uppercase tracking-widest py-4 px-6 rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all text-sm sm:text-base" @click.stop>
              Selesai
            </a>
          </template>
        </div>
      </div>

      <div v-if="currentDialogue" class="absolute bottom-4 sm:bottom-10 left-0 right-0 w-full px-3 sm:px-8 z-40 flex flex-col items-center pointer-events-none">
        
        <transition name="fade">
          <div v-if="hasChoices" class="flex flex-col gap-3 sm:gap-4 mb-6 sm:mb-8 w-full max-w-2xl pointer-events-auto">
            <button
              v-for="choice in currentDialogue.choices"
              :key="choice.id"
              @click.stop="goToDialogue(choice.target_dialogue_id)"
              class="w-full bg-slate-900/95 backdrop-blur-md border-4 border-b-[6px] sm:border-b-[8px] border-slate-700 text-white font-black text-lg sm:text-xl py-4 sm:py-5 px-6 sm:px-8 rounded-2xl sm:rounded-3xl hover:border-violet-500 hover:text-violet-300 active:border-b-4 active:translate-y-1 transition-all shadow-xl text-center"
            >
              {{ choice.choice_text }}
            </button>
          </div>
        </transition>

        <div class="relative w-full max-w-5xl bg-slate-900/95 backdrop-blur-md border-4 border-b-[6px] sm:border-b-[8px] border-slate-800 rounded-3xl sm:rounded-[2rem] p-5 sm:p-10 pointer-events-auto shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
          
          <div v-if="currentDialogue.character" class="absolute -top-5 sm:-top-7 left-4 sm:left-10 bg-violet-600 border-2 border-b-[4px] sm:border-b-[6px] border-violet-800 text-white font-black uppercase tracking-widest px-5 sm:px-8 py-1.5 sm:py-3 rounded-xl sm:rounded-2xl text-xs sm:text-xl shadow-lg transform -rotate-2 origin-bottom-left">
            {{ currentDialogue.character.name }}
          </div>

          <div class="min-h-[80px] sm:min-h-[100px] flex flex-col justify-center mt-3 sm:mt-2">
            
            <p class="text-2xl sm:text-4xl font-black text-white leading-snug sm:leading-relaxed mb-3 sm:mb-4 tracking-wide break-words" :key="'orig-' + currentDialogue.id">
              <span
                v-for="(char, i) in displayedOriginal"
                :key="i"
                class="vn-char inline-block"
                :style="{ animationDelay: `${i * 30}ms` }"
              >{{ char }}</span>
            </p>
            
            <p class="text-xs sm:text-lg font-bold text-slate-400 border-t-2 border-dashed border-slate-700 pt-3 sm:pt-4" :key="'trans-' + currentDialogue.id">
              {{ currentDialogue.translated_text }}
            </p>
          </div>

          <div v-if="!hasChoices" class="absolute bottom-4 sm:bottom-5 right-5 sm:right-8 text-slate-500 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center gap-1.5 sm:gap-2">
            <span class="hidden sm:inline">Klik Layar / Spasi</span><span class="sm:hidden">Tap Layar</span> 
            <span class="text-violet-500 animate-bounce text-lg sm:text-xl">▼</span>
          </div>
        </div>
      </div>

      <audio
        ref="audioEl"
        :src="currentDialogue?.audio_file_path || ''"
        @ended="audioPlaying = false"
        preload="auto"
      />

      <div v-if="currentDialogue" class="absolute top-4 sm:top-6 right-4 sm:right-6 z-50 flex items-center gap-2 sm:gap-3 pointer-events-auto">
        
        <button
          v-if="dialogueHistory.length > 0"
          class="flex items-center justify-center gap-1.5 sm:gap-2 h-10 sm:h-12 px-4 sm:px-6 bg-slate-800/90 backdrop-blur-md border-2 border-b-[4px] border-slate-900 text-white font-black uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-slate-700 active:border-b-2 active:translate-y-1 transition-all shadow-lg text-[10px] sm:text-sm"
          @click.stop="goBack"
        >
          <i class="fas fa-undo text-[10px] sm:text-sm"></i> Back
        </button>

        <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
          <a href="/admin/vn/scenes" class="flex items-center justify-center gap-1.5 sm:gap-2 h-10 sm:h-12 px-4 sm:px-6 bg-rose-600/90 backdrop-blur-md border-2 border-b-[4px] border-rose-800 text-white font-black uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-rose-500 active:border-b-2 active:translate-y-1 transition-all shadow-lg text-[10px] sm:text-sm" @click.stop>
            Keluar
          </a>
        </template>
        <template v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
          <a href="/vn/scenes" class="flex items-center justify-center gap-1.5 sm:gap-2 h-10 sm:h-12 px-4 sm:px-6 bg-rose-600/90 backdrop-blur-md border-2 border-b-[4px] border-rose-800 text-white font-black uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-rose-500 active:border-b-2 active:translate-y-1 transition-all shadow-lg text-[10px] sm:text-sm" @click.stop>
            Keluar
          </a>
        </template>

        <button class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-800/90 backdrop-blur-md border-2 border-b-[4px] border-slate-900 text-white font-black rounded-xl sm:rounded-2xl hover:bg-slate-700 active:border-b-2 active:translate-y-1 transition-all flex items-center justify-center shadow-lg text-sm sm:text-xl" @click.stop="toggleAudio" title="Mute/Unmute Voice">
          <i :class="audioMuted ? 'fas fa-volume-mute text-slate-400' : 'fas fa-volume-up text-emerald-400'"></i>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
  allDialogues: { type: Array, default: () => [] },
  startDialogueId: [Number, String],
  sceneId: [Number, String],
  sceneVersion: [Number, String],
});

// ============================================================
// STATE
// ============================================================
const isLoaded = ref(false);
const loadingProgress = ref(0);
const currentDialogue = ref(null);
const dialogueHistory = ref([]);
const audioEl = ref(null);
const audioPlaying = ref(false);
const audioMuted = ref(false);

// ============================================================
// COMPUTED
// ============================================================
const hasChoices = computed(() => currentDialogue.value?.choices && currentDialogue.value.choices.length > 0);
const displayedOriginal = computed(() => currentDialogue.value?.original_text?.split('') || []);

// ============================================================
// PRELOADER — Load semua aset, decode gambar ke memori
// ============================================================
onMounted(async () => {
  const imageUrls = new Set();
  const audioUrls = new Set();

  // Kumpulkan SEMUA aset dari SEMUA dialogue
  props.allDialogues.forEach(d => {
    if (d.background?.image_url) imageUrls.add(d.background.image_url);
    if (d.character?.default_sprite_path) imageUrls.add(d.character.default_sprite_path);
    if (d.audio_file_path) audioUrls.add(d.audio_file_path);
  });

  const allUrls = [...imageUrls, ...audioUrls];
  let loadedCount = 0;

  if (allUrls.length === 0) {
    finishLoading();
    return;
  }

  const updateProgress = () => {
    loadedCount++;
    loadingProgress.value = (loadedCount / allUrls.length) * 100;
  };

  // Gambar: decode ke memori — tampil instan tanpa pop
  const imagePromises = Array.from(imageUrls).map(url => {
    return new Promise(resolve => {
      const img = new Image();
      img.src = url;
      img.decode()
        .catch(() => {}) // Tetap lanjut kalau gagal
        .finally(() => {
          updateProgress();
          resolve();
          // img sengaja dibiarkan di-GC setelah ini
          // Browser sudah decode ke internal cache, cukup
        });
    });
  });

  // Audio: fetch biar ke-cache di HTTP cache browser
  const audioPromises = Array.from(audioUrls).map(url => {
    return fetch(url)
      .then(r => r.blob())
      .catch(() => {})
      .finally(() => updateProgress());
  });

  await Promise.all([...imagePromises, ...audioPromises]);
  setTimeout(finishLoading, 500);
});

function finishLoading() {
  isLoaded.value = true;
  dialogueHistory.value = [];
  goToDialogue(props.startDialogueId);
  window.addEventListener('keydown', handleKeydown);
}

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

// ============================================================
// AUDIO LOGIC
// ============================================================
watch(() => currentDialogue.value?.id, async () => {
  await nextTick();
  playAudio();
});

function playAudio() {
  if (audioEl.value && currentDialogue.value?.audio_file_path && !audioMuted.value) {
    audioEl.value.load();
    audioEl.value.play().catch(() => {});
    audioPlaying.value = true;
  }
}

function toggleAudio() {
  audioMuted.value = !audioMuted.value;
  if (audioEl.value) {
    audioMuted.value ? audioEl.value.pause() : playAudio();
  }
}

// ============================================================
// GAMEPLAY LOGIC
// ============================================================
function handleClick() {
  if (!currentDialogue.value || hasChoices.value) return;
  advance();
}

function handleKeydown(e) {
  if (e.code === 'Space' || e.code === 'Enter') {
    e.preventDefault();
    if (!currentDialogue.value || hasChoices.value) return;
    advance();
  } else if (e.code === 'ArrowLeft') {
    e.preventDefault();
    goBack();
  }
}

function advance() {
  if (currentDialogue.value?.next_dialogue_id) {
    goToDialogue(currentDialogue.value.next_dialogue_id);
  } else {
    currentDialogue.value = null;
  }
}

function goBack() {
  if (dialogueHistory.value.length === 0) return;
  const prevDialogueId = dialogueHistory.value.pop();
  goToDialogue(prevDialogueId, true);
}

function goToDialogue(dialogueId, isBack = false) {
  if (!isBack && currentDialogue.value) {
    dialogueHistory.value.push(currentDialogue.value.id);
  }

  const nextDialog = props.allDialogues.find(d => String(d.id) === String(dialogueId));

  if (nextDialog) {
    currentDialogue.value = nextDialog;
    window.history.pushState({}, '', `/vn/play/${dialogueId}`);
  } else {
    currentDialogue.value = null;
  }
}
</script>

<style scoped>
/* Transisi Standar Vue */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Transisi Kemunculan Karakter (Slide up dari bawah) */
.slide-up-enter-active {
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.slide-up-leave-active {
  transition: all 0.3s ease-in;
}
.slide-up-enter-from {
  opacity: 0;
  transform: translateY(10%);
}
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-5%);
}

/* Animasi Mesin Tik (Typewriter Effect) */
.vn-char {
  opacity: 0;
  animation: revealChar 0.08s ease forwards;
}

@keyframes revealChar {
  0% { 
    opacity: 0; 
    transform: translateY(4px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}
</style>