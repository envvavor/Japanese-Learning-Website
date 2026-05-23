@extends('layouts.admin')

@section('title', 'Kelola Kosakata')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar Kosakata</h3>
    <a href="{{ route('admin.vocabularies.create') }}" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-all flex items-center shrink-0">
        <i class="fas fa-plus mr-2"></i> Tambah Kosakata
    </a>
</div>

<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form action="{{ route('admin.vocabularies.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                   placeholder="Cari kosakata, furigana, arti...">
        </div>

        <div class="w-full sm:w-48 shrink-0">
            <select name="jlpt_level" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm cursor-pointer">
                <option value="">Semua Level</option>
                <option value="N5" {{ request('jlpt_level') == 'N5' ? 'selected' : '' }}>N5</option>
                <option value="N4" {{ request('jlpt_level') == 'N4' ? 'selected' : '' }}>N4</option>
                <option value="N3" {{ request('jlpt_level') == 'N3' ? 'selected' : '' }}>N3</option>
                <option value="N2" {{ request('jlpt_level') == 'N2' ? 'selected' : '' }}>N2</option>
                <option value="N1" {{ request('jlpt_level') == 'N1' ? 'selected' : '' }}>N1</option>
                <option value="none" {{ request('jlpt_level') == 'none' ? 'selected' : '' }}>Tanpa Level</option>
            </select>
        </div>

        <div class="flex gap-2 w-full sm:w-auto shrink-0">
            <button type="submit" class="flex-1 sm:flex-none bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            @if(request('search') || request('jlpt_level'))
                <a href="{{ route('admin.vocabularies.index') }}" class="flex items-center justify-center px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-800 transition-colors shadow-sm" title="Reset Filter">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kosakata</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Furigana</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Arti (EN)</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Arti (ID)</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($vocabularies as $vocab)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $vocab->original }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $vocab->furigana ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                            {{ $vocab->english }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                            {{ $vocab->indonesian ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($vocab->jlpt_level)
                                @php
                                    $levelColors = [
                                        'N1' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800',
                                        'N2' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800',
                                        'N3' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                                        'N4' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                                        'N5' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:border-sky-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $levelColors[$vocab->jlpt_level] ?? 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-900/30 dark:text-gray-400 dark:border-gray-800' }}">
                                    {{ $vocab->jlpt_level }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.vocabularies.edit', $vocab) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg transition-colors border border-indigo-100 dark:border-indigo-800" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                <form action="{{ route('admin.vocabularies.destroy', $vocab) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kosakata {{ $vocab->original }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg transition-colors border border-red-100 dark:border-red-800" title="Hapus">
                                        <i class="fas fa-trash-alt w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-search text-2xl"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 font-semibold mb-1 text-lg">Kosakata tidak ditemukan</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Coba gunakan kata kunci atau level yang berbeda.</p>
                                <a href="{{ route('admin.vocabularies.index') }}" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 font-medium transition-colors">
                                    Hapus Filter
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vocabularies->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $vocabularies->links() }}
        </div>
    @endif
</div>
@endsection
