@extends('layouts.admin')
@section('title', 'Manajemen Quiz')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Quiz</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola quiz berurutan</p>
    </div>
    <a href="{{ route('admin.quizzes.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
        <i class="fas fa-plus"></i> Tambah Quiz
    </a>
</div>

@if($quizzes->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 p-16 text-center">
        <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-layer-group text-2xl text-indigo-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-2">Belum ada quiz</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Mulai dengan membuat quiz pertama Anda</p>
        <a href="{{ route('admin.quizzes.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
            <i class="fas fa-plus"></i> Buat Quiz Pertama
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($quizzes as $quiz)
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow group">

            {{-- Order Badge --}}
            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ $quiz->order }}</span>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 truncate">{{ $quiz->title }}</h3>
                    @if(!$quiz->is_active)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Non-aktif</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Aktif</span>
                    @endif
                </div>
                @if($quiz->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $quiz->description }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        <i class="fas fa-question-circle mr-1"></i> {{ $quiz->items_count }} soal
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                   class="px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
                <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                   class="px-3 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-700 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST"
                      onsubmit="return confirm('Hapus quiz ini beserta semua soalnya?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-700 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
