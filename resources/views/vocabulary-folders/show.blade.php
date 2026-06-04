@extends('layouts.app')

@section('title', $folder->name . ' — Manabu')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20"
     x-data="folderShow()"
     x-init="init()">

    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5 mb-6 sm:mb-10">
            <a href="{{ route('vocabulary-folders.index') }}"
                class="w-11 h-11 sm:w-16 sm:h-16 flex items-center justify-center bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:border-b-2 active:translate-y-1 transition-all shrink-0 shadow-sm">
                <i class="fas fa-arrow-left text-lg sm:text-2xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider leading-tight">
                    {{ $folder->name }}
                </h1>
                <p class="text-[10px] sm:text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    {{ $folder->description ?? 'Folder Kosakata' }} · {{ $stats['total'] }} Kata
                </p>
            </div>
            <div class="flex gap-2 sm:gap-3">
                @if($stats['total'] > 0)
                <a href="{{ route('vocabulary-folders.practice', $folder) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 sm:px-6 sm:py-3 bg-[#1cb0f6] border-2 border-b-[4px] sm:border-b-[6px] border-[#1899d6] text-white font-black text-xs sm:text-sm uppercase tracking-widest rounded-xl sm:rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-play"></i> Latihan
                </a>
                @endif
                @if($canEdit)
                <a href="{{ route('vocabulary-folders.edit', $folder) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 sm:px-6 sm:py-3 bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 font-black text-xs sm:text-sm uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-pen"></i> <span class="hidden sm:inline">Edit</span>
                </a>
                @endif
            </div>
        </div>

        @if($stats['total'] > 0)
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Progres Latihan</span>
                <span class="text-xs font-black text-emerald-500">{{ $stats['correct'] }}/{{ $stats['total'] }} Benar ({{ $stats['percent'] }}%)</span>
            </div>
            <div class="h-3 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden border border-slate-200 dark:border-gray-600">
                <div class="h-full bg-emerald-400 rounded-full transition-all duration-500" style="width: {{ $stats['percent'] }}%"></div>
            </div>
        </div>
        @endif

        @if($canEdit)
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-sm">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Kata ke Folder
            </h3>
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="searchVocab()"
                       @focus="showResults = searchResults.length > 0"
                       @click.away="showResults = false"
                       placeholder="Cari kosakata..."
                       class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-b-[4px] border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-gray-200 font-bold text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-600 transition-all">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <div x-show="showResults && searchResults.length > 0" x-transition
                     class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 border-2 border-slate-200 dark:border-gray-700 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                    <template x-for="vocab in searchResults" :key="vocab.id">
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 dark:hover:bg-gray-700 border-b border-slate-100 dark:border-gray-700 last:border-0">
                            <div>
                                <span class="font-black text-slate-800 dark:text-white text-sm" x-text="vocab.original"></span>
                                <span class="text-xs font-bold text-slate-400 ml-2" x-text="vocab.furigana"></span>
                                <p class="text-xs font-bold text-slate-500" x-text="vocab.indonesian || vocab.english"></p>
                            </div>
                            <button @click="addWordToFolder(vocab.id)"
                                    class="shrink-0 w-8 h-8 rounded-lg border-2 border-b-[3px] border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 hover:bg-emerald-100 active:border-b-2 active:translate-y-0.5 transition-all flex items-center justify-center">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800 rounded-xl p-4 text-emerald-700 dark:text-emerald-400 font-bold text-sm flex items-center gap-2" x-data="{show:true}" x-show="show" x-transition>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button @click="show=false" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-rose-200 dark:border-rose-800 rounded-xl p-4 text-rose-700 dark:text-rose-400 font-bold text-sm flex items-center gap-2" x-data="{show:true}" x-show="show" x-transition>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button @click="show=false" class="ml-auto text-rose-400 hover:text-rose-600"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if($vocabularies->isEmpty())
        <div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-10 sm:p-16 text-center shadow-sm">
            <i class="fas fa-book-open text-5xl sm:text-6xl text-slate-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Belum Ada Kata</h3>
            <p class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">Gunakan pencarian di atas untuk menambahkan kosakata ke folder ini.</p>
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-6 mb-8 sm:mb-10">
            @foreach($vocabularies as $vocab)
            @php
                $hasProgress = isset($progressMap[$vocab->id]);
                $isCorrect = $hasProgress && $progressMap[$vocab->id];
                $borderColor = $hasProgress ? ($isCorrect ? 'border-emerald-300 dark:border-emerald-700' : 'border-rose-300 dark:border-rose-700') : 'border-slate-200 dark:border-gray-700';
            @endphp
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] {{ $borderColor }} rounded-2xl sm:rounded-[1.5rem] p-4 sm:p-5 transition-all hover:-translate-y-1 active:border-b-2 active:translate-y-[2px] group relative shadow-sm flex flex-col h-full">
                <div class="flex items-start justify-between mb-3">
                    <span class="px-2 py-1 rounded-lg border-2 text-[9px] font-black uppercase tracking-widest
                        {{ $vocab->jlpt_level ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800' : 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 border-violet-200 dark:border-violet-800' }}">
                        {{ $vocab->jlpt_level ?? 'JMDict' }}
                    </span>
                    <div class="flex gap-1.5">
                        @if($hasProgress)
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs {{ $isCorrect ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-500' }}">
                            <i class="fas {{ $isCorrect ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        @endif
                        @if($canEdit)
                        <form method="POST" action="{{ route('vocabulary-folders.remove-word', [$folder, $vocab]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-lg border-2 border-slate-200 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-slate-400 hover:text-rose-500 hover:border-rose-300 active:translate-y-0.5 transition-all flex items-center justify-center opacity-100 sm:opacity-0 sm:group-hover:opacity-100"
                                    onclick="return confirm('Hapus kata ini dari folder?')">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white mb-1 leading-tight tracking-wide break-words">
                    {{ $vocab->original }}
                </p>
                @if($vocab->furigana && $vocab->furigana !== $vocab->original)
                <p class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 bg-slate-50 dark:bg-gray-900/50 inline-block px-2 py-0.5 rounded-md border-2 border-dashed border-slate-200 dark:border-gray-700 self-start break-words">
                    {{ $vocab->furigana }}
                </p>
                @endif

                <p class="text-sm sm:text-base font-black text-slate-700 dark:text-slate-200 leading-snug mt-auto border-t-2 border-dashed border-slate-100 dark:border-gray-700 pt-2">
                    {{ $vocab->indonesian ?? 'Menerjemahkan...' }}
                </p>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-1 italic">
                    {{ $vocab->english }}
                </p>
            </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-8 sm:mt-12">
            {{ $vocabularies->links() }}
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function folderShow() {
    return {
        searchQuery: '',
        searchResults: [],
        showResults: false,
        init() {},
        async searchVocab() {
            if (this.searchQuery.length < 1) {
                this.searchResults = [];
                this.showResults = false;
                return;
            }
            try {
                const res = await fetch(`{{ route('vocabulary-folders.api.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                this.searchResults = await res.json();
                this.showResults = this.searchResults.length > 0;
            } catch(e) {
                console.error(e);
            }
        },
        async addWordToFolder(vocabId) {
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('vocabulary-folders.add-word', $folder) }}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="vocabulary_id" value="${vocabId}">
                `;
                document.body.appendChild(form);
                form.submit();
            } catch(e) {
                console.error(e);
            }
        }
    }
}
</script>
@endpush
@endsection
