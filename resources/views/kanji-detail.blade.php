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
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Detail Karakter
                    </h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Informasi dan cara penulisan karakter.</p>
                </div>
            </div>

            <a onclick="window.history.back()"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0 cursor-pointer">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Main Card Container --}}
        <div id="infoArea" class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-sm p-8 sm:p-10 relative overflow-hidden transition-all duration-300 min-h-[500px]">
            
            <div class="absolute top-0 left-0 w-full h-3 bg-[#1cb0f6]"></div>

            {{-- Skeleton Loading --}}
            <div id="skeletonLoading" class="animate-pulse w-full">
                <div class="flex flex-col items-center justify-center mb-10 mt-4 space-y-6">
                    <div class="w-32 h-32 bg-slate-200 dark:bg-gray-700 rounded-[2rem]"></div>
                    <div class="w-48 h-6 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
                    <div class="w-16 h-16 bg-slate-200 dark:bg-gray-700 rounded-2xl mt-2"></div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-2xl border-2 border-slate-100 dark:border-gray-600">
                    <div>
                        <div class="w-16 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div>
                        <div class="w-24 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div>
                    </div>
                    <div>
                        <div class="w-20 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div>
                        <div class="w-24 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div>
                    </div>
                    <div class="col-span-2">
                        <div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-3"></div>
                        <div class="w-64 h-6 bg-slate-200 dark:bg-gray-500 rounded-md"></div>
                    </div>
                </div>

                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
                        <div class="w-40 h-6 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start bg-white dark:bg-gray-800 border-2 border-slate-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
                            <div class="w-10 h-10 bg-slate-200 dark:bg-gray-700 rounded-xl mr-4 shrink-0"></div>
                            <div class="flex-1 space-y-3 mt-1">
                                <div class="w-3/4 h-5 bg-slate-200 dark:bg-gray-700 rounded-md"></div>
                                <div class="w-1/2 h-4 bg-slate-100 dark:bg-gray-600 rounded-md"></div>
                            </div>
                            <div class="w-12 h-12 bg-slate-200 dark:bg-gray-700 rounded-xl ml-4 shrink-0"></div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-center justify-center mb-10">
                    <div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded-md mb-5"></div>
                    <div class="w-48 h-48 bg-slate-200 dark:bg-gray-700 rounded-[2rem]"></div>
                </div>
            </div>

            {{-- Konten Asli --}}
            <div id="realContent" class="hidden fade-in">
                
                <div class="text-center mb-10 mt-2 relative">
                    <h1 id="character" class="text-[6rem] sm:text-[8rem] font-black text-slate-800 dark:text-white tracking-tight drop-shadow-sm leading-none"></h1>
                    <p id="meaning" class="mt-4 text-xl font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest"></p>
                    
                    <button onclick="window.speakText(window.currentKanjiChar)" 
                            class="mt-6 w-16 h-16 inline-flex items-center justify-center text-[#1cb0f6] bg-[#1cb0f6]/10 border-2 border-b-[6px] border-[#1cb0f6]/30 hover:bg-[#1cb0f6]/20 active:translate-y-1 active:border-b-2 rounded-2xl transition-all" 
                            title="Dengarkan Cara Baca">
                        <i class="fas fa-volume-up text-2xl"></i>
                    </button>
                </div>

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

                <div id="examplesSection" class="mb-12 hidden">
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-3">
                        <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-500 w-10 h-10 rounded-xl flex items-center justify-center border-2 border-b-4 border-amber-200 dark:border-amber-800 shrink-0">
                            <i class="fas fa-book-open"></i>
                        </div>
                        Contoh Kalimat
                    </h3>
                    <div id="examplesList" class="space-y-4"></div>
                </div>

                <div class="flex flex-col items-center justify-center mb-10">
                    <p class="uppercase tracking-widest text-xs font-black text-slate-400 dark:text-slate-500 mb-4"><i class="fas fa-play-circle mr-1"></i> Animasi Urutan Goresan</p>
                    <div class="relative bg-white dark:bg-gray-900 border-4 border-b-[8px] border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm w-56 h-56 flex items-center justify-center overflow-hidden">
                        <div class="absolute pointer-events-none border-l-2 border-dashed border-rose-200 dark:border-rose-900/50 h-full left-1/2 opacity-70"></div>
                        <div class="absolute pointer-events-none border-t-2 border-dashed border-rose-200 dark:border-rose-900/50 w-full top-1/2 opacity-70"></div>
                        <canvas id="playbackCanvas" width="300" height="300" class="block w-full h-full relative z-10"></canvas>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <button id="practiceBtn" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] text-white font-black uppercase tracking-widest hidden hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all shadow-sm">
                        <i class="fas fa-pencil-alt mr-2"></i> Latihan Menulis
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* CSS kecil untuk animasi kemunculan yang mulus */
    .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        window.currentKanjiChar = "";
        let animationTimeout; 
        let synth = null;

        try {
            if ('speechSynthesis' in window) {
                synth = window.speechSynthesis;
            }
        } catch (e) {
            console.warn("Speech Synthesis diblokir oleh browser.");
        }

        window.speakText = function(text) {
            if (!text) return;
            if (!synth) {
                alert("Browser Anda memblokir fitur suara.");
                return;
            }
            try {
                synth.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'ja-JP';
                synth.speak(utterance);
            } catch (error) {
                console.error("Gagal memainkan suara:", error);
            }
        };

        // --- FUNGSI ANIMASI GORESAN (dengan nomor urutan) ---
        function playStrokesAnimation(strokes) {
            const canvas = document.getElementById('playbackCanvas');
            if (!canvas || !strokes || strokes.length === 0) {
                showNoStrokeMessage();
                return;
            }
            
            const ctx = canvas.getContext('2d');
            
            const strokeColors = [
                '#6366f1', '#eab308', '#ef4444', '#10b981', '#f97316', '#a855f7', '#1e293b',
                '#f97316', '#a855f7', '#1e293b', '#6366f1', '#eab308', '#ef4444', '#10b981', 
                '#f97316', '#a855f7', '#1e293b', '#f97316', '#a855f7', '#1e293b'
            ];

            function drawCompletedStrokes(upToIndex) {
                strokes.forEach((stroke, i) => {
                    if (i >= upToIndex || !stroke || stroke.length === 0) return;
                    const color = strokeColors[i % strokeColors.length];

                    ctx.globalAlpha = 0.25;
                    ctx.lineWidth = 12;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    ctx.strokeStyle = color;
                    ctx.beginPath();
                    ctx.moveTo(stroke[0].x, stroke[0].y);
                    for (let p = 1; p < stroke.length; p++) {
                        ctx.lineTo(stroke[p].x, stroke[p].y);
                    }
                    ctx.stroke();
                    ctx.globalAlpha = 1.0;

                    drawStrokeBadge(ctx, stroke[0].x, stroke[0].y, i + 1, color, 0.35);
                });
            }

            function drawStrokeBadge(ctx, x, y, number, color, alpha) {
                const prevAlpha = ctx.globalAlpha;
                ctx.globalAlpha = alpha;

                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.arc(x, y, 9, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 10px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(number, x, y);

                ctx.globalAlpha = prevAlpha;
                ctx.textAlign = 'start';
                ctx.textBaseline = 'alphabetic';
            }

            let currentStrokeIndex = 0;
            let currentPointIndex = 0;

            function animate() {
                if (currentStrokeIndex >= strokes.length) {
                    animationTimeout = setTimeout(() => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        currentStrokeIndex = 0;
                        currentPointIndex = 0;
                        animate();
                    }, 2000);
                    return;
                }

                const stroke = strokes[currentStrokeIndex];
                if (!stroke || stroke.length === 0) {
                    currentStrokeIndex++;
                    setTimeout(animate, 0);
                    return;
                }

                const color = strokeColors[currentStrokeIndex % strokeColors.length];

                if (currentPointIndex === 0) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    drawCompletedStrokes(currentStrokeIndex);
                    drawStrokeBadge(ctx, stroke[0].x, stroke[0].y, currentStrokeIndex + 1, color, 1.0);

                    ctx.lineWidth = 12;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    ctx.strokeStyle = color;
                    ctx.globalAlpha = 1.0;
                    ctx.beginPath();
                    ctx.moveTo(stroke[0].x, stroke[0].y);
                    currentPointIndex++;

                } else if (currentPointIndex < stroke.length) {
                    ctx.lineTo(stroke[currentPointIndex].x, stroke[currentPointIndex].y);
                    ctx.stroke();
                    drawStrokeBadge(ctx, stroke[0].x, stroke[0].y, currentStrokeIndex + 1, color, 1.0);

                    ctx.beginPath();
                    ctx.moveTo(stroke[currentPointIndex].x, stroke[currentPointIndex].y);
                    currentPointIndex++;
                } else {
                    currentStrokeIndex++;
                    currentPointIndex = 0;
                }

                animationTimeout = setTimeout(animate, 15);
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            clearTimeout(animationTimeout);
            animate();
        }

        function showNoStrokeMessage() {
            const canvasBox = document.getElementById('playbackCanvas')?.parentElement;
            if (canvasBox) {
                canvasBox.innerHTML = '<span class="text-xs text-slate-400 font-bold p-4 text-center"><i class="fas fa-eye-slash text-2xl block mb-2"></i> Data goresan belum ditambahkan.</span>';
            }
        }

        // --- LOAD DATA DARI API ---
        async function loadDetail() {
            try {
                const char = `{!! $character !!}`; 
                const response = await fetch(`/api/kanjis/${encodeURIComponent(char)}`);
                
                if (!response.ok) {
                    throw new Error("Karakter tidak ditemukan");
                }
                
                const data = await response.json();
                
                // MENGHILANGKAN SKELETON & MENAMPILKAN KONTEN ASLI
                document.getElementById('skeletonLoading').classList.add('hidden');
                document.getElementById('realContent').classList.remove('hidden');

                window.currentKanjiChar = data.character;
                document.getElementById('character').innerText = data.character;
                document.getElementById('meaning').innerText = data.meaning;

                if (data.category && String(data.category).trim() !== "") {
                    document.getElementById('category').innerText = data.category;
                    document.getElementById('categoryWrapper').classList.remove('hidden');
                }

                if (data.level !== null && data.level !== undefined && String(data.level).trim() !== "") {
                    document.getElementById('level').innerText = data.level;
                    document.getElementById('levelWrapper').classList.remove('hidden');
                }

                let readings = [];
                if (data.kunyomi && String(data.kunyomi).trim() !== "") readings.push(`Kun: ${data.kunyomi}`);
                if (data.onyomi && String(data.onyomi).trim() !== "") readings.push(`On: ${data.onyomi}`);
                if (readings.length > 0) {
                    document.getElementById('readings').innerText = readings.join(' | ');
                    document.getElementById('readingsWrapper').classList.remove('hidden');
                }

                if (data.category || data.level || readings.length > 0) {
                    document.getElementById('infoGrid').classList.remove('hidden');
                }
                
                if (data.examples && data.examples.length > 0) {
                    const examplesSection = document.getElementById('examplesSection');
                    const examplesList = document.getElementById('examplesList');
                    
                    examplesSection.classList.remove('hidden');

                    let examplesHTML = '';
                    data.examples.forEach((ex, index) => {
                        const safeTextForTTS = ex.japanese_text.replace(/['"]/g, ''); 
                        let displayText = ex.japanese_text; 
                        
                        if (ex.furigana_html) {
                            displayText = ex.furigana_html.replace(/([\u4e00-\u9faf]+)\(([^)]+)\)/g, '<ruby>$1<rt>$2</rt></ruby>');
                        }
                        const number = index + 1;
                        
                        examplesHTML += `
                            <div class="flex items-start bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl p-5 mb-4 hover:border-[#1cb0f6]/50 transition-colors">
                                <div class="mr-4 mt-1 shrink-0">
                                    <div class="bg-[#1cb0f6] text-white w-10 h-10 flex items-center justify-center rounded-xl font-black text-sm border-2 border-b-4 border-[#1899d6]">
                                        ${number}
                                    </div>
                                </div>
                                <div class="flex-1 mr-4">
                                    <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-slate-100 mb-2 leading-relaxed">
                                        ${displayText}
                                    </p>
                                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                        ${ex.meaning}
                                    </p>
                                </div>
                                <button onclick="window.speakText('${safeTextForTTS}')" 
                                        class="w-12 h-12 rounded-xl border-2 border-b-[4px] border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-slate-500 hover:text-[#1cb0f6] hover:bg-slate-100 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all flex items-center justify-center shrink-0 mt-1" 
                                        title="Dengarkan Kalimat">
                                    <i class="fas fa-volume-up text-xl"></i>
                                </button>
                            </div>
                        `;
                    });
                    examplesList.innerHTML = examplesHTML;
                }

                if (data.strokes && data.strokes !== "null" && data.strokes !== "[]") {
                    let strokesArray = typeof data.strokes === 'string' ? JSON.parse(data.strokes) : data.strokes;
                    if (Array.isArray(strokesArray) && strokesArray.length > 0) {
                        playStrokesAnimation(strokesArray);
                    } else {
                        showNoStrokeMessage();
                    }
                } else {
                    showNoStrokeMessage();
                }

                const practiceBtn = document.getElementById('practiceBtn');
                practiceBtn.classList.remove('hidden');
                practiceBtn.addEventListener('click', () => {
                    // Redirect kembali ke halaman grid list untuk latihan
                    window.location = `/list?practice=${encodeURIComponent(data.character)}`;
                });

            } catch (e) {
                console.error("Error Detail:", e);
                document.getElementById('infoArea').innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-6xl text-rose-500 mb-4 animate-bounce"></i>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-widest mb-2">Oops! Terjadi Kesalahan</h2>
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Data karakter gagal dimuat. Buka Console (F12) untuk melihat detail error.</p>
                    </div>
                `;
            }
        }

        loadDetail();
    });
</script>
@endsection