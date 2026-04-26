@extends('layouts.app')

@section('title', 'Visual Novel - Scenes')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-violet-100 dark:bg-violet-900/30 text-violet-500 border-2 border-b-4 border-violet-200 dark:border-violet-800 rounded-2xl flex items-center justify-center text-3xl shrink-0 shadow-sm">
                    <i class="fas fa-theater-masks"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Visual Novel
                    </h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Pilih scene untuk memulai cerita interaktif.</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Main Content --}}
        @if($scenes->isEmpty())
            {{-- Empty State (Gamified) --}}
            <div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-16 text-center shadow-sm">
                <i class="fas fa-book-dead text-6xl text-slate-300 dark:text-gray-600 mb-4 animate-pulse"></i>
                <h3 class="text-2xl font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Belum Ada Cerita</h3>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Admin belum menambahkan scene visual novel.</p>
            </div>
        @else
            {{-- Scene Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($scenes as $scene)
                    <a href="{{ route('vn.play', $scene->first_dialogue_id) }}"
                       class="group block relative rounded-[2rem] h-72 overflow-hidden border-2 border-b-[8px] border-slate-200 dark:border-gray-700 hover:border-violet-400 dark:hover:border-violet-500 active:border-b-2 active:translate-y-[6px] transition-all duration-200 shadow-sm bg-slate-100 dark:bg-gray-800">
                        
                        {{-- Background Image / Placeholder --}}
                        @if($scene->thumbnail_path)
                            <img src="{{ asset('storage/' . $scene->thumbnail_path) }}" 
                                 alt="{{ $scene->title }}" 
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-indigo-600 z-0 flex items-center justify-center group-hover:scale-110 transition-transform duration-700">
                                <i class="fas fa-film text-7xl text-white opacity-20"></i>
                            </div>
                        @endif

                        {{-- Gradient Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                        
                        {{-- Content --}}
                        <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                            <div class="flex justify-between items-end gap-4">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-black text-white mb-1 group-hover:text-violet-300 transition-colors uppercase tracking-wide drop-shadow-md line-clamp-1">
                                        {{ $scene->title }}
                                    </h3>
                                    @if($scene->description)
                                        <p class="text-xs font-bold text-gray-300 line-clamp-2 drop-shadow-sm">{{ $scene->description }}</p>
                                    @else
                                        <p class="text-xs font-bold text-gray-400 drop-shadow-sm italic">Tidak ada deskripsi.</p>
                                    @endif
                                </div>

                                {{-- Play Button Icon --}}
                                <div class="w-14 h-14 rounded-2xl bg-violet-500 text-white flex items-center justify-center text-xl shrink-0 border-2 border-b-4 border-violet-700 group-hover:bg-violet-400 transition-colors shadow-lg">
                                    <i class="fas fa-play ml-1"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection