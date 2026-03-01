<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Perekam Template Kanji</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; display: flex; flex-direction: column; padding: 0; margin: 0; }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar h2 {
            margin: 0;
            font-size: 24px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info span {
            font-size: 14px;
            opacity: 0.9;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .main-content { padding: 20px; flex-grow: 1; }
        
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 600px; margin: 0 auto; }
        
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }

        .canvas-container { position: relative; margin: 20px auto; border: 2px solid #2c3e50; background: #fff; cursor: crosshair; display: inline-block; }
        canvas { display: block; touch-action: none; }
        
        /* Grid Panduan Kanji */
        .grid { position: absolute; pointer-events: none; background: rgba(231, 76, 60, 0.3); }
        .grid.v { width: 1px; height: 100%; left: 50%; top: 0; }
        .grid.h { height: 1px; width: 100%; top: 50%; left: 0; }

        .btn { padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; color: white; transition: 0.2s; margin: 5px; }
        .btn-clear { background: #e74c3c; }
        .btn-save { background: #27ae60; width: 100%; font-size: 16px; margin-top: 15px; }
        .btn:hover { opacity: 0.8; }

        #status { margin-top: 10px; font-weight: bold; color: #2980b9; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🔧 Admin Panel - Rekam Kanji</h2>
        <div class="user-info">
            <span>Welcome, <strong>{{ Auth::user()->name }}</strong> (Admin)</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Admin Panel: Rekam Kanji</h2>
            
            <div class="form-group">
                <label>Huruf Kanji Target</label>
                <input type="text" id="inputChar" placeholder="Contoh: 一, 二, 三, 水" autocomplete="off">
            </div>
            <div class="form-group">
                <label>Arti / Makna</label>
                <input type="text" id="inputMeaning" placeholder="Contoh: Satu, Dua, Tiga, Air" autocomplete="off">
            </div>

            <div class="canvas-container">
                <div class="grid v"></div>
                <div class="grid h"></div>
                <canvas id="kanjiCanvas" width="300" height="300"></canvas>
            </div>

        <div>
            <button class="btn btn-clear" onclick="clearCanvas()">Hapus Canvas</button>
        </div>

        <p id="status">Total Goresan: 0</p>

        <button class="btn btn-save" onclick="saveToDatabase()">SIMPAN KE DATABASE</button>
    </div>

    <script>
        const canvas = document.getElementById('kanjiCanvas');
        const ctx = canvas.getContext('2d');
        const statusText = document.getElementById('status');

        let isDrawing = false;
        let currentStroke = [];
        let allStrokes = [];

        // Setting alat tulis
        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#2c3e50';

        // 1. EVENT LISTENER MOUSE & TOUCH
        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            // Mendukung Mouse dan Touch Screen (HP)
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            currentStroke = [];
            const pos = getPos(e);
            currentStroke.push(pos);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            currentStroke.push(pos); // Rekam pergerakan titik koordinat
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function stopDrawing() {
            if (!isDrawing) return;
            isDrawing = false;
            if (currentStroke.length > 2) {
                allStrokes.push(currentStroke);
                statusText.innerText = `Total Goresan: ${allStrokes.length}`;
            }
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing, {passive: false});
        canvas.addEventListener('touchmove', draw, {passive: false});
        canvas.addEventListener('touchend', stopDrawing);

        // 2. FUNGSI HAPUS CANVAS
        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            allStrokes = [];
            statusText.innerText = `Total Goresan: 0`;
        }

        // 3. FUNGSI SIMPAN KE LARAVEL API
        async function saveToDatabase() {
            const char = document.getElementById('inputChar').value.trim();
            const meaning = document.getElementById('inputMeaning').value.trim();

            // Validasi sebelum dikirim
            if (!char || !meaning) {
                alert("⚠️ Harap isi Huruf Kanji dan Artinya!");
                return;
            }
            if (allStrokes.length === 0) {
                alert("⚠️ Canvas masih kosong! Silakan gambar kanjinya terlebih dahulu.");
                return;
            }

            // Siapkan Data
            const payload = {
                character: char,
                meaning: meaning,
                strokes: allStrokes
            };

            // Kirim ke Backend Laravel via AJAX (Fetch API)
            try {
                // Menggunakan relative URL (/api/kanjis) karena berjalan di domain yang sama
                const response = await fetch('/api/kanjis', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    alert("✅ Sukses: " + result.message);
                    clearCanvas();
                    document.getElementById('inputChar').value = '';
                    document.getElementById('inputMeaning').value = '';
                } else {
                    alert("❌ Gagal Menyimpan: Cek koneksi atau data.");
                    console.error(result);
                }
            } catch (error) {
                alert("❌ Error Server: Tidak dapat menghubungi Backend.");
                console.error('Error:', error);
            }
        }
    </script>
        </div>
    </div>
</body>
</html>