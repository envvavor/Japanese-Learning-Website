@extends('layouts.admin')
@section('title', 'Tambah Soal')

@push('styles')
<style>
.type-btn { transition: all .2s; }
.type-btn.active { ring: 2px; }
#drawing-section, #listening-section, #mc-section { display: none; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Quiz: {{ $quiz->title }}</p>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Tambah Soal Baru</h1>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-8">
        <form action="{{ route('admin.quizzes.items.store', $quiz) }}" method="POST" id="itemForm" class="space-y-6">
            @csrf

            {{-- Step 1: Type Selector --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Jenis Soal <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3" id="typePicker">
                    <button type="button" onclick="selectType('multiple_choice')"
                            class="type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-indigo-400 transition-all group" data-type="multiple_choice">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-list text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Pilihan Ganda</p>
                        <p class="text-xs text-gray-400 mt-0.5">4 pilihan, 1 benar</p>
                    </button>
                    <button type="button" onclick="selectType('drawing')"
                            class="type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-emerald-400 transition-all group" data-type="drawing">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-pen-nib text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Menggambar</p>
                        <p class="text-xs text-gray-400 mt-0.5">Stroke order huruf</p>
                    </button>
                    <button type="button" onclick="selectType('listening')"
                            class="type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-amber-400 transition-all group" data-type="listening">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-volume-up text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Listening</p>
                        <p class="text-xs text-gray-400 mt-0.5">Audio + pilihan ganda</p>
                    </button>
                </div>
                <input type="hidden" name="question_type" id="question_type" value="{{ old('question_type') }}">
            </div>

            {{-- Question Text --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Teks Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="question_text" id="question_text" value="{{ old('question_text') }}" required
                       placeholder="Contoh: Apa arti dari karakter ini?"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            {{-- === MULTIPLE CHOICE SECTION === --}}
            <div id="mc-section" class="space-y-4 p-5 bg-indigo-50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900">
                <p class="text-sm font-bold text-indigo-700 dark:text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-list"></i> Pilihan Jawaban
                </p>
                <div class="space-y-2" id="mc-options">
                    @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-2">
                        <input type="radio" name="correct_answer" value="" class="mc-radio w-4 h-4 text-indigo-600" id="radio_{{ $i }}">
                        <input type="text" name="options[]" placeholder="Pilihan {{ $i + 1 }}"
                               class="flex-1 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400 mc-option-input"
                               value="{{ old('options.' . $i) }}"
                               oninput="syncRadioValue(this, {{ $i }})">
                    </div>
                    @endfor
                </div>
                <p class="text-xs text-indigo-500 dark:text-indigo-400 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i> Klik radio button untuk menandai jawaban benar
                </p>
            </div>

            {{-- === DRAWING SECTION === --}}
            <div id="drawing-section" class="space-y-4 p-5 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900">
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class="fas fa-pen-nib"></i> Pilih Karakter (dari database)
                </p>
                <p class="text-xs text-emerald-600 dark:text-emerald-500">Hanya karakter yang memiliki data stroke order yang bisa dipilih.</p>

                {{-- Search --}}
                <div class="relative">
                    <input type="text" id="kanji_search" placeholder="Cari karakter atau arti... (contoh: あ atau gunung)"
                           class="w-full px-4 py-3 rounded-xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all">
                    <div id="kanji_results" class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-xl max-h-60 overflow-y-auto hidden">
                    </div>
                </div>

                {{-- Selected kanji display --}}
                <div id="selected_kanji_display" class="hidden p-4 bg-white dark:bg-gray-900 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-4">
                    <span id="selected_kanji_char" class="text-4xl font-bold text-gray-800 dark:text-white"></span>
                    <div>
                        <p id="selected_kanji_meaning" class="text-sm font-semibold text-gray-700 dark:text-gray-300"></p>
                        <p id="selected_kanji_cat" class="text-xs text-gray-400"></p>
                    </div>
                    <button type="button" onclick="clearKanji()" class="ml-auto text-xs text-rose-500 hover:text-rose-700">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>

                <input type="hidden" name="kanji_id" id="kanji_id" value="{{ old('kanji_id') }}">
                {{-- For drawing, correct_answer = character; auto-filled by JS --}}
            </div>

            {{-- === LISTENING SECTION === --}}
            <div id="listening-section" class="space-y-4 p-5 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-100 dark:border-amber-900">
                <p class="text-sm font-bold text-amber-700 dark:text-amber-400 flex items-center gap-2">
                    <i class="fas fa-volume-up"></i> Generate Audio (ElevenLabs)
                </p>

                {{-- Voice selector --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Voice</label>
                    <select id="voice_select" class="w-full px-3 py-2 rounded-lg border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-amber-400">
                        @forelse($voices as $voice)
                            <option value="{{ $voice['voice_id'] }}">{{ $voice['name'] }}</option>
                        @empty
                            <option value="">— Tidak ada voice (cek API key) —</option>
                        @endforelse
                    </select>
                </div>

                {{-- Text to speak --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Teks untuk diucapkan</label>
                    <div class="flex gap-2">
                        <input type="text" id="tts_text" placeholder="Masukkan teks Jepang..."
                               class="flex-1 px-3 py-2.5 rounded-lg border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-amber-400">
                        <button type="button" onclick="generateAudio()" id="gen_btn"
                                class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow transition-all flex items-center gap-1.5">
                            <i class="fas fa-magic" id="gen_icon"></i> Generate
                        </button>
                    </div>
                </div>

                {{-- Audio preview + regenerate --}}
                <div id="audio_preview_wrap" class="hidden">
                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-lg border border-amber-200 dark:border-amber-700">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <audio id="audio_preview" controls class="flex-1 h-8"></audio>
                        <button type="button" onclick="regenerateAudio()" id="regen_btn"
                                class="px-3 py-1.5 text-xs font-bold text-amber-600 border border-amber-300 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all flex items-center gap-1">
                            <i class="fas fa-redo" id="regen_icon"></i> Regenerate
                        </button>
                    </div>
                </div>

                <p id="audio_status" class="text-xs text-gray-400"></p>
                <input type="hidden" name="audio_url" id="audio_url_input" value="{{ old('audio_url') }}">

                {{-- MC Options for listening --}}
                <div class="pt-2 border-t border-amber-200 dark:border-amber-800 space-y-3">
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">Pilihan Jawaban</p>
                    <div class="space-y-2" id="listening-options">
                        @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_answer" value="" class="listening-radio w-4 h-4 text-amber-500" id="lradio_{{ $i }}">
                            <input type="text" name="options[]" placeholder="Pilihan {{ $i + 1 }}"
                                   class="flex-1 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-amber-400 listening-option-input"
                                   oninput="syncListeningRadio(this, {{ $i }})">
                        </div>
                        @endfor
                    </div>
                    <p class="text-xs text-amber-500 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i> Klik radio button untuk menandai jawaban benar
                    </p>
                </div>
            </div>

            {{-- Order --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Urutan Soal</label>
                <input type="number" name="order" value="{{ old('order', $nextOrder) }}" min="1"
                       class="w-28 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                   class="flex-1 py-3 text-center text-sm font-semibold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const QUIZ_ID = {{ $quiz->id }};
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
let currentType = '{{ old('question_type') }}';
let generatedAudioUrl = '{{ old('audio_url') }}';
// Pre-load saved kanji if editing
let selectedKanjiData = null;

// ── Type Selector ──────────────────────────────────────────────
function selectType(type) {
    currentType = type;
    document.getElementById('question_type').value = type;

    // Update button styles
    document.querySelectorAll('.type-btn').forEach(btn => {
        const t = btn.dataset.type;
        if (t === type) {
            if (t === 'multiple_choice') btn.className = 'type-btn p-4 rounded-xl border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-left ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
            if (t === 'drawing') btn.className = 'type-btn p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-left ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
            if (t === 'listening') btn.className = 'type-btn p-4 rounded-xl border-2 border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-left ring-2 ring-amber-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
        } else {
            btn.className = 'type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-indigo-400 transition-all group';
        }
    });

    // Show/hide sections
    document.getElementById('mc-section').style.display      = type === 'multiple_choice' ? 'block' : 'none';
    document.getElementById('drawing-section').style.display = type === 'drawing'          ? 'block' : 'none';
    document.getElementById('listening-section').style.display = type === 'listening'      ? 'block' : 'none';
}

// Init if old type
if (currentType) selectType(currentType);

// ── MC Radio Sync ──────────────────────────────────────────────
function syncRadioValue(input, idx) {
    const radios = document.querySelectorAll('.mc-radio');
    radios[idx].value = input.value;
}

function syncListeningRadio(input, idx) {
    const radios = document.querySelectorAll('.listening-radio');
    radios[idx].value = input.value;
}

// ── Drawing: Kanji Search ──────────────────────────────────────
let searchTimeout;
document.getElementById('kanji_search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (!q) { document.getElementById('kanji_results').classList.add('hidden'); return; }
    searchTimeout = setTimeout(() => searchKanjis(q), 300);
});

async function searchKanjis(q) {
    try {
        const res = await fetch(`/admin/quizzes/api/search-kanjis?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();
        renderKanjiResults(data);
    } catch(e) { console.error(e); }
}

function renderKanjiResults(kanjis) {
    const box = document.getElementById('kanji_results');
    if (!kanjis.length) { box.innerHTML = '<p class="text-sm text-gray-400 p-3">Tidak ditemukan</p>'; box.classList.remove('hidden'); return; }
    box.innerHTML = kanjis.map(k => `
        <button type="button" onclick='selectKanji(${JSON.stringify(k)})'
                class="w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-700 flex items-center gap-3 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors">
            <span class="text-2xl font-bold text-gray-800 dark:text-white w-8">${k.character}</span>
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">${k.meaning}</p>
                <p class="text-xs text-gray-400">${k.category} · ${(k.strokes||[]).length} goresan</p>
            </div>
        </button>
    `).join('');
    box.classList.remove('hidden');
}

function selectKanji(k) {
    selectedKanjiData = k;
    document.getElementById('kanji_id').value = k.id;
    // For drawing, correct_answer = the character itself
    // We inject a hidden input
    let ca = document.getElementById('drawing_correct_answer');
    if (!ca) {
        ca = document.createElement('input');
        ca.type = 'hidden'; ca.name = 'correct_answer'; ca.id = 'drawing_correct_answer';
        document.getElementById('itemForm').appendChild(ca);
    }
    ca.value = k.character;

    // Auto-fill question text
    if (!document.getElementById('question_text').value) {
        document.getElementById('question_text').value = `Tulis karakter 「${k.character}」 (${k.meaning}) dengan urutan goresan yang benar!`;
    }

    document.getElementById('selected_kanji_char').textContent    = k.character;
    document.getElementById('selected_kanji_meaning').textContent = k.meaning;
    document.getElementById('selected_kanji_cat').textContent     = ucFirst(k.category) + ' · ' + (k.strokes||[]).length + ' goresan';
    document.getElementById('selected_kanji_display').classList.remove('hidden');
    document.getElementById('kanji_results').classList.add('hidden');
    document.getElementById('kanji_search').value = '';
}

function clearKanji() {
    selectedKanjiData = null;
    document.getElementById('kanji_id').value = '';
    const ca = document.getElementById('drawing_correct_answer');
    if (ca) ca.value = '';
    document.getElementById('selected_kanji_display').classList.add('hidden');
}

function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

// Close dropdown on outside click
document.addEventListener('click', (e) => {
    if (!e.target.closest('#kanji_search') && !e.target.closest('#kanji_results')) {
        document.getElementById('kanji_results').classList.add('hidden');
    }
});

// ── Listening: Audio Generation ────────────────────────────────
async function generateAudio() {
    const text    = document.getElementById('tts_text').value.trim();
    const voiceId = document.getElementById('voice_select').value;
    if (!text) { alert('Masukkan teks terlebih dahulu!'); return; }

    const btn  = document.getElementById('gen_btn');
    const icon = document.getElementById('gen_icon');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';

    try {
        // Use a temp item id = 0, generate via a special endpoint
        const res = await fetch('/admin/quizzes/api/generate-audio-preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ text, voice_id: voiceId })
        });
        const data = await res.json();
        if (data.audio_url) {
            setAudioPreview(data.audio_url);
            document.getElementById('audio_status').textContent = '✅ Audio berhasil digenerate';
        } else {
            document.getElementById('audio_status').textContent = '❌ Gagal: ' + (data.error || 'Unknown error');
        }
    } catch(e) {
        document.getElementById('audio_status').textContent = '❌ Error: ' + e.message;
    } finally {
        btn.disabled = false;
        icon.className = 'fas fa-magic';
    }
}

async function regenerateAudio() {
    const text    = document.getElementById('tts_text').value.trim();
    const voiceId = document.getElementById('voice_select').value;
    if (!text) { alert('Masukkan teks terlebih dahulu!'); return; }

    const btn  = document.getElementById('regen_btn');
    const icon = document.getElementById('regen_icon');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';

    try {
        const res = await fetch('/admin/quizzes/api/regenerate-audio-preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ text, voice_id: voiceId, current_url: generatedAudioUrl })
        });
        const data = await res.json();
        if (data.audio_url) {
            setAudioPreview(data.audio_url);
            document.getElementById('audio_status').textContent = '✅ Audio berhasil digenerate ulang';
        } else {
            document.getElementById('audio_status').textContent = '❌ Gagal: ' + (data.error || 'Unknown error');
        }
    } catch(e) {
        document.getElementById('audio_status').textContent = '❌ Error: ' + e.message;
    } finally {
        btn.disabled = false;
        icon.className = 'fas fa-redo';
    }
}

function setAudioPreview(url) {
    generatedAudioUrl = url;
    document.getElementById('audio_url_input').value  = url;
    document.getElementById('audio_preview').src      = url;
    document.getElementById('audio_preview_wrap').classList.remove('hidden');
}

// ── Form Validation ────────────────────────────────────────────
document.getElementById('itemForm').addEventListener('submit', function(e) {
    if (!currentType) { e.preventDefault(); alert('Pilih jenis soal terlebih dahulu!'); return; }

    if (currentType === 'drawing' && !document.getElementById('kanji_id').value) {
        e.preventDefault(); alert('Pilih karakter dari database untuk soal Drawing!'); return;
    }

    if (currentType === 'multiple_choice') {
        const checked = document.querySelector('.mc-radio:checked');
        if (!checked || !checked.value) { e.preventDefault(); alert('Pilih jawaban yang benar untuk soal Pilihan Ganda!'); return; }
    }

    if (currentType === 'listening') {
        const checked = document.querySelector('.listening-radio:checked');
        if (!checked || !checked.value) { e.preventDefault(); alert('Pilih jawaban yang benar untuk soal Listening!'); return; }
        if (!document.getElementById('audio_url_input').value) {
            if (!confirm('Audio belum digenerate. Lanjutkan simpan tanpa audio?')) { e.preventDefault(); return; }
        }
    }
});
</script>
@endpush
