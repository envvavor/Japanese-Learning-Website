@extends('layouts.app')

@section('title', 'Buat Folder Kosakata — Manabu')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        <div class="flex items-center gap-3 sm:gap-5 mb-6 sm:mb-10">
            <a href="{{ route('vocabulary-folders.index') }}"
                class="w-11 h-11 sm:w-16 sm:h-16 flex items-center justify-center bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:border-b-2 active:translate-y-1 transition-all shrink-0 shadow-sm">
                <i class="fas fa-arrow-left text-lg sm:text-2xl"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider leading-tight">
                    Buat <span class="text-emerald-500">Folder</span>
                </h1>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-rose-200 dark:border-rose-800 rounded-xl p-4">
            @foreach($errors->all() as $error)
            <p class="text-rose-600 dark:text-rose-400 font-bold text-sm"><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('vocabulary-folders.store') }}"
              class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 shadow-sm">
            @csrf

            <div class="mb-6">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nama Folder</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 rounded-xl border-2 border-b-[4px] border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-gray-200 font-bold text-sm focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-600 transition-all"
                       placeholder="Contoh: Kosakata Sehari-hari">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Deskripsi (Opsional)</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border-2 border-b-[4px] border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-gray-200 font-bold text-sm focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-600 transition-all resize-none"
                          placeholder="Deskripsi singkat folder...">{{ old('description') }}</textarea>
            </div>

            <div class="mb-8" x-data="{ selected: '{{ old('color', 'indigo') }}' }">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Warna Folder</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($colors as $color)
                    <label class="cursor-pointer">
                        <input type="radio" name="color" value="{{ $color }}" class="hidden" x-model="selected">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl border-3 transition-all flex items-center justify-center bg-{{ $color }}-500"
                             :class="selected === '{{ $color }}' ? 'ring-4 ring-{{ $color }}-300 dark:ring-{{ $color }}-700 scale-110 border-white dark:border-gray-900' : 'border-transparent opacity-60 hover:opacity-100'">
                            <i class="fas fa-check text-white text-sm" x-show="selected === '{{ $color }}'"></i>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="w-full py-4 bg-emerald-500 border-2 border-b-[6px] border-emerald-700 text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                <i class="fas fa-folder-plus mr-2"></i> Buat Folder
            </button>
        </form>
    </div>
</div>
@endsection
