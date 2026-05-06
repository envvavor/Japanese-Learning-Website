@extends('layouts.app')

@section('title', 'Detail Karakter — Manabu')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20">

    <div class="max-w-3xl mx-auto px-6 py-12">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-[#1cb0f6]/10 dark:bg-[#1899d6]/20 text-[#1cb0f6] dark:text-[#1899d6] rounded-2xl flex items-center justify-center text-3xl border-2 border-b-4 border-[#1cb0f6]/20 shrink-0 shadow-sm">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">Detail Karakter</h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Informasi dan cara penulisan karakter.</p>
                </div>
            </div>
            <a onclick="window.history.back()"
               class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0 cursor-pointer">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Main Card --}}
        <div id="infoArea" class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-sm p-8 sm:p-10 relative overflow-hidden transition-all duration-300 min-h-[500px]">

            <div class="absolute top-0 left-0 w-full h-3 bg-[#1cb0f6]"></div>

            {{-- Skeleton --}}
            <div id="skeletonLoading" class="animate-pulse w-full">
                <div class="flex flex-col items-center justify-center mb-10 mt-4 space-y-6">
                    <div class="w-32 h-32 bg-slate-200 dark:bg-gray-700 rounded-[2rem]"></div>
                    <div class="w-48 h-6 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
                    <div class="w-16 h-16 bg-slate-200 dark:bg-gray-700 rounded-2xl mt-2"></div>
                </div>
                <div class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-2xl border-2 border-slate-100 dark:border-gray-600">
                    <div><div class="w-16 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div><div class="w-24 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div></div>
                    <div><div class="w-20 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div><div class="w-24 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div></div>
                    <div class="col-span-2"><div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div><div class="w-64 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div></div>
                </div>
                <div class="flex flex-col items-center justify-center mb-10">
                    <div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-5"></div>
                    <div class="w-48 h-48 bg-slate-200 dark:bg-gray-700 rounded-[2rem]"></div>
                </div>
            </div>

            {{-- Real Content --}}
            <div id="realContent" class="hidden fade-in">

                {{-- Karakter besar + speaker --}}
                <div class="text-center mb-10 mt-2">
                    <h1 id="character" class="text-[6rem] sm:text-[8rem] font-black text-slate-800 dark:text-white tracking-tight drop-shadow-sm leading-none"></h1>
                    <p id="meaning" class="mt-4 text-xl font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest"></p>
                    <button onclick="window.speakText(window.currentKanjiChar)"
                            class="mt-6 w-16 h-16 inline-flex items-center justify-center text-[#1cb0f6] bg-[#1cb0f6]/10 border-2 border-b-[6px] border-[#1cb0f6]/30 hover:bg-[#1cb0f6]/20 active:translate-y-1 active:border-b-2 rounded-2xl transition-all"
                            title="Dengarkan Cara Baca">
                        <i class="fas fa-volume-up text-2xl"></i>
                    </button>
                </div>

                {{-- Info Grid --}}
                <div id="infoGrid" class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-2xl border-2 border-slate-100 dark:border-gray-600 hidden">
                    <div id="categoryWrapper" class="hidden">
                        <p class="uppercase tracking-widest text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1">Kategori</p>
                        <p id="category" class="text-slate-800 dark:text-slate-200 font-bold text-lg capitalize">-</p>
                    </div>
                    <div id="levelWrapper" class="hidden">
                        <p class="uppercase tracking-widest text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1">Bab / Level</p>
                        <p id="level" class="text-slate-800 dark:text-slate-200 font-bold text-lg">-</p>
                    </div>
                    <div id="readingsWrapper" class="col-span-2 hidden">
                        <p class="uppercase tracking-widest text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1">Cara Baca (Kunyomi / Onyomi)</p>
                        <p id="readings" class="text-slate-800 dark:text-slate-200 font-bold text-lg">-</p>
                    </div>
                </div>

                {{-- Contoh Kalimat --}}
                <div id="examplesSection" class="mb-12 hidden">
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-3">
                        <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-500 w-10 h-10 rounded-xl flex items-center justify-center border-2 border-b-4 border-amber-200 dark:border-amber-800 shrink-0">
                            <i class="fas fa-book-open"></i>
                        </div>
                        Contoh Kalimat
                    </h3>
                    <div id="examplesList" class="space-y-4"></div>
                </div>

                {{-- ============================================================
                     KOSAKATA TERKAIT
                     Section ini HANYA muncul jika kategori kanji DAN ada vocab
                     ============================================================ --}}
                <div id="relatedVocabSection" class="mb-12 hidden">
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-3">
                        <div class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 w-10 h-10 rounded-xl flex items-center justify-center border-2 border-b-4 border-indigo-200 dark:border-indigo-800 shrink-0">
                            <i class="fas fa-book"></i>
                        </div>
                        Kosakata Terkait
                        <span id="vocabCount" class="ml-auto text-xs font-bold text-slate-400 normal-case tracking-normal border border-slate-200 dark:border-gray-600 px-2.5 py-1 rounded-lg"></span>
                    </h3>

                    {{-- Filter Level (muncul kalau lebih dari 1 level) --}}
                    <div id="vocabLevelFilter" class="flex flex-wrap gap-2 mb-4 hidden">
                        @php
                            $filterBtns = ['' => 'Semua', 'N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1'];
                        @endphp
                        @foreach($filterBtns as $lvl => $lbl)
                        <button onclick="filterVocabLevel('{{ $lvl }}')" data-lvl="{{ $lvl }}"
                                class="vocab-lvl-btn px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-xl border-2 border-b-[4px] transition-all active:border-b-2 active:translate-y-0.5
                                       {{ $lvl === '' ? 'border-slate-800 dark:border-slate-200 bg-slate-800 dark:bg-white text-white dark:text-slate-900' : 'border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-slate-500 dark:text-slate-400' }}">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Skeleton vocab --}}
                    <div id="vocabSkeleton" class="grid grid-cols-1 sm:grid-cols-2 gap-3 animate-pulse">
                        @for($i = 0; $i < 4; $i++)
                        <div class="h-20 bg-slate-100 dark:bg-gray-700 rounded-2xl"></div>
                        @endfor
                    </div>

                    {{-- Daftar vocab --}}
                    <div id="vocabList" class="hidden grid grid-cols-2 sm:grid-cols-2 gap-3"></div>

                    {{-- Load more --}}
                    <div id="vocabLoadMore" class="hidden mt-4 text-center">
                        <button onclick="loadMoreVocab()"
                                class="px-6 py-2.5 border-2 border-b-[5px] border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-600 dark:text-slate-300 font-black text-xs uppercase tracking-widest rounded-2xl hover:border-indigo-300 hover:text-indigo-600 active:border-b-2 active:translate-y-0.5 transition-all">
                            <i class="fas fa-chevron-down mr-2"></i> Tampilkan Lebih Banyak
                        </button>
                    </div>
                </div>

                {{-- Animasi Goresan --}}
                <div class="flex flex-col items-center justify-center mb-10">
                    <p class="uppercase tracking-widest text-xs font-black text-slate-400 dark:text-slate-500 mb-4">
                        <i class="fas fa-play-circle mr-1"></i> Animasi Urutan Goresan
                    </p>
                    <div class="relative bg-white dark:bg-gray-900 border-4 border-b-[8px] border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm w-56 h-56 flex items-center justify-center overflow-hidden">
                        <div class="absolute pointer-events-none border-l-2 border-dashed border-rose-200 dark:border-rose-900/50 h-full left-1/2 opacity-70"></div>
                        <div class="absolute pointer-events-none border-t-2 border-dashed border-rose-200 dark:border-rose-900/50 w-full top-1/2 opacity-70"></div>
                        <canvas id="playbackCanvas" width="300" height="300" class="block w-full h-full relative z-10"></canvas>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <button id="practiceBtn"
                            class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] text-white font-black uppercase tracking-widest hidden hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all shadow-sm">
                        <i class="fas fa-pencil-alt mr-2"></i> Latihan Menulis
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.fade-in { animation: fadeIn 0.4s ease-in-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
.vocab-card-enter { animation: vocabIn 0.2s ease both; }
@keyframes vocabIn { from { opacity:0; transform:scale(0.97); } to { opacity:1; transform:scale(1); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    window.currentKanjiChar = '';
    let animationTimeout;
    let synth = null;
    try { if ('speechSynthesis' in window) synth = window.speechSynthesis; } catch(e) {}

    // ── State kosakata ───────────────────────────────────────────
    let allVocab      = [];
    let filteredVocab = [];
    let shownCount    = 0;
    const PAGE_SIZE   = 10;
    const LEVEL_COLOR = {
        N1: 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
        N2: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',
        N3: 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
        N4: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
        N5: 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800',
    };

    // ── Speech ───────────────────────────────────────────────────
    window.speakText = function(text) {
        if (!text || !synth) return;
        try { synth.cancel(); const u = new SpeechSynthesisUtterance(text); u.lang = 'ja-JP'; synth.speak(u); }
        catch(e) { console.error(e); }
    };

    // ── Filter level kosakata ────────────────────────────────────
    window.filterVocabLevel = function(level) {
        filteredVocab = level ? allVocab.filter(v => v.jlpt_level === level) : allVocab;
        shownCount    = 0;
        document.getElementById('vocabList').innerHTML = '';

        // Update button style
        document.querySelectorAll('.vocab-lvl-btn').forEach(btn => {
            const active = btn.dataset.lvl === level;
            btn.className = btn.className
                .replace(/border-slate-800 dark:border-slate-200 bg-slate-800 dark:bg-white text-white dark:text-slate-900/g, '')
                .replace(/border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-slate-500 dark:text-slate-400/g, '')
                .trim();
            btn.className += active
                ? ' border-slate-800 dark:border-slate-200 bg-slate-800 dark:bg-white text-white dark:text-slate-900'
                : ' border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-slate-500 dark:text-slate-400';
        });

        updateVocabCount();
        renderVocabBatch();
    };

    function updateVocabCount() {
        const el = document.getElementById('vocabCount');
        if (el) el.textContent = filteredVocab.length + ' kata';
    }

    // ── Render kartu kosakata ────────────────────────────────────
    function renderVocabBatch() {
        const list  = document.getElementById('vocabList');
        const slice = filteredVocab.slice(shownCount, shownCount + PAGE_SIZE);
        shownCount += slice.length;

        if (slice.length === 0) {
            list.innerHTML = '<p class="col-span-2 text-center text-sm text-slate-400 py-6 font-bold">Tidak ada kata untuk level ini.</p>';
            document.getElementById('vocabLoadMore').classList.add('hidden');
            return;
        }

        slice.forEach((v, i) => {
            const badge      = LEVEL_COLOR[v.jlpt_level] ?? LEVEL_COLOR['N3'];
            const textToSpeak = (v.furigana || v.original).replace(/['"\\]/g, '');
            const copyTarget  = v.original.replace(/['"\\]/g, '');
            const levelLabel  = v.jlpt_level || '辞書';

            const card = document.createElement('div');
            card.className = 'vocab-card-enter bg-white dark:bg-gray-800 border-2 border-b-[5px] border-slate-200 dark:border-gray-700 rounded-2xl p-4 sm:p-5 hover:-translate-y-1 transition-all flex flex-col h-full group';
            card.style.animationDelay = `${i * 30}ms`;

            card.innerHTML = `
                <div class="flex items-start justify-between mb-3">
                    <span class="px-2 py-1 rounded-lg border-2 text-[9px] font-black uppercase tracking-widest ${badge}">${levelLabel}</span>
                    <div class="flex gap-2">
                        <button onclick="window.speakText('${textToSpeak}')"
                                title="Dengarkan"
                                class="w-8 h-8 rounded-xl border-2 border-b-[3px] border-slate-200 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-slate-400 hover:text-[#1cb0f6] hover:border-[#1cb0f6]/30 active:border-b-2 active:translate-y-0.5 transition-all flex items-center justify-center">
                            <i class="fas fa-volume-up text-xs"></i>
                        </button>
                    </div>
                </div>

                <p class="text-3xl font-black text-slate-800 dark:text-white mb-1 leading-tight tracking-wide break-words">${v.original}</p>

                ${v.furigana && v.furigana !== v.original
                    ? `<p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 bg-slate-50 dark:bg-gray-900/50 inline-block px-2 py-0.5 rounded-md border-2 border-dashed border-slate-200 dark:border-gray-700 self-start break-words">${v.furigana}</p>`
                    : ''}

                <p class="text-sm font-black text-slate-700 dark:text-slate-200 leading-snug mt-auto border-t-2 border-dashed border-slate-100 dark:border-gray-700 pt-2">
                    ${v.indonesian || v.english}
                </p>

                ${v.indonesian
                    ? `<p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-1 italic">${v.english}</p>`
                    : ''}`;

            list.appendChild(card);
        });
        document.getElementById('vocabLoadMore').classList.toggle('hidden', shownCount >= filteredVocab.length);
    }

    window.loadMoreVocab = function() { renderVocabBatch(); };

    // ── Load kosakata dari API ────────────────────────────────────
    async function loadRelatedVocab(character) {
        const section = document.getElementById('relatedVocabSection');

        try {
            // Encode URI component untuk handle karakter multibyte
            const url = `/api/kanjis/${encodeURIComponent(character)}/vocabulary`;

            const res  = await fetch(url);

            if (!res.ok) {
                console.warn('[Vocab] API error:', res.status, res.statusText);
                section.classList.add('hidden');
                return;
            }

            const data = await res.json();

            // Sembunyikan skeleton
            document.getElementById('vocabSkeleton').classList.add('hidden');
            document.getElementById('vocabList').classList.remove('hidden');

            if (!data || data.length === 0) {
                console.log('[Vocab] No related vocabulary found');
                section.classList.add('hidden');
                return;
            }

            // Tampilkan section
            section.classList.remove('hidden');
            allVocab      = data;
            filteredVocab = data;
            shownCount    = 0;

            // Tampilkan filter level kalau ada lebih dari 1 level
            const levels = [...new Set(data.map(v => v.jlpt_level))];
            if (levels.length > 1) {
                document.getElementById('vocabLevelFilter').classList.remove('hidden');
            }

            updateVocabCount();
            renderVocabBatch();

        } catch(e) {
            console.error('[Vocab] Exception:', e);
            section.classList.add('hidden');
        }
    }

    // ── Stroke animation ─────────────────────────────────────────
    function playStrokesAnimation(strokes) {
        const canvas = document.getElementById('playbackCanvas');
        if (!canvas || !strokes || !strokes.length) { showNoStrokeMessage(); return; }
        const ctx = canvas.getContext('2d');
        const colors = ['#6366f1','#eab308','#ef4444','#10b981','#f97316','#a855f7','#1e293b','#f97316','#a855f7','#1e293b'];

        function drawCompleted(upTo) {
            strokes.forEach((s, i) => {
                if (i >= upTo || !s?.length) return;
                const c = colors[i % colors.length];
                ctx.globalAlpha = 0.25; ctx.lineWidth = 12; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
                ctx.strokeStyle = c; ctx.beginPath(); ctx.moveTo(s[0].x, s[0].y);
                for (let p = 1; p < s.length; p++) ctx.lineTo(s[p].x, s[p].y);
                ctx.stroke(); ctx.globalAlpha = 1.0;
                drawBadge(s[0].x, s[0].y, i + 1, c, 0.35);
            });
        }

        function drawBadge(x, y, n, c, a) {
            const prev = ctx.globalAlpha; ctx.globalAlpha = a;
            ctx.fillStyle = c; ctx.beginPath(); ctx.arc(x, y, 9, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#fff'; ctx.font = 'bold 10px sans-serif';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillText(n, x, y);
            ctx.globalAlpha = prev; ctx.textAlign = 'start'; ctx.textBaseline = 'alphabetic';
        }

        let si = 0, pi = 0;
        function animate() {
            if (si >= strokes.length) {
                animationTimeout = setTimeout(() => { ctx.clearRect(0,0,canvas.width,canvas.height); si=0; pi=0; animate(); }, 2000);
                return;
            }
            const s = strokes[si]; const c = colors[si % colors.length];
            if (!s?.length) { si++; setTimeout(animate, 0); return; }
            if (pi === 0) {
                ctx.clearRect(0,0,canvas.width,canvas.height); drawCompleted(si);
                drawBadge(s[0].x, s[0].y, si+1, c, 1.0);
                ctx.lineWidth=12; ctx.lineCap='round'; ctx.lineJoin='round';
                ctx.strokeStyle=c; ctx.globalAlpha=1.0; ctx.beginPath(); ctx.moveTo(s[0].x, s[0].y);
                pi++;
            } else if (pi < s.length) {
                ctx.lineTo(s[pi].x, s[pi].y); ctx.stroke();
                drawBadge(s[0].x, s[0].y, si+1, c, 1.0);
                ctx.beginPath(); ctx.moveTo(s[pi].x, s[pi].y); pi++;
            } else { si++; pi=0; }
            animationTimeout = setTimeout(animate, 15);
        }
        ctx.clearRect(0,0,canvas.width,canvas.height); clearTimeout(animationTimeout); animate();
    }

    function showNoStrokeMessage() {
        const box = document.getElementById('playbackCanvas')?.parentElement;
        if (box) box.innerHTML = '<span class="text-xs text-slate-400 font-bold p-4 text-center"><i class="fas fa-eye-slash text-2xl block mb-2"></i> Data goresan belum ditambahkan.</span>';
    }

    // ── Main: load detail ────────────────────────────────────────
    async function loadDetail() {
        try {
            const char     = `{!! $character !!}`;
            const response = await fetch(`/api/kanjis/${encodeURIComponent(char)}`);
            if (!response.ok) throw new Error('Karakter tidak ditemukan');

            const data = await response.json();
            console.log('[Detail] category:', data.category); // debug

            document.getElementById('skeletonLoading').classList.add('hidden');
            document.getElementById('realContent').classList.remove('hidden');

            window.currentKanjiChar = data.character;
            document.getElementById('character').innerText = data.character;
            document.getElementById('meaning').innerText   = data.meaning;

            if (data.category?.trim()) {
                document.getElementById('category').innerText = data.category;
                document.getElementById('categoryWrapper').classList.remove('hidden');
            }
            if (String(data.level ?? '').trim()) {
                document.getElementById('level').innerText = data.level;
                document.getElementById('levelWrapper').classList.remove('hidden');
            }
            const readings = [];
            if (data.kunyomi?.trim()) readings.push(`Kun: ${data.kunyomi}`);
            if (data.onyomi?.trim())  readings.push(`On: ${data.onyomi}`);
            if (readings.length) {
                document.getElementById('readings').innerText = readings.join(' | ');
                document.getElementById('readingsWrapper').classList.remove('hidden');
            }
            if (data.category || data.level || readings.length) {
                document.getElementById('infoGrid').classList.remove('hidden');
            }

            // Contoh kalimat
            if (data.examples?.length) {
                document.getElementById('examplesSection').classList.remove('hidden');
                document.getElementById('examplesList').innerHTML = data.examples.map((ex, i) => {
                    const safe = ex.japanese_text.replace(/['"]/g, '');
                    let disp   = ex.japanese_text;
                    if (ex.furigana_html) disp = ex.furigana_html.replace(/([\u4e00-\u9faf]+)\(([^)]+)\)/g, '<ruby>$1<rt>$2</rt></ruby>');
                    return `<div class="flex items-start bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl p-5 hover:border-[#1cb0f6]/50 transition-colors">
                        <div class="mr-4 mt-1 shrink-0"><div class="bg-[#1cb0f6] text-white w-10 h-10 flex items-center justify-center rounded-xl font-black text-sm border-2 border-b-4 border-[#1899d6]">${i+1}</div></div>
                        <div class="flex-1 mr-4">
                            <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-slate-100 mb-2 leading-relaxed">${disp}</p>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">${ex.meaning}</p>
                        </div>
                        <button onclick="window.speakText('${safe}')" class="w-12 h-12 rounded-xl border-2 border-b-[4px] border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-slate-500 hover:text-[#1cb0f6] active:translate-y-1 active:border-b-2 transition-all flex items-center justify-center shrink-0 mt-1">
                            <i class="fas fa-volume-up text-xl"></i>
                        </button>
                    </div>`;
                }).join('');
            }

            // ↓ Kosakata terkait — cek kategori case-insensitive
            const isKanji = (data.category ?? '').toLowerCase() === 'kanji';
            console.log('[Detail] isKanji:', isKanji);
            if (isKanji) {
                loadRelatedVocab(data.character);
            }

            // Stroke
            if (data.strokes && data.strokes !== 'null' && data.strokes !== '[]') {
                const arr = typeof data.strokes === 'string' ? JSON.parse(data.strokes) : data.strokes;
                if (Array.isArray(arr) && arr.length) playStrokesAnimation(arr);
                else showNoStrokeMessage();
            } else {
                showNoStrokeMessage();
            }

            const practiceBtn = document.getElementById('practiceBtn');
            practiceBtn.classList.remove('hidden');
            practiceBtn.addEventListener('click', () => {
                window.location = `/list?practice=${encodeURIComponent(data.character)}`;
            });

        } catch(e) {
            console.error('Error Detail:', e);
            document.getElementById('infoArea').innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-6xl text-rose-500 mb-4 animate-bounce"></i>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-2">Oops!</h2>
                    <p class="text-sm font-bold text-slate-500">Data karakter gagal dimuat.</p>
                </div>`;
        }
    }

    loadDetail();
});
</script>
@endsection