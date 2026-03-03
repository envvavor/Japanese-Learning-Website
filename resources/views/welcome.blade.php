@extends('layouts.app')

@section('title', 'Belajar Kanji')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">
                Belajar {{ $category ? ucfirst($category) : 'Huruf' }}
            </h1>
            <p class="text-base text-slate-500 mt-1">
                Pilih karakter untuk mulai latihan penulisan.
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div id="menuArea" class="transition-all duration-500">
        <div id="kanjiGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <div class="col-span-full text-center py-10">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-4"></div>
                <p class="text-slate-400 font-medium">Memuat data karakter...</p>
            </div>
        </div>
    </div>

    <div id="practiceArea" class="hidden mt-6 bg-white border border-slate-200 rounded-3xl shadow-2xl p-8 sm:p-10 max-w-2xl mx-auto relative overflow-hidden transition-all duration-500">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <div class="flex justify-between items-center mb-8 mt-2">
            <h2 id="targetTitle" class="text-2xl font-bold text-slate-800">
                Latihan
            </h2>

            <button onclick="backToMenu()" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-rose-600 transition-colors bg-slate-50 hover:bg-rose-50 px-4 py-2 rounded-xl">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tutup
            </button>
        </div>

        <p class="text-sm text-slate-500 mb-8 text-center bg-slate-50 p-3 rounded-lg border border-slate-100">
            Ikuti urutan dan arah goresan sesuai standar penulisan Jepang.
        </p>

       <div class="flex justify-center mb-8">
            <div class="relative bg-white border-4 border-slate-700 rounded-lg shadow-inner overflow-hidden w-full max-w-[320px] aspect-square">
                <div class="absolute pointer-events-none border-l-2 border-dashed border-red-300 h-full left-1/2 opacity-60"></div>
                <div class="absolute pointer-events-none border-t-2 border-dashed border-red-300 w-full top-1/2 opacity-60"></div>
                
                <canvas id="kanjiCanvas"
                        width="320"
                        height="320"
                        class="block w-full h-full touch-none relative z-10 cursor-crosshair">
                </canvas>
            </div>
        </div>

        <div class="flex justify-center gap-4 mb-6">
            <button onclick="clearCanvas()" class="flex-1 sm:flex-none sm:w-32 px-5 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all">
                Reset
            </button>

            <button onclick="validateStroke()" class="flex-1 sm:flex-none sm:w-40 px-5 py-3 text-sm font-bold rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                Periksa Tulisan
            </button>
        </div>

        <div id="statusMsg" class="text-center text-sm font-bold text-slate-600 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
            Pilih karakter untuk memulai.
        </div>

    </div>

</div>

