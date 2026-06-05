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

{{-- Tutorial/Help Modal --}}
<div id="quizTutorialOverlay" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-y-auto" style="display:none;">
    {{-- Backdrop --}}
    <div id="tutorialBackdrop" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

    {{-- Modal Card --}}
    <div id="tutorialModal" class="relative z-10 w-full max-w-md max-h-[90vh] overflow-y-auto bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-2xl transform transition-all duration-300 scale-95 opacity-0 my-auto">

        {{-- Progress Bar --}}
        <div class="h-1.5 bg-slate-100 dark:bg-gray-700">
            <div id="tutorialProgressBar" class="h-full bg-[#1cb0f6] rounded-r-full transition-all duration-500" style="width:0%"></div>
        </div>

        {{-- Content Area --}}
        <div class="p-6 sm:p-8">
            {{-- Icon + Step Label --}}
            <div class="flex items-center gap-3 mb-4">
                <div id="tutorialStepIcon" class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 border-2 border-b-4 transition-colors duration-300">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <p id="tutorialStepLabel" class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400"></p>
                    <h3 id="tutorialStepTitle" class="text-lg font-black uppercase tracking-wide text-slate-800 dark:text-white"></h3>
                </div>
            </div>

            {{-- Description --}}
            <div id="tutorialStepDesc" class="text-sm font-bold text-slate-500 dark:text-slate-400 leading-relaxed mb-6"></div>

            {{-- Visual Illustration --}}
            <div id="tutorialStepVisual" class="mb-6"></div>

            {{-- Footer --}}
            <div class="space-y-3">
                {{-- Dots --}}
                <div id="tutorialDots" class="flex items-center justify-center gap-1.5"></div>

                {{-- Buttons --}}
                <div class="flex items-center justify-between gap-2">
                    <button id="tutorialSkipBtn" class="px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors shrink-0">
                        Lewati
                    </button>

                    <div class="flex items-center gap-2 shrink-0">
                        <button id="tutorialPrevBtn" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-b-4 border-slate-200 dark:border-gray-600 text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all" style="display:none;">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </button>

                        <button id="tutorialNextBtn" class="px-5 py-2.5 rounded-xl border-2 border-b-4 border-[#1899d6] bg-[#1cb0f6] text-white text-xs font-black uppercase tracking-[0.15em] hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all whitespace-nowrap">
                            Lanjut <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Help Button --}}
<div class="fixed bottom-[5.5rem] right-4 sm:bottom-6 sm:right-6 z-50">
    <button onclick="openQuizTutorial()" class="flex items-center justify-center w-14 h-14 rounded-full bg-white dark:bg-gray-800 border-2 border-b-[4px] border-[#1cb0f6] dark:border-[#1899d6] text-[#1cb0f6] dark:text-[#1899d6] hover:bg-[#1cb0f6]/10 dark:hover:bg-[#1899d6]/20 active:translate-y-[2px] active:border-b-2 transition-all shadow-lg" title="Cara Main Quiz">
        <i class="fas fa-question text-xl"></i>
    </button>
</div>

<style>
    #quizTutorialOverlay.tutorial-active #tutorialModal {
        transform: scale(1);
        opacity: 1;
    }
    .tutorial-visual-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 2px solid #e2e8f0;
        border-bottom-width: 6px;
        border-radius: 1.25rem;
        padding: 1rem 1.25rem;
    }
    html.dark .tutorial-visual-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-color: #334155;
    }
    .tutorial-visual-card .visual-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        border: 2px solid;
        border-bottom-width: 4px;
    }
    .tutorial-mini-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        font-size: 0.625rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-radius: 0.5rem;
        border: 2px solid;
        border-bottom-width: 3px;
    }
    @keyframes tutorialPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .tutorial-pulse {
        animation: tutorialPulse 2s ease-in-out infinite;
    }
</style>

