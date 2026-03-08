@extends('layouts.app')

@section('title', 'Visual Novel - Scenes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                Visual Novel
            </h1>
            <p class="text-base text-slate-500 dark:text-slate-400 mt-1">
                Pilih scene untuk memulai cerita interaktif
            </p>
        </div>
    </div>

    @if($scenes->isEmpty())
        <div class="text-center py-20">
            <div class="text-7xl mb-4">📖</div>
            <p class="text-lg text-slate-500 dark:text-slate-400">Belum ada scene tersedia.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($scenes as $scene)
                <a href="{{ route('vn.play', $scene->first_dialogue_id) }}"
                   class="group relative rounded-2xl h-64 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    
                    @if($scene->thumbnail_path)
                        <img src="{{ asset('storage/' . $scene->thumbnail_path) }}" 
                             alt="{{ $scene->title }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 to-purple-900 z-0 flex items-center justify-center">
                            <span class="text-7xl opacity-30">🎬</span>
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                    
                    <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">
                            {{ $scene->title }}
                        </h3>
                        @if($scene->description)
                            <p class="text-sm text-gray-300 mb-4 line-clamp-2">{{ $scene->description }}</p>
                        @endif
                        <span class="inline-flex items-center text-sm font-semibold text-indigo-400 group-hover:text-indigo-300 w-max">
                            Mulai Cerita
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
