<template>
  <div class="vn-wrapper">
    <div v-if="!isLoaded" class="vn-loader">
      <div class="loader-inner">
        <div class="loader-icon">✦</div>
        <h2 class="loader-title">Loading...</h2>
        <p class="loader-pct">{{ Math.round(loadingProgress) }}%</p>
        <div class="loader-track">
          <div class="loader-fill" :style="{ width: `${loadingProgress}%` }"></div>
        </div>
      </div>
    </div>

    <div v-else class="vn-player" @click="handleClick">

      <transition name="fade">
        <div
          v-if="currentDialogue"
          :key="currentDialogue.background?.image_url"
          class="vn-background"
          :style="{ backgroundImage: `url(${currentDialogue.background?.image_url || ''})` }"
        />
      </transition>

      <div class="vn-overlay" />
      <div class="vn-vignette" />

      <transition name="slide-up">
        <div
          v-if="currentDialogue?.character?.default_sprite_path"
          :key="currentDialogue.character.name"
          class="vn-character"
        >
          <img
            :src="currentDialogue.character.default_sprite_path"
            :alt="currentDialogue.character?.name"
            class="vn-character-img"
          />
        </div>
      </transition>

      <div v-if="!currentDialogue" class="vn-empty">
        <div class="end-card">
          <div class="end-ornament">✦</div>
          <h1 class="end-title">Tamat</h1>
          <div class="end-rule"></div>
          <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
            <a href="/admin/vn/scenes" class="end-btn" @click.stop>Kembali ke Admin</a>
          </template>
          <template v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
            <a href="/vn/scenes" class="end-btn" @click.stop>Kembali ke Dashboard</a>
          </template>
        </div>
      </div>

      <div v-if="currentDialogue" class="vn-dialogue-area">
        <transition name="fade">
          <div v-if="hasChoices" class="vn-choices">
            <button
              v-for="choice in currentDialogue.choices"
              :key="choice.id"
              @click.stop="goToDialogue(choice.target_dialogue_id)"
              class="vn-choice-btn"
            >
              {{ choice.choice_text }}
            </button>
          </div>
        </transition>

        <div class="vn-dialogue-box">
          <div v-if="currentDialogue.character" class="vn-name-tag">
            <span class="name-accent">◆</span>
            {{ currentDialogue.character.name }}
          </div>

          <div class="vn-text-content">
            <p class="vn-original-text" :key="'orig-' + currentDialogue.id">
              <span
                v-for="(char, i) in displayedOriginal"
                :key="i"
                class="vn-char"
                :style="{ animationDelay: `${i * 30}ms` }"
              >{{ char }}</span>
            </p>
            <p class="vn-translated-text" :key="'trans-' + currentDialogue.id">
              {{ currentDialogue.translated_text }}
            </p>
          </div>

          <div v-if="!hasChoices" class="vn-nav-indicator">
            <span class="vn-nav-arrow">▾</span>
            <span>Klik / Spasi (Next) &nbsp;·&nbsp; ◄ (Back)</span>
          </div>
        </div>
      </div>

      <audio
        ref="audioEl"
        :src="currentDialogue?.audio_file_path || ''"
        @ended="audioPlaying = false"
        preload="auto"
      />

      <div v-if="currentDialogue" class="vn-hud">
        <button
          v-if="dialogueHistory.length > 0"
          class="vn-hud-btn"
          @click.stop="goBack"
          title="Kembali ke teks sebelumnya"
        >
          <svg class="hud-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
          </svg>
          Back
        </button>

        <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
          <a href="/admin/vn/scenes" class="vn-hud-btn" @click.stop>Admin</a>
        </template>
        <template v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
          <a href="/vn/scenes" class="vn-hud-btn" @click.stop>Kembali</a>
        </template>

        <button class="vn-hud-btn vn-hud-audio" @click.stop="toggleAudio">
          {{ audioMuted ? '🔇' : '🔊' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
  allDialogues: {
    type: Array,
    default: () => []
  },
  startDialogueId: [Number, String],
});

// State
const isLoaded = ref(false);
const loadingProgress = ref(0);
const currentDialogue = ref(null);

// ARRAY UNTUK MENYIMPAN JEJAK (HISTORY)
const dialogueHistory = ref([]);

const audioEl = ref(null);
const audioPlaying = ref(false);
const audioMuted = ref(false);

const hasChoices = computed(() => currentDialogue.value?.choices && currentDialogue.value.choices.length > 0);
const displayedOriginal = computed(() => currentDialogue.value?.original_text?.split('') || []);