<script>
    // Tutorial Steps Data
    const tutorialSteps = [
        {
            icon: 'fas fa-torii-gate',
            color: '#1cb0f6',
            title: 'Selamat Datang!',
            desc: 'Ini adalah <b>Peta Quiz</b> — petualanganmu belajar bahasa Jepang! Setiap lingkaran di peta adalah sebuah <b>misi quiz</b> yang harus kamu selesaikan untuk membuka misi berikutnya.',
            visual: `
                <div class="tutorial-visual-card flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-[#1cb0f6] border-2 border-b-4 border-[#1899d6] flex items-center justify-center text-white text-lg tutorial-pulse">
                        <i class="fas fa-play ml-0.5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wide">Misi Saat Ini</p>
                        <p class="text-[10px] font-bold text-slate-400">Tekan tombol biru untuk memulai!</p>
                    </div>
                </div>
            `
        },
        {
            icon: 'fas fa-list-ul',
            color: '#6366f1',
            title: 'Pilihan Ganda',
            desc: 'Untuk soal <b>pilihan ganda</b>, kamu akan melihat pertanyaan dan beberapa opsi jawaban. <b>Pilih jawaban yang benar</b> dari opsi yang tersedia.',
            visual: `
                <div class="tutorial-visual-card space-y-2">
                    <p class="text-xs font-black text-slate-600 dark:text-slate-300 mb-2"><i class="fas fa-question-circle text-indigo-500 mr-1"></i> Apa arti dari "あ" ?</p>
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-slate-200 dark:border-gray-600 text-xs font-bold text-slate-500 dark:text-slate-400"><span class="tutorial-mini-btn border-slate-300 dark:border-gray-500 text-slate-400">1</span> Ka</div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 text-xs font-black text-emerald-600 dark:text-emerald-400"><span class="tutorial-mini-btn border-emerald-400 text-emerald-500">2</span> A <i class="fas fa-check-circle ml-auto text-emerald-500"></i></div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-slate-200 dark:border-gray-600 text-xs font-bold text-slate-500 dark:text-slate-400"><span class="tutorial-mini-btn border-slate-300 dark:border-gray-500 text-slate-400">3</span> I</div>
                    </div>
                </div>
            `
        },
        {
            icon: 'fas fa-pencil-alt',
            color: '#10b981',
            title: 'Soal Menggambar',
            desc: 'Untuk soal <b>menggambar</b>, kamu harus <b>menulis huruf Jepang</b> di kanvas. Ikuti urutan goresan yang benar, lalu tekan tombol <b>"Periksa"</b> untuk mengecek jawabanmu.',
            visual: `
                <div class="tutorial-visual-card">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-16 h-16 rounded-xl border-2 border-b-4 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-900 flex items-center justify-center relative">
                            <span class="text-2xl text-slate-300 dark:text-gray-600 font-bold">あ</span>
                            <i class="fas fa-pen absolute -bottom-1 -right-1 text-emerald-500 text-xs bg-white dark:bg-gray-800 rounded-full p-1 border border-emerald-300"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-600 dark:text-slate-300">Tulis huruf di kanvas</p>
                            <p class="text-[10px] font-bold text-slate-400">Ikuti urutan goresan!</p>
                        </div>
                    </div>
                    <div class="flex justify-center gap-2">
                        <span class="tutorial-mini-btn border-slate-300 dark:border-gray-500 text-slate-400 bg-white dark:bg-gray-700"><i class="fas fa-trash-alt"></i> Reset</span>
                        <span class="tutorial-mini-btn border-slate-300 dark:border-gray-500 text-slate-400 bg-white dark:bg-gray-700"><i class="fas fa-undo"></i> Undo</span>
                        <span class="tutorial-mini-btn border-[#1899d6] bg-[#1cb0f6] text-white tutorial-pulse"><i class="fas fa-check"></i> Periksa</span>
                    </div>
                </div>
            `
        },
        {
            icon: 'fas fa-lightbulb',
            color: '#f59e0b',
            title: 'Gunakan Bantuan',
            desc: 'Bingung? Kamu bisa menekan tombol <b>"Gunakan Bantuan"</b> untuk melihat <b>hint/petunjuk</b>. Untuk soal menggambar, hint akan menampilkan <b>garis panduan goresan</b> di kanvas.',
            visual: `
                <div class="tutorial-visual-card flex items-center gap-3">
                    <div class="visual-icon bg-amber-100 dark:bg-amber-900/30 text-amber-500 border-amber-200 dark:border-amber-700">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-wide">Gunakan Bantuan</p>
                        <p class="text-[10px] font-bold text-slate-400">Muncul petunjuk arti huruf / goresan</p>
                    </div>
                    <span class="tutorial-mini-btn border-amber-300 text-amber-500 bg-amber-50 dark:bg-amber-900/30">H</span>
                </div>
            `
        },
        {
            icon: 'fas fa-arrows-alt-h',
            color: '#8b5cf6',
            title: 'Navigasi Soal',
            desc: 'Gunakan tombol <b>"Lanjut"</b> dan <b>"Kembali"</b> untuk berpindah antar soal. Semua soal harus dijawab sebelum bisa menyelesaikan quiz. Kamu harus mendapat <b>skor 100%</b> untuk lulus misi!',
            visual: `
                <div class="tutorial-visual-card">
                    <div class="flex items-center justify-between gap-2">
                        <span class="tutorial-mini-btn border-slate-300 dark:border-gray-500 text-slate-400 bg-white dark:bg-gray-700"><i class="fas fa-chevron-left"></i></span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <div class="w-3 h-3 rounded-full bg-[#1cb0f6] ring-2 ring-[#1cb0f6]/30"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-gray-600"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-gray-600"></div>
                        </div>
                        <span class="tutorial-mini-btn border-[#1899d6] bg-[#1cb0f6] text-white">Lanjut <i class="fas fa-chevron-right"></i></span>
                    </div>
                    <p class="text-[10px] font-bold text-center text-slate-400 mt-2 uppercase tracking-wider">3 / 5 Soal</p>
                </div>
            `
        },
        {
            icon: 'fas fa-rocket',
            color: '#1cb0f6',
            title: 'Siap Bermain!',
            desc: 'Sekarang kamu sudah paham cara mainnya! Pilih misi di peta dan mulai petualanganmu. <b>Ganbare! (がんばれ!)</b> 🎌',
            visual: `
                <div class="tutorial-visual-card text-center py-4">
                    <div class="text-4xl mb-2">🏯</div>
                    <p class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">Selamat Berpetualang!</p>
                    <p class="text-xs font-bold text-slate-400 mt-1">Selesaikan semua misi untuk jadi master!</p>
                </div>
            `
        }
    ];

    let tutorialCurrentStep = 0;

    function openQuizTutorial() {
        tutorialCurrentStep = 0;
        const overlay = document.getElementById('quizTutorialOverlay');
        overlay.style.display = 'flex';
        requestAnimationFrame(() => {
            overlay.classList.add('tutorial-active');
            renderTutorialStep(0);
        });
    }

    function closeQuizTutorial() {
        const overlay = document.getElementById('quizTutorialOverlay');
        overlay.classList.remove('tutorial-active');
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
        localStorage.setItem('hasSeenQuizTutorial', 'true');
    }

    function renderTutorialStep(idx) {
        tutorialCurrentStep = idx;
        const step = tutorialSteps[idx];
        const total = tutorialSteps.length;

        // Progress bar
        document.getElementById('tutorialProgressBar').style.width = ((idx + 1) / total * 100) + '%';

        // Icon
        const iconEl = document.getElementById('tutorialStepIcon');
        iconEl.innerHTML = '<i class="' + step.icon + '"></i>';
        iconEl.style.background = step.color + '1a';
        iconEl.style.color = step.color;
        iconEl.style.borderColor = step.color + '33';

        // Text
        document.getElementById('tutorialStepLabel').textContent = 'Langkah ' + (idx + 1) + ' dari ' + total;
        document.getElementById('tutorialStepTitle').textContent = step.title;
        document.getElementById('tutorialStepDesc').innerHTML = step.desc;

        // Visual
        document.getElementById('tutorialStepVisual').innerHTML = step.visual;

        // Prev button
        document.getElementById('tutorialPrevBtn').style.display = idx > 0 ? '' : 'none';

        // Next button
        const nextBtn = document.getElementById('tutorialNextBtn');
        if (idx === total - 1) {
            nextBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Mengerti!';
        } else {
            nextBtn.innerHTML = 'Lanjut <i class="fas fa-arrow-right ml-1"></i>';
        }

        // Skip button
        document.getElementById('tutorialSkipBtn').style.display = idx === total - 1 ? 'none' : '';

        // Dots
        const dotsEl = document.getElementById('tutorialDots');
        dotsEl.innerHTML = '';
        for (let i = 0; i < total; i++) {
            const dot = document.createElement('div');
            dot.className = 'rounded-full transition-all duration-200 ' +
                (i === idx ? 'w-2.5 h-2.5 bg-[#1cb0f6]' : 'w-2 h-2 bg-slate-300 dark:bg-gray-600');
            dotsEl.appendChild(dot);
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to current mission
        const currentMission = document.getElementById('current-mission');
        if (currentMission) {
            setTimeout(() => {
                currentMission.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }

        // Tutorial buttons
        document.getElementById('tutorialNextBtn').addEventListener('click', function() {
            if (tutorialCurrentStep < tutorialSteps.length - 1) {
                renderTutorialStep(tutorialCurrentStep + 1);
            } else {
                closeQuizTutorial();
            }
        });

        document.getElementById('tutorialPrevBtn').addEventListener('click', function() {
            if (tutorialCurrentStep > 0) {
                renderTutorialStep(tutorialCurrentStep - 1);
            }
        });

        document.getElementById('tutorialSkipBtn').addEventListener('click', closeQuizTutorial);
        document.getElementById('tutorialBackdrop').addEventListener('click', closeQuizTutorial);

        // Auto-show tutorial on first visit
        if (!localStorage.getItem('hasSeenQuizTutorial')) {
            setTimeout(() => {
                openQuizTutorial();
            }, 600);
        }
    });
</script>
@endsection