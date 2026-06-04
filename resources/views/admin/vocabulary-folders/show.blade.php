@extends('layouts.admin')

@section('title', 'Folder: ' . $folder->name)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.vocabulary-folders.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $folder->name }}</h3>
        @if($folder->description)
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $folder->description }}</p>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.vocabulary-folders.edit', $folder) }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition-all flex items-center">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6"
     x-data="adminFolderSearch()" x-init="init()">
    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
        <i class="fas fa-plus-circle mr-1 text-indigo-500"></i> Tambah Kata ke Folder
    </h4>
    <div class="relative">
        <input type="text"
               x-model="q"
               @input.debounce.300ms="search()"
               @focus="showResults = results.length > 0"
               @click.away="showResults = false"
               placeholder="Cari kosakata..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

        <div x-show="showResults && results.length > 0" x-transition
             class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            <template x-for="v in results" :key="v.id">
                <div class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <span class="font-bold text-gray-900 dark:text-white text-sm" x-text="v.original"></span>
                        <span class="text-xs text-gray-400 ml-1" x-text="v.furigana"></span>
                        <span class="text-xs text-gray-500 ml-2" x-text="v.indonesian || v.english"></span>
                    </div>
                    <button @click="addWord(v.id)"
                            class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $vocabularies->total() }} kata dalam folder</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kosakata</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Furigana</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Arti (ID)</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($vocabularies as $vocab)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-3 whitespace-nowrap text-base font-bold text-gray-900 dark:text-white">{{ $vocab->original }}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $vocab->furigana ?: '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $vocab->indonesian ?: $vocab->english }}</td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                            {{ $vocab->jlpt_level ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">
                        <form action="{{ route('admin.vocabulary-folders.remove-word', [$folder, $vocab]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kata ini dari folder?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg transition-colors border border-red-100 dark:border-red-800" title="Hapus dari folder">
                                <i class="fas fa-trash-alt w-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-book-open text-3xl mb-3 text-gray-300 dark:text-gray-600"></i>
                        <p class="font-semibold">Belum ada kata di folder ini</p>
                        <p class="text-sm">Gunakan pencarian di atas untuk menambahkan.</p>
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

@push('scripts')
<script>
function adminFolderSearch() {
    return {
        q: '',
        results: [],
        showResults: false,
        init() {},
        async search() {
            if (this.q.length < 1) { this.results = []; this.showResults = false; return; }
            try {
                const res = await fetch(`{{ route('admin.vocabulary-folders.api.search') }}?q=${encodeURIComponent(this.q)}`);
                this.results = await res.json();
                this.showResults = this.results.length > 0;
            } catch(e) {}
        },
        addWord(vocabId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.vocabulary-folders.add-word', $folder) }}`;
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="vocabulary_id" value="${vocabId}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