<script>
        // --- 1. INISIALISASI VARIABEL GLOBAL ---
        let templateKanji = []; 
        let currentStroke = [];
        let allStrokes = [];
        let isDrawing = false;
        
        const canvas = document.getElementById('kanjiCanvas');
        const ctx = canvas.getContext('2d');
        const statusMsg = document.getElementById('statusMsg');

        ctx.lineWidth = 14;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#2c3e50';

        // --- 2. FUNGSI LOAD DATA KARTU DARI API ---
        // category passed from blade
        const currentCategory = "{{ $category ?? '' }}";

        async function loadKanjiList() {
            try {
                let url = '/api/kanjis';
                if (currentCategory) {
                    url += '?category=' + encodeURIComponent(currentCategory);
                }

                const response = await fetch(url);
                const kanjis = await response.json();
                const grid = document.getElementById('kanjiGrid');

                if (kanjis.length === 0) {
                    grid.innerHTML = `<p style="text-align:center; grid-column: 1 / -1;">Data Kanji kosong. Silakan tambahkan data melalui <a href="/admin">Halaman Admin</a>.</p>`;
                    return;
                }

                let htmlContent = '';
                kanjis.forEach(k => {
                    htmlContent += `
                        <div class="group bg-white border border-slate-200 rounded-xl p-6 text-center 
                                    hover:shadow-md hover:border-slate-300 transition cursor-pointer"
                            onclick="window.location='/kanji/${k.character}'">

                            <div class="text-4xl font-semibold text-slate-800 mb-2 
                                        group-hover:scale-105 transition">
                                ${k.character}
                            </div>

                            <div class="text-xs uppercase tracking-wide text-slate-400">
                                ${k.meaning}
                            </div>
                        </div>
                    `;
                });
                grid.innerHTML = htmlContent;
            } catch (error) {
                console.error("Gagal load data:", error);
                document.getElementById('kanjiGrid').innerHTML = "<p style='color:red;'>Gagal terhubung ke database.</p>";
            }
        }

        loadKanjiList(); // Panggil saat halaman dibuka

        // jika URL memiliki parameter practice, jalankan latihan otomatis
        const params = new URLSearchParams(window.location.search);
        if (params.has('practice')) {
            const char = params.get('practice');
            if (char) {
                startPractice(char);
            }
        }

        // --- 3. FUNGSI MEMULAI LATIHAN ---
        async function startPractice(char) {
            try {
                // Ambil koordinat template untuk Kanji yang dipilih
                statusMsg.innerText = "Memuat template...";
                const response = await fetch(`/api/kanjis/${char}`);
                const data = await response.json();

                if (response.ok && data.strokes) {
                    templateKanji = data.strokes; 
                    
                    document.getElementById('targetTitle').innerText = `Latihan: ${data.character} (${data.meaning})`;
                    document.getElementById('menuArea').style.display = 'none';
                    document.getElementById('practiceArea').style.display = 'block';
                    
                    clearCanvas();
                    statusMsg.innerText = "Silakan mulai menulis.";
                    statusMsg.style.color = "#333";
                } else {
                    alert("Gagal memuat template dari database.");
                }
            } catch (error) {
                alert("Error koneksi ke server.");
            }
        }

        function backToMenu() {
            document.getElementById('menuArea').style.display = 'block';
            document.getElementById('practiceArea').style.display = 'none';
        }

        // --- 4. LOGIKA MENGGAMBAR DI CANVAS ---
       function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            
            // Hitung skala (ukuran asli canvas dibagi ukuran tampilan di layar)
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            // Ambil posisi jari/mouse
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;

            // Kalikan koordinat dengan skala agar presisi
            return { 
                x: (clientX - rect.left) * scaleX, 
                y: (clientY - rect.top) * scaleY 
            };
        }

        function startDrawing(e) {
            e.preventDefault(); isDrawing = true; currentStroke = [];
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

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            allStrokes = [];
            statusMsg.innerText = "Canvas Bersih";
            statusMsg.style.color = "#333";
        }

        // --- 5. LOGIKA VALIDASI STROKE ORDER ---
        function validateStroke() {
            if (allStrokes.length === 0) {
                statusMsg.innerText = "⚠️ Tulis hurufnya dulu!";
                statusMsg.style.color = "#e67e22"; return;
            }

            // A. Cek Jumlah Goresan
            if (allStrokes.length !== templateKanji.length) {
                statusMsg.innerText = `❌ SALAH! Jumlah goresan tidak pas. (Kamu: ${allStrokes.length}, Target: ${templateKanji.length})`;
                statusMsg.style.color = "#e74c3c"; return;
            }

            // B. Cek Arah & Bentuk per Goresan (Resampling ke 30 titik agar setara)
            const NUM_POINTS = 30; 
            const TOLERANCE = 40; // Batas toleransi error pixel

            for (let i = 0; i < templateKanji.length; i++) {
                const userPts = resample(allStrokes[i], NUM_POINTS);
                const tempPts = resample(templateKanji[i], NUM_POINTS);

                let totalError = 0;
                for (let j = 0; j < NUM_POINTS; j++) {
                    totalError += getDistance(userPts[j], tempPts[j]);
                }
                let avgError = totalError / NUM_POINTS;

                if (avgError > TOLERANCE) {
                    statusMsg.innerText = `❌ SALAH di Goresan ke-${i+1}! (Arah/Bentuk salah)`;
                    statusMsg.style.color = "#e74c3c"; return;
                }
            }

            statusMsg.innerText = "✅ BENAR! Tulisan Sempurna!";
            statusMsg.style.color = "#27ae60";
        }

        // --- 5. LOGIKA VALIDASI STROKE ORDER WITH POINT ---
        // function validateStroke() {
        //     if (allStrokes.length === 0) {
        //         statusMsg.innerText = "⚠️ Tulis hurufnya dulu!";
        //         statusMsg.style.color = "#e67e22";
        //         return;
        //     }

        //     // Parameter untuk evaluasi bentuk/goresan
        //     const NUM_POINTS = 30;
        //     const TOLERANCE = 35; // batas toleransi error pixel

        //     const templateCount = templateKanji.length;
        //     const userCount = allStrokes.length;
        //     const matchedCount = Math.min(templateCount, userCount);

        //     let totalScore = 0; // kumulatif skor per goresan

        //     for (let i = 0; i < matchedCount; i++) {
        //         const userPts = resample(allStrokes[i], NUM_POINTS);
        //         const tempPts = resample(templateKanji[i], NUM_POINTS);

        //         let totalError = 0;
        //         for (let j = 0; j < NUM_POINTS; j++) {
        //             totalError += getDistance(userPts[j], tempPts[j]);
        //         }
        //         const avgError = totalError / NUM_POINTS;

        //         // hitung persentase untuk goresan ini (0‑100)
        //         const strokePct = Math.max(0, 100 - (avgError / TOLERANCE) * 100);
        //         totalScore += strokePct;
        //     }

        //     // jika jumlah goresan tidak sama, sisa goresan dihitung 0 (penalti otomatis)
        //     const overallPct = totalScore / templateCount;
        //     let msg = `Skor: ${overallPct.toFixed(1)}%`;

        //     // tambahkan informasi jumlah goresan jika berbeda
        //     if (userCount !== templateCount) {
        //         msg += ` (Kamu: ${userCount}, Target: ${templateCount})`;
        //     }

        //     if (overallPct >= 100) {
        //         statusMsg.innerText = "✅ BENAR! Tulisan Sempurna!";
        //         statusMsg.style.color = "#27ae60";
        //     } else if (overallPct >= 70) {
        //         statusMsg.innerText = msg;
        //         statusMsg.style.color = "#f39c12"; // oranye ketika mendekati benar
        //     } else {
        //         statusMsg.innerText = msg;
        //         statusMsg.style.color = "#e74c3c"; // merah untuk skor rendah
        //     }
        // }

        // --- FUNGSI MATEMATIKA UNTUK VALIDASI ---
        function getDistance(p1, p2) {
            return Math.hypot(p1.x - p2.x, p1.y - p2.y);
        }

        function pathLength(points) {
            let d = 0;
            for (let i = 1; i < points.length; i++) {
                d += getDistance(points[i - 1], points[i]);
            }
            return d;
        }

        function resample(points, n) {
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
    </script>
@endsection