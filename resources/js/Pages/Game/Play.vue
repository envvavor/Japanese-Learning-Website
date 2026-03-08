<template>
  <div class="vn-player" @click="handleClick">
    <!-- Background -->
    <transition name="fade" mode="out-in">
      <div
        v-if="dialogue"
        :key="dialogue.background?.image_url"
        class="vn-background"
        :style="{ backgroundImage: `url(${dialogue.background?.image_url || ''})` }"
      />
    </transition>

    <!-- Gradient overlay for readability -->
    <div class="vn-overlay" />

    <!-- Character Sprite -->
    <transition name="slide-up">
      <div
        v-if="dialogue?.character?.default_sprite_path"
        :key="dialogue.character.name"
        class="vn-character"
      >
        <img
          :src="dialogue.character.default_sprite_path"
          :alt="dialogue.character?.name"
          class="vn-character-img"
        />
      </div>
    </transition>

    <!-- Empty State -->
    <div v-if="!dialogue" class="vn-empty">
      <div class="text-center">
        <div class="text-8xl mb-6 animate-bounce">📖</div>
        <h1 class="text-4xl font-bold text-white mb-3">No Story Yet</h1>
        <p class="text-gray-400 text-lg mb-8">Create dialogues in the admin panel to start your visual novel.</p>
        <a
          href="/admin/vn/dialogues"
          class="inline-flex px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-semibold transition-all duration-200 shadow-2xl shadow-indigo-500/30"
        >
          Open Admin Panel →
        </a>
      </div>
    </div>

    <!-- Dialogue Box Area -->
    <div v-if="dialogue" class="vn-dialogue-area">
      <!-- Choices (shown when available) -->
      <transition name="fade">
        <div v-if="hasChoices" class="vn-choices">
          <button
            v-for="choice in dialogue.choices"
            :key="choice.id"
            @click.stop="goToDialogue(choice.target_dialogue_id)"
            class="vn-choice-btn"
          >
            {{ choice.choice_text }}
          </button>
        </div>
      </transition>

      <!-- Dialogue Box -->
      <div class="vn-dialogue-box">
        <!-- Character Name Tag -->
        <div v-if="dialogue.character" class="vn-name-tag">
          {{ dialogue.character.name }}
        </div>

        <!-- Text Content -->
        <div class="vn-text-content">
          <p class="vn-original-text" :key="'orig-' + dialogue.id">
            <span
              v-for="(char, i) in displayedOriginal"
              :key="i"
              class="vn-char"
              :style="{ animationDelay: `${i * 30}ms` }"
            >{{ char }}</span>
          </p>
          <p class="vn-translated-text" :key="'trans-' + dialogue.id">
            {{ dialogue.translated_text }}
          </p>
        </div>

        <!-- Navigation indicator -->
        <div v-if="!hasChoices" class="vn-nav-indicator">
          <span class="vn-nav-arrow">▼</span>
          <span class="text-xs">Click anywhere or press Space</span>
        </div>
      </div>
    </div>

    <!-- Audio Player (hidden) -->
    <audio
      ref="audioEl"
      :src="dialogue?.audio_file_path || ''"
      @ended="audioPlaying = false"
      preload="auto"
    />

    <div v-if="dialogue" class="vn-hud">
      <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
        <a
          href="/admin/vn/scenes"
          class="vn-hud-btn"
          @click.stop
        >
          ⚙️ Admin
        </a>
        <button
          class="vn-hud-btn"
          @click.stop="toggleAudio"
        >
          {{ audioMuted ? '🔇' : '🔊' }}
        </button>
      </template>

      <template v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
        <a
          href="/dashboard"
          class="vn-hud-btn"
          @click.stop
        >
          Kembali
        </a>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  dialogue: Object,
});

const audioEl = ref(null);
const audioPlaying = ref(false);
const audioMuted = ref(false);

const hasChoices = computed(() => {
  return props.dialogue?.choices && props.dialogue.choices.length > 0;
});

const displayedOriginal = computed(() => {
  return props.dialogue?.original_text?.split('') || [];
});

// Play audio when dialogue changes
watch(
  () => props.dialogue?.id,
  async () => {
    await nextTick();
    playAudio();
  }
);

