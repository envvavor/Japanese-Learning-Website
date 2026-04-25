@extends('layouts.app')

@section('title', 'Belajar Kanji')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                Belajar {{ $category ? ucfirst($category) : 'Huruf' }}
            </h1>
            <p class="text-base text-slate-500 dark:text-slate-400 mt-1">
                Pilih karakter untuk mulai latihan penulisan.
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-700 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div id="menuArea" class="transition-all duration-500">
        <div id="kanjiContainer" class="space-y-8">
            {{-- Skeleton Loading Group --}}
            <div class="animate-pulse mb-12">
                {{-- Skeleton Header --}}
                <div class="flex items-center border-b border-slate-200 dark:border-gray-700 pb-3 mb-6">
                    <div class="w-8 h-8 bg-slate-200 dark:bg-gray-700 rounded-lg mr-3"></div>
                    <div class="h-6 w-48 bg-slate-200 dark:bg-gray-700 rounded-md"></div>
                </div>
                {{-- Skeleton Grid Cards (Dibuat 10 kotak sebagai placeholder) --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                    @for ($i = 0; $i < 10; $i++)
                        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 flex flex-col items-center justify-center">
                            {{-- Placeholder untuk huruf besar --}}
                            <div class="w-12 h-14 bg-slate-200 dark:bg-gray-700 rounded-md mb-4"></div>
                            {{-- Placeholder untuk label arti --}}
                            <div class="w-20 h-5 bg-slate-100 dark:bg-gray-600 rounded-lg"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div id="practiceArea" class="hidden mt-6 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-3xl shadow-2xl p-8 sm:p-10 max-w-2xl mx-auto relative overflow-hidden transition-all duration-500">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-blue-500"></div>

        <div class="flex justify-between items-center mb-8 mt-2">
            <h2 id="targetTitle" class="text-2xl font-bold text-slate-800 dark:text-white">
                Latihan
            </h2>

            <button onclick="backToMenu()" class="inline-flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors bg-slate-50 dark:bg-gray-700 hover:bg-rose-50 dark:hover:bg-rose-900/30 px-4 py-2 rounded-xl">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tutup
            </button>
        </div>

        <div class="flex items-center gap-3 mb-8">
            <p class="text-sm text-slate-500 dark:text-slate-400 flex-1 bg-slate-50 dark:bg-gray-700 p-3 rounded-lg border border-slate-100 dark:border-gray-600">
                Ikuti urutan dan arah goresan sesuai standar penulisan Jepang.
            </p>
            <label class="flex flex-col items-center gap-1 cursor-pointer select-none shrink-0">
                <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Panduan</span>
                <div class="relative w-10 h-6">
                    <input type="checkbox" id="guideToggle" class="sr-only" checked onchange="toggleGuide(this.checked)">
                    <div id="guideTrack" class="w-10 h-6 bg-indigo-500 rounded-full transition-colors duration-200"></div>
                    <div id="guideThumb" class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 translate-x-4"></div>
                </div>
            </label>
        </div>

       <div class="flex justify-center mb-8">
            <div class="relative bg-white dark:bg-gray-900 border-4 border-slate-700 dark:border-slate-500 rounded-lg shadow-inner overflow-hidden w-full max-w-[300px] aspect-square">
                <div class="absolute pointer-events-none border-l-2 border-dashed border-red-300 h-full left-1/2 opacity-60"></div>
                <div class="absolute pointer-events-none border-t-2 border-dashed border-red-300 w-full top-1/2 opacity-60"></div>

                {{-- Guide canvas: layer paling bawah, tidak bisa diklik --}}
                <canvas id="guideCanvas"
                        width="300"
                        height="300"
                        class="block w-full h-full absolute top-0 left-0 z-0 pointer-events-none transition-opacity duration-300">
                </canvas>

                <canvas id="kanjiCanvas"
                        width="300"
                        height="300"
                        class="block w-full h-full touch-none relative z-10 cursor-crosshair">
                </canvas>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-3 mb-6">
            <button onclick="clearCanvas()" class="flex-1 sm:flex-none sm:w-28 px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 hover:border-slate-300 dark:hover:border-gray-500 hover:text-slate-800 dark:hover:text-white transition-all">
                Reset
            </button>

            <button onclick="undoStroke()" class="flex-1 sm:flex-none sm:w-28 px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 hover:border-slate-300 dark:hover:border-gray-500 hover:text-slate-800 dark:hover:text-white transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                Undo
            </button>

            <button onclick="validateStroke()" class="w-full sm:w-auto sm:flex-none px-6 py-3 text-sm font-bold rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                Periksa Tulisan
            </button>
        </div>

        <div id="statusMsg" class="text-center text-sm font-bold text-slate-600 dark:text-slate-300 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 dark:bg-gray-700 border border-slate-100 dark:border-gray-600">
            Pilih karakter untuk memulai.
        </div>

    </div>

</div>

<script>
    // グローバル変数の初期化
    let templateKanji = []; 
    let currentStroke = [];
    let allStrokes = [];
    let isDrawing = false;

    // State toggle panduan — baca dari localStorage agar persisten
    let isGuideVisible = localStorage.getItem('kanjiGuideVisible') !== 'false';
    
    const canvas = document.getElementById('kanjiCanvas');
    const ctx = canvas.getContext('2d');
    const statusMsg = document.getElementById('statusMsg');

    // Guide canvas context
    const guideCanvas = document.getElementById('guideCanvas');
    const gCtx = guideCanvas.getContext('2d');

    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#2c3e50';

    let currentCategory = "{{ $category ?? '' }}";

    // APIからのカードデータロード機能（グループ化機能付き
    async function loadKanjiList() {
        try {
            let url = '/api/kanjis';
            if (currentCategory) {
                url += '?category=' + encodeURIComponent(currentCategory);
            }

            const response = await fetch(url);
            const kanjis = await response.json();
            const container = document.getElementById('kanjiContainer');

            if (!kanjis || kanjis.length === 0) {
                container.innerHTML = `<p style="text-align:center;" class="text-slate-500 dark:text-slate-400">Data kosong.</p>`;
                return;
            }

            // カテゴリーフィルター
            const hiragana = kanjis.filter(k => k.category === 'hiragana');
            const katakana = kanjis.filter(k => k.category === 'katakana');
            const kanjiList = kanjis.filter(k => k.category === 'kanji');

            // グループ化
            const kanjiGroups = {};
            kanjiList.forEach(k => {
                const lvl = k.level ? k.level : 'Lainnya';
                if (!kanjiGroups[lvl]) kanjiGroups[lvl] = [];
                kanjiGroups[lvl].push(k);
            });

            let htmlContent = '';

            const renderGridCards = (items) => {
                let cardsHtml = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">';
                items.forEach(k => {
                    cardsHtml += `
                        <div class="group bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 text-center hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:-translate-y-1 transition-all cursor-pointer" onclick="window.location='/kanji/${k.character}'">
                            <div class="text-4xl sm:text-5xl font-semibold text-slate-800 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                ${k.character}
                            </div>
                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-white bg-slate-50 dark:bg-gray-700 py-1.5 rounded-lg border border-slate-100 dark:border-gray-600 whitespace-nowrap overflow-hidden text-ellipsis px-2">
                                ${k.meaning}
                            </div>
                        </div>
                    `;
                });
                cardsHtml += '</div>';
                return cardsHtml;
            };

            if (hiragana.length > 0) {
                htmlContent += `
                    <div class="mb-12">
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center border-b border-slate-200 dark:border-gray-700 pb-3">
                            <span class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 w-8 h-8 flex items-center justify-center rounded-lg mr-3 text-lg">あ</span> Huruf Hiragana
                        </h2>
                        ${renderGridCards(hiragana)}
                    </div>`;
            }

            if (katakana.length > 0) {
                htmlContent += `
                    <div class="mb-12">
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center border-b border-slate-200 dark:border-gray-700 pb-3">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 w-8 h-8 flex items-center justify-center rounded-lg mr-3 text-lg">ア</span> Huruf Katakana
                        </h2>
                        ${renderGridCards(katakana)}
                    </div>`;
            }

            // RENDER KANJI
            if (kanjiList.length > 0) {
                // JIKA SEDANG DI MENU KHUSUS "KANJI", PISAHKAN BERDASARKAN LEVEL
                // Kita gunakan toLowerCase() agar "Kanji" atau "kanji" tetap terdeteksi
                if (currentCategory && currentCategory.toLowerCase() === 'kanji') {
                    const kanjiGroups = {};
                    kanjiList.forEach(k => {
                        // Pastikan k.level ada nilainya, jika kosong masuk ke 'Lainnya'
                        const lvl = (k.level && k.level !== "null") ? k.level : 'Lainnya';
                        if (!kanjiGroups[lvl]) kanjiGroups[lvl] = [];
                        kanjiGroups[lvl].push(k);
                    });

                    const sortedLevels = Object.keys(kanjiGroups).sort((a, b) => {
                        if (a === 'Lainnya') return 1; 
                        if (b === 'Lainnya') return -1;
                        return a - b; 
                    });

                    sortedLevels.forEach(lvl => {
                        const title = lvl === 'Lainnya' ? 'Kanji Ekstra (Tanpa Bab)' : `Kanji Bab ${lvl}`;
                        const badgeColor = lvl === 'Lainnya' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400';
                        htmlContent += `
                            <div class="mb-12">
                                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center border-b border-slate-200 dark:border-gray-700 pb-3">
                                    <span class="${badgeColor} w-8 h-8 flex items-center justify-center rounded-lg mr-3 text-lg">漢</span> ${title}
                                </h2>
                                ${renderGridCards(kanjiGroups[lvl])}
                            </div>`;
                    });
                } 
                // JIKA DI MENU CAMPURAN (SEMUA HURUF), JADIKAN SATU KELOMPOK SAJA
                else {
                    htmlContent += `
                        <div class="mb-12">
                            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center border-b border-slate-200 dark:border-gray-700 pb-3">
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 w-8 h-8 flex items-center justify-center rounded-lg mr-3 text-lg">漢</span> Huruf Kanji
                            </h2>
                            ${renderGridCards(kanjiList)}
                        </div>`;
                }
            }

            container.innerHTML = htmlContent;

        } catch (error) {
            console.error("Gagal load data:", error);
            document.getElementById('kanjiContainer').innerHTML = "<p class='text-red-500 dark:text-red-400 font-bold text-center'>Gagal terhubung ke database. Periksa koneksi atau API Anda.</p>";
        }
    }

    loadKanjiList();

    const params = new URLSearchParams(window.location.search);
    if (params.has('practice')) {
        const char = params.get('practice');
        if (char) startPractice(char);
    }

    // FUNGSI MEMULAI LATIHAN
    async function startPractice(char) {
        try {
            statusMsg.innerText = "Memuat template...";
            const response = await fetch(`/api/kanjis/${char}`);
            const data = await response.json();

            if (response.ok && data.strokes) {
                
                // Deteksi kategori otomatis jika kosong 
                if (!currentCategory && data.category) {
                    currentCategory = data.category; // Set kategori sesuai karakter
                    loadKanjiList(); // Render ulang menu grid di belakang layar
                }

                // Konversi string JSON ke Array jika diperlukan
                templateKanji = typeof data.strokes === 'string' ? JSON.parse(data.strokes) : data.strokes; 
                
                document.getElementById('targetTitle').innerText = `Latihan: ${data.character} (${data.meaning})`;
                document.getElementById('menuArea').style.display = 'none';
                document.getElementById('practiceArea').style.display = 'block';
                
                clearCanvas();
                statusMsg.innerText = "Silakan mulai menulis.";
                statusMsg.className = "text-center text-sm font-bold text-slate-600 dark:text-slate-300 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 dark:bg-gray-700 border border-slate-100 dark:border-gray-600 mt-4";
            } else {
                alert("Gagal memuat template dari database. Mungkin karakter ini belum diberi urutan garis.");
            }
        } catch (error) {
            alert("Error koneksi ke server saat memuat detail karakter.");
        }
    }

    function backToMenu() {
        document.getElementById('menuArea').style.display = 'block';
        document.getElementById('practiceArea').style.display = 'none';
        
        let newUrl = window.location.pathname;
        if (currentCategory) {
            newUrl += '?category=' + encodeURIComponent(currentCategory);
        }
        window.history.replaceState({}, document.title, newUrl);
    }

    // --- LOGIKA MENGGAMBAR DI CANVAS ---
    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        return { x: (clientX - rect.left) * scaleX, y: (clientY - rect.top) * scaleY };
    }

    function startDrawing(e) {
        e.preventDefault(); isDrawing = true; currentStroke = [];
        
        const isDark = document.documentElement.classList.contains('dark');
        ctx.strokeStyle = isDark ? '#f8fafc' : '#2c3e50';

        const pos = getPos(e); currentStroke.push(pos);
        ctx.beginPath(); ctx.moveTo(pos.x, pos.y);
    }

    function draw(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const pos = getPos(e); currentStroke.push(pos);
        ctx.lineTo(pos.x, pos.y); ctx.stroke();
    }

    function stopDrawing() {
        if (!isDrawing) return;
        isDrawing = false;
        if (currentStroke.length > 2) allStrokes.push(currentStroke);
    }

    canvas.addEventListener('mousedown', startDrawing); canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing); canvas.addEventListener('mouseout', stopDrawing);
    canvas.addEventListener('touchstart', startDrawing, {passive: false}); canvas.addEventListener('touchmove', draw, {passive: false});
    canvas.addEventListener('touchend', stopDrawing);

    // ============================================================
    //  FUNGSI GUIDE CANVAS
    // ============================================================

    // Gambar template samar di guideCanvas — dipanggil saat karakter dimuat & saat toggle
    function drawTemplateGuide() {
        gCtx.clearRect(0, 0, guideCanvas.width, guideCanvas.height);

        if (!isGuideVisible || !templateKanji || templateKanji.length === 0) return;

        // Warna berbeda tiap goresan agar urutan terlihat jelas
        const strokeColors = [
            'rgba(99,102,241,0.18)',   // indigo  – goresan 1
            'rgba(234,179,8,0.18)',    // yellow  – goresan 2
            'rgba(239,68,68,0.18)',    // red     – goresan 3
            'rgba(16,185,129,0.18)',   // green   – goresan 4
            'rgba(249,115,22,0.18)',   // orange  – goresan 5
            'rgba(168,85,247,0.18)',   // purple  – goresan 6+
        ];

        templateKanji.forEach((stroke, index) => {
            if (!stroke || stroke.length === 0) return;

            // Garis panduan
            gCtx.beginPath();
            gCtx.lineWidth = 22;
            gCtx.lineCap = 'round';
            gCtx.lineJoin = 'round';
            gCtx.strokeStyle = strokeColors[index % strokeColors.length];
            gCtx.moveTo(stroke[0].x, stroke[0].y);
            for (let i = 1; i < stroke.length; i++) {
                gCtx.lineTo(stroke[i].x, stroke[i].y);
            }
            gCtx.stroke();

            // Lingkaran + angka urutan di titik awal setiap goresan
            gCtx.fillStyle = 'rgba(99,102,241,0.45)';
            gCtx.beginPath();
            gCtx.arc(stroke[0].x, stroke[0].y, 8, 0, Math.PI * 2);
            gCtx.fill();

            gCtx.fillStyle = 'rgba(255,255,255,0.95)';
            gCtx.font = 'bold 9px sans-serif';
            gCtx.textAlign = 'center';
            gCtx.textBaseline = 'middle';
            gCtx.fillText(index + 1, stroke[0].x, stroke[0].y);
        });
    }

    // Toggle panduan on/off via checkbox
    function toggleGuide(visible) {
        isGuideVisible = visible;
        localStorage.setItem('kanjiGuideVisible', visible); // simpan ke localStorage
        const thumb = document.getElementById('guideThumb');
        const track = document.getElementById('guideTrack');
        thumb.style.transform = visible ? 'translateX(16px)' : 'translateX(0)';
        track.style.backgroundColor = visible ? '#6366f1' : '#94a3b8';
        drawTemplateGuide();
    }

    // Sinkronkan tampilan toggle dengan state yang tersimpan saat halaman pertama dimuat
    (function syncGuideToggleUI() {
        const checkbox = document.getElementById('guideToggle');
        const thumb    = document.getElementById('guideThumb');
        const track    = document.getElementById('guideTrack');
        if (!checkbox) return;
        checkbox.checked           = isGuideVisible;
        thumb.style.transform      = isGuideVisible ? 'translateX(16px)' : 'translateX(0)';
        track.style.backgroundColor = isGuideVisible ? '#6366f1' : '#94a3b8';
    })();

    function highlightWrongStrokes(wrongStrokeIndices) {
        redrawAllStrokes(); // gambar ulang dulu supaya bersih

        const prevStyle    = ctx.strokeStyle;
        const prevWidth    = ctx.lineWidth;
        const prevFont     = ctx.font;
        const prevFill     = ctx.fillStyle;
        const prevAlign    = ctx.textAlign;
        const prevBaseline = ctx.textBaseline;

        wrongStrokeIndices.forEach(strokeNum => {
            const stroke = allStrokes[strokeNum - 1];
            if (!stroke || stroke.length === 0) return;

            // Overlay merah semi-transparan di atas goresan yang salah
            ctx.strokeStyle = 'rgba(239,68,68,0.75)';
            ctx.lineWidth = 18;
            ctx.beginPath();
            ctx.moveTo(stroke[0].x, stroke[0].y);
            for (let i = 1; i < stroke.length; i++) {
                ctx.lineTo(stroke[i].x, stroke[i].y);
            }
            ctx.stroke();

            // Badge lingkaran merah berisi nomor goresan di titik awalnya
            const bx = stroke[0].x;
            const by = Math.max(stroke[0].y - 16, 12); // jangan sampai keluar canvas atas

            ctx.fillStyle = 'rgba(239,68,68,0.9)';
            ctx.beginPath();
            ctx.arc(bx, by, 10, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 11px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(strokeNum, bx, by);
        });

        // Restore semua style supaya tidak merusak gambar berikutnya
        ctx.strokeStyle   = prevStyle;
        ctx.lineWidth     = prevWidth;
        ctx.font          = prevFont;
        ctx.fillStyle     = prevFill;
        ctx.textAlign     = prevAlign;
        ctx.textBaseline  = prevBaseline;
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        allStrokes = [];
        // Refresh guide setiap kali reset
        drawTemplateGuide();
        statusMsg.innerText = "Canvas Bersih";
        statusMsg.className = "text-center text-sm font-bold text-slate-600 dark:text-slate-300 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 dark:bg-gray-700 border border-slate-100 dark:border-gray-600 mt-4";
    }

    function redrawAllStrokes() {
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
    }

    function undoStroke() {
        if (allStrokes.length > 0) {
            allStrokes.pop(); 
            redrawAllStrokes(); 
            statusMsg.innerHTML = "Goresan terakhir dihapus.";
            statusMsg.className = "text-center text-sm font-bold text-slate-500 dark:text-slate-400 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 dark:bg-gray-700 border border-slate-100 dark:border-gray-600 mt-4";
        } else {
            statusMsg.innerHTML = "Kanvas sudah kosong.";
            statusMsg.className = "text-center text-sm font-bold text-amber-500 dark:text-amber-400 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 mt-4";
        }
    }

    // NOTE: trueに変えたらdebugmodeが表示される
    window.DEBUG_MODE = true;

    // TODO: refactor semua fungsi biar rapih
    // LOGIKA VALIDASI STROKE ORDER + AI HYBRID
    function validateStroke() {
        if (allStrokes.length === 0) {
            statusMsg.innerHTML = "Tulis hurufnya dulu!";
            statusMsg.className = "text-center text-sm font-bold text-amber-600 dark:text-amber-400 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 mt-4";
            return;
        }

        if (!templateKanji || templateKanji.length === 0) {
            statusMsg.innerHTML = "Data template dari Admin belum siap!";
            return;
        }

        const templateCount = templateKanji.length;
        const userCount = allStrokes.length;

        if (window.DEBUG_MODE) {
            console.log("%c=== MEMULAI VALIDASI STROKE ===", "color: white; background: #4f46e5; font-size: 14px; padding: 4px; border-radius: 4px;");
            console.log(`Jumlah Goresan Template: ${templateCount} | Pengguna: ${userCount}`);
        }

        const normUser = normalizeStrokes(allStrokes);
        const normTemp = normalizeStrokes(templateKanji);

        const NUM_POINTS = 30;
        const TOLERANCE_ERROR = 35;

        const matchedCount = Math.min(templateCount, userCount);
        let totalScore = 0;
        let wrongStrokes = []; 

        for (let i = 0; i < matchedCount; i++) {
            const userPts = resample(normUser[i], NUM_POINTS);
            const tempPts = resample(normTemp[i], NUM_POINTS);

            let cxU = 0, cyU = 0, cxT = 0, cyT = 0;
            for(let j = 0; j < NUM_POINTS; j++) {
                cxU += userPts[j].x; cyU += userPts[j].y;
                cxT += tempPts[j].x; cyT += tempPts[j].y;
            }
            cxU /= NUM_POINTS; cyU /= NUM_POINTS;
            cxT /= NUM_POINTS; cyT /= NUM_POINTS;

            const posError = getDistance({x: cxU, y: cyU}, {x: cxT, y: cyT});

            let shapeError = 0;
            for(let j = 0; j < NUM_POINTS; j++) {
                const shiftedUserPt = { 
                    x: userPts[j].x - cxU + cxT, 
                    y: userPts[j].y - cyU + cyT 
                };
                shapeError += getDistance(shiftedUserPt, tempPts[j]);
            }
            shapeError /= NUM_POINTS;

            const totalError = shapeError + (posError * 0.4);
            let strokePct = 100 - (totalError / TOLERANCE_ERROR) * 100;
            strokePct = Math.max(0, Math.min(100, strokePct)); 
            
            // --- TAMBAHAN DEBUG SCORE PER STROKE ---
            if (window.DEBUG_MODE) {
                console.log(`[DEBUG] Goresan ke-${i + 1} | Akurasi: ${strokePct.toFixed(2)}% (Bentuk: ${shapeError.toFixed(2)}, Posisi: ${posError.toFixed(2)})`);
            }
            // ---------------------------------------

            totalScore += strokePct;

            if (strokePct < 65) {
                wrongStrokes.push(i + 1); 
            }
        }

        if (userCount < templateCount) {
            for (let k = userCount + 1; k <= templateCount; k++) {
                wrongStrokes.push(k);
            }
        }

        const overallPct = totalScore / templateCount; 
        let msg = `Akurasi Urutan: ${overallPct.toFixed(1)}%`;
        // kelebihan goresan
        if (userCount > templateCount) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Kelebihan ${userCount - templateCount} goresan!</span>`;
        }
        // kekurangan goresan
        if (userCount < templateCount) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Kekurangan ${templateCount - userCount} goresan!</span>`;
        }
        // kalau ada goresan yang salah
        if (wrongStrokes.length > 0) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Cek lagi goresan ke: ${wrongStrokes.join(', ')}</span>`;
            // Tampilkan highlight di kanvas untuk goresan yang salah
            highlightWrongStrokes(wrongStrokes);
        }

        // KALO URUTAN BENAR, PANGGIL CNN DAN SIMPAN KE DATASET
        if (overallPct >= 75 && userCount === templateCount && wrongStrokes.length === 0) {
            
            // Loading
            statusMsg.innerHTML = `<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 mr-2 mb-[-3px]"></div> Validating...`;
            statusMsg.className = "text-center text-sm font-bold text-indigo-600 dark:text-indigo-400 min-h-[24px] px-4 py-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 mt-4";
            
            //-------------------------------------------------------
            // Kumpulkan Dataset. apus // biar nyala
            autoSaveToDataset();
            //-------------------------------------------------------

            // Buat kanvas bayangan untuk dikirim ke AI (64x64, background hitam)
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = 64;  
            tempCanvas.height = 64;
            const tCtx = tempCanvas.getContext('2d');
            tCtx.fillStyle = "#000000";
            tCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            tCtx.lineWidth = 3; 
            tCtx.lineCap = 'round';
            tCtx.strokeStyle = '#ffffff';

            normUser.forEach(stroke => {
                if (stroke.length === 0) return;
                tCtx.beginPath();
                
                let startX = (stroke[0].x * 0.45) + 32;
                let startY = (stroke[0].y * 0.45) + 32;
                tCtx.moveTo(startX, startY);
                
                for (let i = 1; i < stroke.length; i++) {
                    let currX = (stroke[i].x * 0.45) + 32;
                    let currY = (stroke[i].y * 0.45) + 32;
                    tCtx.lineTo(currX, currY);
                }
                tCtx.stroke();
            });

            const imageData = tempCanvas.toDataURL("image/png");
            const targetChar = document.getElementById('targetTitle').innerText.split(' ')[1]; // Mengambil huruf dari judul

            // Proses Fetch ke Laravel -> Python
            fetch('/api/validate-ai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                },
                body: JSON.stringify({ character: targetChar, image_base64: imageData })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_supported === false) {
                        statusMsg.innerHTML = `<b>SEMPURNA!</b><br>Urutan goresan benar (${overallPct.toFixed(1)}%).<br><span class="text-xs text-indigo-600 dark:text-indigo-400 font-normal">Sistem AI belum dilatih untuk menilai bentuk huruf ini, namun urutan goresan Anda sudah tepat!</span>`;
                        statusMsg.className = "text-center text-sm font-bold text-emerald-600 dark:text-emerald-400 min-h-[24px] px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 mt-4";
                        return;
                    }
                    
                    // Buat elemen Diagram Batang (Bar Chart) dari data Top 3
                    let chartHtml = `<div class="mt-4 pt-3 border-t border-slate-200 dark:border-gray-600 text-left">
                                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Analisis Probabilitas AI:</p>`;
                    
                    data.top_3.forEach((item, index) => {
                        // Warnai batang: Hijau untuk tebakan ke-1, abu-abu/kuning untuk sisanya
                        let barColor = index === 0 ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-600';
                        chartHtml += `
                            <div class="flex items-center mb-1.5">
                                <span class="w-6 text-sm font-bold text-slate-700 dark:text-slate-300">${item.char}</span>
                                <div class="flex-1 bg-slate-100 dark:bg-gray-800 h-2.5 rounded-full mx-2 overflow-hidden border border-slate-200 dark:border-gray-700">
                                    <div class="${barColor} h-2.5 rounded-full transition-all duration-700" style="width: ${item.prob}%"></div>
                                </div>
                                <span class="text-xs w-10 text-right font-mono text-slate-500">${item.prob}%</span>
                            </div>`;
                    });
                    chartHtml += `</div>`;

                    // Tampilkan Hasil Utama + Diagramnya
                    if (data.is_match) {
                        statusMsg.innerHTML = `<b>SEMPURNA!</b><br>Urutan goresan benar (${overallPct.toFixed(1)}%).<br>AI yakin ini huruf <b>${data.predicted_char}</b>. ${chartHtml}`;
                        statusMsg.className = "text-center text-sm font-bold text-emerald-600 dark:text-emerald-400 min-h-[24px] px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 mt-4";
                    } else {
                        statusMsg.innerHTML = `<b>HAMPIR!</b><br>Urutan benar, tapi AI menebak ini huruf <b>${data.predicted_char}</b>.<br>Coba perbaiki proporsi garisnya! ${chartHtml}`;
                        statusMsg.className = "text-center text-sm font-bold text-amber-600 dark:text-amber-400 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 mt-4";
                    }
                } else {
                    statusMsg.innerHTML = `Urutan Benar (${overallPct.toFixed(1)}%)!<br><span class="text-xs font-normal text-rose-500">Server AI sedang offline.</span>`;
                }
            })

        // JIKA URUTAN MASIH SALAH, JANGAN PANGGIL AI
        } else if (overallPct >= 45 && userCount === templateCount) {
            statusMsg.innerHTML = `Hampir Benar!<br>${msg}`;
            statusMsg.className = "text-center text-sm font-bold text-amber-600 dark:text-amber-400 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 mt-4";
        } else {
            statusMsg.innerHTML = `Coba Perbaiki!<br>${msg}`;
            statusMsg.className = "text-center text-sm font-bold text-rose-600 dark:text-rose-400 min-h-[24px] px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 mt-4";
        }
    }
    
    // FUNGSI AUTO-SAVE (MENGUMPULKAN DATASET UNTUK AI)
    function autoSaveToDataset() {
        try {
            // Ekstrak huruf dari judul
            const titleText = document.getElementById('targetTitle').innerText;
            const currentCharacter = titleText.split(' ')[1]; 
            
            if(!currentCharacter) return;

            // Siapkan kanvas tersembunyi (64x64, latar hitam, garis putih)
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = 64;  
            tempCanvas.height = 64;
            const tCtx = tempCanvas.getContext('2d');

            tCtx.fillStyle = "#000000";
            tCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            tCtx.lineWidth = 3; 
            tCtx.lineCap = 'round';
            tCtx.lineJoin = 'round';
            tCtx.strokeStyle = '#ffffff';

            const normUser = normalizeStrokes(allStrokes);

            normUser.forEach(stroke => {
                if (stroke.length === 0) return;
                tCtx.beginPath();
                
                // Dikali 0.45 (skala) dan ditambah 32 (geser ke titik tengah kanvas 64x64)
                let startX = (stroke[0].x * 0.45) + 32;
                let startY = (stroke[0].y * 0.45) + 32;
                tCtx.moveTo(startX, startY);
                
                for (let i = 1; i < stroke.length; i++) {
                    let currX = (stroke[i].x * 0.45) + 32;
                    let currY = (stroke[i].y * 0.45) + 32;
                    tCtx.lineTo(currX, currY);
                }
                tCtx.stroke();
            });

            // Konversi jadi teks Base64
            const imageData = tempCanvas.toDataURL("image/png");
            
            // Kirim ke Backend Laravel
            fetch('/api/dataset/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                },
                body: JSON.stringify({
                    character: currentCharacter,
                    image_base64: imageData
                })
            })
            .then(response => {
                if(window.DEBUG_MODE) console.log("Auto-Save berhasil");
            })
            .catch(error => {
                if(window.DEBUG_MODE) console.error('Auto-Save gagal:', error);
            });

        } catch (err) {
             if(window.DEBUG_MODE) console.error('Error pada fungsi Auto-Save:', err);
        }
    }

    // FUNGSI MATEMATIKA
    function getBoundingBox(strokes) {
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
        strokes.forEach(stroke => {
            stroke.forEach(p => {
                if (p.x < minX) minX = p.x; if (p.y < minY) minY = p.y;
                if (p.x > maxX) maxX = p.x; if (p.y > maxY) maxY = p.y;
            });
        });
        return { width: maxX - minX, height: maxY - minY };
    }

    function normalizeStrokes(strokes) {
        if (!strokes || strokes.length === 0) return [];
        const box = getBoundingBox(strokes);
        const maxDim = Math.max(box.width, box.height) || 1;
        const scale = 100 / maxDim; 

        let cx = 0, cy = 0, pts = 0;
        strokes.forEach(stroke => {
            stroke.forEach(p => { cx += p.x; cy += p.y; pts++; });
        });
        if(pts === 0) return strokes;
        cx /= pts; cy /= pts;

        return strokes.map(stroke => stroke.map(p => ({
            x: (p.x - cx) * scale,
            y: (p.y - cy) * scale
        })));
    }

    function getDistance(p1, p2) { return Math.hypot(p1.x - p2.x, p1.y - p2.y); }
    
    function pathLength(points) { 
        let d = 0; 
        for (let i = 1; i < points.length; i++) d += getDistance(points[i - 1], points[i]); 
        return d; 
    }

    function resample(points, n) {
        if (!points || points.length === 0) return points;
        let I = pathLength(points) / (n - 1);
        let D = 0; let newPoints = [points[0]];
        for (let i = 1; i < points.length; i++) {
            let d = getDistance(points[i - 1], points[i]);
            if ((D + d) >= I) {
                let qx = points[i - 1].x + ((I - D) / d) * (points[i].x - points[i - 1].x);
                let qy = points[i - 1].y + ((I - D) / d) * (points[i].y - points[i - 1].y);
                let q = {x: qx, y: qy};
                newPoints.push(q); points.splice(i, 0, q); D = 0;
            } else { D += d; }
        }
        while (newPoints.length < n) { newPoints.push(points[points.length - 1]); }
        return newPoints;
    }

    let isDebugVisible = false;

    window.drawDebugPoints = function() {
        if (!window.DEBUG_MODE || allStrokes.length === 0) return;
        
        const NUM_POINTS = 30; 
        const previousFillStyle = ctx.fillStyle;
        const previousStrokeStyle = ctx.strokeStyle;
        const previousLineWidth = ctx.lineWidth;
        const previousFont = ctx.font;
        const previousLineDash = ctx.getLineDash();
        
        // HITUNG POSISI & SKALA USER
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
        let cx = 0, cy = 0, pts = 0;

        allStrokes.forEach(stroke => {
            stroke.forEach(p => {
                if (p.x < minX) minX = p.x; if (p.y < minY) minY = p.y;
                if (p.x > maxX) maxX = p.x; if (p.y > maxY) maxY = p.y;
                cx += p.x; cy += p.y; pts++;
            });
        });

        // Hitung skala terpanjang dari coretan User (untuk jadi patokan)
        const maxDim = Math.max(maxX - minX, maxY - minY) || 1;

        // Gambar Kotak Batas & Center User
        ctx.strokeStyle = '#10b981'; 
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 6]); 
        ctx.strokeRect(minX, minY, maxX - minX, maxY - minY);

        if (pts > 0) {
            cx /= pts; cy /= pts;
            ctx.fillStyle = '#10b981';
            ctx.beginPath();
            ctx.arc(cx, cy, 6, 0, Math.PI * 2);
            ctx.fill();
            ctx.font = "bold 12px sans-serif";
            ctx.fillText("Center", cx + 10, cy - 10);
        }

        // Gambar Titik Resampling User
        ctx.setLineDash([]);
        ctx.fillStyle = '#ef4444'; 
        ctx.font = "bold 10px sans-serif";
        
        allStrokes.forEach((stroke) => {
            const resampledPoints = resample(stroke, NUM_POINTS);
            resampledPoints.forEach((pt, ptIndex) => {
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, 4, 0, Math.PI * 2);
                ctx.fill();
                if (ptIndex === 0) ctx.fillText("U-Start", pt.x + 8, pt.y + 4);
                else if (ptIndex === NUM_POINTS - 1) ctx.fillText("U-End", pt.x + 8, pt.y + 4);
            });
        });

        // TEMPLATE MENGIKUTI USER
        if (templateKanji && templateKanji.length > 0) {
            
            // Panggil fungsi normalisasi milik Anda (Skala 100, Pusat 0,0)
            const normTemp = normalizeStrokes(templateKanji);

            // PROYEKSI BALIK: Ubah skala 100 jadi skala User, dan geser ke Titik Tengah User
            const mappedTemp = normTemp.map(stroke => stroke.map(p => ({
                x: p.x * (maxDim / 100) + cx,
                y: p.y * (maxDim / 100) + cy
            })));

            // Gambar Titik Template yang sudah menempel dengan User
            ctx.fillStyle = '#3b82f6'; // Warna Biru
            ctx.font = "bold 10px sans-serif";
            
            mappedTemp.forEach((stroke) => {
                // Lakukan resampling 30 titik untuk Template yang sudah diproyeksikan
                const resampledPoints = resample(stroke, NUM_POINTS);
                resampledPoints.forEach((pt, ptIndex) => {
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y, 4, 0, Math.PI * 2);
                    ctx.fill();
                    if (ptIndex === 0) ctx.fillText("T-Start", pt.x + 8, pt.y + 4);
                    else if (ptIndex === NUM_POINTS - 1) ctx.fillText("T-End", pt.x + 8, pt.y + 4);
                });
            });
        }
        
        // Restore style
        ctx.fillStyle = previousFillStyle;
        ctx.strokeStyle = previousStrokeStyle;
        ctx.lineWidth = previousLineWidth;
        ctx.font = previousFont;
        ctx.setLineDash(previousLineDash);
    };
    // Tombol Cepat: Shift + D (Khusus saat Debug Mode nyala)
    document.addEventListener('keydown', function(e) {
        if (window.DEBUG_MODE && e.shiftKey && e.key.toLowerCase() === 'd') {
            e.preventDefault(); 
            isDebugVisible = !isDebugVisible; 
            if (isDebugVisible) {
                window.drawDebugPoints();
            } else {
                redrawAllStrokes(); // Hapus titik merah
            }
        }
    });
</script>
@endsection