// PRELOADER
onMounted(async () => {
  const assetUrls = new Set();
  
  // 1. Kumpulkan semua alamat file (Gambar + Audio)
  props.allDialogues.forEach(d => {
    if (d.background?.image_url) assetUrls.add(d.background.image_url);
    if (d.character?.default_sprite_path) assetUrls.add(d.character.default_sprite_path);
    
    // 🔥 TAMBAHKAN AUDIO KE DAFTAR SEDOTAN
    if (d.audio_file_path) assetUrls.add(d.audio_file_path);
  });

  const urlsToLoad = Array.from(assetUrls);
  let loadedCount = 0;

  if (urlsToLoad.length === 0) {
    finishLoading();
    return;
  }

  // Fungsi untuk update progress bar
  const updateProgress = () => {
    loadedCount++;
    loadingProgress.value = (loadedCount / urlsToLoad.length) * 100;
  };

  // 2. Proses Download Semua Aset
  const loadPromises = urlsToLoad.map(url => {
    // A. Jika file adalah Audio (Cek dari ekstensi)
    if (url.includes('.mp3') || url.includes('.wav') || url.includes('.ogg')) {
      return fetch(url)
        .then(response => response.blob()) // Sedot file ke cache browser
        .then(() => { updateProgress(); })
        .catch(() => { updateProgress(); }); // Kalau error tetep lanjut biar gak stuck
    } 
    // B. Jika file adalah Gambar
    else {
      return new Promise((resolve) => {
        const img = new Image();
        img.src = url;
        img.onload = () => { updateProgress(); resolve(); };
        img.onerror = () => { updateProgress(); resolve(); };
      });
    }
  });

  // Tunggu semua bala bantuan selesai didownload
  await Promise.all(loadPromises);
  
  // Kasih jeda estetik sebelum masuk layar utama
  setTimeout(finishLoading, 500);
});

function finishLoading() {
  isLoaded.value = true;
  dialogueHistory.value = []; // Reset history di awal
  goToDialogue(props.startDialogueId);
  window.addEventListener('keydown', handleKeydown);
}

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

// AUDIO LOGIC
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
    if (audioMuted.value) {
      audioEl.value.pause();
    } else {
      playAudio();
    }
  }
}

// GAMEPLAY LOGIC
function handleClick() {
  if (!currentDialogue.value || hasChoices.value) return;
  advance();
}

function handleKeydown(e) {
  // Tombol Next (Spasi / Enter)
  if (e.code === 'Space' || e.code === 'Enter') {
    e.preventDefault();
    if (!currentDialogue.value || hasChoices.value) return;
    advance();
  }
  // Tombol Back (Panah Kiri)
  else if (e.code === 'ArrowLeft') {
    e.preventDefault();
    goBack();
  }
}

function advance() {
  if (currentDialogue.value?.next_dialogue_id) {
    goToDialogue(currentDialogue.value.next_dialogue_id);
  } else {
    currentDialogue.value = null; // Tamat
  }
}

// FUNGSI KEMBALI 1 DIALOG
function goBack() {
  // Cek apakah ada history sebelumnya
  if (dialogueHistory.value.length === 0) return;
  
  // Ambil ID dialog sebelumnya dari array history dan hapus dari array tersebut (pop)
  const prevDialogueId = dialogueHistory.value.pop();
  
  // Pergi ke dialog tersebut (isBack = true, agar tidak masuk ke history lagi)
  goToDialogue(prevDialogueId, true);
}

