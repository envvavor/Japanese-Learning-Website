@extends('layouts.app')
@section('title', 'Roadmap Quiz — Manabu')

@section('content')
{{-- Game Board Wrapper --}}
<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-sky-100 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-950 font-sans pb-20 relative z-0 overflow-x-clip">

    {{-- Latar Belakang Ornamen (FontAwesome) --}}
    <i class="fas fa-cloud absolute top-20 left-10 text-6xl text-white dark:text-slate-700 opacity-60 drop-shadow-md pointer-events-none -z-10"></i>
    <i class="fas fa-cloud absolute top-80 right-10 text-5xl text-white dark:text-slate-700 opacity-60 drop-shadow-md pointer-events-none -z-10"></i>
    <i class="fas fa-tree absolute bottom-40 left-10 text-6xl text-indigo-600/10 dark:text-indigo-900/30 pointer-events-none -z-10"></i>
    <i class="fas fa-mountain absolute bottom-20 right-10 text-7xl text-slate-400/20 dark:text-slate-700/40 pointer-events-none -z-10"></i>

    {{-- Sticky HUD Header --}}
    <div class="sticky top-4 z-50 max-w-2xl mx-auto px-4 sm:px-6 pt-2">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-3xl p-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 flex justify-between items-center shadow-sm">
            <a href="{{ route('dashboard') }}" class="w-12 h-12 flex items-center justify-center bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-slate-300 font-bold rounded-2xl border-2 border-b-4 border-slate-200 dark:border-gray-600 hover:bg-slate-200 dark:hover:bg-gray-600 active:border-b-0 active:translate-y-1 transition-all">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-500 dark:text-indigo-400 rounded-xl flex items-center justify-center text-xl border-2 border-indigo-200 dark:border-indigo-800">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h1 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mt-1">Perjalanan Quiz</h1>
            </div>

            <div class="w-12 h-12 flex items-center justify-center bg-amber-100 dark:bg-amber-900/30 text-amber-500 rounded-2xl font-black border-2 border-b-4 border-amber-200 dark:border-amber-800">
                <i class="fas fa-star text-sm mr-1"></i> {{ $quizzesWithStatus->where('is_passed', true)->count() }}
            </div>
        </div>
    </div>

    {{-- Latihan Bebas Fixed Bottom Left --}}
    <div class="fixed bottom-6 left-4 sm:left-6 z-50 hidden sm:block">
        <a href="{{ route('quiz.index') }}" class="flex items-center justify-center p-4 rounded-3xl bg-white dark:bg-gray-800 border-2 border-b-[6px] border-indigo-200 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:border-indigo-300 transition-all shadow-lg active:translate-y-[4px] active:border-b-2 group" title="Latihan Bebas">
            <i class="fas fa-dumbbell text-2xl text-indigo-500"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap font-black ml-0 group-hover:ml-3 uppercase tracking-widest text-sm">Latihan Bebas</span>
        </a>
    </div>

    {{-- Mobile Floating Action Button (visible only on small screens) --}}
    <div class="fixed bottom-6 right-4 z-50 sm:hidden">
        <a href="{{ route('quiz.index') }}" class="flex items-center justify-center w-14 h-14 rounded-full bg-white dark:bg-gray-800 border-2 border-b-[4px] border-indigo-200 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 active:translate-y-[2px] active:border-b-2 transition-all shadow-lg">
            <i class="fas fa-dumbbell text-xl text-indigo-500"></i>
        </a>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 mt-8">
        
        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-100 dark:bg-rose-900/40 border-2 border-b-4 border-rose-300 dark:border-rose-800 rounded-2xl text-rose-700 dark:text-rose-400 font-bold text-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
        @endif

        @if($quizzesWithStatus->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] border-4 border-dashed border-slate-300 dark:border-gray-600 p-16 text-center mt-10 shadow-sm relative z-10">
            <i class="fas fa-tools text-6xl text-slate-400 dark:text-slate-500 mb-4 animate-bounce"></i>
            <h3 class="text-2xl font-black text-slate-700 dark:text-slate-300 mb-2">Peta Kosong!</h3>
            <p class="text-slate-500 dark:text-slate-400 font-bold">Misi belum dibuat oleh Admin. Tunggu update selanjutnya!</p>
        </div>
        @else
        
        {{-- GAME MAP CONTAINER --}}
        <div class="relative py-12 flex flex-col items-center">

            {{-- Bendera Start --}}
            <div class="mb-8 z-20 flex flex-col items-center">
                <div class="bg-white dark:bg-gray-800 px-6 py-2 rounded-full border-2 border-b-4 border-slate-200 dark:border-gray-700 font-black text-slate-500 dark:text-slate-400 text-sm uppercase tracking-widest shadow-sm flex items-center gap-2">
                    <i class="fas fa-play-circle text-blue-500"></i> Mulai
                </div>
            </div>

            @foreach($quizzesWithStatus as $i => $quiz)
            @php
                $isLocked  = $quiz['is_locked'];
                $isPassed  = $quiz['is_passed'];
                $bestScore = $quiz['best_score'];

                $cycle = $i % 4;
                $xClass = 'translate-x-0';
                if($cycle == 1) $xClass = '-translate-x-[65px]';
                elseif($cycle == 3) $xClass = 'translate-x-[65px]';

                $lineColor = $isPassed ? 'text-amber-400 dark:text-amber-500/80' : 'text-slate-300 dark:text-gray-600';
            @endphp

            <div class="relative z-10 flex flex-col items-center {{ $xClass }} mb-[24px] group w-full">

                {{-- SVG CONNECTORS --}}
                @if(!$loop->last)
                    @if($cycle == 0)
                        <svg class="absolute top-1/2 left-1/2 w-[65px] h-[104px] -z-10 {{ $lineColor }} overflow-visible pointer-events-none" style="transform: translate(-65px, 0);" viewBox="0 0 65 104"><path d="M 65 0 C 65 52, 0 52, 0 104" fill="none" stroke="currentColor" stroke-width="22" stroke-linecap="round"/></svg>
                    @elseif($cycle == 1 || $cycle == 2)
                        <svg class="absolute top-1/2 left-1/2 w-[65px] h-[104px] -z-10 {{ $lineColor }} overflow-visible pointer-events-none" style="transform: translate(0, 0);" viewBox="0 0 65 104"><path d="M 0 0 C 0 52, 65 52, 65 104" fill="none" stroke="currentColor" stroke-width="22" stroke-linecap="round"/></svg>
                    @elseif($cycle == 3)
                        <svg class="absolute top-1/2 left-1/2 w-[65px] h-[104px] -z-10 {{ $lineColor }} overflow-visible pointer-events-none" style="transform: translate(-65px, 0);" viewBox="0 0 65 104"><path d="M 65 0 C 65 52, 0 52, 0 104" fill="none" stroke="currentColor" stroke-width="22" stroke-linecap="round"/></svg>
                    @endif
                @endif

                {{-- Floating Tooltip --}}
                <div class="absolute bottom-[100%] mb-4 bg-white dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-2xl px-5 py-3 font-bold text-slate-700 dark:text-slate-200 opacity-0 group-hover:opacity-100 transition-all transform scale-75 group-hover:scale-100 pointer-events-none whitespace-nowrap z-30 flex flex-col items-center min-w-[160px] origin-bottom shadow-lg">
                    <span class="text-base font-black">{{ $quiz['title'] }}</span>
                    @if($isPassed)
                        <span class="text-xs text-amber-500 mt-1 flex items-center gap-1 font-bold"><i class="fas fa-star"></i> Skor: {{ number_format($bestScore, 0) }}%</span>
                    @elseif(!$isLocked)
                        <span class="text-xs text-blue-500 mt-1 font-black uppercase tracking-wide">Misi Saat Ini</span>
                    @else
                        <span class="text-xs text-slate-400 mt-1 font-bold"><i class="fas fa-lock"></i> Terkunci</span>
                    @endif
                    <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 w-4 h-4 bg-white dark:bg-gray-800 border-b-2 border-r-2 border-slate-200 dark:border-gray-700 rotate-45"></div>
                </div>

                {{-- TOMBOL ROADMAP --}}
                @if($isLocked)
                    <div class="relative w-[85px] h-[85px] rounded-full bg-slate-200 dark:bg-gray-700 border-2 border-b-[8px] border-slate-300 dark:border-gray-800 flex items-center justify-center cursor-not-allowed">
                        <i class="fas fa-lock text-3xl text-slate-400 dark:text-gray-500"></i>
                    </div>
                @elseif($isPassed)
                    <a href="{{ route('quizzes.show', $quiz['id']) }}" class="block relative group-active:translate-y-[8px]">
                        <div class="relative w-[85px] h-[85px] rounded-full bg-[#ffc800] dark:bg-[#e6b400] border-2 border-b-[8px] border-[#d6a800] dark:border-[#b38c00] flex items-center justify-center hover:brightness-110 group-active:border-b-0 transition-all">
                            <i class="fas fa-crown text-4xl text-white drop-shadow-md"></i>
                        </div>
                    </a>
                @else
                    {{-- DIUBAH KE TEMA BIRU CERAH --}}
                    <a href="{{ route('quizzes.show', $quiz['id']) }}" class="block relative group-active:translate-y-[8px] z-20" id="current-mission">
                        <div class="absolute -inset-3 rounded-full border-4 border-[#1cb0f6] dark:border-[#1899d6] animate-ping opacity-50 pointer-events-none"></div>
                        <div class="relative w-[95px] h-[95px] rounded-full bg-[#1cb0f6] dark:bg-[#1899d6] border-2 border-b-[10px] border-[#1899d6] dark:border-[#1172a1] flex items-center justify-center hover:brightness-110 group-active:border-b-0 transition-all shadow-xl">
                            <i class="fas fa-play text-4xl text-white ml-2 drop-shadow-md"></i>
                            
                            {{-- Notifikasi Dot kuning --}}
                            <span class="absolute top-0 right-0 flex h-5 w-5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-5 w-5 bg-yellow-500 border-2 border-white dark:border-gray-800"></span>
                            </span>
                        </div>
                    </a>
                @endif

            </div>
            @endforeach

            {{-- Bendera Finish --}}
            <div class="mt-8 z-20 flex flex-col items-center">
                <i class="fas fa-flag-checkered text-4xl text-slate-400 dark:text-gray-500 mb-2 drop-shadow-sm"></i>
                <div class="bg-white dark:bg-gray-800 px-6 py-2 rounded-full border-2 border-b-4 border-slate-200 dark:border-gray-700 font-black text-slate-500 dark:text-slate-400 text-sm uppercase tracking-widest shadow-sm flex items-center gap-2">
                    Segera Dilanjutkan! ^^
                </div>
            </div>

        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to current mission
        const currentMission = document.getElementById('current-mission');
        if (currentMission) {
            setTimeout(() => {
                currentMission.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    });
</script>
@endsection