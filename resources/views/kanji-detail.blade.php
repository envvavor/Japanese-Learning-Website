@extends('layouts.app')

@section('title', 'Detail Kanji')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">

    <!-- Back -->
    <button onclick="window.history.back()"
        class="text-sm text-slate-500 hover:text-slate-800 transition mb-8">
        ← Kembali
    </button>

    <!-- Card -->
    <div id="infoArea"
         class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10">

        <!-- Character -->
        <div class="text-center mb-8">
            <h1 id="character"
                class="text-7xl font-semibold text-slate-800 tracking-tight"></h1>

            <p id="meaning"
               class="mt-3 text-lg text-slate-600"></p>
        </div>

        <!-- Meta Info -->
        <div class="grid grid-cols-2 gap-6 text-sm text-slate-500 mb-8">

            <div>
                <p class="uppercase tracking-wide text-xs text-slate-400 mb-1">Kategori</p>
                <p id="category" class="text-slate-700"></p>
            </div>

            <div>
                <p class="uppercase tracking-wide text-xs text-slate-400 mb-1">Level</p>
                <p id="level" class="text-slate-700"></p>
            </div>

            <div class="col-span-2">
                <p class="uppercase tracking-wide text-xs text-slate-400 mb-1">Cara Baca</p>
                <p id="readings" class="text-slate-700"></p>
            </div>

        </div>

        <!-- Stroke Image -->
        <div id="strokeImage"
             class="flex justify-center items-center mb-8">
        </div>

        <!-- Action -->
        <div class="text-center">
            <button id="practiceBtn"
                class="px-6 py-3 rounded-lg bg-slate-800 text-white 
                       hover:bg-slate-700 transition text-sm tracking-wide">
                Mulai Latihan
            </button>
        </div>

    </div>

</div>

<script>
    async function loadDetail() {
        try {
            const char = "{{ $character }}";
            const response = await fetch(`/api/kanjis/${char}`);
            const data = await response.json();
            if (response.ok) {
                document.getElementById('character').innerText = data.character;
                document.getElementById('meaning').innerText = data.meaning;
                document.getElementById('category').innerText = data.category ? `Kategori: ${data.category}` : '';
                document.getElementById('level').innerText = data.level ? `Level: ${data.level}` : '';
                let readings = [];
                if (data.kunyomi) readings.push(`Kun: ${data.kunyomi}`);
                if (data.onyomi) readings.push(`On: ${data.onyomi}`);
                document.getElementById('readings').innerText = readings.join(' | ');
                if (data.stroke_order_image) {
                    const url = `/storage/${data.stroke_order_image}`;
                    document.getElementById('strokeImage').innerHTML = `<img src="${url}" alt="Stroke order" class="mx-auto max-h-64">`;
                }
                document.getElementById('practiceBtn').addEventListener('click', () => {
                    window.location = `/list?practice=${data.character}`;
                });
            } else {
                document.getElementById('infoArea').innerHTML = '<p class="text-red-500">Kanji tidak ditemukan.</p>';
            }
        } catch (e) {
            document.getElementById('infoArea').innerHTML = '<p class="text-red-500">Gagal memuat informasi.</p>';
        }
    }

    loadDetail();
</script>
@endsection
