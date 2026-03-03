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

    <div id="infoArea"
         class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <div class="text-center mb-10 mt-2">
            <div class="flex justify-center items-center gap-4">
                <h1 id="character"
                    class="text-8xl font-bold text-slate-800 tracking-tight"></h1>
            </div>

            <p id="meaning"
               class="mt-4 text-xl font-medium text-slate-600 capitalize">
            </p>
            
            <button onclick="speakCurrentKanji()" 
            class="p-3 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-full transition-all hover:scale-110 active:scale-95" 
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

        <div id="strokeImage"
             class="flex justify-center items-center mb-10 min-h-[100px]">
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
    // Variabel global untuk menyimpan karakter yang sedang dibuka
    let currentKanjiChar = "";

    // --- FUNGSI TEXT TO SPEECH ---
    function speakCurrentKanji() {
        if (!currentKanjiChar) return;

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(currentKanjiChar);
            utterance.lang = 'ja-JP'; 
            utterance.rate = 0.8; 
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Browser Anda tidak mendukung fitur Text-to-Speech.");
        }
    }

    // --- FUNGSI LOAD DATA KANJI ---
    async function loadDetail() {
        try {
            const char = "{{ $character }}";
            const response = await fetch(`/api/kanjis/${char}`);
            const data = await response.json();
            
            if (response.ok) {
                // Simpan karakter ke variabel global untuk TTS
                currentKanjiChar = data.character;

                document.getElementById('character').innerText = data.character;
                document.getElementById('meaning').innerText = data.meaning;
                
                // Menghilangkan tulisan "Kategori:" dan "Level:" karena sudah ada judul di atasnya
                document.getElementById('category').innerText = data.category ? data.category : '-';
                document.getElementById('level').innerText = data.level ? data.level : '-';
                
                let readings = [];
                if (data.kunyomi) readings.push(`Kun: ${data.kunyomi}`);
                if (data.onyomi) readings.push(`On: ${data.onyomi}`);
                document.getElementById('readings').innerText = readings.length > 0 ? readings.join(' | ') : '-';
                
                if (data.stroke_order_image) {
                    const url = `/storage/${data.stroke_order_image}`;
                    document.getElementById('strokeImage').innerHTML = `<img src="${url}" alt="Stroke order for ${data.character}" class="mx-auto max-h-48 border border-slate-200 rounded-lg p-2 bg-white shadow-sm">`;
                }

                // Setup tombol practice
                document.getElementById('practiceBtn').addEventListener('click', () => {
                    // Pastikan rute '/list' memang bisa menerima parameter ?practice=
                    window.location = `/list?practice=${data.character}`;
                });
            } else {
                document.getElementById('infoArea').innerHTML = '<p class="text-rose-500 font-medium text-center py-10">Kanji tidak ditemukan.</p>';
            }
        } catch (e) {
            document.getElementById('infoArea').innerHTML = '<p class="text-rose-500 font-medium text-center py-10">Gagal memuat informasi dari server.</p>';
        }
    }

    loadDetail();
</script>
@endsection