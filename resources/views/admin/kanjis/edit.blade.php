@extends('layouts.admin')

@section('title', 'Edit Huruf')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.kanjis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Huruf
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden max-w-4xl">
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fas fa-edit text-indigo-500 dark:text-indigo-400 mr-2 border dark:border-gray-600 bg-white dark:bg-gray-700 rounded-full p-2 shadow-sm"></i> Edit Kanji: {{ $kanji->character }}
        </h3>
    </div>
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.kanjis.update', $kanji) }}" method="POST" id="kanjiForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="character" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Karakter *</label>
                    <input type="text" name="character" id="character" value="{{ old('character', $kanji->character) }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-2xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: 日">
                </div>
                
                <div>
                    <label for="meaning" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Arti (Meaning) *</label>
                    <input type="text" name="meaning" id="meaning" value="{{ old('meaning', $kanji->meaning) }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: Matahari, Hari">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori *</label>
                    <select name="category" id="category" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm">
                        <option value="kanji" {{ old('category', $kanji->category) == 'kanji' ? 'selected' : '' }}>Kanji</option>
                        <option value="hiragana" {{ old('category', $kanji->category) == 'hiragana' ? 'selected' : '' }}>Hiragana</option>
                        <option value="katakana" {{ old('category', $kanji->category) == 'katakana' ? 'selected' : '' }}>Katakana</option>
                    </select>
                </div>
                
                <div>
                    <label for="level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Bab <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span>
                    </label>
                    <input type="number" name="level" id="level" min="1" step="1" value="{{ old('level', $kanji->level) }}" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm"
                           placeholder="Contoh: 1, 2, 3...">
                </div>

                <div>
                    <label for="kunyomi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kunyomi <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span></label>
                    <input type="text" name="kunyomi" id="kunyomi" value="{{ old('kunyomi', $kanji->kunyomi) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: ひ, -び, -か">
                </div>

                <div>
                    <label for="onyomi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Onyomi <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span></label>
                    <input type="text" name="onyomi" id="onyomi" value="{{ old('onyomi', $kanji->onyomi) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: ニチ, ジツ">
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-800 dark:text-gray-100 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2"><i class="fas fa-pen-nib mr-2 text-indigo-500 dark:text-indigo-400"></i> Rekam Coretan (Strokes) *</label>
                <div class="bg-gray-50 dark:bg-gray-800/50 border border-t-0 border-x-0 border-b-4 border-indigo-100 dark:border-indigo-800 p-4 sm:p-5 rounded-xl shadow-sm">
                    <div class="flex flex-col lg:flex-row gap-6 items-center lg:items-start">

                        {{-- Canvas wrapper --}}
                        <div class="bg-white dark:bg-gray-700 p-2 rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 relative flex-shrink-0 mx-auto lg:mx-0 w-full max-w-[320px] flex justify-center">
                            <canvas id="drawingCanvas" width="300" height="300"
                                    class="border border-dashed border-indigo-200 dark:border-indigo-700 rounded-lg cursor-crosshair bg-transparent w-full max-w-[300px] h-auto aspect-square"
                                    style="touch-action: none;"></canvas>
                            {{-- Fullscreen button --}}
                            <button type="button" id="openFullscreenBtn"
                                    class="absolute top-3 right-3 z-10 bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 text-white rounded-lg px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1.5 shadow-md transition-all hover:shadow-lg"
                                    title="Buka mode fullscreen">
                                <i class="fas fa-expand-alt text-xs"></i>
                                <span class="hidden sm:inline">Fullscreen</span>
                            </button>
                        </div>
                        
                        <div class="flex-1 space-y-4 w-full">
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-200 justify-center p-4 rounded-lg text-sm border border-indigo-100 dark:border-indigo-800 shadow-sm leading-relaxed">
                                <i class="fas fa-info-circle mr-2 mb-2 text-indigo-600 dark:text-indigo-400 block text-xl"></i>
                                <strong>Panduan merekam:</strong>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-indigo-800 dark:text-indigo-300">
                                    <li>Mulai menggambar di atas kotak border putus-putus.</li>
                                    <li>Setiap tarikan garis dihitung sebagai 1 stroke.</li>
                                    <li>Perhatikan urutan stroke Anda dengan cermat.</li>
                                    <li>Gunakan <strong>Fullscreen</strong> untuk area gambar lebih besar.</li>
                                </ul>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button type="button" id="clearBtn" class="bg-white dark:bg-gray-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-semibold py-2.5 px-4 rounded-lg border border-red-200 dark:border-red-800 transition-colors flex items-center justify-center flex-1 shadow-sm">
                                    <i class="fas fa-trash-alt mr-2"></i> Bersihkan Semua
                                </button>
                                <button type="button" id="undoBtn" class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-semibold py-2.5 px-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors flex items-center justify-center flex-1 shadow-sm">
                                    <i class="fas fa-undo mr-2"></i> Hapus Terakhir
                                </button>
                            </div>
                            <div class="mt-4 pt-4 border-t border-indigo-100/50 dark:border-indigo-800/50 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-400">Total Tarikan:</span>
                                <span id="strokeCount" class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 font-black text-lg px-4 py-1 rounded-full shadow-inner">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="strokes" id="strokesData" value="{{ old('strokes', is_string($kanji->strokes) ? $kanji->strokes : json_encode($kanji->strokes)) }}">
            </div>

            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center">
                            <i class="fas fa-language text-indigo-500 dark:text-indigo-400 mr-2"></i> Contoh Kalimat
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambahkan kalimat untuk mendemonstrasikan penggunaan kanji ini (Opsional).</p>
                    </div>
                    <button type="button" onclick="addExampleRow()" 
                            class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/60 transition-colors text-sm flex items-center shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah Kalimat
                    </button>
                </div>

                <div id="examples-container" class="space-y-4">
                    @if(isset($kanji->examples) && count($kanji->examples) > 0)
                        @foreach($kanji->examples as $index => $example)
                        <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm relative group" id="example-row-{{ $index }}">
                            <div class="flex-1 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teks Jepang Murni <span class="text-red-500">*</span></label>
                                    <input type="text" name="examples[{{ $index }}][japanese_text]" value="{{ $example->japanese_text }}" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm placeholder-gray-400 dark:placeholder-gray-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teks Furigana (HTML Tag &lt;ruby&gt;)</label>
                                    <input type="text" name="examples[{{ $index }}][furigana_html]" value="{{ $example->furigana_html }}"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm shadow-sm placeholder-gray-400 dark:placeholder-gray-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Arti (Bahasa Indonesia) <span class="text-red-500">*</span></label>
                                    <input type="text" name="examples[{{ $index }}][meaning]" value="{{ $example->meaning }}" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm placeholder-gray-400 dark:placeholder-gray-500">
                                </div>
                            </div>
                            <div class="flex items-start pt-8">
                                <button type="button" onclick="removeExampleRow({{ $index }})" 
                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800" title="Hapus Kalimat">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center text-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Perbarui Data Huruf
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== FULLSCREEN CANVAS MODAL ===== --}}
<div id="fullscreenModal" 
     class="hidden fixed inset-0 z-[999] flex flex-col bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm transition-colors duration-300">

    {{-- Top bar --}}
    <div class="flex items-center justify-between px-4 sm:px-6 py-3 flex-shrink-0 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                <i class="fas fa-pen-nib text-indigo-600 dark:text-indigo-400 text-sm"></i>
            </div>
            <div>
                <p class="text-gray-800 dark:text-white font-bold text-sm leading-none">Mode Fullscreen</p>
                <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Gambar lebih bebas & presisi</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800">
                <i class="fas fa-layer-group text-indigo-500 dark:text-indigo-400 text-xs"></i>
                <span class="text-indigo-600 dark:text-indigo-300 text-xs font-medium">Stroke</span>
                <span id="fsStrokeCount" class="text-indigo-800 dark:text-indigo-100 font-black text-sm">0</span>
            </div>
            <button id="closeFullscreenBtn" type="button"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm font-semibold transition-all">
                <i class="fas fa-compress-alt text-xs"></i>
                <span>Keluar</span>
            </button>
        </div>
    </div>

    {{-- Canvas area --}}
    <div class="flex-1 flex items-center justify-center p-4 overflow-hidden">
        <div class="relative bg-white dark:bg-gray-800 shadow-lg rounded-2xl" id="fsCanvasContainer">
            {{-- Grid reference lines --}}
            <div class="absolute inset-0 pointer-events-none rounded-2xl overflow-hidden" style="background-image: linear-gradient(rgba(99,102,241,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,0.1) 1px, transparent 1px); background-size: 25% 25%;"></div>
            {{-- Center crosshair --}}
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div style="width: 1px; height: 100%; background: rgba(99,102,241,0.15);"></div>
            </div>
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div style="height: 1px; width: 100%; background: rgba(99,102,241,0.15);"></div>
            </div>
            <canvas id="fullscreenCanvas" width="300" height="300"
                    class="relative z-10 rounded-2xl cursor-crosshair block border-2 border-dashed border-indigo-300 dark:border-indigo-700"
                    style="touch-action: none;"></canvas>
        </div>
    </div>

    {{-- Bottom action bar --}}
    <div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center gap-3 max-w-lg mx-auto">
            <button id="fsUndoBtn" type="button"
                    class="flex-1 flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-xl font-semibold text-sm sm:text-base transition-all active:scale-95 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 shadow-sm">
                <i class="fas fa-undo"></i>
                <span>Hapus Terakhir</span>
            </button>
            <button id="fsClearBtn" type="button"
                    class="flex-1 flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-xl font-semibold text-sm sm:text-base transition-all active:scale-95 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800 shadow-sm">
                <i class="fas fa-trash-alt"></i>
                <span>Bersihkan Semua</span>
            </button>
        </div>
        <p class="text-center text-gray-500 dark:text-gray-400 text-xs mt-3">Tekan <kbd class="px-1.5 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600">Esc</kbd> untuk keluar</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- SCRIPT UNTUK FORM DINAMIS (CONTOH KALIMAT) ---
    let exampleIndex = {{ isset($kanji->examples) ? count($kanji->examples) : 0 }};

    function addExampleRow() {
        const container = document.getElementById('examples-container');
        
        const html = `
            <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm relative group" id="example-row-${exampleIndex}">
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teks Jepang Murni <span class="text-red-500">*</span></label>
                        <input type="text" name="examples[${exampleIndex}][japanese_text]" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Contoh: 日本の生活様式">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teks Jepang dengan Furigana</label>
                        <input type="text" name="examples[${exampleIndex}][furigana_html]" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm font-mono text-sm" 
                               placeholder="Contoh: 日本(にほん)の生活様式(せいかつようしき)">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Ketik Kanji lalu langsung beri kurung. Contoh: <b>私(わたし)</b> atau <b>食(た)べる</b>.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Arti (Bahasa Indonesia) <span class="text-red-500">*</span></label>
                        <input type="text" name="examples[${exampleIndex}][meaning]" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Contoh: Gaya hidup Jepang...">
                    </div>
                </div>
                <div class="flex items-start pt-8">
                    <button type="button" onclick="removeExampleRow(${exampleIndex})" 
                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800" title="Hapus Kalimat">
                        <i class="fas fa-trash-alt text-lg"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        exampleIndex++;
    }

    function removeExampleRow(index) {
        const row = document.getElementById(`example-row-${index}`);
        if(row) row.remove();
    }

    // --- SCRIPT CANVAS STROKES ---
    document.addEventListener('DOMContentLoaded', function() {
        // ── Elements ──────────────────────────────────────────────
        const canvas        = document.getElementById('drawingCanvas');
        const ctx           = canvas.getContext('2d');
        const fsCanvas      = document.getElementById('fullscreenCanvas');
        const fsCtx         = fsCanvas.getContext('2d');
        const strokesInput  = document.getElementById('strokesData');
        const strokeCountEl = document.getElementById('strokeCount');
        const fsStrokeEl    = document.getElementById('fsStrokeCount');
        const modal         = document.getElementById('fullscreenModal');
        const form          = document.getElementById('kanjiForm');

        // ── Shared state ──────────────────────────────────────────
        let allStrokes    = [];
        let isDrawing     = false;
        let currentStroke = [];
        let activeCtx     = ctx;
        let activeCanvas  = canvas;

        // ── Context setup ─────────────────────────────────────────
        function setupCtx(c) {
            c.lineWidth = 4;
            c.lineCap   = 'round';
            c.lineJoin  = 'round';
        }
        setupCtx(ctx);
        setupCtx(fsCtx);

        function updateStrokeColor() {
            // Cek apakah class 'dark' aktif di elemen root (html)
            const isDark = document.documentElement.classList.contains('dark');
            
            // Jika dark mode, warna tinta putih-abu (#f8fafc). Jika light mode, biru gelap (#2d3748)
            const inkColor = isDark ? '#f8fafc' : '#2d3748';
            
            // Terapkan ke kanvas kecil dan kanvas fullscreen sekaligus
            ctx.strokeStyle   = inkColor;
            fsCtx.strokeStyle = inkColor;
        }
        updateStrokeColor();

        const observer = new MutationObserver(() => { updateStrokeColor(); redrawBoth(); });
        observer.observe(document.documentElement, { attributes: true });

        // ── Resize fullscreen canvas to fit viewport ───────────────
        function resizeFsCanvas() {
            const topBar    = document.querySelector('#fullscreenModal > div:first-child').offsetHeight;
            const bottomBar = document.querySelector('#fullscreenModal > div:last-child').offsetHeight;
            const padding   = 32;
            const availH    = window.innerHeight - topBar - bottomBar - padding;
            const availW    = window.innerWidth  - padding;
            const size      = Math.min(availH, availW, 720);
            fsCanvas.style.width  = size + 'px';
            fsCanvas.style.height = size + 'px';
        }
        window.addEventListener('resize', () => { resizeFsCanvas(); redrawFsCanvas(); });

        // ── Draw helpers ──────────────────────────────────────────
        function redrawSingle(c, context) {
            context.clearRect(0, 0, c.width, c.height);
            allStrokes.forEach(stroke => {
                if (!stroke.length) return;
                context.beginPath();
                context.moveTo(stroke[0].x, stroke[0].y);
                for (let i = 1; i < stroke.length; i++) context.lineTo(stroke[i].x, stroke[i].y);
                context.stroke();
            });
        }

        function redrawCanvas()   { redrawSingle(canvas,   ctx);   }
        function redrawFsCanvas() { redrawSingle(fsCanvas, fsCtx); }

        function redrawBoth() {
            redrawCanvas();
            redrawFsCanvas();
            syncData();
        }

        function syncData() {
            strokesInput.value        = JSON.stringify(allStrokes);
            strokeCountEl.textContent = allStrokes.length;
            fsStrokeEl.textContent    = allStrokes.length;
        }

        // ── Initial load (existing strokes for edit page) ─────────
        try {
            const parsed = JSON.parse(strokesInput.value);
            if (Array.isArray(parsed) && parsed.length > 0) {
                allStrokes = parsed;
                redrawCanvas();
                syncData();
            }
        } catch(e) {}

        // ── Pointer helpers ───────────────────────────────────────
        function getPos(evt, targetCanvas) {
            const rect   = targetCanvas.getBoundingClientRect();
            const scaleX = targetCanvas.width  / rect.width;
            const scaleY = targetCanvas.height / rect.height;
            return {
                x: (evt.clientX - rect.left) * scaleX,
                y: (evt.clientY - rect.top)  * scaleY
            };
        }
        function getTouchPos(evt, targetCanvas) {
            if (!evt.touches || !evt.touches.length) return null;
            return getPos(evt.touches[0], targetCanvas);
        }

        // ── Generic draw handlers ─────────────────────────────────
        function startDraw(pos) {
            isDrawing     = true;
            currentStroke = [pos];
            activeCtx.beginPath();
            activeCtx.moveTo(pos.x, pos.y);
        }
        function continueDraw(pos) {
            if (!isDrawing) return;
            currentStroke.push(pos);
            activeCtx.lineTo(pos.x, pos.y);
            activeCtx.stroke();
        }
        function endDraw() {
            if (!isDrawing) return;
            isDrawing = false;
            if (currentStroke.length > 0) {
                allStrokes.push(currentStroke);
                if (activeCanvas === canvas) redrawFsCanvas();
                else                         redrawCanvas();
                syncData();
            }
        }

        // ── Normal canvas events ───────────────────────────────────
        canvas.addEventListener('mousedown', e => { activeCtx = ctx; activeCanvas = canvas; startDraw(getPos(e, canvas)); });
        canvas.addEventListener('mousemove', e => { if (activeCanvas === canvas) continueDraw(getPos(e, canvas)); });
        canvas.addEventListener('mouseup',   endDraw);
        canvas.addEventListener('mouseout',  endDraw);

        canvas.addEventListener('touchstart', e => { e.preventDefault(); activeCtx = ctx; activeCanvas = canvas; const p = getTouchPos(e, canvas); if(p) startDraw(p); }, {passive:false});
        canvas.addEventListener('touchmove',  e => { e.preventDefault(); if(activeCanvas===canvas){ const p = getTouchPos(e, canvas); if(p) continueDraw(p); } }, {passive:false});
        canvas.addEventListener('touchend',   endDraw);

        // ── Fullscreen canvas events ───────────────────────────────
        fsCanvas.addEventListener('mousedown', e => { activeCtx = fsCtx; activeCanvas = fsCanvas; startDraw(getPos(e, fsCanvas)); });
        fsCanvas.addEventListener('mousemove', e => { if (activeCanvas === fsCanvas) continueDraw(getPos(e, fsCanvas)); });
        fsCanvas.addEventListener('mouseup',   endDraw);
        fsCanvas.addEventListener('mouseout',  endDraw);

        fsCanvas.addEventListener('touchstart', e => { e.preventDefault(); activeCtx = fsCtx; activeCanvas = fsCanvas; const p = getTouchPos(e, fsCanvas); if(p) startDraw(p); }, {passive:false});
        fsCanvas.addEventListener('touchmove',  e => { e.preventDefault(); if(activeCanvas===fsCanvas){ const p = getTouchPos(e, fsCanvas); if(p) continueDraw(p); } }, {passive:false});
        fsCanvas.addEventListener('touchend',   endDraw);

        // ── Undo / Clear (shared, works from both views) ──────────
        function doUndo()  { if (allStrokes.length > 0) { allStrokes.pop(); redrawBoth(); } }
        function doClear() { allStrokes = []; redrawBoth(); }

        document.getElementById('clearBtn').addEventListener('click', doClear);
        document.getElementById('undoBtn').addEventListener('click', doUndo);
        document.getElementById('fsClearBtn').addEventListener('click', doClear);
        document.getElementById('fsUndoBtn').addEventListener('click', doUndo);

        // ── Fullscreen open / close ───────────────────────────────
        document.getElementById('openFullscreenBtn').addEventListener('click', () => {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            resizeFsCanvas();
            redrawFsCanvas();
            syncData();
        });

        function closeFullscreen() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            redrawCanvas();
        }

        document.getElementById('closeFullscreenBtn').addEventListener('click', closeFullscreen);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFullscreen(); });

        // ── Form submit validation ────────────────────────────────
        form.addEventListener('submit', e => {
            if (allStrokes.length === 0) {
                e.preventDefault();
                alert('Tolong rekam minimal 1 coretan (stroke) kanji.');
            }
        });
    });
</script>
@endpush