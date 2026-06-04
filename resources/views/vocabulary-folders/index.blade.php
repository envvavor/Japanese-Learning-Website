@extends('layouts.app')

@section('title', 'Folder Kosakata — Manabu')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">
    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        <div class="flex items-center gap-3 sm:gap-5 mb-6 sm:mb-10">
            <a href="{{ route('dashboard') }}"
                class="w-11 h-11 sm:w-16 sm:h-16 flex items-center justify-center bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:border-b-2 active:translate-y-1 transition-all shrink-0 shadow-sm">
                <i class="fas fa-arrow-left text-lg sm:text-2xl"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider leading-tight">
                    Folder <span class="text-indigo-500">Kosakata</span>
                </h1>
                <p class="text-[10px] sm:text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    Kelompokkan dan latih kosakatamu
                </p>
            </div>
        </div>

                <div>
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-sm sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-sm sm:text-base"></i>
                    </div>
                    Folder Saya
                    <span class="h-1 flex-1 bg-slate-200 dark:bg-gray-800 rounded-full hidden sm:block"></span>
                </h2>
                <a href="{{ route('vocabulary-folders.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 sm:px-6 sm:py-3 bg-emerald-500 border-2 border-b-[4px] sm:border-b-[6px] border-emerald-700 text-white font-black text-xs sm:text-sm uppercase tracking-widest rounded-xl sm:rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-plus"></i> <span class="hidden sm:inline">Buat Folder</span>
                </a>
            </div>

            @if($myFolders->isEmpty())
            <div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-10 sm:p-16 text-center shadow-sm">
                <i class="fas fa-folder-plus text-5xl sm:text-6xl text-slate-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Belum Ada Folder</h3>
                <p class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 mb-6">Buat folder pertamamu untuk mulai mengelompokkan kosakata!</p>
                <a href="{{ route('vocabulary-folders.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 border-2 border-b-[6px] border-emerald-700 text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-plus"></i> Buat Folder Baru
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($myFolders as $folder)
                @php
                    $progress = $folder->user_progress;
                @endphp
                <a href="{{ route('vocabulary-folders.show', $folder) }}"
                   class="block bg-white dark:bg-gray-800 border-2 border-b-[6px] sm:border-b-[8px] border-slate-200 dark:border-gray-700 rounded-2xl sm:rounded-[1.5rem] p-5 sm:p-6 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-{{ $folder->color }}-100 dark:bg-{{ $folder->color }}-900/30 text-{{ $folder->color }}-500 border-2 border-b-4 border-{{ $folder->color }}-200 dark:border-{{ $folder->color }}-800 rounded-2xl flex items-center justify-center text-xl sm:text-2xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-folder"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-2 border-amber-200 dark:border-amber-800 rounded-lg text-[9px] sm:text-[10px] font-black uppercase tracking-widest">
                            Milik Saya
                        </span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-1 group-hover:text-{{ $folder->color }}-500 transition-colors">
                        {{ $folder->name }}
                    </h3>
                    @if($folder->description)
                    <p class="text-xs font-bold text-slate-400 mb-3 line-clamp-2">{{ $folder->description }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-3 pt-3 border-t-2 border-dashed border-slate-100 dark:border-gray-700">
                        <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest">
                            <i class="fas fa-layer-group mr-1"></i> {{ $folder->items_count }} Kata
                        </span>
                        @if($progress['total'] > 0)
                        <span class="text-[10px] sm:text-xs font-black text-emerald-500 uppercase tracking-widest">
                            {{ $progress['percent'] }}%
                        </span>
                        @endif
                    </div>
                    @if($progress['total'] > 0)
                    <div class="h-2 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden mt-2 border border-slate-200 dark:border-gray-600">
                        <div class="h-full bg-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progress['percent'] }}%"></div>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
            @endif
        </div>

        @if($adminFolders->isNotEmpty())
        <div class="mb-10 mt-10 sm:mb-14">
            <h2 class="text-sm sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-sm sm:text-base"></i>
                </div>
                Folder Kosakata Modul
                <span class="h-1 flex-1 bg-slate-200 dark:bg-gray-800 rounded-full"></span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($adminFolders as $folder)
                @php
                    $progress = $folder->user_progress;
                @endphp
                <a href="{{ route('vocabulary-folders.show', $folder) }}"
                   class="block bg-white dark:bg-gray-800 border-2 border-b-[6px] sm:border-b-[8px] border-slate-200 dark:border-gray-700 rounded-2xl sm:rounded-[1.5rem] p-5 sm:p-6 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-{{ $folder->color }}-100 dark:bg-{{ $folder->color }}-900/30 text-{{ $folder->color }}-500 border-2 border-b-4 border-{{ $folder->color }}-200 dark:border-{{ $folder->color }}-800 rounded-2xl flex items-center justify-center text-xl sm:text-2xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-1 group-hover:text-{{ $folder->color }}-500 transition-colors">
                        {{ $folder->name }}
                    </h3>
                    @if($folder->description)
                    <p class="text-xs font-bold text-slate-400 mb-3 line-clamp-2">{{ $folder->description }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-3 pt-3 border-t-2 border-dashed border-slate-100 dark:border-gray-700">
                        <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest">
                            <i class="fas fa-layer-group mr-1"></i> {{ $folder->items_count }} Kata
                        </span>
                        @if($progress['total'] > 0)
                        <span class="text-[10px] sm:text-xs font-black text-emerald-500 uppercase tracking-widest">
                            {{ $progress['percent'] }}%
                        </span>
                        @endif
                    </div>
                    @if($progress['total'] > 0)
                    <div class="h-2 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden mt-2 border border-slate-200 dark:border-gray-600">
                        <div class="h-full bg-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progress['percent'] }}%"></div>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
