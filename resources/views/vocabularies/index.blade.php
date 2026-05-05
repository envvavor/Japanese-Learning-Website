@extends('layouts.app')

@section('title', 'Kosakata Jepang — Manabu')

@push('styles')
<style>
    /* Sembunyikan scrollbar untuk menu filter di mobile agar terlihat rapi */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        {{-- Header Section (Tombol Kembali Sudah Diperbaiki) --}}
        <div class="flex items-center gap-3 sm:gap-5 mb-6 sm:mb-10">
            {{-- Tombol Kembali Square (Muncul di Mobile & Desktop) --}}
            <a href="{{ route('dashboard') }}"
                class="w-11 h-11 sm:w-16 sm:h-16 flex items-center justify-center bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:border-b-2 active:translate-y-1 transition-all shrink-0 shadow-sm">
                <i class="fas fa-arrow-left text-lg sm:text-2xl"></i>
            </a>
            
            <div>
                <h1 class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider leading-tight">
                    Kosakata <span class="text-indigo-500">Jepang</span>
                </h1>
                <p class="text-[10px] sm:text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    {{ number_format($vocabularies->total()) }} Kata · JLPT & JMDict
                </p>
            </div>
        </div>

        {{-- Level Filter Badges (Scrollable on Mobile) --}}
        <div class="flex overflow-x-auto no-scrollbar pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap gap-2 sm:gap-3 mb-4 sm:mb-6">
            @php
                $levelColors = [
                    ''     => ['bg' => 'bg-slate-800 dark:bg-white',   'text' => 'text-white dark:text-slate-900',   'border' => 'border-slate-700 dark:border-gray-300', 'shadow' => 'border-b-slate-900 dark:border-b-gray-400'],
                    'N5'   => ['bg' => 'bg-sky-500',     'text' => 'text-white', 'border' => 'border-sky-600', 'shadow' => 'border-b-sky-700'],
                    'N4'   => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'border' => 'border-emerald-600', 'shadow' => 'border-b-emerald-700'],
                    'N3'   => ['bg' => 'bg-amber-500',   'text' => 'text-white', 'border' => 'border-amber-600', 'shadow' => 'border-b-amber-700'],
                    'N2'   => ['bg' => 'bg-orange-500',  'text' => 'text-white', 'border' => 'border-orange-600', 'shadow' => 'border-b-orange-700'],
                    'N1'   => ['bg' => 'bg-rose-500',    'text' => 'text-white', 'border' => 'border-rose-600', 'shadow' => 'border-b-rose-700'],
                    'none' => ['bg' => 'bg-violet-500',  'text' => 'text-white', 'border' => 'border-violet-600', 'shadow' => 'border-b-violet-700'],
                ];
                $levelLabels = ['' => 'Semua', 'N5' => 'N5', 'N4' => 'N4', 'N3' => 'N3', 'N2' => 'N2', 'N1' => 'N1', 'none' => 'JMDict'];
            @endphp

            @foreach($levelLabels as $lvl => $label)
            @php
                $isActive = ($level === $lvl);
                $c = $levelColors[$lvl];
                $count = $lvl === 'none' ? ($counts[''] ?? 0) : ($lvl ? ($counts[$lvl] ?? 0) : $vocabularies->total());
            @endphp
            <a href="{{ route('vocabulary.index', array_filter(['level' => $lvl, 'q' => $search])) }}"
               class="inline-flex shrink-0 items-center gap-1.5 sm:gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl border-2 font-black text-xs sm:text-sm uppercase tracking-widest transition-all active:translate-y-1 shadow-sm
                      {{ $isActive 
                          ? $c['bg'] . ' ' . $c['text'] . ' ' . $c['border'] . ' border-b-[4px] sm:border-b-[6px] ' . $c['shadow'] . ' active:border-b-2' 
                          : 'bg-white dark:bg-gray-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-gray-700 border-b-[4px] sm:border-b-[6px] hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2' }}">
                {{ $label }}
                @if($lvl)
                <span class="text-[9px] sm:text-[10px] font-black opacity-80 bg-black/10 dark:bg-white/10 px-1.5 sm:px-2 py-0.5 rounded-md sm:rounded-lg">
                    {{ number_format($lvl === 'none' ? ($counts[''] ?? 0) : ($counts[$lvl] ?? 0)) }}
                </span>
                @endif
            </a>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('vocabulary.index') }}" class="mb-6 sm:mb-10">
            @if($level)
                <input type="hidden" name="level" value="{{ $level }}">
            @endif
            <div class="flex gap-2 sm:gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm sm:text-lg"></i>
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Cari kata..."
                           class="w-full pl-10 sm:pl-14 pr-3 sm:pr-4 py-3 sm:py-4 rounded-xl sm:rounded-[1.25rem] border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-800 dark:text-gray-200 font-bold text-sm sm:text-base placeholder-slate-400 focus:outline-none focus:ring-0 focus:border-indigo-400 dark:focus:border-indigo-600 transition-all shadow-sm">
                </div>
                <button type="submit"
                        class="px-4 sm:px-8 py-3 sm:py-4 bg-indigo-600 border-2 border-b-[4px] sm:border-b-[6px] border-indigo-800 text-white font-black text-xs sm:text-sm uppercase tracking-widest rounded-xl sm:rounded-[1.25rem] hover:brightness-110 transition-all active:border-b-2 active:translate-y-1 shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> <span class="hidden sm:inline">Cari</span>
                </button>
                @if($search)
                <a href="{{ route('vocabulary.index', $level ? ['level' => $level] : []) }}"
                   class="px-4 sm:px-6 py-3 sm:py-4 bg-rose-50 dark:bg-rose-900/30 border-2 border-b-[4px] sm:border-b-[6px] border-rose-200 dark:border-rose-800 text-rose-500 font-black text-sm sm:text-xl rounded-xl sm:rounded-[1.25rem] transition-all hover:brightness-105 active:border-b-2 active:translate-y-1 shadow-sm flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>

        {{-- Results --}}
        @if($vocabularies->isEmpty())
            {{-- Empty State (Gamified) --}}
            <div class="bg-white dark:bg-gray-800 border-4 border-dashed border-slate-300 dark:border-gray-600 rounded-[2rem] p-10 sm:p-16 text-center shadow-sm">
                <i class="fas fa-search-minus text-5xl sm:text-6xl text-slate-300 dark:text-gray-600 mb-4 animate-pulse"></i>
                <h3 class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Kata Tidak Ditemukan</h3>
                <p class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">Coba gunakan kata kunci lain atau ubah filter level JLPT di atas.</p>
            </div>
        @else
            {{-- GRID RESPONSIVE: 2 Kolom di HP agar muat banyak --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-6 mb-8 sm:mb-10">
                @foreach($vocabularies as $vocab)
                @php
                    $colorMap = [
                        'N1' => ['badge' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',    'glow' => 'hover:border-rose-300 dark:hover:border-rose-700'],
                        'N2' => ['badge' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',  'glow' => 'hover:border-orange-300 dark:hover:border-orange-700'],
                        'N3' => ['badge' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',   'glow' => 'hover:border-amber-300 dark:hover:border-amber-700'],
                        'N4' => ['badge' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', 'glow' => 'hover:border-emerald-300 dark:hover:border-emerald-700'],
                        'N5' => ['badge' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800',    'glow' => 'hover:border-sky-300 dark:hover:border-sky-700'],
                    ];
                    $defaultColor = ['badge' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 border-violet-200 dark:border-violet-800', 'glow' => 'hover:border-violet-300 dark:hover:border-violet-700'];
                    $c = $colorMap[$vocab->jlpt_level] ?? $defaultColor;
                @endphp
                
                {{-- Vocab Card Gamified Compact --}}
                <div class="bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 {{ $c['glow'] }} rounded-2xl sm:rounded-[1.5rem] p-4 sm:p-5 transition-all hover:-translate-y-1 active:border-b-2 active:translate-y-[2px] sm:active:translate-y-[4px] group relative shadow-sm flex flex-col h-full">
                    
                    {{-- Header Level & Copy --}}
                    <div class="flex items-start justify-between mb-3">
                        <span class="px-2 sm:px-2.5 py-1 rounded-lg sm:rounded-xl border-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest {{ $c['badge'] }}">
                            {{ $vocab->jlpt_level ?? '辞書' }}
                        </span>
                        
                        <div class="flex gap-2">
                            {{-- Tombol Suara --}}
                            @php
                                $textToSpeak = addslashes($vocab->furigana ?: $vocab->original);
                            @endphp
                            {{-- Tombol Salin --}}
                            <button onclick="copyText('{{ $vocab->original }}')"
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border-2 border-b-[3px] border-slate-200 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-slate-400 hover:text-indigo-500 hover:border-indigo-300 dark:hover:border-indigo-600 active:border-b-2 active:translate-y-0.5 transition-all flex items-center justify-center opacity-100 sm:opacity-0 sm:group-hover:opacity-100 shrink-0"
                                    title="Salin Kosakata">
                                <i class="fas fa-copy text-xs"></i>
                            </button>
                            
                            <button onclick="window.speakText('{{ $textToSpeak }}')"
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border-2 border-b-[3px] border-slate-200 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-slate-400 hover:text-[#1cb0f6] hover:border-[#1cb0f6]/30 active:border-b-2 active:translate-y-0.5 transition-all flex items-center justify-center shrink-0"
                                    title="Dengarkan Cara Baca">
                                <i class="fas fa-volume-up text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Kata Jepang --}}
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white mb-1 sm:mb-1.5 leading-tight tracking-wide break-words">
                        {{ $vocab->original }}
                    </p>

                    {{-- Furigana --}}
                    @if($vocab->furigana && $vocab->furigana !== $vocab->original)
                        <p class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 sm:mb-3 bg-slate-50 dark:bg-gray-900/50 inline-block px-2 sm:px-3 py-0.5 sm:py-1 rounded-md sm:rounded-lg border-2 border-dashed border-slate-200 dark:border-gray-700 self-start break-words">
                            {{ $vocab->furigana }}
                        </p>
                    @endif

                    {{-- Arti Bahasa Indonesia (Utama) --}}
                    <p class="text-sm sm:text-base font-black text-slate-700 dark:text-slate-200 leading-snug mt-auto border-t-2 border-dashed border-slate-100 dark:border-gray-700 pt-2 sm:pt-3">
                        {{ $vocab->indonesian ?? 'Menerjemahkan...' }}
                    </p>

                    {{-- Arti Bahasa Inggris (Sub/Tambahan) --}}
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-1 italic">
                        {{ $vocab->english }}
                    </p>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-8 sm:mt-12">
                {{ $vocabularies->links() }}
            </div>
        @endif

    </div>
</div>

{{-- Toast copy notif --}}
<div id="copy-toast"
     class="fixed bottom-6 right-6 sm:bottom-8 sm:right-8 px-5 py-3 sm:px-6 sm:py-4 bg-emerald-500 border-2 border-b-[6px] border-emerald-700 text-white text-xs sm:text-sm font-black uppercase tracking-widest rounded-2xl shadow-xl translate-y-24 opacity-0 transition-all duration-300 pointer-events-none z-50 flex items-center gap-2 sm:gap-3">
    <i class="fas fa-check-circle text-lg sm:text-xl"></i> <span>Teks Disalin!</span>
</div>

@push('scripts')
<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.getElementById('copy-toast');
        toast.classList.remove('translate-y-24', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-24', 'opacity-0'), 2000);
    });
}



let synth = null;
try { 
    if ('speechSynthesis' in window) synth = window.speechSynthesis; 
} catch(e) {}

window.speakText = function(text) {
    if (!text || !synth) return;
    try { 
        synth.cancel(); 
        const u = new SpeechSynthesisUtterance(text); 
        u.lang = 'ja-JP'; 
        synth.speak(u); 
    } catch(e) { 
        console.error("Gagal memutar suara:", e); 
    }
};
</script>
@endpush
@endsection