@extends('layouts.admin')
@section('title', 'Edit Soal - ' . $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes.show', $quiz) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-400 transition-all">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Soal #{{ $item->order }}</h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">{{ $quiz->title }}</p>
            </div>
        </div>
    </div>

    {{-- Error Banner --}}
    <div id="form-error" class="hidden mb-4 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-sm font-medium rounded-xl flex items-center gap-2">
        <i class="fas fa-exclamation-triangle flex-shrink-0"></i>
        <span id="form-error-msg"></span>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 text-sm rounded-xl">
        <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Terdapat kesalahan:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-8">
        <form action="{{ route('admin.quizzes.items.update', [$quiz, $item]) }}" method="POST" id="itemForm" class="space-y-8">
            @csrf @method('PUT')
            <input type="hidden" name="question_type" id="question_type" value="{{ old('question_type', $item->question_type) }}">

            {{-- === TYPE SELECTOR === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Jenis Soal</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <button type="button" onclick="selectType('multiple_choice')" data-type="multiple_choice"
                            class="type-btn p-4 rounded-xl border-2 text-left transition-all group">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                <i class="fas fa-list-ul text-indigo-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Pilihan Ganda</p>
                        <p class="text-xs text-gray-500 mt-1">Soal teks dengan 4 pilihan jawaban.</p>
                    </button>
                    <button type="button" onclick="selectType('drawing')" data-type="drawing"
                            class="type-btn p-4 rounded-xl border-2 text-left transition-all group">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                <i class="fas fa-pencil-alt text-emerald-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Menggambar</p>
                        <p class="text-xs text-gray-500 mt-1">Evaluasi urutan goresan Kanji.</p>
                    </button>
                    <button type="button" onclick="selectType('listening')" data-type="listening"
                            class="type-btn p-4 rounded-xl border-2 text-left transition-all group">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                                <i class="fas fa-headphones text-amber-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Listening</p>
                        <p class="text-xs text-gray-500 mt-1">Soal audio berbasis ElevenLabs.</p>
                    </button>
                    <button type="button" onclick="selectType('matching')" data-type="matching"
                            class="type-btn p-4 rounded-xl border-2 text-left transition-all group">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center">
                                <i class="fas fa-puzzle-piece text-pink-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Mencocokkan</p>
                        <p class="text-xs text-gray-500 mt-1">Pasangkan kata kiri dan kanan.</p>
                    </button>
                </div>
            </div>

            {{-- === COMMON FIELD: QUESTION TEXT === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Teks Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <textarea name="question_text" id="question_text" rows="2" required
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all resize-none">{{ old('question_text', $item->question_text) }}</textarea>
            </div>

            @php
                $optionsArray = is_array($item->options) ? $item->options : [];
            @endphp

            {{-- ================================================================
                 MULTIPLE CHOICE SECTION
                 ================================================================ --}}
            <div id="mc-section" class="space-y-3 p-5 bg-indigo-50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-indigo-700 dark:text-indigo-400">
                        <i class="fas fa-list-ul mr-1"></i> Pilihan Jawaban
                    </p>
                    <p class="text-xs text-indigo-400 dark:text-indigo-500">
                        <i class="fas fa-info-circle mr-1"></i>Klik baris untuk tandai jawaban benar
                    </p>
                </div>
                <div class="space-y-2" id="mc-options">
                    @for($i = 0; $i < 4; $i++)
                    @php
                        $optValue   = $optionsArray[$i] ?? '';
                        $isCorrect  = ($optValue !== '' && $optValue === $item->correct_answer);
                    @endphp
                    <label class="option-row mc-row flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all hover:border-indigo-200 dark:hover:border-indigo-800 group
                                  {{ $isCorrect ? 'border-indigo-400 dark:border-indigo-600 bg-indigo-50/80 dark:bg-indigo-900/20' : 'border-transparent bg-white dark:bg-gray-900' }}">
                        <input type="radio" name="correct_answer" value="{{ $optValue }}" data-index="{{ $i }}"
                               class="mc-radio w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 flex-shrink-0"
                               {{ $isCorrect ? 'checked' : '' }}
                               onchange="highlightSelectedRow(this, 'mc-row', 'indigo')">
                        <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-black flex items-center justify-center flex-shrink-0">
                            {{ chr(65 + $i) }}
                        </span>
                        <input type="text" name="options[]" value="{{ $optValue }}" placeholder="Opsi {{ chr(65 + $i) }}"
                               class="flex-1 bg-transparent border-none focus:ring-0 focus:outline-none text-gray-800 dark:text-gray-200 text-sm placeholder-gray-400"
                               oninput="syncRadioValue(this, {{ $i }})">
                        <i class="fas fa-check text-indigo-500 flex-shrink-0 correct-check transition-opacity text-sm"
                           style="opacity: {{ $isCorrect ? '1' : '0' }}"></i>
                    </label>
                    @endfor
                </div>
            </div>

            {{-- ================================================================
                 DRAWING SECTION
                 ================================================================ --}}
            <div id="drawing-section" class="space-y-4 hidden p-5 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400 mb-3">
                    <i class="fas fa-pencil-alt mr-1"></i> Target Karakter (Kanji)
                </p>

                <div class="relative">
                    <div class="flex items-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500 transition-all">
                        <i class="fas fa-search text-gray-400 ml-4 text-sm"></i>
                        <input type="text" id="kanji_search"
                               placeholder="Ganti karakter... (Ketik untuk mencari)"
                               class="w-full px-3 py-3 bg-transparent border-none focus:ring-0 text-sm dark:text-gray-200 placeholder-gray-400">
                    </div>
                    <div id="kanji_results"
                         class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden"></div>
                </div>

                {{-- Selected Display (Pre-filled if exists) --}}
                <div id="selected_kanji_display"
                     class="{{ $item->kanji ? '' : 'hidden' }} mt-3 flex items-center justify-between p-4 bg-white dark:bg-gray-900 rounded-xl border-2 border-emerald-300 dark:border-emerald-700">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center">
                            <span id="selected_kanji_char" class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $item->kanji->character ?? '' }}</span>
                        </div>
                        <div>
                            <p id="selected_kanji_meaning" class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $item->kanji->meaning ?? '' }}</p>
                            <p id="selected_kanji_cat" class="text-xs text-gray-500 mt-0.5">{{ $item->kanji ? ucfirst($item->kanji->category) : '' }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="clearKanji()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <input type="hidden" name="kanji_id" id="kanji_id" value="{{ $item->kanji_id }}">
                <input type="hidden" name="correct_answer" id="drawing_correct_answer" value="{{ $item->correct_answer }}">
            </div>

            {{-- ================================================================
                 LISTENING SECTION
                 ================================================================ --}}
            <div id="listening-section" class="hidden space-y-4 p-5 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-900/50">
                <p class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-3">
                    <i class="fas fa-volume-up mr-1"></i> Setup Audio
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Voice Model</label>
                        <select id="voice_select"
                                class="w-full px-3 py-2.5 rounded-xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @foreach($voices as $voice)
                                <option value="{{ $voice['voice_id'] }}">{{ $voice['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 block">Teks untuk diucapkan AI</label>
                        <div class="flex gap-2">
                            <input type="text" id="tts_text" placeholder="Masukkan tulisan Jepang..."
                                   class="flex-1 px-4 py-2.5 rounded-xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <button type="button" onclick="regenerateAudio()" id="regen_btn"
                                    class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:bg-amber-300 text-white text-sm font-bold rounded-xl shadow transition-all flex items-center gap-2 whitespace-nowrap">
                                <i class="fas fa-redo" id="regen_icon"></i>
                                <span id="regen_label">Re-Generate</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Audio Preview --}}
                <div id="audio_preview_wrap" class="{{ $item->audio_url ? '' : 'hidden' }} mt-2">
                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-xl border border-emerald-200 dark:border-emerald-800">
                        <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-emerald-600 text-xs"></i>
                        </div>
                        <audio id="audio_preview" controls class="flex-1 h-9" src="{{ $item->audio_url }}"></audio>
                    </div>
                </div>

                @if($item->audio_url)
                <p class="text-xs text-gray-400 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i>
                    Audio sudah ada. Isi teks lalu klik Re-Generate untuk memperbarui.
                </p>
                @endif
                <input type="hidden" name="audio_url" id="audio_url_input" value="{{ $item->audio_url }}">

                <hr class="border-amber-200 dark:border-amber-800">

                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 mb-2">
                    Pilihan Jawaban <span class="font-normal text-gray-400">(klik baris untuk pilih jawaban benar)</span>
                </p>
                <div class="space-y-2" id="listening-options">
                    @for($i = 0; $i < 4; $i++)
                    @php
                        $optValue  = $optionsArray[$i] ?? '';
                        $isCorrect = ($optValue !== '' && $optValue === $item->correct_answer);
                    @endphp
                    <label class="option-row listening-row flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all hover:border-amber-200 dark:hover:border-amber-800 group
                                  {{ $isCorrect ? 'border-amber-400 dark:border-amber-600 bg-amber-50/80 dark:bg-amber-900/20' : 'border-transparent bg-white dark:bg-gray-900' }}">
                        <input type="radio" name="correct_answer" value="{{ $optValue }}" data-index="{{ $i }}"
                               class="listening-radio w-4 h-4 text-amber-500 border-gray-300 dark:border-gray-600 focus:ring-amber-500 flex-shrink-0"
                               {{ $isCorrect ? 'checked' : '' }}
                               onchange="highlightSelectedRow(this, 'listening-row', 'amber')">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 text-xs font-black flex items-center justify-center flex-shrink-0">
                            {{ chr(65 + $i) }}
                        </span>
                        <input type="text" name="options[]" value="{{ $optValue }}" placeholder="Opsi {{ chr(65 + $i) }}"
                               class="flex-1 bg-transparent border-none focus:ring-0 focus:outline-none text-gray-800 dark:text-gray-200 text-sm placeholder-gray-400"
                               oninput="syncListeningRadio(this, {{ $i }})">
                        <i class="fas fa-check text-amber-500 flex-shrink-0 correct-check transition-opacity text-sm"
                           style="opacity: {{ $isCorrect ? '1' : '0' }}"></i>
                    </label>
                    @endfor
                </div>
            </div>

            {{-- ================================================================
                 MATCHING SECTION
                 ================================================================ --}}
            <div id="matching-section" class="hidden space-y-4 p-5 bg-pink-50 dark:bg-pink-900/10 rounded-xl border border-pink-100 dark:border-pink-900/50">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-pink-700 dark:text-pink-400">
                        <i class="fas fa-puzzle-piece mr-1"></i> Pasangan Kata
                    </p>
                    <button type="button" onclick="addMatchingPair()"
                            class="px-3 py-1.5 bg-pink-200 dark:bg-pink-800 hover:bg-pink-300 dark:hover:bg-pink-700 text-pink-700 dark:text-pink-100 text-xs font-bold rounded-lg transition-colors">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </div>
                
                <div id="matching-pairs-container" class="space-y-3">
                    @if($item->question_type === 'matching' && is_array($optionsArray) && count($optionsArray) > 0)
                        @foreach($optionsArray as $jsonPair)
                            @php
                                $pair = json_decode($jsonPair, true);
                                $left = $pair['left'] ?? '';
                                $right = $pair['right'] ?? '';
                            @endphp
                            <div class="matching-pair-row flex items-center gap-3">
                                <input type="hidden" name="options[]" class="matching-json-input" value="{{ $jsonPair }}">
                                <input type="text" placeholder="Kiri (ex: 犬)" value="{{ $left }}" class="matching-left flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                                <i class="fas fa-arrows-alt-h text-pink-300 dark:text-pink-700"></i>
                                <input type="text" placeholder="Kanan (ex: Anjing)" value="{{ $right }}" class="matching-right flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                                <button type="button" onclick="removeMatchingPair(this)" class="w-10 h-10 flex items-center justify-center rounded-xl text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 hover:text-rose-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="matching-pair-row flex items-center gap-3">
                            <input type="hidden" name="options[]" class="matching-json-input">
                            <input type="text" placeholder="Kiri (ex: 犬)" class="matching-left flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                            <i class="fas fa-arrows-alt-h text-pink-300 dark:text-pink-700"></i>
                            <input type="text" placeholder="Kanan (ex: Anjing)" class="matching-right flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                            <button type="button" onclick="removeMatchingPair(this)" class="w-10 h-10 flex items-center justify-center rounded-xl text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 hover:text-rose-600 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="matching-pair-row flex items-center gap-3">
                            <input type="hidden" name="options[]" class="matching-json-input">
                            <input type="text" placeholder="Kiri (ex: 猫)" class="matching-left flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                            <i class="fas fa-arrows-alt-h text-pink-300 dark:text-pink-700"></i>
                            <input type="text" placeholder="Kanan (ex: Kucing)" class="matching-right flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                            <button type="button" onclick="removeMatchingPair(this)" class="w-10 h-10 flex items-center justify-center rounded-xl text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 hover:text-rose-600 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <p class="text-xs text-pink-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Kartu akan otomatis diacak saat kuis dimainkan.</p>
            </div>

            {{-- === COMMON FIELD: ORDER === --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Urutan Tampil
                </label>
                <input type="number" name="order" value="{{ old('order', $item->order) }}" min="1"
                       class="w-28 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-center text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- === SUBMIT BUTTONS === --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                   class="px-5 py-3 text-center text-sm font-semibold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold rounded-xl shadow transition-all">
                    <i class="fas fa-save mr-2"></i> Perbarui Soal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let currentType = '{{ old('question_type', $item->question_type) }}';
    let generatedAudioUrl = '{{ $item->audio_url }}';

    // ─── Type Selector ───────────────────────────────────────────────────────
    function selectType(type) {
        currentType = type;
        document.getElementById('question_type').value = type;

        const colorMap = {
            multiple_choice: { border: 'border-indigo-500', bg: 'bg-indigo-50 dark:bg-indigo-900/20', ring: 'ring-indigo-500' },
            drawing:         { border: 'border-emerald-500', bg: 'bg-emerald-50 dark:bg-emerald-900/20', ring: 'ring-emerald-500' },
            listening:       { border: 'border-amber-500', bg: 'bg-amber-50 dark:bg-amber-900/20', ring: 'ring-amber-500' },
            matching:        { border: 'border-pink-500',   bg: 'bg-pink-50 dark:bg-pink-900/20',   ring: 'ring-pink-500' },
        };

        document.querySelectorAll('.type-btn').forEach(btn => {
            const t = btn.dataset.type;
            if (t === type) {
                const c = colorMap[t];
                btn.className = `type-btn p-4 rounded-xl border-2 text-left transition-all group ${c.border} ${c.bg} ring-2 ${c.ring} ring-offset-2 dark:ring-offset-gray-800`;
            } else {
                btn.className = 'type-btn p-4 rounded-xl border-2 text-left transition-all group border-slate-200 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500';
            }
        });

        const sections = {
            multiple_choice: 'mc-section',
            drawing:         'drawing-section',
            listening:       'listening-section',
            matching:        'matching-section',
        };

        for (const [key, id] of Object.entries(sections)) {
            const section = document.getElementById(id);
            if (key === type) {
                section.style.display = 'block';
                // Re-enable inputs so they are submitted
                section.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            } else {
                section.style.display = 'none';
                // Disable inputs to prevent stale data from submitting
                section.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
            }
        }
    }

    // ─── Multiple Choice Sync ─────────────────────────────────────────────────
    function syncRadioValue(input, idx) {
        const radio = document.querySelectorAll('.mc-radio')[idx];
        radio.value = input.value;
        if (radio.checked) {
            highlightSelectedRow(radio, 'mc-row', 'indigo');
        }
    }

    function syncListeningRadio(input, idx) {
        const radio = document.querySelectorAll('.listening-radio')[idx];
        radio.value = input.value;
        if (radio.checked) {
            highlightSelectedRow(radio, 'listening-row', 'amber');
        }
    }

    // ─── Matching Handlers ───────────────────────────────────────────────────
    function addMatchingPair() {
        const container = document.getElementById('matching-pairs-container');
        const html = `
            <div class="matching-pair-row flex items-center gap-3">
                <input type="hidden" name="options[]" class="matching-json-input">
                <input type="text" placeholder="Kiri" class="matching-left flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                <i class="fas fa-arrows-alt-h text-pink-300 dark:text-pink-700"></i>
                <input type="text" placeholder="Kanan" class="matching-right flex-1 px-4 py-2.5 rounded-xl border border-pink-200 dark:border-pink-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500" oninput="updateMatchingJson(this)">
                <button type="button" onclick="removeMatchingPair(this)" class="w-10 h-10 flex items-center justify-center rounded-xl text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 hover:text-rose-600 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeMatchingPair(btn) {
        if (document.querySelectorAll('.matching-pair-row').length > 1) {
            btn.closest('.matching-pair-row').remove();
        } else {
            showError('Minimal harus ada 1 pasangan!');
        }
    }

    function updateMatchingJson(input) {
        const row = input.closest('.matching-pair-row');
        const left = row.querySelector('.matching-left').value.trim();
        const right = row.querySelector('.matching-right').value.trim();
        const hidden = row.querySelector('.matching-json-input');
        if (left && right) {
            hidden.value = JSON.stringify({ left, right });
        } else {
            hidden.value = '';
        }
    }

    // ─── Row Highlight ────────────────────────────────────────────────────────
    function highlightSelectedRow(radio, rowClass, color) {
        const colorStyles = {
            indigo: { border: ['border-indigo-400', 'dark:border-indigo-600'], bg: ['bg-indigo-50/80', 'dark:bg-indigo-900/20'] },
            amber:  { border: ['border-amber-400',  'dark:border-amber-600'],  bg: ['bg-amber-50/80',  'dark:bg-amber-900/20']  },
        };
        const c = colorStyles[color];

        document.querySelectorAll('.' + rowClass).forEach(row => {
            row.classList.remove(
                'border-indigo-400', 'dark:border-indigo-600', 'bg-indigo-50/80', 'dark:bg-indigo-900/20',
                'border-amber-400',  'dark:border-amber-600',  'bg-amber-50/80',  'dark:bg-amber-900/20'
            );
            row.classList.add('border-transparent');
            const check = row.querySelector('.correct-check');
            if (check) check.style.opacity = '0';
        });

        const row = radio.closest('.' + rowClass);
        if (row) {
            row.classList.remove('border-transparent');
            row.classList.add(...c.border, ...c.bg);
            const check = row.querySelector('.correct-check');
            if (check) check.style.opacity = '1';
        }
    }

    // ─── Kanji Search ─────────────────────────────────────────────────────────
    let searchTimeout;
    document.getElementById('kanji_search').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (!q) { document.getElementById('kanji_results').classList.add('hidden'); return; }
        searchTimeout = setTimeout(() => searchKanjis(q), 300);
    });

    async function searchKanjis(q) {
        try {
            const res  = await fetch(`/admin/quizzes/api/search-kanjis?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            const box  = document.getElementById('kanji_results');

            if (!data.length) {
                box.innerHTML = '<p class="text-sm text-gray-400 p-4 text-center"><i class="fas fa-search mr-1"></i>Tidak ditemukan</p>';
            } else {
                box.innerHTML = data.map(k => `
                    <button type="button" onclick='selectKanji(${JSON.stringify(k)})'
                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-700 flex items-center gap-3 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors">
                        <span class="text-2xl font-bold text-gray-800 dark:text-white w-8 text-center">${k.character}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">${k.meaning}</p>
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
        document.getElementById('selected_kanji_char').textContent    = k.character;
        document.getElementById('selected_kanji_meaning').textContent  = k.meaning;
        document.getElementById('selected_kanji_cat').textContent      = (k.category || '') + ' · ' + (k.strokes||[]).length + ' goresan';
        document.getElementById('selected_kanji_display').classList.remove('hidden');
        document.getElementById('kanji_results').classList.add('hidden');
        document.getElementById('kanji_search').value = '';
    }

    function clearKanji() {
        document.getElementById('kanji_id').value = '';
        document.getElementById('drawing_correct_answer').value = '';
        document.getElementById('selected_kanji_display').classList.add('hidden');
        document.getElementById('kanji_search').focus();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#kanji_search') && !e.target.closest('#kanji_results')) {
            document.getElementById('kanji_results').classList.add('hidden');
        }
    });

    // ─── Audio Regeneration ───────────────────────────────────────────────────
    async function regenerateAudio() {
        const text    = document.getElementById('tts_text').value.trim();
        const voiceId = document.getElementById('voice_select').value;
        if (!text) { showError('Masukkan teks terlebih dahulu!'); return; }

        const btn = document.getElementById('regen_btn');
        btn.disabled = true;
        document.getElementById('regen_icon').className  = 'fas fa-spinner fa-spin';
        document.getElementById('regen_label').textContent = 'Generating...';

        try {
            const res  = await fetch('/admin/quizzes/api/regenerate-audio-preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ text, voice_id: voiceId, current_url: generatedAudioUrl })
            });
            const data = await res.json();
            if (data.audio_url) {
                generatedAudioUrl = data.audio_url;
                document.getElementById('audio_url_input').value = data.audio_url;
                document.getElementById('audio_preview').src     = data.audio_url;
                document.getElementById('audio_preview_wrap').classList.remove('hidden');
            } else {
                showError(data.error || 'Gagal regenerate audio.');
            }
        } catch(e) {
            showError('Error: ' + e.message);
        } finally {
            btn.disabled = false;
            document.getElementById('regen_icon').className   = 'fas fa-redo';
            document.getElementById('regen_label').textContent = 'Re-Generate';
        }
    }

    // ─── Error Display ────────────────────────────────────────────────────────
    function showError(msg) {
        const banner = document.getElementById('form-error');
        document.getElementById('form-error-msg').textContent = msg;
        banner.classList.remove('hidden');
        banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => banner.classList.add('hidden'), 5000);
    }

    // ─── Form Submit Validation ───────────────────────────────────────────────
    document.getElementById('itemForm').addEventListener('submit', function(e) {
        document.getElementById('form-error').classList.add('hidden');

        if (!document.getElementById('question_text').value.trim()) {
            e.preventDefault();
            showError('Teks pertanyaan tidak boleh kosong!');
            return;
        }

        if (currentType === 'drawing') {
            if (!document.getElementById('kanji_id').value) {
                e.preventDefault();
                showError('Pilih karakter Kanji terlebih dahulu!');
            }
        } else if (currentType === 'multiple_choice') {
            const checked = document.querySelector('.mc-radio:checked');
            if (!checked || !checked.value.trim()) {
                e.preventDefault();
                showError('Pilih satu jawaban yang benar untuk soal Pilihan Ganda!');
            }
        } else if (currentType === 'listening') {
            const checked = document.querySelector('.listening-radio:checked');
            if (!checked || !checked.value.trim()) {
                e.preventDefault();
                showError('Pilih satu jawaban yang benar untuk soal Listening!');
            }
        } else if (currentType === 'matching') {
            const pairs = document.querySelectorAll('.matching-json-input');
            let validCount = 0;
            pairs.forEach(p => { if (p.value) validCount++; });
            if (validCount < 2) {
                e.preventDefault();
                showError('Soal mencocokkan minimal harus memiliki 2 pasangan yang terisi penuh!');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        selectType(currentType);

        // Restore highlighted rows for pre-checked radios (MC)
        document.querySelectorAll('.mc-radio:checked').forEach(r => highlightSelectedRow(r, 'mc-row', 'indigo'));
        // Restore highlighted rows for pre-checked radios (Listening)
        document.querySelectorAll('.listening-radio:checked').forEach(r => highlightSelectedRow(r, 'listening-row', 'amber'));
    });
</script>
@endpush