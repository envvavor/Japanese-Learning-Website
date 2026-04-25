@extends('layouts.admin')

@section('title', 'Dataset Gambar')

@section('content')
<div x-data="{ searchQuery: '' }">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Dataset</h1>
            <p class="text-gray-600 dark:text-gray-400">Total Karakter: {{ count($datasets) }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       x-model.debounce.500ms="searchQuery" 
                       class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 transition-colors shadow-sm" 
                       placeholder="Cari huruf...">
                
                <button x-show="searchQuery !== ''" @click="searchQuery = ''" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none" x-cloak>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <a href="{{ route('admin.dataset.download.all') }}" class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm justify-center whitespace-nowrap">
                <i class="fas fa-download mr-2"></i> Download Semua (ZIP)
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($datasets as $char => $files)
            <div x-data="{ open: false }" 
                 x-show="searchQuery === '' || '{{ $char }}'.toLowerCase().includes(searchQuery.toLowerCase())" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div @click="open = !open" class="cursor-pointer px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-chevron-right text-gray-400 transition-transform duration-300" :class="{'rotate-90': open}"></i>
                        
                        <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $char }}</span>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                            {{ count($files) }} Gambar
                        </span>
                    </div>
                    
                    <div @click.stop>
                        <a href="{{ route('admin.dataset.download', $char) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-file-archive mr-2"></i> Download
                        </a>
                    </div>
                </div>

                <div x-show="open" x-transition x-cloak class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($files as $file)
                            <div class="relative group bg-gray-100 dark:bg-gray-900 rounded-lg p-2 border border-gray-200 dark:border-gray-700 transition-all hover:border-indigo-500">
                                <img src="{{ asset('storage/' . $file) }}" alt="{{ $char }}" class="w-full h-auto aspect-square object-contain bg-white dark:bg-gray-800 rounded">
                                
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 truncate text-center" title="{{ basename($file) }}">
                                    {{ basename($file) }}
                                </p>

                                <form action="{{ route('admin.dataset.destroy') }}" method="POST" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $file }}">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-transform shadow-lg" title="Hapus Gambar">
                                        <i class="fas fa-trash-alt w-4 h-4 flex items-center justify-center"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center border border-gray-200 dark:border-gray-700">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mb-4">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Dataset Kosong</h3>
                <p class="text-gray-500 dark:text-gray-400">Belum ada data gambar yang dikirimkan.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection