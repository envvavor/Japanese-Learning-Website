@extends('layouts.app')

@section('title', 'Daftar Materi Pembelajaran — Manabu')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500 border-2 border-b-4 border-cyan-200 dark:border-cyan-800 rounded-2xl flex items-center justify-center text-3xl shrink-0 shadow-sm">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Materi Pembelajaran
                    </h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Jelajahi dan baca materi bahasa Jepang terbaru.</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Main Content --}}
        @if($materis->count() > 0)
            {{-- Materi Grid Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @foreach($materis as $materi)
                    <a href="{{ route('materi.show', $materi) }}" 
                       class="group block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] hover:border-cyan-300 dark:hover:border-cyan-700 hover:-translate-y-1 active:translate-y-[6px] active:border-b-2 transition-all duration-200 shadow-sm flex flex-col h-full relative overflow-hidden">
                        
                        {{-- Ornamen Latar Tipis (Opsional) --}}
                        <i class="fas fa-book-open absolute -bottom-6 -right-4 text-7xl text-slate-100 dark:text-gray-700 opacity-50 group-hover:rotate-12 transition-transform duration-300 z-0"></i>

                        <div class="p-6 sm:p-8 flex flex-col h-full relative z-10">
                            {{-- Date Badge --}}
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 border-2 border-cyan-100 dark:border-cyan-800">
                                    <i class="fas fa-calendar-day mr-1.5"></i> {{ $materi->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-xl font-black text-slate-800 dark:text-white group-hover:text-cyan-500 transition-colors line-clamp-2 mb-3 leading-tight uppercase tracking-wide">
                                {{ $materi->title }}
                            </h3>

                            {{-- Content Preview --}}
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 line-clamp-3 mb-6 flex-1">
                                {{ Str::limit(strip_tags($materi->content), 120) }}
                            </p>

                            {{-- Footer / CTA Button --}}
                            <div class="mt-auto pt-4 border-t-2 border-dashed border-slate-100 dark:border-gray-700 flex items-center justify-end">
                                <span class="inline-flex items-center justify-center bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 border-2 border-b-4 border-cyan-200 dark:border-cyan-800 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest group-hover:bg-cyan-500 group-hover:border-cyan-600 group-hover:text-white transition-all">
                                    Baca <i class="fas fa-book-reader ml-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination (jika ada) --}}
            <div class="flex justify-center mt-12">
                {{ $materis->links() }}
            </div>
            
        @else
            {{-- Empty State (Gamified) --}}
            <div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-16 text-center shadow-sm">
                <i class="fas fa-folder-open text-6xl text-slate-300 dark:text-gray-600 mb-4 animate-pulse"></i>
                <h3 class="text-2xl font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Belum Ada Materi</h3>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Admin belum menambahkan materi pembelajaran apapun.</p>
            </div>
        @endif

    </div>
</div>
@endsection