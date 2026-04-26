@extends('layouts.admin')
@section('title', 'Tambah Soal - ' . $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Tambah Soal Baru</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-8">
        <form action="{{ route('admin.quizzes.items.store', $quiz) }}" method="POST" id="itemForm" class="space-y-8">
            @csrf
            <input type="hidden" name="question_type" id="question_type" value="{{ old('question_type', 'multiple_choice') }}">

            {{-- === TYPE SELECTOR === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Pilih Jenis Soal</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <button type="button" onclick="selectType('multiple_choice')" data-type="multiple_choice" class="type-btn p-4 rounded-xl border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-left ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group">
                        <i class="fas fa-list-ul text-indigo-500 text-xl mb-2"></i>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Pilihan Ganda</p>
                        <p class="text-xs text-gray-500 mt-1">Soal teks dengan 4 pilihan jawaban.</p>
                    </button>
                    <button type="button" onclick="selectType('drawing')" data-type="drawing" class="type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-emerald-400 transition-all group">
                        <i class="fas fa-pencil-alt text-emerald-500 text-xl mb-2"></i>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Menggambar</p>
                        <p class="text-xs text-gray-500 mt-1">Evaluasi urutan goresan Kanji.</p>
                    </button>
                    <button type="button" onclick="selectType('listening')" data-type="listening" class="type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-amber-400 transition-all group">
                        <i class="fas fa-headphones text-amber-500 text-xl mb-2"></i>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Listening</p>
                        <p class="text-xs text-gray-500 mt-1">Soal audio berbasis ElevenLabs.</p>
                    </button>
                </div>
            </div>

            {{-- === COMMON FIELD: QUESTION TEXT === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teks Pertanyaan <span class="text-rose-500">*</span></label>
                <textarea name="question_text" id="question_text" rows="2" placeholder="Tuliskan pertanyaan di sini..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('question_text') }}</textarea>
                @error('question_text') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- === MULTIPLE CHOICE SECTION === --}}
            <div id="mc-section" class="space-y-4 p-5 bg-indigo-50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                <p class="text-sm font-bold text-indigo-700 dark:text-indigo-400 mb-2"><i class="fas fa-list-ul"></i> Pilihan Jawaban</p>
                <div class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-3">
                        <input type="radio" name="correct_answer" value="" class="mc-radio w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <input type="text" name="options[]" placeholder="Opsi {{ $i + 1 }}"
                               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               oninput="syncRadioValue(this, {{ $i }})">
                    </div>
                    @endfor
                </div>
                <p class="text-xs text-indigo-500 flex items-center gap-1 mt-2">
                    <i class="fas fa-info-circle"></i> Klik radio button bulat di sebelah kiri untuk menandai jawaban yang benar.
                </p>
            </div>

            {{-- === DRAWING SECTION === --}}
            <div id="drawing-section" class="space-y-4 hidden p-5 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400 mb-2"><i class="fas fa-pencil-alt"></i> Target Karakter (Kanji)</p>
                
                <div class="relative">
                    <div class="flex items-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500">
                        <i class="fas fa-search text-gray-400 ml-4"></i>
                        <input type="text" id="kanji_search" placeholder="Cari karakter atau arti bahasa Indonesia..." class="w-full px-3 py-3 bg-transparent border-none focus:ring-0 text-sm dark:text-gray-200">
                    </div>
                    
                    {{-- Search Results --}}
                    <div id="kanji_results" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden"></div>
                </div>

                {{-- Selected Display --}}
                <div id="selected_kanji_display" class="hidden mt-4 flex items-center justify-between p-4 bg-white dark:bg-gray-900 rounded-xl border-2 border-emerald-200 dark:border-emerald-800">
                    <div class="flex items-center gap-4">
                        <span id="selected_kanji_char" class="text-4xl font-black text-emerald-600 dark:text-emerald-400"></span>
                        <div>
                            <p id="selected_kanji_meaning" class="text-sm font-bold text-gray-800 dark:text-gray-100"></p>
                            <p id="selected_kanji_cat" class="text-xs text-gray-500"></p>
                        </div>
                    </div>
                    <button type="button" onclick="clearKanji()" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <input type="hidden" name="kanji_id" id="kanji_id">
                <input type="hidden" name="correct_answer" id="drawing_correct_answer">
            </div>

            {{-- === LISTENING SECTION === --}}
            <div id="listening-section" class="hidden space-y-4 p-5 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-900/50">
                <p class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-2"><i class="fas fa-volume-up"></i> Setup Audio ElevenLabs</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Voice Model</label>
                        <select id="voice_select" class="w-full px-3 py-2.5 rounded-xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @forelse($voices as $voice)
                                <option value="{{ $voice['voice_id'] }}">{{ $voice['name'] }}</option>
                            @empty
                                <option value="">— API Error —</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Teks untuk diucapkan AI</label>
                        <div class="flex gap-2">
                            <input type="text" id="tts_text" placeholder="Masukkan tulisan Jepang..."
                                   class="flex-1 px-4 py-2.5 rounded-xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <button type="button" onclick="generateAudio()" id="gen_btn"
                                    class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow transition-all flex items-center gap-2">
                                <i class="fas fa-magic" id="gen_icon"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Audio Preview --}}
                <div id="audio_preview_wrap" class="hidden mt-3">
                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-xl border border-amber-200 dark:border-amber-700">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        <audio id="audio_preview" controls class="flex-1 h-10"></audio>
                        <button type="button" onclick="regenerateAudio()" id="regen_btn"
                                class="px-4 py-2 text-xs font-bold text-amber-600 border border-amber-300 rounded-lg hover:bg-amber-50 transition-all flex items-center gap-1.5">
                            <i class="fas fa-redo" id="regen_icon"></i> Regenerate
                        </button>
                    </div>
                </div>
                <p id="audio_status" class="text-xs text-gray-500"></p>
                <input type="hidden" name="audio_url" id="audio_url_input">

                <hr class="border-amber-200 dark:border-amber-800 my-4">

                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 mb-2">Pilihan Jawaban</p>
                <div class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-3">
                        <input type="radio" name="correct_answer" value="" class="listening-radio w-5 h-5 text-amber-500 border-gray-300 focus:ring-amber-500">
                        <input type="text" name="options[]" placeholder="Opsi {{ $i + 1 }}"
                               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                               oninput="syncListeningRadio(this, {{ $i }})">
                    </div>
                    @endfor
                </div>
            </div>

            {{-- === COMMON FIELD: ORDER === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Urutan Tampil (Order)</label>
                <input type="number" name="order" value="{{ old('order', $nextOrder) }}" min="1"
                       class="w-32 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-center text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('order') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- === SUBMIT === --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="flex-1 py-3 text-center text-sm font-semibold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Batal
                </a>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let currentType = '{{ old('question_type', 'multiple_choice') }}';
    let generatedAudioUrl = '';

    function selectType(type) {
        currentType = type;
        document.getElementById('question_type').value = type;

        document.querySelectorAll('.type-btn').forEach(btn => {
            const t = btn.dataset.type;
            if (t === type) {
                if (t === 'multiple_choice') btn.className = 'type-btn p-4 rounded-xl border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-left ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
                if (t === 'drawing') btn.className = 'type-btn p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-left ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
                if (t === 'listening') btn.className = 'type-btn p-4 rounded-xl border-2 border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-left ring-2 ring-amber-500 ring-offset-2 dark:ring-offset-gray-800 transition-all group';
            } else {
                btn.className = 'type-btn p-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-left hover:border-gray-400 transition-all group';
            }
        });

        document.getElementById('mc-section').style.display = type === 'multiple_choice' ? 'block' : 'none';
        document.getElementById('drawing-section').style.display = type === 'drawing' ? 'block' : 'none';
        document.getElementById('listening-section').style.display = type === 'listening' ? 'block' : 'none';
    }

    function syncRadioValue(input, idx) {
        document.querySelectorAll('.mc-radio')[idx].value = input.value;
    }

    function syncListeningRadio(input, idx) {
        document.querySelectorAll('.listening-radio')[idx].value = input.value;
    }

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
            const box = document.getElementById('kanji_results');
            if (!data.length) { 
                box.innerHTML = '<p class="text-sm text-gray-400 p-3">Tidak ditemukan</p>'; 
            } else {
                box.innerHTML = data.map(k => `
                    <button type="button" onclick='selectKanji(${JSON.stringify(k)})' class="w-full text-left px-4 py-3 hover:bg-indigo-50 flex items-center gap-3 border-b border-gray-100 transition-colors">
                        <span class="text-2xl font-bold text-gray-800 w-8">${k.character}</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${k.meaning}</p>
                            <p class="text-xs text-gray-400">${k.category} · ${(k.strokes||[]).length} goresan</p>
                        </div>
                    </button>`).join('');
            }
            box.classList.remove('hidden');
        } catch(e) { console.error(e); }
    }

    function selectKanji(k) {
        document.getElementById('kanji_id').value = k.id;
        document.getElementById('drawing_correct_answer').value = k.character;
        if (!document.getElementById('question_text').value) {
            document.getElementById('question_text').value = `Tulis karakter 「${k.character}」 (${k.meaning}) dengan urutan goresan yang benar!`;
        }
        document.getElementById('selected_kanji_char').textContent = k.character;
        document.getElementById('selected_kanji_meaning').textContent = k.meaning;
        document.getElementById('selected_kanji_cat').textContent = (k.category || '') + ' · ' + (k.strokes||[]).length + ' goresan';
        document.getElementById('selected_kanji_display').classList.remove('hidden');
        document.getElementById('kanji_results').classList.add('hidden');
        document.getElementById('kanji_search').value = '';
    }

    function clearKanji() {
        document.getElementById('kanji_id').value = '';
        document.getElementById('drawing_correct_answer').value = '';
        document.getElementById('selected_kanji_display').classList.add('hidden');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#kanji_search') && !e.target.closest('#kanji_results')) {
            document.getElementById('kanji_results').classList.add('hidden');
        }
    });

    async function generateAudio() {
        const text = document.getElementById('tts_text').value.trim();
        const voiceId = document.getElementById('voice_select').value;
        if (!text) { alert('Masukkan teks terlebih dahulu!'); return; }

        const btn = document.getElementById('gen_btn');
        btn.disabled = true; document.getElementById('gen_icon').className = 'fas fa-spinner fa-spin';

        try {
            const res = await fetch('/admin/quizzes/api/generate-audio-preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ text, voice_id: voiceId })
            });
            const data = await res.json();
            if (data.audio_url) {
                setAudioPreview(data.audio_url);
                document.getElementById('audio_status').textContent = '✅ Berhasil digenerate';
            } else { alert(data.error); }
        } catch(e) { alert('Error: ' + e.message); } finally {
            btn.disabled = false; document.getElementById('gen_icon').className = 'fas fa-magic';
        }
    }

    async function regenerateAudio() {
        const text = document.getElementById('tts_text').value.trim();
        const voiceId = document.getElementById('voice_select').value;
        if (!text) { alert('Masukkan teks!'); return; }

        const btn = document.getElementById('regen_btn');
        btn.disabled = true; document.getElementById('regen_icon').className = 'fas fa-spinner fa-spin';

        try {
            const res = await fetch('/admin/quizzes/api/regenerate-audio-preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ text, voice_id: voiceId, current_url: generatedAudioUrl })
            });
            const data = await res.json();
            if (data.audio_url) { setAudioPreview(data.audio_url); }
        } catch(e) { alert('Error: ' + e.message); } finally {
            btn.disabled = false; document.getElementById('regen_icon').className = 'fas fa-redo';
        }
    }

    function setAudioPreview(url) {
        generatedAudioUrl = url;
        document.getElementById('audio_url_input').value = url;
        document.getElementById('audio_preview').src = url;
        document.getElementById('audio_preview_wrap').classList.remove('hidden');
    }

    document.getElementById('itemForm').addEventListener('submit', function(e) {
        if (currentType === 'drawing' && !document.getElementById('kanji_id').value) {
            e.preventDefault(); alert('Pilih karakter Kanji terlebih dahulu!');
        } else if (currentType === 'multiple_choice' && !document.querySelector('.mc-radio:checked')?.value) {
            e.preventDefault(); alert('Pilih satu jawaban yang benar untuk Pilihan Ganda!');
        } else if (currentType === 'listening' && !document.querySelector('.listening-radio:checked')?.value) {
            e.preventDefault(); alert('Pilih satu jawaban yang benar untuk Listening!');
        }
    });

    document.addEventListener('DOMContentLoaded', () => selectType(currentType));
</script>
@endpush