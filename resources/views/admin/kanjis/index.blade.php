@extends('layouts.admin')

@section('title', 'Kelola Kanji')

@section('content')
<div class="mb-6 flex space-x-4 items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">Daftar Kanji</h3>
    <a href="{{ route('admin.kanjis.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Kanji
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Karakter</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Arti</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Level</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kanjis as $kanji)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-3xl font-bold text-gray-900 bg-gray-100 w-12 h-12 flex items-center justify-center rounded-lg shadow-inner">{{ $kanji->character }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-800">{{ $kanji->meaning }}</div>
                            <div class="text-xs text-gray-500 mt-1 flex space-x-3">
                                <span title="Kunyomi"><i class="fas fa-book-reader mr-1 opacity-50"></i> K: <span class="text-gray-700 font-medium">{{ $kanji->kunyomi ?: '-' }}</span></span>
                                <span title="Onyomi"><i class="fas fa-headphones mr-1 opacity-50"></i> O: <span class="text-gray-700 font-medium">{{ $kanji->onyomi ?: '-' }}</span></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border 
                                {{ $kanji->category == 'kanji' ? 'bg-red-50 text-red-700 border-red-200' : ($kanji->category == 'hiragana' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-green-50 text-green-700 border-green-200') }}">
                                {{ ucfirst($kanji->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">
                            {{ $kanji->level ? 'N' . $kanji->level : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.kanjis.edit', $kanji) }}" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 bg-indigo-50 p-2 rounded-lg transition-colors border border-indigo-100" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                <form action="{{ route('admin.kanjis.destroy', $kanji) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kanji {{ $kanji->character }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 hover:bg-red-100 bg-red-50 p-2 rounded-lg transition-colors border border-red-100" title="Hapus">
                                        <i class="fas fa-trash-alt w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center bg-gray-50">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-semibold mb-1 text-lg">Belum ada data kanji</p>
                                <p class="text-gray-500 text-sm mb-4">Tambahkan data kanji pertama Anda sekarang.</p>
                                <a href="{{ route('admin.kanjis.create') }}" class="bg-white border border-indigo-200 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-50 font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Tambah Kanji
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kanjis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $kanjis->links() }}
        </div>
    @endif
</div>
@endsection
