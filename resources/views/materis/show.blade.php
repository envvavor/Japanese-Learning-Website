@extends('layouts.app')

@section('title', $materi->title . ' — Manabu')

@push('styles')
<script>
    tailwind.config = {
        darkMode: 'class',
    }
</script>
<style>
    /* 1. TYPOGRAPHY DASAR (GAMIFIED) */
    .prose {
        color: #334155;
        font-size: 1.1rem;
        line-height: 1.8;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        -webkit-font-smoothing: antialiased;
    }
    .dark .prose { color: #cbd5e1; }
    .prose.max-w-none { max-width: none; }

    /* 2. HEADINGS (Judul) */
    .prose h1, .prose h2, .prose h3, .prose h4 {
        color: #0f172a;
        font-weight: 900; 
        letter-spacing: -0.025em;
    }
    .dark .prose h1, .dark .prose h2, .dark .prose h3, .dark .prose h4 { color: #f8fafc; }
    .prose h1 { font-size: 2.5em; margin-bottom: 0.8em; line-height: 1.2; }
    .prose h2 { font-size: 1.75em; margin-top: 2em; margin-bottom: 1em; line-height: 1.3; padding-bottom: 0.3em; border-bottom: 4px solid #e2e8f0; border-radius: 2px; }
    .dark .prose h2 { border-bottom-color: #334155; }
    .prose h3 { font-size: 1.25em; margin-top: 1.6em; margin-bottom: 0.6em; }

    /* 3. PARAGRAF & LINK */
    .prose p { margin-top: 1.25em; margin-bottom: 1.25em; }
    .prose a {
        color: #06b6d4; 
        text-decoration: none;
        font-weight: 900;
        border-bottom: 3px solid #cffafe;
        transition: all 0.2s ease;
        padding: 0 4px;
        border-radius: 6px;
    }
    .dark .prose a { color: #22d3ee; border-bottom-color: #164e63; }
    .prose a:hover { background-color: #cffafe; border-bottom-color: #06b6d4; }
    .dark .prose a:hover { background-color: #164e63; border-bottom-color: #22d3ee; color: #fff; }
    .prose strong { color: #0f172a; font-weight: 900; }
    .dark .prose strong { color: #f8fafc; }

    /* 🇯🇵 4. KHUSUS BAHASA JEPANG (Furigana/Ruby) */
    .prose ruby { ruby-align: center; margin-right: 0.1em; font-weight: 700; color: #1e293b; }
    .dark .prose ruby { color: #f8fafc; }
    .prose rt { 
        color: #06b6d4;
        font-size: 0.6em;
        font-weight: 900;
        line-height: 1;
        transform: translateY(-10%);
        user-select: none; 
        -webkit-user-select: none;
    }
    .dark .prose rt { color: #22d3ee; }

    /* 5. BLOCKQUOTE (Kotak Kutipan/Catatan Chunky) */
    .prose blockquote {
        position: relative;
        font-weight: 700;
        color: #0891b2; 
        border: 2px solid #cffafe;
        border-left: 8px solid #06b6d4;
        background-color: #ecfeff;
        margin: 2em 0;
        padding: 1.5em 1.5em;
        border-radius: 1rem;
        box-shadow: 0 4px 0 #cffafe;
    }
    .dark .prose blockquote {
        color: #67e8f9;
        border-color: #164e63;
        border-left-color: #0891b2;
        background-color: #083344;
        box-shadow: 0 4px 0 #164e63;
    }
    .prose blockquote p { margin: 0; }

    /* 6. LISTS (Daftar) */
    .prose ul, .prose ol { margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.5em; }
    .prose li { margin-top: 0.5em; margin-bottom: 0.5em; padding-left: 0.375em; font-weight: 500; }
    .prose ul li { list-style-type: none; position: relative; }
    .prose ul li::before {
        content: "\f00c"; /* FontAwesome check-circle */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        color: #06b6d4;
        font-size: 0.8em;
        top: 0.2em;
        left: -1.5em;
    }
    .dark .prose ul li::before { color: #22d3ee; }
    .prose ol { list-style-type: decimal; }
    .prose ol li::marker { color: #06b6d4; font-weight: 900; }

    /* 7. IMAGES & FIGURES */
    .prose img {
        max-width: 100% !important; /* Paksa gambar tidak tumpah */
        height: auto !important;
        border-radius: 1.5rem;
        border: 2px solid #e2e8f0;
        border-bottom: 8px solid #cbd5e1;
        margin: 2.5em auto;
        display: block;
        transition: transform 0.2s ease;
    }
    .dark .prose img { border-color: #334155; border-bottom-color: #1e293b; }
    .prose img:hover { transform: translateY(-2px); }
    .prose figure { margin: 2.5em 0; }
    .prose figcaption { text-align: center; color: #64748b; font-size: 0.875em; margin-top: 1em; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .dark .prose figcaption { color: #94a3b8; }

    /* 8. TABLES (Tabel Data Gamified & Responsive) */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 2.5em 0;
        border-radius: 1rem;
        border: 2px solid #e2e8f0;
        border-bottom: 6px solid #cbd5e1;
        background-color: #ffffff;
    }
    .dark .table-responsive-wrapper {
        border-color: #334155;
        border-bottom-color: #1e293b;
        background-color: #0f172a;
    }
    .prose table {
        width: 100%;
        min-width: max-content; /* Memaksa tabel tidak penyok di layar kecil */
        border-collapse: separate;
        border-spacing: 0;
        margin: 0; /* Margin dipindah ke wrapper */
        font-size: 0.95em;
    }
    .prose thead { background-color: #f8fafc; }
    .dark .prose thead { background-color: #1e293b; }
    .prose thead th { color: #0f172a; font-weight: 900; padding: 1.2em 1em; text-align: left; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; white-space: nowrap; }
    .dark .prose thead th { color: #f8fafc; border-bottom-color: #334155; }
    .prose tbody tr { transition: background-color 0.15s ease; }
    .prose tbody tr:hover { background-color: #f1f5f9; }
    .dark .prose tbody tr:hover { background-color: #020617; }
    .prose tbody td { padding: 1em; border-bottom: 2px solid #f1f5f9; color: #475569; font-weight: 500; }
    .dark .prose tbody td { border-bottom-color: #1e293b; color: #cbd5e1; }
    .prose tbody tr:last-child td { border-bottom: none; }

    /* 9. CODE & PRE (Blok Kode) */
    .prose code {
        color: #be123c; 
        font-weight: 800;
        font-size: 0.875em;
        background-color: #ffe4e6;
        padding: 0.25em 0.5em;
        border-radius: 0.5rem;
        border: 2px solid #fecdd3;
        border-bottom: 3px solid #fda4af;
        font-family: ui-monospace, monospace;
    }
    .dark .prose code { color: #fda4af; background-color: #881337; border-color: #be123c; border-bottom-color: #9f1239; }
    .prose pre {
        color: #f8fafc;
        background-color: #0f172a;
        overflow-x: auto;
        font-size: 0.9em;
        line-height: 1.7;
        margin: 2em 0;
        border-radius: 1rem;
        padding: 1.5em;
        border: 2px solid #334155;
        border-bottom: 8px solid #1e293b;
    }
    .dark .prose pre { background-color: #020617; border-color: #1e293b; border-bottom-color: #000; }
    .prose pre code { background-color: transparent; color: inherit; padding: 0; font-weight: 500; border: none; }

    /* 10. DIVIDER (Garis Pemisah) */
    .prose hr { border: 0; border-top: 4px dashed #cbd5e1; margin: 3em 0; border-radius: 2px; }
    .dark .prose hr { border-top-color: #334155; }
    
    /* 11. YOUTUBE / IFRAME RESPONSIVE */
    .prose iframe { max-width: 100% !important; border-radius: 1rem; }
</style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Menu Dinamis --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500 border-2 border-b-4 border-cyan-200 dark:border-cyan-800 rounded-2xl flex items-center justify-center text-3xl shrink-0 shadow-sm">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Materi Pembelajaran
                    </h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Membaca materi bahasa Jepang.</p>
                </div>
            </div>

            <a href="{{ route('materi.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Article Main Card --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-sm relative overflow-hidden transition-all duration-300">
            
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 w-full h-3 bg-cyan-500"></div>

            <div class="p-8 sm:p-12 mt-2">
                {{-- Article Header --}}
                <header class="mb-12">
                    {{-- Date Badge --}}
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 border-2 border-cyan-100 dark:border-cyan-800">
                            <i class="fas fa-calendar-day mr-2"></i> {{ $materi->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight mb-6">
                        {{ $materi->title }}
                    </h1>
                    
                    {{-- Decorative Divider --}}
                    <div class="w-24 h-2 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full"></div>
                </header>

                {{-- Article Content (Prose) --}}
                <article class="prose prose-slate max-w-none" id="articleContent">
                    {!! $materi->content !!}
                </article>
            </div>
            
            {{-- Footer / Bottom Area --}}
            <div class="bg-slate-50 dark:bg-gray-900/50 p-8 sm:p-10 border-t-2 border-dashed border-slate-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-6">
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    <i class="fas fa-flag-checkered mr-2"></i> Selesai Membaca
                </p>
                <a href="{{ route('materi.index') }}" 
                   class="inline-flex items-center px-8 py-4 border-2 border-b-[6px] border-cyan-600 rounded-2xl text-sm font-black text-white bg-cyan-500 hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shadow-sm w-full sm:w-auto justify-center">
                    <i class="fas fa-book-reader mr-2"></i> Materi Lainnya
                </a>
            </div>

        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Otomatis membungkus semua tabel di dalam artikel dengan class wrapper
        // Ini memastikan border tebal (chunky) kotak tabel tetap diam, dan hanya isinya yang ter-scroll di HP
        const article = document.getElementById('articleContent');
        if (article) {
            const tables = article.querySelectorAll('table');
            tables.forEach(table => {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive-wrapper';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });
        }
    });
</script>
@endsection