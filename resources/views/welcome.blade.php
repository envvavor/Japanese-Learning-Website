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

        <div class="flex flex-wrap justify-center gap-3 mb-6">
            <button onclick="clearCanvas()" class="flex-1 sm:flex-none sm:w-28 px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all">
                Reset
            </button>

            <button onclick="undoStroke()" class="flex-1 sm:flex-none sm:w-28 px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                Undo
            </button>

            <button onclick="validateStroke()" class="w-full sm:w-auto sm:flex-none px-6 py-3 text-sm font-bold rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all">
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

        // --- FUNGSI MENGGAMBAR ULANG (DIBUTUHKAN UNTUK UNDO) ---
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

        // --- FUNGSI UNDO (HAPUS GORESAN TERAKHIR) ---
        function undoStroke() {
            if (allStrokes.length > 0) {
                allStrokes.pop(); // Buang data goresan paling akhir dari array
                redrawAllStrokes(); // Gambar ulang sisanya
                
                statusMsg.innerHTML = "Goresan terakhir dihapus.";
                statusMsg.className = "text-center text-sm font-bold text-slate-500 min-h-[24px] px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 mt-4";
            } else {
                statusMsg.innerHTML = "Kanvas sudah kosong.";
                statusMsg.className = "text-center text-sm font-bold text-amber-500 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 border border-amber-100 mt-4";
            }
        }

        // --- 5. LOGIKA VALIDASI STROKE ORDER (AKURASI MAKSIMAL) ---
        // --- 5. LOGIKA VALIDASI STROKE ORDER (DENGAN DETEKSI GORESAN SPESIFIK) ---
        function validateStroke() {
            if (allStrokes.length === 0) {
                statusMsg.innerHTML = "⚠️ Tulis hurufnya dulu!";
                statusMsg.className = "text-center text-sm font-bold text-amber-600 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 border border-amber-200";
                return;
            }

            if (!templateKanji || templateKanji.length === 0) {
                statusMsg.innerHTML = "⚠️ Data template belum siap!";
                return;
            }

            const templateCount = templateKanji.length;
            const userCount = allStrokes.length;

            const normUser = normalizeStrokes(allStrokes);
            const normTemp = normalizeStrokes(templateKanji);

            const NUM_POINTS = 30; 
            const TOLERANCE_ERROR = 40; 

            const matchedCount = Math.min(templateCount, userCount);
            let totalScore = 0;
            
            // Array untuk menyimpan nomor goresan yang salah
            let wrongStrokes = []; 

            // Evaluasi Per-Goresan
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
                
                totalScore += strokePct;

                // JIKA NILAI GORESAN INI DI BAWAH 65%, KITA CATAT SEBAGAI "SALAH"
                if (strokePct < 65) {
                    wrongStrokes.push(i + 1); // i + 1 karena urutan manusia mulai dari 1
                }
            }

            // Jika user menggambar kurang dari target, catat goresan sisanya sebagai "salah/kurang"
            if (userCount < templateCount) {
                for (let k = userCount + 1; k <= templateCount; k++) {
                    wrongStrokes.push(k);
                }
            }

            const overallPct = totalScore / templateCount; 
            
            let msg = `Akurasi: ${overallPct.toFixed(1)}%`;
            
            // Tambahkan info jika kelebihan goresan
            if (userCount > templateCount) {
                msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">⚠️ Kelebihan ${userCount - templateCount} goresan!</span>`;
            }

            // Tampilkan nomor goresan yang salah jika ada
            if (wrongStrokes.length > 0) {
                msg += `<br><span class="text-xs font-bold text-rose-600 mt-1 block">❌ Cek lagi goresan ke: ${wrongStrokes.join(', ')}</span>`;
            }

            // Tampilkan Hasil Akhir
            if (overallPct >= 75 && userCount === templateCount && wrongStrokes.length === 0) {
                statusMsg.innerHTML = `✅ Bagus Sekali!<br>${msg}`;
                statusMsg.className = "text-center text-sm font-bold text-emerald-600 min-h-[24px] px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 mt-4";
            } else if (overallPct >= 45 && userCount === templateCount) {
                statusMsg.innerHTML = `⚠️ Hampir Benar!<br>${msg}`;
                statusMsg.className = "text-center text-sm font-bold text-amber-600 min-h-[24px] px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 mt-4";
            } else {
                statusMsg.innerHTML = `❌ Coba Perbaiki!<br>${msg}`;
                statusMsg.className = "text-center text-sm font-bold text-rose-600 min-h-[24px] px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 mt-4";
            }
        }

        // --- FUNGSI MATEMATIKA (JANGAN DIHAPUS) ---
        function getBoundingBox(strokes) {
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            strokes.forEach(stroke => {
                stroke.forEach(p => {
                    if (p.x < minX) minX = p.x;
                    if (p.y < minY) minY = p.y;
                    if (p.x > maxX) maxX = p.x;
                    if (p.y > maxY) maxY = p.y;
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
    </script>
@endsection