@extends('layouts.admin')

@section('title', 'Tambah Kanji')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.kanjis.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kanji
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
    <div class="border-b border-gray-100 px-6 py-5 bg-gray-50">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-plus-circle text-indigo-500 mr-2 border bg-white rounded-full p-2 shadow-sm"></i> Form Tambah Kanji
        </h3>
    </div>
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.kanjis.store') }}" method="POST" id="kanjiForm" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="character" class="block text-sm font-semibold text-gray-700 mb-2">Karakter *</label>
                    <input type="text" name="character" id="character" value="{{ old('character') }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-2xl shadow-sm" placeholder="Contoh: 日">
                </div>
                
                <div>
                    <label for="meaning" class="block text-sm font-semibold text-gray-700 mb-2">Arti (Meaning) *</label>
                    <input type="text" name="meaning" id="meaning" value="{{ old('meaning') }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" placeholder="Contoh: Matahari, Hari">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
                    <select name="category" id="category" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white shadow-sm">
                        <option value="kanji" {{ old('category') == 'kanji' ? 'selected' : '' }}>Kanji</option>
                        <option value="hiragana" {{ old('category') == 'hiragana' ? 'selected' : '' }}>Hiragana</option>
                        <option value="katakana" {{ old('category') == 'katakana' ? 'selected' : '' }}>Katakana</option>
                    </select>
                </div>
                
                <div>
                    <label for="level" class="block text-sm font-semibold text-gray-700 mb-2">Level (JLPT) <span class="text-gray-400 font-normal ml-1 text-xs bg-gray-100 px-2 py-1 rounded">Opsional</span></label>
                    <select name="level" id="level" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white shadow-sm">
                        <option value="">Pilih Level...</option>
                        <option value="5" {{ old('level') == '5' ? 'selected' : '' }}>N5</option>
                        <option value="4" {{ old('level') == '4' ? 'selected' : '' }}>N4</option>
                        <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>N3</option>
                        <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>N2</option>
                        <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>N1</option>
                    </select>
                </div>

                <div>
                    <label for="kunyomi" class="block text-sm font-semibold text-gray-700 mb-2">Kunyomi <span class="text-gray-400 font-normal ml-1 text-xs bg-gray-100 px-2 py-1 rounded">Opsional</span></label>
                    <input type="text" name="kunyomi" id="kunyomi" value="{{ old('kunyomi') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" placeholder="Contoh: ひ, -び, -か">
                </div>

                <div>
                    <label for="onyomi" class="block text-sm font-semibold text-gray-700 mb-2">Onyomi <span class="text-gray-400 font-normal ml-1 text-xs bg-gray-100 px-2 py-1 rounded">Opsional</span></label>
                    <input type="text" name="onyomi" id="onyomi" value="{{ old('onyomi') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" placeholder="Contoh: ニチ, ジツ">
                </div>

                <div class="md:col-span-2">
                    <label for="stroke_order_image" class="block text-sm font-semibold text-gray-700 mb-2">Gambar Urutan Stroke <span class="text-gray-400 font-normal ml-1 text-xs bg-gray-100 px-2 py-1 rounded">Opsional</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg bg-white hover:bg-gray-50 transition-colors relative" x-data="{ fileName: null, previewUrl: null }">
                        <div class="space-y-1 text-center w-full">
                            <template x-if="!previewUrl">
                                <i class="fas fa-image mx-auto h-12 w-12 text-gray-400 mb-3"></i>
                            </template>
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="mx-auto h-32 object-contain mb-4 rounded border border-gray-200 shadow-sm" alt="Preview">
                            </template>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="stroke_order_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload file gambar</span>
                                    <input id="stroke_order_image" name="stroke_order_image" type="file" class="sr-only" accept="image/*" @change="if($refs.fileInput.files.length > 0) { fileName = $refs.fileInput.files[0].name; previewUrl = URL.createObjectURL($refs.fileInput.files[0]); }" x-ref="fileInput">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-2" x-text="fileName ? fileName : 'PNG, JPG, GIF up to 10MB'"></p>
                            <p class="text-xs text-gray-500 mt-2"><a href="https://www.japanesejlpt.com/tools/kanji-gif-generator/" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors inline-flex items-center group" target="_blank">Untuk membuat gambar stroke, kunjungi link ini</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-language text-indigo-500 mr-2"></i> Contoh Kalimat
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tambahkan kalimat untuk mendemonstrasikan penggunaan kanji ini (Opsional).</p>
                    </div>
                    <button type="button" onclick="addExampleRow()" 
                            class="px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold rounded-lg hover:bg-indigo-200 transition-colors text-sm flex items-center shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah Kalimat
                    </button>
                </div>

                <div id="examples-container" class="space-y-4">
                    </div>
            </div>
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2"><i class="fas fa-pen-nib mr-2 text-indigo-500"></i> Rekam Coretan (Strokes) *</label>
                <div class="bg-gray-50 border border-t-0 border-x-0 border-b-4 border-indigo-100 p-5 rounded-xl shadow-sm">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="bg-white p-2 rounded-xl shadow-sm inline-block border border-gray-200 relative">
                            <canvas id="drawingCanvas" width="300" height="300" class="border border-dashed border-indigo-200 rounded-lg cursor-crosshair bg-white" style="touch-action: none;"></canvas>
                        </div>
                        
                        <div class="flex-1 space-y-4 w-full">
                            <div class="bg-indigo-50 text-indigo-900 justify-center p-4 rounded-lg text-sm border border-indigo-100 shadow-sm leading-relaxed">
                                <i class="fas fa-info-circle mr-2 mb-2 text-indigo-600 block text-xl"></i>
                                <strong>Panduan merekam:</strong>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-indigo-800">
                                    <li>Mulai menggambar di atas kotak border putus-putus.</li>
                                    <li>Setiap tarikan garis dihitung sebagai 1 stroke.</li>
                                    <li>Perhatikan urutan stroke Anda dengan cermat.</li>
                                </ul>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button type="button" id="clearBtn" class="bg-white text-red-600 hover:bg-red-50 font-semibold py-2 px-4 rounded-lg border border-red-200 transition-colors flex items-center justify-center flex-1 shadow-sm">
                                    <i class="fas fa-trash-alt mr-2"></i> Bersihkan Semua
                                </button>
                                <button type="button" id="undoBtn" class="bg-white text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg border border-gray-200 transition-colors flex items-center justify-center flex-1 shadow-sm">
                                    <i class="fas fa-undo mr-2"></i> Hapus Terakhir
                                </button>
                            </div>
                            <div class="mt-4 pt-4 border-t border-indigo-100/50 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-600">Total Tarikan:</span>
                                <span id="strokeCount" class="bg-indigo-100 text-indigo-800 font-black text-lg px-4 py-1 rounded-full shadow-inner">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="strokes" id="strokesData" value="{{ old('strokes', '[]') }}">
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center text-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Simpan Data Kanji
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- SCRIPT UNTUK FORM DINAMIS (CONTOH KALIMAT) ---
    let exampleIndex = 0;

    function addExampleRow() {
        const container = document.getElementById('examples-container');
        
        const html = `
            <div class="flex flex-col sm:flex-row gap-4 bg-white p-5 border border-gray-200 rounded-xl shadow-sm relative group" id="example-row-${exampleIndex}">
                
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Jepang Murni (Untuk Suara TTS) <span class="text-red-500">*</span></label>
                        <input type="text" name="examples[${exampleIndex}][japanese_text]" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                               placeholder="Contoh: 日本の生活様式">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Furigana (HTML Tag &lt;ruby&gt;)</label>
                        <input type="text" name="examples[${exampleIndex}][furigana_html]" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm font-mono text-sm text-gray-600" 
                               placeholder="Contoh: <ruby>日本<rt>にほん</rt></ruby>の<ruby>生活様式<rt>せいかつようしき</rt></ruby>">
                        <p class="text-xs text-gray-500 mt-2">Gunakan tag ruby untuk memunculkan hiragana kecil di atas kanji.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Arti (Bahasa Indonesia) <span class="text-red-500">*</span></label>
                        <input type="text" name="examples[${exampleIndex}][meaning]" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                               placeholder="Contoh: Gaya hidup Jepang...">
                    </div>
                </div>

                <div class="flex items-start pt-8">
                    <button type="button" onclick="removeExampleRow(${exampleIndex})" 
                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-200" title="Hapus Kalimat">
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
        if(row) {
            row.remove();
        }
    }

    // --- SCRIPT CANVAS STROKES (BAWAAN ANDA) ---
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('drawingCanvas');
        const ctx = canvas.getContext('2d');
        const strokesDataInput = document.getElementById('strokesData');
        const clearBtn = document.getElementById('clearBtn');
        const undoBtn = document.getElementById('undoBtn');
        const strokeCountDisplay = document.getElementById('strokeCount');
        const form = document.getElementById('kanjiForm');
        
        ctx.lineWidth = 4;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#2d3748';

        let isDrawing = false;
        let currentStroke = [];
        let allStrokes = [];

        try {
            const initialStrokes = JSON.parse(strokesDataInput.value);
            if (Array.isArray(initialStrokes) && initialStrokes.length > 0) {
                allStrokes = initialStrokes;
                redrawCanvas();
            }
        } catch (e) {
            console.error('Invalid initial strokes data');
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', endDrawing);
        canvas.addEventListener('mouseout', endDrawing);

        canvas.addEventListener('touchstart', handleTouchStart, {passive: false});
        canvas.addEventListener('touchmove', handleTouchMove, {passive: false});
        canvas.addEventListener('touchend', endDrawing);

        function getMousePos(evt) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (evt.clientX - rect.left) * scaleX,
                y: (evt.clientY - rect.top) * scaleY
            };
        }

        function getTouchPos(evt) {
            if (!evt.touches || evt.touches.length === 0) return null;
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (evt.touches[0].clientX - rect.left) * scaleX,
                y: (evt.touches[0].clientY - rect.top) * scaleY
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            currentStroke = [];
            const pos = getMousePos(e);
            currentStroke.push(pos);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function handleTouchStart(e) {
            e.preventDefault();
            const pos = getTouchPos(e);
            if (pos) {
                isDrawing = true;
                currentStroke = [];
                currentStroke.push(pos);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }
        }

        function draw(e) {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            currentStroke.push(pos);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function handleTouchMove(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getTouchPos(e);
            if (pos) {
                currentStroke.push(pos);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
            }
        }

        function endDrawing() {
            if (isDrawing) {
                isDrawing = false;
                if (currentStroke.length > 0) {
                    allStrokes.push(currentStroke);
                    updateStrokesData();
                }
            }
        }

        function updateStrokesData() {
            strokesDataInput.value = JSON.stringify(allStrokes);
            strokeCountDisplay.textContent = allStrokes.length;
        }

        function redrawCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            allStrokes.forEach(stroke => {
                if (stroke.length === 0) return;
                ctx.beginPath();
                ctx.moveTo(stroke[0].x, stroke[0].y);
                for (let i = 1; i < stroke.length; i++) {
                    ctx.lineTo(stroke[i].x, stroke[i].y);
                }
                ctx.stroke();
            });
            updateStrokesData();
        }

        clearBtn.addEventListener('click', () => {
            allStrokes = [];
            redrawCanvas();
        });

        undoBtn.addEventListener('click', () => {
            if (allStrokes.length > 0) {
                allStrokes.pop();
                redrawCanvas();
            }
        });

        form.addEventListener('submit', function(e) {
            if (allStrokes.length === 0) {
                e.preventDefault();
                alert('Tolong rekam minimal 1 coretan (stroke) kanji.');
            }
        });
    });
</script>
@endpush