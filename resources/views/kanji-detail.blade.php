@extends('layouts.app')

@section('title', 'Detail Kanji')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">

    <a onclick="window.history.back()"
       class="inline-flex items-center justify-center px-5 py-2.5 
              border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium 
              text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 
              hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-700 
              transition-all shadow-sm mb-8 cursor-pointer">
        
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
    </a>

    <div id="infoArea" class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl shadow-sm p-10 relative overflow-hidden transition-all duration-300 min-h-[500px]">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-blue-500"></div>

        <div id="skeletonLoading" class="animate-pulse w-full">
            
            <div class="flex flex-col items-center justify-center mb-10 mt-4 space-y-6">
                <div class="w-32 h-32 bg-slate-200 dark:bg-gray-700 rounded-3xl"></div>
                <div class="w-48 h-6 bg-slate-200 dark:bg-gray-700 rounded-lg"></div>
                <div class="w-14 h-14 bg-slate-200 dark:bg-gray-700 rounded-full mt-2"></div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-xl border border-slate-100 dark:border-gray-600">
                <div>
                    <div class="w-16 h-3 bg-slate-300 dark:bg-gray-600 rounded mb-2"></div>
                    <div class="w-24 h-5 bg-slate-200 dark:bg-gray-500 rounded"></div>
                </div>
                <div>
                    <div class="w-20 h-3 bg-slate-300 dark:bg-gray-600 rounded mb-2"></div>
                    <div class="w-24 h-5 bg-slate-200 dark:bg-gray-500 rounded"></div>
                </div>
                <div class="col-span-2">
                    <div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded mb-2"></div>
                    <div class="w-64 h-5 bg-slate-200 dark:bg-gray-500 rounded"></div>
                </div>
            </div>

            <div class="mb-12">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-slate-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="w-40 h-6 bg-slate-200 dark:bg-gray-700 rounded-md"></div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <div class="w-7 h-7 bg-slate-200 dark:bg-gray-700 rounded mr-4 shrink-0"></div>
                        <div class="flex-1 space-y-3 mt-1">
                            <div class="w-3/4 h-5 bg-slate-200 dark:bg-gray-700 rounded"></div>
                            <div class="w-1/2 h-4 bg-slate-100 dark:bg-gray-600 rounded"></div>
                        </div>
                        <div class="w-10 h-10 bg-slate-200 dark:bg-gray-700 rounded-full ml-4 shrink-0"></div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col items-center justify-center mb-10">
                <div class="w-40 h-3 bg-slate-300 dark:bg-gray-600 rounded mb-4"></div>
                <div class="w-48 h-48 bg-slate-200 dark:bg-gray-700 rounded-xl"></div>
            </div>

        </div>

        <div id="realContent" class="hidden fade-in">
            <div class="text-center mb-10 mt-2">
                <div class="flex justify-center items-center gap-4">
                    <h1 id="character" class="text-8xl font-bold text-slate-800 dark:text-white tracking-tight"></h1>
                </div>

                <p id="meaning" class="mt-4 text-xl font-medium text-slate-600 dark:text-slate-300 capitalize"></p>
                
                <button onclick="window.speakText(window.currentKanjiChar)" 
                        class="p-3 text-indigo-500 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-full transition-all hover:scale-110 active:scale-95 mt-2" 
                        title="Dengarkan Cara Baca">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                    </svg>
                </button>
            </div>

            <div id="infoGrid" class="grid grid-cols-2 gap-6 text-sm text-slate-500 dark:text-slate-400 mb-10 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-xl border border-slate-100 dark:border-gray-600 hidden">
                
                <div id="categoryWrapper" class="hidden">
                    <p class="uppercase tracking-wide text-xs font-bold text-slate-400 dark:text-slate-500 mb-1">Kategori</p>
                    <p id="category" class="text-slate-800 dark:text-slate-200 font-medium text-base">-</p>
                </div>
                
                <div id="levelWrapper" class="hidden">
                    <p class="uppercase tracking-wide text-xs font-bold text-slate-400 dark:text-slate-500 mb-1">Bab / Level</p>
                    <p id="level" class="text-slate-800 dark:text-slate-200 font-medium text-base">-</p>
                </div>
                
                <div id="readingsWrapper" class="col-span-2 hidden">
                    <p class="uppercase tracking-wide text-xs font-bold text-slate-400 dark:text-slate-500 mb-1">Cara Baca (Kunyomi / Onyomi)</p>
                    <p id="readings" class="text-slate-800 dark:text-slate-200 font-medium text-base">-</p>
                </div>
            </div>

            <div id="examplesSection" class="mb-12 hidden">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-3">
                    <span class="bg-rose-500 text-white p-1.5 rounded-lg shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"></path>
                        </svg>
                    </span>
                    Contoh Kalimat
                </h3>
                <div id="examplesList" class="space-y-4"></div>
            </div>

            <div class="flex flex-col items-center justify-center mb-10">
                <p class="uppercase tracking-wide text-xs font-bold text-slate-400 dark:text-slate-500 mb-3">Animasi Urutan Goresan</p>
                <div class="relative bg-white dark:bg-gray-900 border-2 border-slate-200 dark:border-slate-600 rounded-xl shadow-inner w-48 h-48 flex items-center justify-center overflow-hidden">
                    <div class="absolute pointer-events-none border-l border-dashed border-red-200 h-full left-1/2 opacity-70"></div>
                    <div class="absolute pointer-events-none border-t border-dashed border-red-200 w-full top-1/2 opacity-70"></div>
                    <canvas id="playbackCanvas" width="300" height="300" class="block w-full h-full relative z-10"></canvas>
                </div>
            </div>

            <div class="text-center">
                <button id="practiceBtn" class="px-8 py-3.5 rounded-xl bg-indigo-600 text-white font-bold hidden hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-200 dark:shadow-indigo-900/50">
                    Mulai Latihan Menulis
                </button>
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
                canvasBox.innerHTML = '<span class="text-xs text-slate-400 font-medium p-4 text-center">Data goresan belum ditambahkan.</span>';
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
                            <div class="flex items-start bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 border-l-[5px] border-l-rose-600 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="mr-4 mt-1 shrink-0">
                                    <div class="bg-rose-600 text-white w-7 h-7 flex items-center justify-center rounded text-sm font-bold shadow-sm">
                                        ${number}
                                    </div>
                                </div>
                                <div class="flex-1 mr-4">
                                    <p class="text-lg sm:text-xl font-medium text-slate-800 dark:text-slate-100 mb-2 leading-relaxed">
                                        ${displayText}
                                    </p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        ${ex.meaning}
                                    </p>
                                </div>
                                <button onclick="window.speakText('${safeTextForTTS}')" 
                                        class="w-10 h-10 rounded-full bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-gray-600 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center justify-center shrink-0 mt-1" 
                                        title="Dengarkan Kalimat">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                    </svg>
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
                    window.location = `/list?practice=${encodeURIComponent(data.character)}`;
                });

            } catch (e) {
                console.error("Error Detail:", e);
                document.getElementById('infoArea').innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-4xl text-rose-500 mb-4"></i>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Oops! Terjadi Kesalahan</h2>
                        <p class="text-slate-500 dark:text-slate-400">Data karakter gagal dimuat. Buka Console (F12) untuk melihat detail error.</p>
                    </div>
                `;
            }
        }

        loadDetail();
    });
</script>
@endsection