// LOCAL NAVIGATION
function goToDialogue(dialogueId, isBack = false) {
  // Jika ini adalah pergerakan maju (bukan back), rekam jejak dialog saat ini ke History
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
@import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@400;500;600&display=swap');

/* ── Variables ───────────────────────────────────────── */
/* :root {
  --gold:   #c9a86c;
  --gold-d: #a07840;
  --ink:    #050404;
  --panel:  rgba(8, 7, 14, 0.82);
  --border: rgba(201, 168, 108, 0.18);
  --muted:  rgba(201, 168, 108, 0.45);
  --text:   #ffffffe5;
  --sub:    #8c8070;
} */

/* ── Loader ──────────────────────────────────────────── */
.vn-loader {
  position: fixed; inset: 0;
  background: var(--ink);
  display: flex; align-items: center; justify-content: center;
  z-index: 100;
}
.loader-inner {
  display: flex; flex-direction: column; align-items: center;
  gap: 12px;
}
.loader-icon {
  font-size: 2rem; color: var(--gold);
  animation: spin-slow 4s linear infinite;
}
@keyframes spin-slow { to { transform: rotate(360deg); } }

.loader-title {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.75rem; font-weight: 600;
  letter-spacing: 0.25em; text-transform: uppercase;
  color: var(--muted); margin: 0;
}
.loader-pct {
  font-family: 'Crimson Pro', serif;
  font-size: 2.5rem; font-weight: 400; color: var(--text);
  line-height: 1; margin: 0;
}
.loader-track {
  width: 220px; height: 2px;
  background: rgba(255,255,255,0.07);
  border-radius: 2px; overflow: hidden;
}
.loader-fill {
  height: 100%; background: var(--gold);
  border-radius: 2px;
  transition: width 0.3s ease-out;
  box-shadow: 0 0 8px rgba(201,168,108,0.5);
}

/* ── Base ────────────────────────────────────────────── */
.vn-wrapper { 
  background: var(--ink); 
  min-height: 100vh; 
  color-scheme: dark; /* Tambahan: Kunci agar browser tahu ini area mode gelap */
  --gold:   #006eff;
  --gold-d: #1000f0;
  --ink:     #050404;
  --panel:   rgba(8, 7, 14, 0.82);
  --border: rgba(0, 0, 0, 0.18);
  --muted:  rgba(46, 42, 253, 0.45);
  --text:   #ffffffe5; /* Teks putihmu aman di sini */
  --sub:    #f8f8f8c5;
}

.vn-player {
  position: fixed; inset: 0; overflow: hidden;
  cursor: pointer; user-select: none;
  background: var(--ink);
  font-family: 'DM Sans', sans-serif;
}

/* ── Background ──────────────────────────────────────── */
.vn-background {
  position: absolute; inset: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: opacity 0.7s ease;
  z-index: 0;
}

/* ── Overlays ────────────────────────────────────────── */
.vn-overlay {
  position: absolute; inset: 0; z-index: 5; pointer-events: none;
  background: linear-gradient(
    to bottom,
    rgba(0,0,0,0.15) 0%,
    transparent        35%,
    rgba(0,0,0,0.4)   65%,
    rgba(8,7,14,0.92) 100%
  );
}
/* subtle radial vignette on edges */
.vn-vignette {
  position: absolute; inset: 0; z-index: 6; pointer-events: none;
  background: radial-gradient(ellipse at center, transparent 50%, rgba(0,0,0,0.55) 100%);
}

/* ── Character ───────────────────────────────────────── */
.vn-character {
  position: absolute; bottom: 0; left: 50%;
  transform: translateX(-50%);
  z-index: 10; pointer-events: none;
  filter: drop-shadow(0 20px 50px rgba(0,0,0,0.6));
}
.vn-character-img {
  height: 110vh; width: auto;
  max-width: none !important; max-height: none;
  object-fit: contain; object-position: bottom center;
}

/* ── End Screen ──────────────────────────────────────── */
.vn-empty {
  position: absolute; inset: 0; z-index: 30;
  display: flex; align-items: center; justify-content: center;
  background: radial-gradient(ellipse at center, rgba(201,168,108,0.04) 0%, transparent 70%);
}
.end-card {
  display: flex; flex-direction: column; align-items: center;
  gap: 16px;
}
.end-ornament {
  font-size: 1.5rem; color: var(--gold);
  animation: pulse-gold 2.5s ease-in-out infinite;
}
@keyframes pulse-gold {
  0%, 100% { opacity: 0.6; transform: scale(1);    }
  50%       { opacity: 1;   transform: scale(1.15); }
}
.end-title {
  font-family: 'Crimson Pro', serif;
  font-size: 3.5rem; font-weight: 600; color: var(--text);
  letter-spacing: 0.08em; margin: 0;
}
.end-rule {
  width: 60px; height: 1px;
  background: linear-gradient(to right, transparent, var(--gold), transparent);
}
.end-btn {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.8rem; font-weight: 600;
  letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--gold); text-decoration: none;
  padding: 10px 28px;
  border: 1px solid var(--border);
  border-radius: 4px;
  background: rgba(201,168,108,0.06);
  transition: all 0.2s ease;
}
.end-btn:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.45);
  color: white;
}

/* ── Dialogue Area ───────────────────────────────────── */
.vn-dialogue-area {
  position: absolute; bottom: 0; left: 0; right: 0;
  z-index: 20; padding: 0 28px 28px;
}

/* ── Choices ─────────────────────────────────────────── */
.vn-choices {
  display: flex; flex-direction: column; align-items: center;
  gap: 10px; margin-bottom: 18px; padding: 0 18%;
}
.vn-choice-btn {
  width: 100%; max-width: 520px;
  padding: 14px 24px;
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 4px;
  color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem; font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: center;
  position: relative; overflow: hidden;
}
.vn-choice-btn::before {
  content: '';
  position: absolute; left: 0; top: 0; bottom: 0;
  width: 2px;
  background: var(--gold);
  transform: scaleY(0);
  transition: transform 0.2s ease;
  transform-origin: bottom;
}
.vn-choice-btn:hover {
  background: rgba(0, 0, 0, 0.514);
  border-color: rgba(0, 89, 255, 0.4);
  color: white;
  transform: translateX(4px);
}
.vn-choice-btn:hover::before { transform: scaleY(1); }

