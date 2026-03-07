@extends('layouts.app')

@section('title', $materi->title)

@push('styles')
<script>
    tailwind.config = {
        darkMode: 'class',
    }
</script>
<style>
    /* 1. TYPOGRAPHY DASAR */
    .prose {
        color: #334155;
        font-size: 1.0625rem;
        line-height: 1.8;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        -webkit-font-smoothing: antialiased;
    }
    .dark .prose { color: #cbd5e1; }
    .prose.max-w-none { max-width: none; }

    /* 2. HEADINGS (Judul) */
    .prose h1, .prose h2, .prose h3, .prose h4 {
        color: #0f172a;
        font-weight: 700;
        letter-spacing: -0.025em;
    }
    .dark .prose h1, .dark .prose h2, .dark .prose h3, .dark .prose h4 { color: #f1f5f9; }
    .prose h1 { font-size: 2.5em; margin-bottom: 0.8em; line-height: 1.2; font-weight: 800; }
    .prose h2 { font-size: 1.75em; margin-top: 2em; margin-bottom: 1em; line-height: 1.3; padding-bottom: 0.3em; border-bottom: 2px solid #f1f5f9; }
    .dark .prose h2 { border-bottom-color: #334155; }
    .prose h3 { font-size: 1.25em; margin-top: 1.6em; margin-bottom: 0.6em; }

    /* 3. PARAGRAF & LINK */
    .prose p { margin-top: 1.25em; margin-bottom: 1.25em; }
    .prose a {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 2px solid #c7d2fe;
        transition: all 0.2s ease;
    }
    .dark .prose a { color: #818cf8; border-bottom-color: #4338ca; }
    .prose a:hover { color: #312e81; border-bottom-color: #4f46e5; }
    .dark .prose a:hover { color: #a5b4fc; border-bottom-color: #818cf8; }
    .prose strong { color: #0f172a; font-weight: 700; }
    .dark .prose strong { color: #f1f5f9; }

    /* 🇯🇵 4. KHUSUS BAHASA JEPANG (Furigana/Ruby) */
    .prose ruby { ruby-align: center; margin-right: 0.1em; }
    .prose rt { 
        color: #64748b;
        font-size: 0.6em;
        font-weight: 500;
        line-height: 1;
        transform: translateY(-10%);
        user-select: none; 
        -webkit-user-select: none;
    }
    .dark .prose rt { color: #94a3b8; }

    /* 5. BLOCKQUOTE (Kotak Kutipan/Catatan) */
    .prose blockquote {
        position: relative;
        font-style: italic;
        color: #334155;
        border-left: 4px solid #6366f1;
        background: linear-gradient(to right, #eef2ff 0%, transparent 100%);
        margin: 2em 0;
        padding: 1.2em 1.5em;
        border-radius: 0 0.75rem 0.75rem 0;
    }
    .dark .prose blockquote {
        color: #cbd5e1;
        border-left-color: #818cf8;
        background: linear-gradient(to right, rgba(99,102,241,0.15) 0%, transparent 100%);
    }
    .prose blockquote::before {
        content: '"';
        position: absolute;
        top: -0.2em;
        left: 0.1em;
        font-size: 4em;
        color: #c7d2fe;
        font-family: Georgia, serif;
        opacity: 0.4;
    }
    .dark .prose blockquote::before { color: #4338ca; opacity: 0.5; }
    .prose blockquote p { position: relative; z-index: 1; margin: 0; }

    /* 6. LISTS (Daftar) */
    .prose ul, .prose ol { margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.5em; }
    .prose li { margin-top: 0.5em; margin-bottom: 0.5em; padding-left: 0.375em; }
    .prose ul li { list-style-type: none; position: relative; }
    .prose ul li::before {
        content: "";
        position: absolute;
        background-color: #818cf8;
        border-radius: 50%;
        width: 0.375em;
        height: 0.375em;
        top: 0.6875em;
        left: -1em;
    }
    .dark .prose ul li::before { background-color: #a5b4fc; }
    .prose ol { list-style-type: decimal; }
    .prose ol li::marker { color: #6366f1; font-weight: 700; }
    .dark .prose ol li::marker { color: #818cf8; }

    /* 7. IMAGES & FIGURES */
    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        margin: 2.5em auto;
        display: block;
        transition: transform 0.3s ease;
    }
    .dark .prose img { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.4), 0 4px 6px -2px rgba(0,0,0,0.3); }
    .prose img:hover { transform: scale(1.01); }
    .prose figure { margin: 2.5em 0; }
    .prose figcaption { text-align: center; color: #64748b; font-size: 0.875em; margin-top: 0.875em; }
    .dark .prose figcaption { color: #94a3b8; }

    /* 8. TABLES (Tabel Data) */
    .prose table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 2.5em 0;
        font-size: 0.9375em;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06);
    }
    .dark .prose table { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.4); }
    .prose thead { background-color: #f8fafc; }
    .dark .prose thead { background-color: #1e293b; }
    .prose thead th { color: #0f172a; font-weight: 700; padding: 1em; text-align: left; border-bottom: 2px solid #e2e8f0; }
    .dark .prose thead th { color: #f1f5f9; border-bottom-color: #334155; }
    .prose tbody tr { transition: background-color 0.15s ease; }
    .prose tbody tr:hover { background-color: #f8fafc; }
    .dark .prose tbody tr:hover { background-color: #1e293b; }
    .prose tbody td { padding: 1em; border-bottom: 1px solid #f1f5f9; color: #475569; }
    .dark .prose tbody td { border-bottom-color: #1e293b; color: #94a3b8; }
    .prose tbody tr:last-child td { border-bottom: none; }

    /* 9. CODE & PRE (Blok Kode) */
    .prose code {
        color: #4f46e5;
        font-weight: 600;
        font-size: 0.875em;
        background-color: #e0e7ff;
        padding: 0.25em 0.5em;
        border-radius: 0.375rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .dark .prose code { color: #a5b4fc; background-color: #312e81; }
    .prose pre {
        color: #f8fafc;
        background-color: #0f172a;
        overflow-x: auto;
        font-size: 0.875em;
        line-height: 1.7;
        margin: 2em 0;
        border-radius: 0.75rem;
        padding: 1.25em 1.5em;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1);
    }
    .dark .prose pre { background-color: #020617; }
    .prose pre code { background-color: transparent; color: inherit; padding: 0; font-weight: 400; }
    
    /* 10. DIVIDER (Garis Pemisah) */
    .prose hr { border: 0; border-top: 2px dashed #e2e8f0; margin: 3em 0; }
    .dark .prose hr { border-top-color: #334155; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    {{-- Back Navigation --}}
    <div class="mb-8">
        <a href="{{ route('materi.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors group">
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar Materi
        </a>
    </div>

    {{-- Article Header --}}
    <header class="mb-10">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            {{ $materi->title }}
        </h1>
        <div class="mt-4 flex items-center text-sm text-slate-500 dark:text-slate-400">
            <svg class="w-4 h-4 mr-1.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <time datetime="{{ $materi->created_at->toISOString() }}">
                Dipublikasikan pada {{ $materi->created_at->translatedFormat('l, d F Y') }}
            </time>
        </div>
    </header>

    {{-- Divider --}}
    <div class="w-20 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mb-10"></div>

    {{-- Article Content --}}
    <article class="prose prose-slate max-w-none">
        {!! $materi->content !!}
    </article>

    {{-- Footer --}}
    <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-700">
        <a href="{{ route('materi.index') }}" class="inline-flex items-center px-5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar Materi
        </a>
    </div>
</div>
@endsection

