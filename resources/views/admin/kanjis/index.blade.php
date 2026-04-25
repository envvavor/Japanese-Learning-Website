@extends('layouts.admin')

@section('title', 'Kelola Huruf')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar Huruf</h3>
    <a href="{{ route('admin.kanjis.create') }}" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-all flex items-center shrink-0">
        <i class="fas fa-plus mr-2"></i> Tambah Huruf
    </a>
</div>

{{-- === KOTAK PENCARIAN & FILTER === --}}
<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form action="{{ route('admin.kanjis.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        
        {{-- Input Search --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                   placeholder="Cari karakter, arti, atau cara baca...">
        </div>

        {{-- Dropdown Kategori --}}
        <div class="w-full sm:w-48 shrink-0">
            <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm cursor-pointer">
                <option value="">Semua Kategori</option>
                <option value="kanji" {{ request('category') == 'kanji' ? 'selected' : '' }}>Kanji</option>
                <option value="hiragana" {{ request('category') == 'hiragana' ? 'selected' : '' }}>Hiragana</option>
                <option value="katakana" {{ request('category') == 'katakana' ? 'selected' : '' }}>Katakana</option>
            </select>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-2 w-full sm:w-auto shrink-0">
            <button type="submit" class="flex-1 sm:flex-none bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            {{-- Tombol Reset (Hanya muncul jika sedang mencari) --}}
            @if(request('search') || request('category'))
                <a href="{{ route('admin.kanjis.index') }}" class="flex items-center justify-center px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-800 transition-colors shadow-sm" title="Reset Filter">
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Karakter</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Arti</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bab</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($kanjis as $kanji)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-3xl font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 w-12 h-12 flex items-center justify-center rounded-lg shadow-inner">{{ $kanji->character }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $kanji->meaning }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex space-x-3">
                                <span title="Kunyomi"><i class="fas fa-book-reader mr-1 opacity-50"></i> K: <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $kanji->kunyomi ?: '-' }}</span></span>
                                <span title="Onyomi"><i class="fas fa-headphones mr-1 opacity-50"></i> O: <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $kanji->onyomi ?: '-' }}</span></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border 
                                {{ $kanji->category == 'kanji' ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' : ($kanji->category == 'hiragana' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800' : 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800') }}">
                                {{ ucfirst($kanji->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-bold">
                            {{ $kanji->level ? 'Bab ' . $kanji->level : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.kanjis.edit', $kanji) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg transition-colors border border-indigo-100 dark:border-indigo-800" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                <form action="{{ route('admin.kanjis.destroy', $kanji) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kanji {{ $kanji->character }}?');">
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
                                <p class="text-gray-600 dark:text-gray-300 font-semibold mb-1 text-lg">Pencarian tidak ditemukan</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Coba gunakan kata kunci atau kategori yang berbeda.</p>
                                <a href="{{ route('admin.kanjis.index') }}" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 font-medium transition-colors">
                                    Hapus Filter
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kanjis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $kanjis->links() }}
        </div>
    @endif
</div>
@endsection