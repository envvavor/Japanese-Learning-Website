@extends('layouts.app')

@section('title', 'Detail Kanji')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">

    <a onclick="window.history.back()"
       class="inline-flex items-center justify-center px-5 py-2.5 
              border border-slate-300 rounded-xl text-sm font-medium 
              text-slate-700 bg-white 
              hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-200 
              transition-all shadow-sm mb-8 cursor-pointer">
        
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
    </a>

    <div id="infoArea" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <div class="text-center mb-10 mt-2">
            <div class="flex justify-center items-center gap-4">
                <h1 id="character" class="text-8xl font-bold text-slate-800 tracking-tight"></h1>
            </div>

            <p id="meaning" class="mt-4 text-xl font-medium text-slate-600 capitalize"></p>
            
            <button onclick="speakText(currentKanjiChar)" 
                    class="p-3 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-full transition-all hover:scale-110 active:scale-95 mt-2" 
                    title="Dengarkan Cara Baca">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-6 text-sm text-slate-500 mb-10 bg-slate-50 p-6 rounded-xl border border-slate-100">
            <div>
                <p class="uppercase tracking-wide text-xs font-bold text-slate-400 mb-1">Kategori</p>
                <p id="category" class="text-slate-800 font-medium text-base">-</p>
            </div>
            <div>
                <p class="uppercase tracking-wide text-xs font-bold text-slate-400 mb-1">Level</p>
                <p id="level" class="text-slate-800 font-medium text-base">-</p>
            </div>
            <div class="col-span-2">
                <p class="uppercase tracking-wide text-xs font-bold text-slate-400 mb-1">Cara Baca (Kunyomi / Onyomi)</p>
                <p id="readings" class="text-slate-800 font-medium text-base">-</p>
            </div>
        </div>

        <div id="examplesSection" class="mb-12 hidden">
            <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                <span class="bg-rose-500 text-white p-1.5 rounded-lg shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"></path>
                    </svg>
                </span>
                Contoh Kalimat
            </h3>
            <div id="examplesList" class="space-y-4">
                </div>
        </div>

        <div class="flex flex-col items-center justify-center mb-10">
            <p class="uppercase tracking-wide text-xs font-bold text-slate-400 mb-3">Animasi Urutan Goresan</p>
            <div class="relative bg-white border-2 border-slate-200 rounded-xl shadow-inner w-48 h-48 flex items-center justify-center overflow-hidden">
                <div class="absolute pointer-events-none border-l border-dashed border-red-200 h-full left-1/2 opacity-70"></div>
                <div class="absolute pointer-events-none border-t border-dashed border-red-200 w-full top-1/2 opacity-70"></div>
                
                <canvas id="playbackCanvas" width="300" height="300" class="block w-full h-full relative z-10"></canvas>
            </div>
        </div>

        <div class="text-center">
            <button id="practiceBtn"
                class="px-8 py-3.5 rounded-xl bg-indigo-600 text-white font-bold
                       hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-200">
                Mulai Latihan Menulis
            </button>
        </div>

    </div>

</div>