/* ── Dialogue Box ────────────────────────────────────── */
.vn-dialogue-box {
  position: relative;
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 28px 36px 22px;
  max-width: 920px; margin: 0 auto;
  box-shadow: 0 -8px 40px rgba(0,0,0,0.4), inset 0 1px 0 rgba(201,168,108,0.08);
}
/* top accent line */
.vn-dialogue-box::before {
  content: '';
  position: absolute; top: 0; left: 10%; right: 10%;
  height: 1px;
  background: linear-gradient(to right, transparent, var(--gold-d), transparent);
}

/* ── Name Tag ────────────────────────────────────────── */
.vn-name-tag {
  position: absolute; top: -13px; left: 28px;
  display: inline-flex; align-items: center; gap: 6px;
  background: var(--ink);
  border: 1px solid var(--border);
  color: white;
  padding: 4px 18px 4px 12px;
  border-radius: 3px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.78rem; font-weight: 600;
  letter-spacing: 0.12em; text-transform: uppercase;
}
.name-accent { font-size: 0.55rem; opacity: 0.8; }

/* ── Dialogue Text ───────────────────────────────────── */
.vn-text-content { padding-top: 6px; }

.vn-original-text {
  font-family: 'Crimson Pro', serif;
  font-size: 1.35rem; line-height: 1.85;
  color: var(--text); margin-bottom: 10px; min-height: 2.5em;
}
.vn-char {
  display: inline-block; opacity: 0;
  animation: charAppear 0.08s ease forwards;
}
@keyframes charAppear {
  from { opacity: 0; transform: translateY(3px); }
  to   { opacity: 1; transform: translateY(0);   }
}

.vn-translated-text {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem; color: var(--sub);
  line-height: 1.6; font-style: italic;
  border-top: 1px solid rgba(255,255,255,0.05);
  padding-top: 10px;
}

/* ── Nav Indicator ───────────────────────────────────── */
.vn-nav-indicator {
  display: flex; align-items: center; justify-content: flex-end;
  gap: 6px; margin-top: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.72rem; letter-spacing: 0.05em;
  color: rgba(140,128,112,0.5);
}
.vn-nav-arrow {
  color: var(--gold); opacity: 0.6;
  animation: bounce-gentle 1.8s ease-in-out infinite;
}
@keyframes bounce-gentle {
  0%, 100% { transform: translateY(0);  }
  50%       { transform: translateY(3px);}
}

/* ── HUD ─────────────────────────────────────────────── */
.vn-hud {
  position: absolute; top: 18px; right: 18px;
  z-index: 50; display: flex; gap: 6px;
}
.vn-hud-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 14px;
  background: rgba(8,7,14,0.65);
  border: 1px solid rgba(201,168,108,0.15);
  border-radius: 3px;
  color: var(--gold);
  font-family: 'DM Sans', sans-serif;
  font-size: 0.75rem; font-weight: 500; letter-spacing: 0.06em;
  cursor: pointer; text-decoration: none;
  transition: all 0.2s ease;
}
.vn-hud-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  border-color: rgba(255, 255, 255, 0.35);
  color: var(--gold);
  color: white;
}
.hud-icon { width: 13px; height: 13px; }

/* ── Transitions ─────────────────────────────────────── */
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from,  .fade-leave-to      { opacity: 0; }

.slide-up-enter-active { transition: all 0.55s cubic-bezier(0.22, 1, 0.36, 1); }
.slide-up-leave-active { transition: all 0.25s ease-in; }
.slide-up-enter-from   { opacity: 0; transform: translateX(-50%) translateY(36px); }
.slide-up-leave-to     { opacity: 0; transform: translateX(-50%) translateY(-16px); }

/* ── Mobile ──────────────────────────────────────────── */
@media (max-width: 768px) {
  .vn-character-img { height: 100vh; max-width: none !important; }
  .vn-dialogue-area { padding: 0 14px 16px; }
  .vn-dialogue-box  { padding: 22px 18px 16px; border-radius: 5px; }
  .vn-name-tag      { top: -11px; left: 14px; padding: 3px 14px 3px 10px; font-size: 0.7rem; }
  .vn-original-text { font-size: 1.15rem; line-height: 1.7; margin-bottom: 6px; }
  .vn-translated-text { font-size: 0.82rem; }
  .vn-choices       { padding: 0 4%; gap: 8px; }
  .vn-choice-btn    { padding: 12px 16px; font-size: 0.88rem; border-radius: 3px; }
  .vn-hud           { top: 12px; right: 12px; gap: 5px; }
  .vn-hud-btn       { padding: 6px 11px; font-size: 0.7rem; }
}
@media (max-height: 500px) {
  .vn-character-img { height: 95vh; }
  .vn-dialogue-area { padding-bottom: 8px; }
  .vn-dialogue-box  { padding: 18px 22px 12px; }
  .vn-original-text { font-size: 1.05rem; min-height: 2em; }
}
</style>