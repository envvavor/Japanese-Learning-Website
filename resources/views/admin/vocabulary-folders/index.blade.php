@extends('layouts.admin')

@section('title', 'Kelola Folder Kosakata')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Folder Kosakata</h3>
    <a href="{{ route('admin.vocabulary-folders.create') }}" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-all flex items-center shrink-0">
        <i class="fas fa-plus mr-2"></i> Buat Folder
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Warna</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah Kata</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($folders as $folder)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <span class="text-base font-bold text-gray-900 dark:text-white">{{ $folder->name }}</span>
                                @if($folder->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-xs">{{ $folder->description }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="w-8 h-8 rounded-lg inline-flex items-center justify-center bg-{{ $folder->color }}-500 text-white text-xs font-bold">
                                <i class="fas fa-folder"></i>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800">
                                {{ $folder->items_count }} kata
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $folder->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.vocabulary-folders.show', $folder) }}" class="text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20 p-2 rounded-lg transition-colors border border-emerald-100 dark:border-emerald-800" title="Lihat & Tambah Kata">
                                    <i class="fas fa-eye w-4"></i>
                                </a>
                                <a href="{{ route('admin.vocabulary-folders.edit', $folder) }}" class="text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg transition-colors border border-indigo-100 dark:border-indigo-800" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                <form action="{{ route('admin.vocabulary-folders.destroy', $folder) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus folder {{ $folder->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg transition-colors border border-red-100 dark:border-red-800" title="Hapus">
                                        <i class="fas fa-trash-alt w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-folder-open text-2xl"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 font-semibold mb-1 text-lg">Belum ada folder</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Buat folder baru untuk mengelompokkan kosakata.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($folders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $folders->links() }}
        </div>
    @endif
</div>
@endsection
