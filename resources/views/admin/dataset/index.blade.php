@extends('layouts.admin')

@section('title', 'Dataset Gambar')

@section('content')
<div x-data="{ searchQuery: '' }">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Dataset</h1>
            <p class="text-gray-600 dark:text-gray-400">Total Karakter: {{ count($datasets) }} &middot; Total Gambar: {{ array_sum($datasets) }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
            {{-- Auto-Save Toggle --}}
            <div x-data="autoSaveToggle()" class="flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    <i class="fas fa-save mr-1" :class="enabled ? 'text-green-500' : 'text-gray-400'"></i> Auto-Save
                </span>
                <button @click="toggle()" 
                        :class="enabled ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :disabled="loading">
                    <span :class="enabled ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"></span>
                </button>
                <span x-text="loading ? '...' : (enabled ? 'ON' : 'OFF')" 
                      :class="loading ? 'text-gray-300' : (enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-400')"
                      class="text-xs font-bold uppercase tracking-wider w-6"></span>
            </div>

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
        @forelse($datasets as $char => $fileCount)
            <div x-data="datasetAccordion('{{ $char }}', {{ $fileCount }})" 
                 x-show="searchQuery === '' || char.toLowerCase().includes(searchQuery.toLowerCase())" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div @click="toggle()" class="cursor-pointer px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-chevron-right text-gray-400 transition-transform duration-300" :class="{'rotate-90': open}"></i>
                        
                        <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $char }}</span>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                            <span x-text="totalFiles"></span> Gambar
                        </span>
                    </div>
                    
                    <div @click.stop>
                        <a href="{{ route('admin.dataset.download', $char) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-file-archive mr-2"></i> Download
                        </a>
                    </div>
                </div>

                <div x-show="open" x-transition x-cloak class="p-6">
                    {{-- Loading State --}}
                    <div x-show="loading" class="flex items-center justify-center py-8">
                        <div class="inline-flex items-center gap-3 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin text-indigo-500 text-lg"></i>
                            <span class="text-sm font-medium">Memuat gambar...</span>
                        </div>
                    </div>

                    {{-- Images Grid --}}
                    <div x-show="!loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4" x-ref="grid">
                        <template x-for="file in files" :key="file.path">
                            <div class="relative group bg-gray-100 dark:bg-gray-900 rounded-lg p-2 border border-gray-200 dark:border-gray-700 transition-all hover:border-indigo-500">
                                <img :src="file.url" :alt="char" class="w-full h-auto aspect-square object-contain bg-white dark:bg-gray-800 rounded" loading="lazy">
                                
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 truncate text-center" :title="file.name" x-text="file.name"></p>

                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <button @click="deleteFile(file.path)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-transform shadow-lg" title="Hapus Gambar">
                                        <i class="fas fa-trash-alt w-4 h-4 flex items-center justify-center"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Load More --}}
                    <div x-show="!loading && hasMore" class="flex justify-center mt-6">
                        <button @click="loadMore()" 
                                :disabled="loadingMore"
                                class="inline-flex items-center px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors border border-gray-300 dark:border-gray-600 disabled:opacity-50">
                            <template x-if="loadingMore">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                            </template>
                            <template x-if="!loadingMore">
                                <i class="fas fa-plus mr-2"></i>
                            </template>
                            <span x-text="loadingMore ? 'Memuat...' : 'Muat Lebih Banyak (' + files.length + '/' + totalFiles + ')'"></span>
                        </button>
                    </div>

                    {{-- Info sisa --}}
                    <div x-show="!loading && !hasMore && files.length > 0" class="text-center mt-4">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            <i class="fas fa-check-circle mr-1 text-green-500"></i> 
                            Semua <span x-text="totalFiles"></span> gambar dimuat.
                        </p>
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

<script>
function datasetAccordion(char, initialCount) {
    return {
        char: char,
        open: false,
        loading: false,
        loadingMore: false,
        files: [],
        totalFiles: initialCount,
        page: 1,
        hasMore: false,
        loaded: false,

        toggle() {
            this.open = !this.open;
            // Lazy load: hanya fetch saat pertama kali dibuka
            if (this.open && !this.loaded) {
                this.fetchFiles(1);
            }
        },

        async fetchFiles(page) {
            if (page === 1) {
                this.loading = true;
            } else {
                this.loadingMore = true;
            }

            try {
                const url = `/admin/dataset/files/${encodeURIComponent(this.char)}?page=${page}`;
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (page === 1) {
                    this.files = data.files;
                } else {
                    this.files = this.files.concat(data.files);
                }

                this.totalFiles = data.total;
                this.page = data.page;
                this.hasMore = data.has_more;
                this.loaded = true;
            } catch (err) {
                console.error('Gagal memuat file dataset:', err);
            } finally {
                this.loading = false;
                this.loadingMore = false;
            }
        },

        loadMore() {
            if (!this.loadingMore && this.hasMore) {
                this.fetchFiles(this.page + 1);
            }
        },

        async deleteFile(path) {
            if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) return;

            try {
                const response = await fetch('{{ route("admin.dataset.destroy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE',
                        path: path
                    })
                });

                // Hapus dari UI tanpa reload halaman
                this.files = this.files.filter(f => f.path !== path);
                this.totalFiles = Math.max(0, this.totalFiles - 1);

                // Jika semua file yang di-load sudah habis tapi masih ada di server, refetch
                if (this.files.length === 0 && this.totalFiles > 0) {
                    this.page = 1;
                    this.loaded = false;
                    this.fetchFiles(1);
                }
            } catch (err) {
                console.error('Gagal menghapus:', err);
                alert('Gagal menghapus gambar.');
            }
        }
    };
}

function autoSaveToggle() {
    return {
        enabled: false, // Default ke false agar tidak flash "ON"
        loading: true,
        
        init() {
            fetch('/api/dataset/auto-save-status')
                .then(r => r.json())
                .then(data => { this.enabled = data.auto_save; this.loading = false; })
                .catch(() => { this.loading = false; });
        },
        
        async toggle() {
            this.loading = true;
            try {
                const res = await fetch('/api/dataset/auto-save-toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ enabled: !this.enabled })
                });
                const data = await res.json();
                this.enabled = data.auto_save;
            } catch (e) {
                console.error('Toggle failed:', e);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection