@extends('layouts.app')

@section('title','Admin')

@section('content')
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-4 flex justify-between items-center shadow-md">
        <h2>🔧 Admin Panel - Rekam Kanji</h2>
        <div class="flex items-center space-x-4">
            <span>Welcome, <strong>{{ Auth::user()->name }}</strong> (Admin)</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white py-1 px-3 rounded">Logout</button>
            </form>
        </div>
    </div>

    <div class="p-6">
        <div class="bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto text-center">
            <h2 class="text-2xl font-semibold mb-4">Admin Panel: Rekam Kanji</h2>
            
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Huruf Kanji Target</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="text" id="inputChar" placeholder="Contoh: 一, 二, 三, 水" autocomplete="off">
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Arti / Makna</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="text" id="inputMeaning" placeholder="Contoh: Satu, Dua, Tiga, Air" autocomplete="off">
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Kategori</label>
                <select id="inputCategory" class="w-full p-2 border border-gray-300 rounded">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="hiragana">Hiragana</option>
                    <option value="katakana">Katakana</option>
                    <option value="kanji">Kanji</option>
                </select>
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Level (nullable)</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="number" id="inputLevel" placeholder="Angka, mis. 1" autocomplete="off">
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Gambar/Animasi Stroke (upload)</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="file" id="inputStrokeImage" accept="image/*">
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Kunyomi (nullable)</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="text" id="inputKunyomi" placeholder="Yomi" autocomplete="off">
            </div>
            <div class="mb-4 text-left">
                <label class="block font-bold mb-1">Onyomi (nullable)</label>
                <input class="w-full p-2 border border-gray-300 rounded" type="text" id="inputOnyomi" placeholder="Yomi" autocomplete="off">
            </div>

            <div class="relative mx-auto border-2 border-blue-900 bg-white cursor-crosshair inline-block">
                <div class="absolute pointer-events-none bg-red-300 opacity-30 w-px h-full left-1/2 top-0"></div>
                <div class="absolute pointer-events-none bg-red-300 opacity-30 h-px w-full top-1/2 left-0"></div>
                <canvas id="kanjiCanvas" width="300" height="300" class="block"></canvas>
            </div>

        <div class="mt-4">
            <button class="bg-red-500 text-white py-2 px-4 rounded" onclick="clearCanvas()">Hapus Canvas</button>
        </div>

        <p id="status" class="mt-2 font-bold text-blue-700">Total Goresan: 0</p>

        <button class="bg-green-600 text-white py-2 px-5 rounded w-full mt-4" onclick="saveToDatabase()">SIMPAN KE DATABASE</button>
        </div>
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

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            allStrokes = [];
            statusText.innerText = `Total Goresan: 0`;
        }

        async function saveToDatabase() {
            const char = document.getElementById('inputChar').value.trim();
            const meaning = document.getElementById('inputMeaning').value.trim();
            const category = document.getElementById('inputCategory').value;
            const level = document.getElementById('inputLevel').value;
            const strokeImage = document.getElementById('inputStrokeImage').value.trim();
            const kunyomi = document.getElementById('inputKunyomi').value.trim();
            const onyomi = document.getElementById('inputOnyomi').value.trim();

            // Validasi sebelum dikirim
            if (!char || !meaning) {
                alert("⚠️ Harap isi Huruf Kanji dan Artinya!");
                return;
            }
            if (!category) {
                alert("⚠️ Harap pilih kategori!");
                return;
            }
            if (allStrokes.length === 0) {
                alert("⚠️ Canvas masih kosong! Silakan gambar kanjinya terlebih dahulu.");
                return;
            }

            // Siapkan Data menggunakan FormData (untuk file)
            const formData = new FormData();
            formData.append('character', char);
            formData.append('meaning', meaning);
            formData.append('strokes', JSON.stringify(allStrokes));
            formData.append('category', category);
            if (level) formData.append('level', parseInt(level));
            if (kunyomi) formData.append('kunyomi', kunyomi);
            if (onyomi) formData.append('onyomi', onyomi);
            const fileInput = document.getElementById('inputStrokeImage');
            if (fileInput.files.length > 0) {
                formData.append('stroke_order_image', fileInput.files[0]);
            }

            // Kirim ke Backend Laravel via AJAX (Fetch API)
            try {
                const response = await fetch('/api/kanjis', {
                    method: 'POST',
                    body: formData
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
@endSection