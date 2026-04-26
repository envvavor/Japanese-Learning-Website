@extends('layouts.admin')
@section('title', 'Edit Quiz')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Quiz</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-8">
        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Judul Quiz <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all
                              @error('title') border-rose-400 @enderror">
                @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none transition-all">{{ old('description', $quiz->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Urutan (Order) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="order" value="{{ old('order', $quiz->order) }}" required min="1"
                       class="w-32 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $quiz->is_active) == '1' ? 'checked' : '' }}>
                    
                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:bg-indigo-600 transition-all after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quiz Aktif</span>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                   class="flex-1 py-3 text-center text-sm font-semibold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow transition-all">
                    <i class="fas fa-save mr-2"></i> Perbarui Quiz
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
