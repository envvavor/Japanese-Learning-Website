@extends('layouts.admin')
@section('title', 'Detail Quiz: ' . $quiz->title)

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.quizzes.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">
                    #{{ $quiz->order }}
                </span>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $quiz->title }}</h1>
                @if(!$quiz->is_active)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500">Non-aktif</span>
                @endif
            </div>
            @if($quiz->description)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $quiz->description }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.quizzes.edit', $quiz) }}"
           class="px-4 py-2 text-sm font-semibold text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-700 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all">
            <i class="fas fa-edit mr-1"></i> Edit Quiz
        </a>
        <a href="{{ route('admin.quizzes.items.create', $quiz) }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
            <i class="fas fa-plus mr-1"></i> Tambah Soal
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center shadow-sm">
        <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $quiz->items->count() }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Soal</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center shadow-sm">
        <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $quiz->items->where('question_type','multiple_choice')->count() }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilihan Ganda</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center shadow-sm">
        <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $quiz->items->where('question_type','listening')->count() }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Listening</p>
    </div>
</div>

{{-- Items List --}}
@if($quiz->items->isEmpty())
    <div class="bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-12 text-center">
        <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-question text-xl text-indigo-400"></i>
        </div>
        <h3 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-2">Belum ada soal</h3>
        <a href="{{ route('admin.quizzes.items.create', $quiz) }}"
           class="inline-flex items-center gap-2 mt-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
            <i class="fas fa-plus"></i> Tambah Soal Pertama
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($quiz->items as $item)
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">

                {{-- Order + Type --}}
                <div class="flex-shrink-0 flex flex-col items-center gap-1.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-black
                        @if($item->question_type === 'multiple_choice') bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400
                        @elseif($item->question_type === 'drawing') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                        @else bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 @endif">
                        {{ $item->order }}
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">
                        @if($item->question_type === 'multiple_choice') MC
                        @elseif($item->question_type === 'drawing') ✏️
                        @else 🔊 @endif
                    </span>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $item->question_text }}</p>

                    @if($item->question_type === 'drawing' && $item->kanji)
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $item->kanji->character }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->kanji->meaning }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-500">{{ ucfirst($item->kanji->category) }}</span>
                        </div>
                    @endif

                    @if($item->question_type !== 'drawing' && $item->options)
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($item->options as $opt)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ $opt === $item->correct_answer
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    @if($opt === $item->correct_answer) ✓ @endif {{ $opt }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($item->audio_url)
                        <div class="mt-2 flex items-center gap-2">
                            <i class="fas fa-volume-up text-amber-500 text-xs"></i>
                            <span class="text-xs text-gray-400">Audio tersedia</span>
                            <audio controls class="h-7 max-w-[200px]">
                                <source src="{{ $item->audio_url }}" type="audio/mpeg">
                            </audio>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex-shrink-0 flex gap-2">
                    <a href="{{ route('admin.quizzes.items.edit', [$quiz, $item]) }}"
                       class="px-3 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-700 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.quizzes.items.destroy', [$quiz, $item]) }}" method="POST"
                          onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-700 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
