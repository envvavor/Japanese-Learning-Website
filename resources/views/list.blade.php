@extends('layouts.app')

@section('title', 'Belajar ' . ($category ? ucfirst($category) : 'Huruf') . ' — Manabu')

@section('content')
{{-- BLADE LOGIC UNTUK ICON DINAMIS --}}
@php
    $cat = strtolower($category ?? '');
    $iconText = '';
    $iconClass = 'fas fa-font text-3xl';
    $bgClass = 'bg-amber-100 text-amber-500 border-amber-200 dark:bg-amber-900/30 dark:border-amber-800';

    if ($cat === 'hiragana') {
        $iconText = 'あ';
        $iconClass = 'text-4xl font-black';
        $bgClass = 'bg-rose-100 text-rose-500 border-rose-200 dark:bg-rose-900/30 dark:border-rose-800';
    } elseif ($cat === 'katakana') {
        $iconText = 'ア';
        $iconClass = 'text-4xl font-black';
        $bgClass = 'bg-blue-100 text-blue-500 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800';
    } elseif ($cat === 'kanji') {
        $iconText = '漢';
        $iconClass = 'text-4xl font-black';
        $bgClass = 'bg-emerald-100 text-emerald-500 border-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800';
    }
@endphp

<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section Dinamis --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 {{ $bgClass }} rounded-2xl flex items-center justify-center border-2 shrink-0 shadow-sm">
                    @if($iconText)
                        <span class="{{ $iconClass }}">{{ $iconText }}</span>
                    @else
                        <i class="{{ $iconClass }}"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Belajar {{ $category ? ucfirst($category) : 'Semua Huruf' }}
                    </h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Pilih karakter untuk mulai latihan penulisan.</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Main Menu Area --}}
        <div id="menuArea" class="transition-all duration-500">
            <div id="kanjiContainer" class="space-y-12">
                {{-- Skeleton Loading Group --}}
                <div class="animate-pulse mb-12">
                    <div class="flex items-center border-b-4 border-slate-200 dark:border-gray-700 pb-3 mb-6">
                        <div class="w-10 h-10 bg-slate-200 dark:bg-gray-700 rounded-xl mr-3"></div>
                        <div class="h-6 w-48 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                        @for ($i = 0; $i < 10; $i++)
                            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-6 flex flex-col items-center justify-center h-36">
                                <div class="w-12 h-14 bg-slate-200 dark:bg-gray-700 rounded-xl mb-4"></div>
                                <div class="w-20 h-6 bg-slate-100 dark:bg-gray-600 rounded-lg"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Practice Area --}}
        <div id="practiceArea" class="hidden mt-6 bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-10 max-w-2xl mx-auto relative overflow-hidden transition-all duration-500 shadow-sm">
            
            <div class="absolute top-0 left-0 w-full h-3 bg-[#1cb0f6]"></div>

            <div class="flex justify-between items-center mb-8 mt-2">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-paint-brush text-[#1cb0f6]"></i> 
                    {{-- SPAN ini yang akan diisi JS agar pemisahan Teks tetap akurat --}}
                    <span id="targetTitle">Latihan</span>
                </h2>

                <button onclick="backToMenu()" class="inline-flex items-center text-sm font-black text-rose-500 bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 hover:bg-rose-100 dark:hover:bg-rose-900/40 active:translate-y-1 active:border-b-2 transition-all px-5 py-2.5 rounded-xl uppercase tracking-widest">
                    <i class="fas fa-times mr-2"></i> Tutup
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-8 bg-slate-50 dark:bg-gray-900/50 p-4 rounded-2xl border-2 border-slate-100 dark:border-gray-700">
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">
                        <i class="fas fa-info-circle text-[#1cb0f6] mr-1"></i> Ikuti urutan dan arah goresan sesuai standar penulisan Jepang.
                    </p>
                </div>
                <label class="flex flex-col items-center gap-1.5 cursor-pointer select-none shrink-0 bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border-2 border-slate-200 dark:border-gray-600 shadow-sm">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Panduan Garis</span>
                    <div class="relative w-12 h-6">
                        <input type="checkbox" id="guideToggle" class="sr-only" checked onchange="toggleGuide(this.checked)">
                        <div id="guideTrack" class="w-12 h-6 bg-[#1cb0f6] rounded-full transition-colors duration-200 border-2 border-transparent"></div>
                        <div id="guideThumb" class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 translate-x-6"></div>
                    </div>
                </label>
            </div>

           <div class="flex justify-center mb-8">
                <div class="relative bg-white dark:bg-gray-900 border-4 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden w-full max-w-[300px] aspect-square">
                    <div class="absolute pointer-events-none border-l-2 border-dashed border-rose-200 dark:border-rose-900/50 h-full left-1/2 opacity-60"></div>
                    <div class="absolute pointer-events-none border-t-2 border-dashed border-rose-200 dark:border-rose-900/50 w-full top-1/2 opacity-60"></div>

                    <canvas id="guideCanvas" width="300" height="300" class="block w-full h-full absolute top-0 left-0 z-0 pointer-events-none transition-opacity duration-300"></canvas>
                    <canvas id="kanjiCanvas" width="300" height="300" class="block w-full h-full touch-none relative z-10 cursor-crosshair"></canvas>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-3 mb-6">
                <button onclick="clearCanvas()" class="flex-1 sm:flex-none sm:w-32 px-4 py-3 text-sm font-black uppercase tracking-widest rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-700 text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all">
                    <i class="fas fa-trash-alt mr-1"></i> Reset
                </button>

                <button onclick="undoStroke()" class="flex-1 sm:flex-none sm:w-32 px-4 py-3 text-sm font-black uppercase tracking-widest rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-700 text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all">
                    <i class="fas fa-undo mr-1"></i> Undo
                </button>

                <button onclick="validateStroke()" class="w-full sm:w-auto sm:flex-none px-8 py-3 text-sm font-black uppercase tracking-widest rounded-2xl border-2 border-b-[6px] border-[#1899d6] bg-[#1cb0f6] text-white hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all shadow-sm">
                    <i class="fas fa-check mr-2"></i> Periksa
                </button>
            </div>

            <div id="statusMsg" class="text-center text-sm font-bold text-slate-600 dark:text-slate-300 px-4 py-4 rounded-xl bg-slate-50 dark:bg-gray-700 border-2 border-slate-200 dark:border-gray-600">
                Pilih karakter untuk memulai.
            </div>

        </div>
    </div>
</div>

<script>

    let templateKanji = []; 
    let currentStroke = [];
    let allStrokes = [];
    let isDrawing = false;

    let isGuideVisible = localStorage.getItem('kanjiGuideVisible') !== 'false';
    
    const canvas = document.getElementById('kanjiCanvas');
    const ctx = canvas.getContext('2d');
    const statusMsg = document.getElementById('statusMsg');

    const guideCanvas = document.getElementById('guideCanvas');
    const gCtx = guideCanvas.getContext('2d');

    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#2c3e50';

    let currentCategory = "{{ $category ?? '' }}";

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
                container.innerHTML = `<div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-16 text-center shadow-sm">
                        <i class="fas fa-folder-open text-6xl text-slate-300 dark:text-gray-600 mb-4"></i>
                        <h3 class="text-xl font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">Kategori Kosong</h3>
                    </div>`;
                return;
            }

            const hiragana = kanjis.filter(k => k.category === 'hiragana');
            const katakana = kanjis.filter(k => k.category === 'katakana');
            const kanjiList = kanjis.filter(k => k.category === 'kanji');

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
                        <div class="group bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 text-center hover:border-[#1cb0f6] dark:hover:border-[#1899d6] active:translate-y-1 active:border-b-2 transition-all cursor-pointer flex flex-col items-center justify-center h-36" onclick="window.location='/kanji/${k.character}'">
                            <div class="text-4xl sm:text-5xl font-black text-slate-800 dark:text-white mb-3 group-hover:text-[#1cb0f6] dark:group-hover:text-[#1899d6] transition-colors drop-shadow-sm">
                                ${k.character}
                            </div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-gray-700 py-1.5 px-2 rounded-lg border-2 border-slate-200 dark:border-gray-600 w-full truncate">
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
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center border-b-4 border-slate-200 dark:border-gray-700 pb-3">
                            <span class="bg-rose-100 dark:bg-rose-900/30 text-rose-500 w-10 h-10 flex items-center justify-center rounded-xl mr-3 text-xl border-2 border-rose-200 dark:border-rose-800">あ</span> HIRAGANA
                        </h2>
                        ${renderGridCards(hiragana)}
                    </div>`;
            }

            if (katakana.length > 0) {
                htmlContent += `
                    <div class="mb-12">
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center border-b-4 border-slate-200 dark:border-gray-700 pb-3">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-500 w-10 h-10 flex items-center justify-center rounded-xl mr-3 text-xl border-2 border-blue-200 dark:border-blue-800">ア</span> KATAKANA
                        </h2>
                        ${renderGridCards(katakana)}
                    </div>`;
            }

            if (kanjiList.length > 0) {
                if (currentCategory && currentCategory.toLowerCase() === 'kanji') {
                    const kanjiGroups = {};
                    kanjiList.forEach(k => {
                        const lvl = (k.level && k.level !== "null") ? k.level : 'Lainnya';
                        if (!kanjiGroups[lvl]) kanjiGroups[lvl] = [];
                        kanjiGroups[lvl].push(k);
                    });

                    const sortedLevels = Object.keys(kanjiGroups).sort((a, b) => {
                        if (a === 'Lainnya') return 1; 
                        if (b === 'Lainnya') return -1;
                        return b - a; 
                    });

                    sortedLevels.forEach(lvl => {
                        const title = lvl === 'Lainnya' ? 'Kanji Ekstra (Tanpa Bab)' : `Kanji Level N${lvl}`;
                        const badgeColor = lvl === 'Lainnya' ? 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700' : 'bg-emerald-100 text-emerald-500 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
                        htmlContent += `
                            <div class="mb-12">
                                <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center border-b-4 border-slate-200 dark:border-gray-700 pb-3">
                                    <span class="${badgeColor} w-10 h-10 flex items-center justify-center rounded-xl mr-3 text-xl border-2">漢</span> ${title}
                                </h2>
                                ${renderGridCards(kanjiGroups[lvl])}
                            </div>`;
                    });
                } 
                else {
                    htmlContent += `
                        <div class="mb-12">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center border-b-4 border-slate-200 dark:border-gray-700 pb-3">
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 w-10 h-10 flex items-center justify-center rounded-xl mr-3 text-xl border-2 border-emerald-200 dark:border-emerald-800">漢</span> KANJI
                            </h2>
                            ${renderGridCards(kanjiList)}
                        </div>`;
                }
            }

            container.innerHTML = htmlContent;

        } catch (error) {
            console.error("Gagal load data:", error);
            document.getElementById('kanjiContainer').innerHTML = "<p class='text-rose-500 font-bold text-center bg-rose-50 p-4 rounded-xl border-2 border-rose-200 dark:bg-rose-900/20 dark:border-rose-800'>Gagal terhubung ke database. Periksa koneksi atau API Anda.</p>";
        }
    }

    loadKanjiList();

    const params = new URLSearchParams(window.location.search);
    if (params.has('practice')) {
        const char = params.get('practice');
        if (char) startPractice(char);
    }

    async function startPractice(char) {
        try {
            statusMsg.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memuat template...';
            const response = await fetch(`/api/kanjis/${char}`);
            const data = await response.json();

            if (response.ok && data.strokes) {
                
                if (!currentCategory && data.category) {
                    currentCategory = data.category; 
                    loadKanjiList(); 
                }

                templateKanji = typeof data.strokes === 'string' ? JSON.parse(data.strokes) : data.strokes; 
                
                document.getElementById('targetTitle').innerText = `Latihan: ${data.character}`;
                document.getElementById('menuArea').style.display = 'none';
                document.getElementById('practiceArea').style.display = 'block';
                
                clearCanvas();
                statusMsg.innerHTML = "<i class='fas fa-pen mr-2 text-indigo-500'></i> Silakan mulai menulis.";
                statusMsg.className = "text-center text-sm font-bold text-slate-600 dark:text-slate-300 px-4 py-4 rounded-xl bg-slate-50 dark:bg-gray-700 border-2 border-slate-200 dark:border-gray-600 mt-4";
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

    function drawTemplateGuide() {
        gCtx.clearRect(0, 0, guideCanvas.width, guideCanvas.height);

        if (!isGuideVisible || !templateKanji || templateKanji.length === 0) return;

        const strokeColors = [
            'rgba(99,102,241,0.18)',   
            'rgba(234,179,8,0.18)',    
            'rgba(239,68,68,0.18)',    
            'rgba(16,185,129,0.18)',   
            'rgba(249,115,22,0.18)',   
            'rgba(168,85,247,0.18)',   
        ];

        templateKanji.forEach((stroke, index) => {
            if (!stroke || stroke.length === 0) return;

            gCtx.beginPath();
            gCtx.lineWidth = 12;
            gCtx.lineCap = 'round';
            gCtx.lineJoin = 'round';
            gCtx.strokeStyle = strokeColors[index % strokeColors.length];
            gCtx.moveTo(stroke[0].x, stroke[0].y);
            for (let i = 1; i < stroke.length; i++) {
                gCtx.lineTo(stroke[i].x, stroke[i].y);
            }
            gCtx.stroke();

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

    function toggleGuide(visible) {
        isGuideVisible = visible;
        localStorage.setItem('kanjiGuideVisible', visible); 
        const thumb = document.getElementById('guideThumb');
        const track = document.getElementById('guideTrack');
        thumb.style.transform = visible ? 'translateX(24px)' : 'translateX(0)';
        track.style.backgroundColor = visible ? '#1cb0f6' : '#94a3b8';
        drawTemplateGuide();
    }

    (function syncGuideToggleUI() {
        const checkbox = document.getElementById('guideToggle');
        const thumb    = document.getElementById('guideThumb');
        const track    = document.getElementById('guideTrack');
        if (!checkbox) return;
        checkbox.checked           = isGuideVisible;
        thumb.style.transform      = isGuideVisible ? 'translateX(24px)' : 'translateX(0)';
        track.style.backgroundColor = isGuideVisible ? '#1cb0f6' : '#94a3b8';
    })();

    function highlightWrongStrokes(wrongStrokeIndices) {
        redrawAllStrokes(); 

        const prevStyle    = ctx.strokeStyle;
        const prevWidth    = ctx.lineWidth;
        const prevFont     = ctx.font;
        const prevFill     = ctx.fillStyle;
        const prevAlign    = ctx.textAlign;
        const prevBaseline = ctx.textBaseline;

        wrongStrokeIndices.forEach(strokeNum => {
            const stroke = allStrokes[strokeNum - 1];
            if (!stroke || stroke.length === 0) return;

            ctx.strokeStyle = 'rgba(239,68,68,0.75)';
            ctx.lineWidth = 18;
            ctx.beginPath();
            ctx.moveTo(stroke[0].x, stroke[0].y);
            for (let i = 1; i < stroke.length; i++) {
                ctx.lineTo(stroke[i].x, stroke[i].y);
            }
            ctx.stroke();

            const bx = stroke[0].x;
            const by = Math.max(stroke[0].y - 16, 12); 

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
        drawTemplateGuide();
        statusMsg.innerHTML = "<i class='fas fa-eraser mr-2 text-slate-400'></i> Canvas Bersih";
        statusMsg.className = "text-center text-sm font-bold text-slate-600 dark:text-slate-300 px-4 py-4 rounded-xl bg-slate-50 dark:bg-gray-700 border-2 border-slate-200 dark:border-gray-600 mt-4";
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
            statusMsg.innerHTML = "<i class='fas fa-undo mr-2 text-slate-500'></i> Goresan terakhir dihapus.";
            statusMsg.className = "text-center text-sm font-bold text-slate-500 dark:text-slate-400 px-4 py-4 rounded-xl bg-slate-50 dark:bg-gray-700 border-2 border-slate-200 dark:border-gray-600 mt-4";
        } else {
            statusMsg.innerHTML = "<i class='fas fa-exclamation-circle mr-2'></i> Kanvas sudah kosong.";
            statusMsg.className = "text-center text-sm font-bold text-amber-600 dark:text-amber-400 px-4 py-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 mt-4";
        }
    }

    window.DEBUG_MODE = true;

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

        const NUM_POINTS = 40;
        const TOLERANCE_ERROR = 42;

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

            const totalError = shapeError + (posError * 0.25);
            let strokePct = 100 - (totalError / TOLERANCE_ERROR) * 100;
            strokePct = Math.max(0, Math.min(100, strokePct)); 
            
            if (window.DEBUG_MODE) {
                console.log(`[DEBUG] Goresan ke-${i + 1} | Akurasi: ${strokePct.toFixed(2)}% (Bentuk: ${shapeError.toFixed(2)}, Posisi: ${posError.toFixed(2)})`);
            }

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
        
        if (userCount > templateCount) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Kelebihan ${userCount - templateCount} goresan!</span>`;
        }
        if (userCount < templateCount) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Kekurangan ${templateCount - userCount} goresan!</span>`;
        }
        if (wrongStrokes.length > 0) {
            msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">Cek lagi goresan ke: ${wrongStrokes.join(', ')}</span>`;
            highlightWrongStrokes(wrongStrokes);
        }

        if (overallPct >= 75 && userCount === templateCount && wrongStrokes.length === 0) {
            
            statusMsg.innerHTML = `<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 mr-2 mb-[-3px]"></div> Validating...`;
            statusMsg.className = "text-center text-sm font-bold text-indigo-600 dark:text-indigo-400 min-h-[24px] px-4 py-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 mt-4";
            
            autoSaveToDataset();

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
            const targetChar = document.getElementById('targetTitle').innerText.split(' ')[1];

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
                    
                    let chartHtml = `<div class="mt-4 pt-3 border-t border-slate-200 dark:border-gray-600 text-left">
                                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Analisis Probabilitas AI:</p>`;
                    
                    data.top_3.forEach((item, index) => {
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
            .catch(() => {
                statusMsg.innerHTML = `Urutan Benar (${overallPct.toFixed(1)}%)!<br><span class="text-xs font-normal text-rose-500">Gagal terhubung ke AI.</span>`;
            });

        } else if (overallPct >= 45 && userCount === templateCount) {
            statusMsg.innerHTML = `Hampir Benar!<br>${msg}`;
            statusMsg.className = "text-center text-sm font-bold text-amber-600 dark:text-amber-400 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 mt-4";
        } else {
            statusMsg.innerHTML = `Coba Perbaiki!<br>${msg}`;
            statusMsg.className = "text-center text-sm font-bold text-rose-600 dark:text-rose-400 min-h-[24px] px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 mt-4";
        }
    }
    
    function autoSaveToDataset() {
        try {
            const titleText = document.getElementById('targetTitle').innerText;
            const currentCharacter = titleText.split(' ')[1]; 
            
            if(!currentCharacter) return;

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

    function getBoundingBox(strokes) {
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
        strokes.forEach(stroke => {
            stroke.forEach(p => {
                if (p.x < minX) minX = p.x; if (p.y < minY) minY = p.y;
                if (p.x > maxX) maxX = p.x; if (p.y > maxY) maxY = p.y;
            });
        });
        return { minX, minY, maxX, maxY, width: maxX - minX, height: maxY - minY };
    }

    function normalizeStrokes(strokes) {
        if (!strokes || strokes.length === 0) return [];
        const box = getBoundingBox(strokes);
        const maxDim = Math.max(box.width, box.height) || 1;
        const scale = 100 / maxDim; 

        // Gunakan bounding box center (stabil, tidak terpengaruh kecepatan gambar)
        // Centroid (rata-rata titik) tidak stabil karena titik lebih padat di area lambat
        const cx = (box.minX + box.maxX) / 2;
        const cy = (box.minY + box.maxY) / 2;

        return strokes.map(stroke => stroke.map(p => ({
            x: (p.x - cx) * scale,
            y: (p.y - cy) * scale
        })));
    }

    function getDistance(p1, p2) { return Math.hypot(p1.x - p2.x, p1.y - p2.y); }
    
    function pathLength(points) { 
        let d = 0; 
        for (let i = 1; i < points.length; i++) {
            d += getDistance(points[i - 1], points[i]); 
        }
        return d; 
    }

    function resample(points, n) {
        if (!points || points.length === 0) return points;
        let I = pathLength(points) / (n - 1);
        let D = 0; 
        let newPoints = [points[0]];
        for (let i = 1; i < points.length; i++) {
            let d = getDistance(points[i - 1], points[i]);
            if ((D + d) >= I) {
                let qx = points[i - 1].x + ((I - D) / d) * (points[i].x - points[i - 1].x);
                let qy = points[i - 1].y + ((I - D) / d) * (points[i].y - points[i - 1].y);
                let q = {x: qx, y: qy};
                newPoints.push(q); 
                points.splice(i, 0, q); 
                D = 0;
            } else { 
                D += d; 
            }
        }
        while (newPoints.length < n) { 
            newPoints.push(points[points.length - 1]); 
        }
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
        
        const userBox = getBoundingBox(allStrokes);
        const maxDim = Math.max(userBox.width, userBox.height) || 1;
        // Gunakan bounding box center (konsisten dengan normalizeStrokes)
        const cx = (userBox.minX + userBox.maxX) / 2;
        const cy = (userBox.minY + userBox.maxY) / 2;

        ctx.strokeStyle = '#10b981'; 
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 6]); 
        ctx.strokeRect(userBox.minX, userBox.minY, userBox.width, userBox.height);

        ctx.fillStyle = '#10b981';
        ctx.beginPath();
        ctx.arc(cx, cy, 6, 0, Math.PI * 2);
        ctx.fill();
        ctx.font = "bold 12px sans-serif";
        ctx.fillText("Center", cx + 10, cy - 10);

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

        if (templateKanji && templateKanji.length > 0) {
            const normTemp = normalizeStrokes(templateKanji);

            const mappedTemp = normTemp.map(stroke => stroke.map(p => ({
                x: p.x * (maxDim / 100) + cx,
                y: p.y * (maxDim / 100) + cy
            })));

            ctx.fillStyle = '#3b82f6'; 
            ctx.font = "bold 10px sans-serif";
            
            mappedTemp.forEach((stroke) => {
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
        
        ctx.fillStyle = previousFillStyle;
        ctx.strokeStyle = previousStrokeStyle;
        ctx.lineWidth = previousLineWidth;
        ctx.font = previousFont;
        ctx.setLineDash(previousLineDash);
    };

    document.addEventListener('keydown', function(e) {
        if (window.DEBUG_MODE && e.shiftKey && e.key.toLowerCase() === 'd') {
            e.preventDefault(); 
            isDebugVisible = !isDebugVisible; 
            if (isDebugVisible) {
                window.drawDebugPoints();
            } else {
                redrawAllStrokes(); 
            }
        }
    });
</script>
@endsection