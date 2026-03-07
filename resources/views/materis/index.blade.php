@extends('layouts.app')

@section('title', 'Daftar Materi Pembelajaran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    {{-- Back Navigation --}}
    <div class="mb-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors group">
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            Daftar Materi Pembelajaran
        </h1>
        <p class="mt-3 text-base text-slate-500 dark:text-slate-400 max-w-2xl">
            Jelajahi semua materi pembelajaran bahasa Jepang. Klik pada materi untuk membaca selengkapnya.
        </p>
        <div class="w-20 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mt-5"></div>
    </div>

    {{-- Materi Grid --}}
    @if($materis->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($materis as $materi)
                <a href="{{ route('materi.show', $materi) }}" 
                   class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700/50">
                    
                    {{-- Card Content --}}
                    <div class="p-6">
                        {{-- Title --}}
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 mb-3">
                            {{ $materi->title }}
                        </h3>

                        {{-- Content Preview --}}
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 mb-4 leading-relaxed">
                            {{ Str::limit(strip_tags($materi->content), 120) }}
                        </p>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700/50">
                            {{-- Date --}}
                            <div class="flex items-center text-xs text-slate-400 dark:text-slate-500">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $materi->created_at->translatedFormat('d M Y') }}
                            </div>

                            {{-- Read More --}}
                            <span class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 dark:group-hover:text-indigo-300">
                                Baca
                                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $materis->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-100 dark:bg-indigo-900/30 rounded-full mb-6">
                <svg class="w-10 h-10 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Belum Ada Materi</h3>
            <p class="text-slate-500 dark:text-slate-400">Materi pembelajaran akan segera tersedia. Silakan kembali nanti!</p>
        </div>
    @endif

</div>
@endsection
