@extends('layouts.admin')

@section('title', 'Edit Kosakata')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vocabularies.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kosakata
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden max-w-4xl">
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fas fa-edit text-indigo-500 dark:text-indigo-400 mr-2 border dark:border-gray-600 bg-white dark:bg-gray-700 rounded-full p-2 shadow-sm"></i> Edit Kosakata: {{ $vocabulary->original }}
        </h3>
    </div>
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.vocabularies.update', $vocabulary) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="original" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kosakata (Kanji/Kana) *</label>
                    <input type="text" name="original" id="original" value="{{ old('original', $vocabulary->original) }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-2xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: 食べる">
                </div>
                
                <div>
                    <label for="furigana" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Furigana <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span>
                    </label>
                    <input type="text" name="furigana" id="furigana" value="{{ old('furigana', $vocabulary->furigana) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: たべる">
                </div>
                
                <div>
                    <label for="english" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Arti (English) *</label>
                    <input type="text" name="english" id="english" value="{{ old('english', $vocabulary->english) }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: to eat">
                </div>
                
                <div>
                    <label for="indonesian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Arti (Indonesian) <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span>
                    </label>
                    <input type="text" name="indonesian" id="indonesian" value="{{ old('indonesian', $vocabulary->indonesian) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Contoh: makan">
                </div>
                
                <div>
                    <label for="jlpt_level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Level JLPT <span class="text-gray-400 dark:text-gray-500 font-normal ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Opsional</span>
                    </label>
                    <select name="jlpt_level" id="jlpt_level" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm">
                        <option value="">Pilih Level</option>
                        <option value="N5" {{ old('jlpt_level', $vocabulary->jlpt_level) == 'N5' ? 'selected' : '' }}>N5 (Pemula)</option>
                        <option value="N4" {{ old('jlpt_level', $vocabulary->jlpt_level) == 'N4' ? 'selected' : '' }}>N4</option>
                        <option value="N3" {{ old('jlpt_level', $vocabulary->jlpt_level) == 'N3' ? 'selected' : '' }}>N3</option>
                        <option value="N2" {{ old('jlpt_level', $vocabulary->jlpt_level) == 'N2' ? 'selected' : '' }}>N2</option>
                        <option value="N1" {{ old('jlpt_level', $vocabulary->jlpt_level) == 'N1' ? 'selected' : '' }}>N1 (Mahir)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center text-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Perbarui Kosakata
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