onMounted(() => {
  playAudio();
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

function playAudio() {
  if (audioEl.value && props.dialogue?.audio_file_path && !audioMuted.value) {
    audioEl.value.load();
    audioEl.value.play().catch(() => {
      // Autoplay blocked by browser — that's ok
    });
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

function handleClick() {
  if (!props.dialogue || hasChoices.value) return;
  advance();
}

function handleKeydown(e) {
  if (e.code === 'Space' || e.code === 'Enter') {
    e.preventDefault();
    if (!props.dialogue || hasChoices.value) return;
    advance();
  }
}

function advance() {
  if (props.dialogue?.next_dialogue_id) {
    goToDialogue(props.dialogue.next_dialogue_id);
  }
}

function goToDialogue(dialogueId) {
  router.visit(`/vn/play/${dialogueId}`, {
    preserveScroll: true,
  });
}
</script>

<style scoped>
.vn-player {
  position: fixed;
  inset: 0;
  overflow: hidden;
  cursor: pointer;
  user-select: none;
  background: #0a0a0f;
  font-family: 'Inter', system-ui, sans-serif;
}

/* Background */
.vn-background {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: opacity 0.6s ease;
}

.vn-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    transparent 0%,
    transparent 40%,
    rgba(0, 0, 0, 0.3) 60%,
    rgba(0, 0, 0, 0.85) 100%
  );
  pointer-events: none;
}

/* Character */
.vn-character {
  position: absolute;
  bottom: 0; 
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
  filter: drop-shadow(0 10px 40px rgba(0, 0, 0, 0.5));
  pointer-events: none; 
}

.vn-character-img {
  height: 95vh; /* Tinggi dasar untuk desktop */
  width: auto;
  max-width: none !important; /* 🔥 INI KUNCINYA: Mencegah Tailwind mengecilkan gambar di HP */
  max-height: none;
  object-fit: contain;
  object-position: bottom center;
}

/* Empty State */
.vn-empty {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 30;
  background: radial-gradient(ellipse at center, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
}

/* Dialogue Area */
.vn-dialogue-area {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 20;
  padding: 0 24px 24px;
}

/* Choices */
.vn-choices {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding: 0 20%;
}

.vn-choice-btn {
  width: 100%;
  max-width: 500px;
  padding: 16px 24px;
  background: rgba(99, 102, 241, 0.15);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 16px;
  color: #c7d2fe;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
  text-align: center;
}

.vn-choice-btn:hover {
  background: rgba(99, 102, 241, 0.3);
  border-color: rgba(99, 102, 241, 0.6);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3);
}

/* Dialogue Box */
.vn-dialogue-box {
  position: relative;
  background: rgba(10, 10, 25, 0.75);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 24px 32px 20px;
  max-width: 900px;
  margin: 0 auto;
  box-shadow:
    0 -10px 60px rgba(0, 0, 0, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

/* Name Tag */
.vn-name-tag {
  position: absolute;
  top: -14px;
  left: 28px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  padding: 6px 20px;
  border-radius: 10px;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
}

/* Text */
.vn-text-content {
  padding-top: 8px;
}

.vn-original-text {
  font-size: 1.25rem;
  color: #f1f5f9;
  line-height: 1.8;
  margin-bottom: 8px;
  min-height: 2.5em;
}

.vn-char {
  display: inline-block;
  opacity: 0;
  animation: charAppear 0.1s ease forwards;
}

@keyframes charAppear {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.vn-translated-text {
  font-size: 0.95rem;
  color: #94a3b8;
  line-height: 1.6;
  font-style: italic;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding-top: 8px;
}

/* Nav Indicator */
.vn-nav-indicator {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 6px;
  margin-top: 10px;
  color: rgba(148, 163, 184, 0.6);
}

.vn-nav-arrow {
  animation: bounce-gentle 1.5s ease-in-out infinite;
}

@keyframes bounce-gentle {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(4px); }
}

/* HUD */
.vn-hud {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 50;
  display: flex;
  gap: 8px;
}

.vn-hud-btn {
  padding: 8px 16px;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.vn-hud-btn:hover {
  background: rgba(0, 0, 0, 0.7);
  color: white;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active {
  transition: all 0.6s ease-out;
}
.slide-up-leave-active {
  transition: all 0.3s ease-in;
}
.slide-up-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(40px);
}
.slide-up-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-20px);
}

/* =========================================
   RESPONSIVE DESIGN (MOBILE & TABLET)
   ========================================= */

@media (max-width: 768px) {
  .vn-character-img {
    height: 90vh; /* Tingkatkan dari 85vh agar di HP tetap gagah/besar */
    max-width: none !important;
  }

  /* 2. Kurangi jarak aman area dialog */
  .vn-dialogue-area {
    padding: 0 12px 16px;
  }

  /* 3. Kotak dialog dibuat lebih pas untuk HP */
  .vn-dialogue-box {
    padding: 20px 16px 16px;
    border-radius: 16px;
  }

  /* 4. Name tag disesuaikan posisinya */
  .vn-name-tag {
    top: -12px;
    left: 16px;
    padding: 4px 16px;
    font-size: 0.75rem;
  }

  /* 5. Ukuran font teks diturunkan sedikit agar rapi */
  .vn-original-text {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 6px;
  }
  .vn-translated-text {
    font-size: 0.85rem;
  }

  /* 6. Tombol pilihan dibuat lebih lebar memenuhi layar HP */
  .vn-choices {
    padding: 0 5%; /* Menghilangkan padding 20% yang bikin tombol menyempit di HP */
    gap: 10px;
  }
  .vn-choice-btn {
    padding: 12px 16px;
    font-size: 0.9rem;
    border-radius: 12px;
  }

  /* 7. HUD (Tombol Admin & Audio) diperkecil */
  .vn-hud {
    top: 12px;
    right: 12px;
    gap: 6px;
  }
  .vn-hud-btn {
    padding: 6px 12px;
    font-size: 0.75rem;
  }
}

/* =========================================
   RESPONSIVE UNTUK HP MODE LANDSCAPE (Miring)
   ========================================= */
@media (max-height: 500px) {
  .vn-character-img {
    height: 95vh; /* Karakter dimaksimalkan karena layar pendek */
  }
  
  .vn-dialogue-area {
    padding-bottom: 8px;
  }

  .vn-dialogue-box {
    padding: 16px 20px 12px;
  }

  .vn-original-text {  
    font-size: 1rem;
    min-height: 2em;
  }
}
</style>
