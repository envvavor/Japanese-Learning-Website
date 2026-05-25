@extends('layouts.app')
@section('title', $materi->title . ' — Manabu')
@push('styles')
<script>
    tailwind.config = {
        darkMode: 'class',
    }
</script>
<style>
    /* 1. TYPOGRAPHY DASAR (BLOG STYLE) */
    .prose {
        color: #334155;
        font-size: 1.125rem; /* Sedikit lebih besar untuk readability */
        line-height: 1.8;
        font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; /* Menggunakan serif untuk body text lebih klasik/blog */
    }
    .dark .prose { color: #d1d5db; }
    
    /* Font sans-serif untuk elemen tertentu agar tetap modern */
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6, .prose a, .prose th {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    /* 2. HEADINGS */
    .prose h1, .prose h2, .prose h3, .prose h4 {
        color: #0f172a;
        font-weight: 800; 
        letter-spacing: -0.015em;
    }
    .dark .prose h1, .dark .prose h2, .dark .prose h3, .dark .prose h4 { color: #f8fafc; }
    
    .prose h2 { 
        font-size: 1.875em; 
        margin-top: 2em; 
        margin-bottom: 1em; 
        line-height: 1.3;
        /* Dihilangkan border-bottom tebal gamified */
    }
    .prose h3 { font-size: 1.5em; margin-top: 1.6em; margin-bottom: 0.6em; }
    /* 3. PARAGRAF & LINK */
    .prose p { margin-top: 1.5em; margin-bottom: 1.5em; }
    .prose a {
        color: #0ea5e9; 
        text-decoration: none;
        font-weight: 600;
        border-bottom: 1px solid transparent;
        transition: border-color 0.2s ease;
    }
    .dark .prose a { color: #38bdf8; }
    .prose a:hover { border-bottom-color: currentColor; }
    .prose strong { color: #0f172a; font-weight: 700; }
    .dark .prose strong { color: #f8fafc; }
    /* 🇯🇵 4. KHUSUS BAHASA JEPANG (Furigana/Ruby) */
    .prose ruby { 
        ruby-align: center; 
        margin-right: 0.1em; 
        font-weight: 500; 
        color: #1e293b;
        font-family: "Hiragino Kaku Gothic Pro", "Meiryo", sans-serif; /* Font Jepang yang bagus */
    }
    .dark .prose ruby { color: #f8fafc; }
    .prose rt { 
        color: #64748b;
        font-size: 0.55em;
        font-weight: 600;
        line-height: 1;
        transform: translateY(-10%);
        user-select: none; 
        -webkit-user-select: none;
    }
    .dark .prose rt { color: #94a3b8; }
    /* 5. BLOCKQUOTE (Kutipan Clean) */
    .prose blockquote {
        font-style: italic;
        color: #475569; 
        border-left: 4px solid #cbd5e1;
        padding-left: 1.25em;
        margin: 2em 0;
    }
    .dark .prose blockquote {
        color: #94a3b8;
        border-left-color: #475569;
    }
    /* 6. LISTS */
    .prose ul, .prose ol { margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.5em; }
    .prose li { margin-top: 0.5em; margin-bottom: 0.5em; }
    .prose ul { list-style-type: disc; }
    .prose ol { list-style-type: decimal; }
    .prose ul::marker, .prose ol::marker { color: #64748b; }
    .dark .prose ul::marker, .dark .prose ol::marker { color: #94a3b8; }
    /* 7. IMAGES & FIGURES */
    .prose img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 0.75rem; /* Radius lebih halus */
        margin: 2em auto;
        display: block;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Shadow elegan */
    }
    .prose figure { margin: 2.5em 0; }
    .prose figcaption { text-align: center; color: #64748b; font-size: 0.875em; margin-top: 1em; }
    .dark .prose figcaption { color: #94a3b8; }
    /* 8. TABLES (Clean Data Table) */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 2em 0;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }
    .dark .table-responsive-wrapper { border-color: #334155; }
    .prose table {
        width: 100%;
        min-width: max-content;
        border-collapse: collapse;
        font-size: 0.95em;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .prose thead { background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .dark .prose thead { background-color: #0f172a; border-bottom-color: #334155; }
    .prose thead th { color: #334155; font-weight: 600; padding: 0.75em 1em; text-align: left; }
    .dark .prose thead th { color: #cbd5e1; }
    .prose tbody tr { border-bottom: 1px solid #e2e8f0; }
    .dark .prose tbody tr { border-bottom-color: #1e293b; }
    .prose tbody td { padding: 0.75em 1em; color: #475569; }
    .dark .prose tbody td { color: #94a3b8; }
    .prose tbody tr:last-child { border-bottom: none; }
    /* 9. CODE & PRE */
    .prose code {
        color: #db2777; 
        font-size: 0.875em;
        background-color: #fdf2f8;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .dark .prose code { color: #f472b6; background-color: #4c1d9520; }
    .prose pre {
        color: #e2e8f0;
        background-color: #1e293b;
        overflow-x: auto;
        font-size: 0.875em;
        line-height: 1.7;
        margin: 2em 0;
        border-radius: 0.5rem;
        padding: 1.25em;
    }
    .dark .prose pre { background-color: #0f172a; border: 1px solid #1e293b; }
    .prose pre code { background-color: transparent; color: inherit; padding: 0; }
    /* 10. DIVIDER */
    .prose hr { border: 0; border-top: 1px solid #e2e8f0; margin: 3em auto; width: 50%; }
    .dark .prose hr { border-top-color: #334155; }
    
    /* 11. YOUTUBE / IFRAME RESPONSIVE */
    .prose iframe { max-width: 100% !important; border-radius: 0.5rem; margin: 2em 0; }
    
    /* 12. TTS BUTTON */
    .btn-inline-tts { background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 3px 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 14px; text-decoration: none; margin: 0 2px; transition: all 0.2s; display: inline-flex; align-items: center;}
    .btn-inline-tts:hover { background: #4f46e5; color: white; }
    .btn-inline-tts svg { pointer-events: none; display: inline-block; width: 14px; height: 14px; flex-shrink: 0; }
    .dark .btn-inline-tts { background: #3730a3; color: #818cf8; border: 1px solid #4f46e5; }
    .dark .btn-inline-tts:hover { background: #4f46e5; color: white; }
</style>
@endpush
@section('content')
<div class="min-h-screen bg-white dark:bg-slate-900 font-sans pb-24 pt-8" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">
    {{-- Top Navigation Bar (Kembali) --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <a href="{{ route('materi.index') }}"
            class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Materi
        </a>
    </div>
    {{-- Artikel Area --}}
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Article Header --}}
        <header class="mb-14 text-center">
            {{-- Kategori (Opsional, placeholder jika nanti ditambahkan) --}}
            <div class="mb-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400">
                    Materi Pembelajaran
                </span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-8">
                {{ $materi->title }}
            </h1>
            
            {{-- Meta Info (Author, Date, Read Time) --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 text-sm text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400">
                        <i class="fas fa-user-ninja"></i>
                    </div>
                    <span class="font-medium">Manabu Sensei</span>
                </div>
                <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar-alt"></i>
                    <span>{{ $materi->created_at->translatedFormat('d F Y') }}</span>
                </div>
                <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                <div class="flex items-center gap-2">
                    <i class="far fa-clock"></i>
                    <span>{{ ceil(str_word_count(strip_tags($materi->content)) / 200) }} min read</span>
                </div>
            </div>
            
            <hr class="mt-10 border-slate-200 dark:border-slate-800">
        </header>
        {{-- Article Content (Prose) --}}
        <article class="prose prose-slate dark:prose-invert max-w-none w-full mx-auto" id="articleContent">
            {!! $materi->content !!}
        </article>
        {{-- Article Footer --}}
        <footer class="mt-20 pt-8 border-t border-slate-200 dark:border-slate-800">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Selesai membaca?</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Lanjutkan perjalanan belajarmu ke materi berikutnya.</p>
                </div>
                <a href="{{ route('materi.index') }}" 
                   class="inline-flex items-center px-6 py-3 rounded-lg text-sm font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition-colors shadow-sm">
                    Kembali ke Daftar Materi
                </a>
            </div>
        </footer>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Otomatis membungkus semua tabel di dalam artikel dengan class wrapper
        // Ini memastikan tabel bisa discroll ke samping di layar HP (Responsive)
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

    // Fungsi untuk memutar suara Web Speech API
    function playTTS(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel(); // Hentikan suara sebelumnya
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ja-JP'; // Set aksen Jepang
            utterance.rate = 0.9;
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Browser Anda tidak mendukung fitur Text-to-Speech.");
        }
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-inline-tts');
        if (btn) {
            const textToSpeak = btn.getAttribute('data-speech');
            if (textToSpeak) {
                playTTS(textToSpeak);
            }
        }
    });
</script>
@endsection