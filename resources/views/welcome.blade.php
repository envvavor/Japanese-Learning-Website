<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belajar Kanji JLPT N5</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        h1, h2 { text-align: center; color: #2c3e50; }
        
        /* DESAIN KARTU KANJI */
        .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; max-width: 900px; margin: 0 auto; }
        .kanji-card { background: white; border-radius: 12px; padding: 25px 15px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 2px solid transparent; }
        .kanji-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); border-color: #3498db; }
        .kanji-char { font-size: 50px; font-weight: bold; color: #2980b9; margin-bottom: 5px; }
        .kanji-meaning { font-size: 16px; color: #7f8c8d; margin-bottom: 15px; }
        
        /* TOMBOL UMUM */
        .btn { padding: 10px 20px; border: none; border-radius: 20px; font-weight: bold; cursor: pointer; color: white; transition: 0.2s; font-size: 14px; }
        .btn:hover { opacity: 0.8; }
        .btn-start { background: #3498db; width: 100%; }
        .btn-back { background: #95a5a6; margin-bottom: 20px; display: inline-block; }
        .btn-clear { background: #e74c3c; }
        .btn-check { background: #2ecc71; }

        /* AREA LATIHAN & CANVAS */
        #practiceArea { display: none; text-align: center; max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .canvas-wrapper { position: relative; margin: 20px auto; border: 2px solid #2c3e50; background: #fff; cursor: crosshair; display: inline-block; }
        canvas { display: block; touch-action: none; }
        
        /* Grid Canvas */
        .grid { position: absolute; pointer-events: none; background: rgba(231, 76, 60, 0.3); }
        .grid.v { width: 1px; height: 100%; left: 50%; top: 0; }
        .grid.h { height: 1px; width: 100%; top: 50%; left: 0; }

        #statusMsg { font-size: 18px; font-weight: bold; margin-top: 15px; min-height: 25px; }
    </style>
</head>
<body>

    <div id="menuArea">
        <h1>📚 Daftar Kanji N5</h1>
        <p style="text-align:center; color:#7f8c8d; margin-bottom:30px;">Pilih huruf kanji di bawah ini untuk mulai berlatih.</p>
        
        <div class="grid-container" id="kanjiGrid">
            <p style="text-align:center; grid-column: 1 / -1;">⏳ Memuat data dari server...</p>
        </div>
    </div>

    <div id="practiceArea">
        <button class="btn btn-back" onclick="backToMenu()">🔙 Kembali ke Menu</button>
        
        <h2 id="targetTitle">Latihan: -</h2>
        <p style="color:#7f8c8d; margin-top:-10px;">Ikuti urutan dan arah goresan yang benar.</p>
        
        <div class="canvas-wrapper">
            <div class="grid v"></div>
            <div class="grid h"></div>
            <canvas id="kanjiCanvas" width="300" height="300"></canvas>
        </div>

        <div style="margin-top: 15px;">
            <button class="btn btn-clear" onclick="clearCanvas()">🗑️ Hapus Ulang</button>
            <button class="btn btn-check" onclick="validateStroke()">✅ Cek Jawaban</button>
        </div>
        
        <div id="statusMsg">Mode Latihan Dimulai</div>
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

        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#2c3e50';

        // --- 2. FUNGSI LOAD DATA KARTU DARI API ---
        async function loadKanjiList() {
            try {
                const response = await fetch('/api/kanjis');
                const kanjis = await response.json();
                const grid = document.getElementById('kanjiGrid');

                if (kanjis.length === 0) {
                    grid.innerHTML = `<p style="text-align:center; grid-column: 1 / -1;">Data Kanji kosong. Silakan tambahkan data melalui <a href="/admin">Halaman Admin</a>.</p>`;
                    return;
                }

                let htmlContent = '';
                kanjis.forEach(k => {
                    htmlContent += `
                        <div class="kanji-card" onclick="startPractice('${k.character}')">
                            <div class="kanji-char">${k.character}</div>
                            <div class="kanji-meaning">${k.meaning}</div>
                            <button class="btn btn-start">Latihan ✏️</button>
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
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
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
            const TOLERANCE = 35; // Batas toleransi error pixel

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

        // --- 6. FUNGSI MATEMATIKA UNTUK VALIDASI ---
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
</body>
</html>