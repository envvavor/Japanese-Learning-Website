@extends('layouts.admin')

@section('title', 'Edit Folder: ' . $folder->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vocabulary-folders.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Folder
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">Edit Folder: {{ $folder->name }}</h3>

    <form method="POST" action="{{ route('admin.vocabulary-folders.update', $folder) }}">
        @csrf @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Folder</label>
            <input type="text" name="name" value="{{ old('name', $folder->name) }}" required
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi (Opsional)</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none">{{ old('description', $folder->description) }}</textarea>
        </div>

        <div class="mb-6" x-data="{ selected: '{{ old('color', $folder->color) }}' }">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Warna</label>
            <div class="flex flex-wrap gap-3">
                @foreach($colors as $color)
                <label class="cursor-pointer">
                    <input type="radio" name="color" value="{{ $color }}" class="hidden" x-model="selected">
                    <div class="w-10 h-10 rounded-lg transition-all flex items-center justify-center bg-{{ $color }}-500"
                         :class="selected === '{{ $color }}' ? 'ring-4 ring-{{ $color }}-300 dark:ring-{{ $color }}-700 scale-110' : 'opacity-60 hover:opacity-100'">
                        <i class="fas fa-check text-white text-sm" x-show="selected === '{{ $color }}'"></i>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition-all">
            <i class="fas fa-save mr-2"></i> Simpan Perubahan
        </button>
    </form>
</div>
@endsection
