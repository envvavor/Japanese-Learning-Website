@extends('layouts.admin')

@section('title', 'Kelola Materi')

@section('content')
<div class="mb-6 flex space-x-4 items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar Materi</h3>
    <a href="{{ route('admin.materis.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Materi
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800/50 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($materis as $materi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-file-alt text-indigo-500 dark:text-indigo-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $materi->title }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $materi->slug }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $materi->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('materi.show', $materi) }}" target="_blank" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30 bg-green-50 dark:bg-green-900/20 p-2 rounded-lg transition-colors border border-green-100 dark:border-green-800" title="Lihat">
                                    <i class="fas fa-eye w-4"></i>
                                </a>
                                <a href="{{ route('admin.materis.edit', $materi) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg transition-colors border border-indigo-100 dark:border-indigo-800" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                <form action="{{ route('admin.materis.destroy', $materi) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi \'{{ $materi->title }}\'?');">
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
                        <td colspan="4" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center bg-gray-50 dark:bg-gray-800">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 font-semibold mb-1 text-lg">Belum ada data materi</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Tambahkan materi pertama Anda sekarang.</p>
                                <a href="{{ route('admin.materis.create') }}" class="bg-white dark:bg-gray-700 border border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 px-4 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Tambah Materi
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($materis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $materis->links() }}
        </div>
    @endif
</div>
@endsection