<script>
    let currentKanjiChar = "";
    let animationTimeout; // Variabel global untuk timer animasi

    speechSynthesis.onvoiceschanged = () => {
        console.log(speechSynthesis.getVoices());
    };

    // Text To Speech
    function speakText(text) {
        if (!text) return;

        const synth = window.speechSynthesis;
        synth.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'ja-JP';
        utterance.rate = 1;

        const voices = synth.getVoices();

        const customVoice = voices.find(v =>
            v.name === "Microsoft Sayaka - Japanese (Japan)"
        );

        if (customVoice) {
            utterance.voice = customVoice;
        }

        synth.speak(utterance);
    }


    // --- 2. FUNGSI ANIMASI GORESAN (LIVE CANVAS PLAYBACK) ---
    function playStrokesAnimation(strokes) {
        const canvas = document.getElementById('playbackCanvas');
        if (!canvas || !strokes || strokes.length === 0) return;
        
        const ctx = canvas.getContext('2d');
        
        // Desain Kuas Tinta
        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#1e293b'; 

        let currentStrokeIndex = 0;
        let currentPointIndex = 0;

        function animate() {
            // Jika semua goresan sudah digambar
            if (currentStrokeIndex >= strokes.length) {
                // Jeda 2 detik, lalu bersihkan dan ulang lagi (Looping)
                animationTimeout = setTimeout(() => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    currentStrokeIndex = 0;
                    currentPointIndex = 0;
                    animate();
                }, 2000);
                return;
            }

            const stroke = strokes[currentStrokeIndex];

            // Abaikan array kosong jika ada
            if (!stroke || stroke.length === 0) {
                currentStrokeIndex++;
                animate();
                return;
            }

            // Gambar Titik ke Titik
            if (currentPointIndex === 0) {
                ctx.beginPath();
                ctx.moveTo(stroke[0].x, stroke[0].y);
                currentPointIndex++;
            } else if (currentPointIndex < stroke.length) {
                ctx.lineTo(stroke[currentPointIndex].x, stroke[currentPointIndex].y);
                ctx.stroke();
                currentPointIndex++;
            } else {
                // Goresan ini selesai, lanjut ke goresan berikutnya
                currentStrokeIndex++;
                currentPointIndex = 0;
            }

            // Kecepatan putar (ms). Semakin kecil semakin cepat garisnya terbentuk.
            animationTimeout = setTimeout(animate, 15); 
        }

        // Pastikan bersih sebelum mulai
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        clearTimeout(animationTimeout);
        animate();
    }

    // --- 3. LOAD DATA DARI API ---
    async function loadDetail() {
        try {
            const char = "{{ $character }}";
            const response = await fetch(`/api/kanjis/${char}`);
            const data = await response.json();
            
            if (response.ok) {
                currentKanjiChar = data.character;

                // Isi Text Info
                document.getElementById('character').innerText = data.character;
                document.getElementById('meaning').innerText = data.meaning;
                document.getElementById('category').innerText = data.category ? data.category : '-';
                document.getElementById('level').innerText = data.level ? data.level : '-';
                
                // Kunyomi & Onyomi
                let readings = [];
                if (data.kunyomi) readings.push(`Kun: ${data.kunyomi}`);
                if (data.onyomi) readings.push(`On: ${data.onyomi}`);
                document.getElementById('readings').innerText = readings.length > 0 ? readings.join(' | ') : '-';
                
                // Render Contoh Kalimat
                if (data.examples && data.examples.length > 0) {
                    const examplesSection = document.getElementById('examplesSection');
                    const examplesList = document.getElementById('examplesList');
                    
                    examplesSection.classList.remove('hidden');

                    let examplesHTML = '';
                    data.examples.forEach((ex, index) => {
                        const safeTextForTTS = ex.japanese_text.replace(/'/g, "\\'"); 
                        const displayText = ex.furigana_html ? ex.furigana_html : ex.japanese_text;
                        const number = index + 1;
                        
                        examplesHTML += `
                            <div class="flex items-start bg-white border border-slate-200 border-l-[5px] border-l-rose-600 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="mr-4 mt-1 shrink-0">
                                    <div class="bg-rose-600 text-white w-7 h-7 flex items-center justify-center rounded text-sm font-bold shadow-sm">
                                        ${number}
                                    </div>
                                </div>
                                <div class="flex-1 mr-4">
                                    <p class="text-lg sm:text-xl font-medium text-slate-800 mb-2 leading-relaxed">
                                        ${displayText}
                                    </p>
                                    <p class="text-sm text-slate-500">
                                        ${ex.meaning}
                                    </p>
                                </div>
                                <button onclick="speakText('${safeTextForTTS}')" 
                                        class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-indigo-600 transition-colors flex items-center justify-center shrink-0 mt-1" 
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

                // Render Animasi Canvas
                if (data.strokes && data.strokes !== "null" && data.strokes !== "[]") {
                    // Terkadang database me-return JSON string, terkadang array. Kita amankan keduanya.
                    let strokesArray = typeof data.strokes === 'string' ? JSON.parse(data.strokes) : data.strokes;
                    
                    if (strokesArray.length > 0) {
                        playStrokesAnimation(strokesArray);
                    } else {
                        showNoStrokeMessage();
                    }
                } else {
                    showNoStrokeMessage();
                }

                // Aksi Tombol Latihan
                document.getElementById('practiceBtn').addEventListener('click', () => {
                    window.location = `/list?practice=${data.character}`;
                });

            } else {
                document.getElementById('infoArea').innerHTML = '<p class="text-rose-500 font-medium text-center py-10">Data karakter tidak ditemukan.</p>';
            }
        } catch (e) {
            console.error(e);
            document.getElementById('infoArea').innerHTML = '<p class="text-rose-500 font-medium text-center py-10">Gagal terhubung ke server.</p>';
        }
    }

    // Helper untuk menampilkan teks jika stroke order belum dibuat oleh Admin
    function showNoStrokeMessage() {
        const canvasBox = document.getElementById('playbackCanvas').parentElement;
        canvasBox.innerHTML = '<span class="text-xs text-slate-400 font-medium p-4 text-center">Data goresan belum ditambahkan.</span>';
    }

    // Jalankan saat halaman dibuka
    loadDetail();
</script>
@endsection