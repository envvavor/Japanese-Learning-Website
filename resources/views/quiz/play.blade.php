@extends('layouts.app')
@section('title', 'Quiz — Manabu')

@push('styles')
<style>
    .quiz-header { position:fixed; top:0; left:0; right:0; z-index:50; backdrop-filter:blur(12px); }
    .quiz-bottom { position:fixed; bottom:0; left:0; right:0; z-index:40; }
    
    /* Bubbly dot indicators - Biru Cerah */
    .dot-indicator { width:12px; height:12px; border-radius:50%; transition:all .3s cubic-bezier(0.4, 0, 0.2, 1); border: 2px solid transparent; }
    .dot-current { width:16px; height:16px; background:#1cb0f6; border-color:#bae6fd; box-shadow: 0 0 0 4px rgba(28, 176, 246, 0.2); }
    .dot-correct { background:#10b981; } 
    .dot-wrong { background:#ef4444; } 
    .dot-unanswered { background:#e2e8f0; border-color:#cbd5e1; }
    .dark .dot-unanswered { background:#334155; border-color:#475569; }
    
    .float-points { position:absolute; pointer-events:none; animation:floatUp 1.2s ease-out forwards; font-weight:900; z-index:60; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    @keyframes floatUp { 0% {opacity:1; transform:translateY(0) scale(1)} 100% {opacity:0; transform:translateY(-60px) scale(1.2)} }
    
    /* Chunky Option Buttons */
    .option-btn { transition:all .15s ease-out; border-bottom-width: 6px !important; }
    .option-btn:active:not(:disabled) { transform: translateY(4px); border-bottom-width: 2px !important; }
    .option-btn:hover:not(:disabled) { border-color: #1cb0f6; background: rgba(28, 176, 246, 0.05); }
    .option-correct { border-color:#059669 !important; background:#10b981 !important; color: white !important; }
    .option-wrong { border-color:#b91c1c !important; background:#ef4444 !important; color: white !important; }
    .option-reveal { border-color:#1cb0f6 !important; border-style:dashed !important; }
    
    /* Audio Wave */
    .wave-bar { display:inline-block; width:4px; margin:0 2px; border-radius:2px; background:#1cb0f6; animation:wave 1s ease-in-out infinite; }
    .wave-bar:nth-child(2) { animation-delay:.15s; }
    .wave-bar:nth-child(3) { animation-delay:.3s; }
    @keyframes wave { 0%, 100% {height:8px} 50% {height:24px} }
    
    /* Canvas & Badge */
    .canvas-container { position:relative; width:100%; max-width:300px; aspect-ratio:1; }
    .score-circle { transition:stroke-dashoffset 1.5s ease-out; }
    
    .streak-badge { animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%, 100% {opacity:1} 50% {opacity:.7} }
    
    /* Keyboard hints UI */
    .kbd { display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:26px; padding:0 6px; border-radius:6px; font-size:11px; font-weight:800; border:2px solid; border-bottom-width: 4px; opacity: 0.7; font-family: monospace; }
    
    body { padding-bottom: 90px; padding-top: 80px; background-color: #f8fafc; }
    .dark body { background-color: #0f172a; }
</style>
@endpush

@section('content')
{{-- FIXED HEADER (HUD) --}}
<div class="quiz-header bg-white/95 dark:bg-gray-900/95 border-b-4 border-slate-200 dark:border-gray-800 px-4 py-3">
    <div class="max-w-3xl mx-auto flex items-center justify-between gap-4">
        
        <div class="flex items-center gap-3 font-black text-[#1cb0f6] uppercase tracking-wide text-sm hidden sm:block">
            <i class="fas fa-gamepad"></i> Quiz
        </div>

        <div class="flex-1 max-w-md mx-auto">
            <div class="h-4 bg-slate-200 dark:bg-gray-700 rounded-full overflow-hidden border-2 border-slate-100 dark:border-gray-800 shadow-inner">
                <div id="progressBar" class="h-full bg-[#1cb0f6] rounded-full transition-all duration-500 relative" style="width:0%">
                    <div class="absolute top-1 left-2 right-2 h-1 bg-white/40 rounded-full"></div>
                </div>
            </div>
            <p id="progressText" class="text-xs font-bold text-slate-400 text-center mt-1 uppercase tracking-widest">0 / 0 Soal</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Global Text Toggle --}}
            <button id="globalTextToggle" onclick="toggleGlobalTextMode()" class="w-10 h-10 flex items-center justify-center rounded-xl font-black border-2 border-b-4 border-slate-200 dark:border-gray-700 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800 active:border-b-2 active:translate-y-1 transition-all" title="Mode Teks On/Off">
                <i class="fas fa-volume-up"></i>
            </button>
            
            {{-- Timer --}}
            <div class="flex items-center gap-2 text-amber-500 font-black bg-amber-100 dark:bg-amber-900/30 px-3 py-1.5 rounded-xl border-2 border-amber-200 dark:border-amber-800">
                <i class="fas fa-clock"></i>
                <span id="timerDisplay" class="text-sm font-mono tracking-wider">00:00</span>
            </div>
        </div>
    </div>
</div>

{{-- MAIN QUIZ AREA --}}
<div class="max-w-2xl mx-auto px-4 pt-6 pb-20" id="quizContainer">
    <div id="quizCard" class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 relative overflow-visible">
        
        {{-- Streak badge --}}
        <div id="streakBadge" class="hidden absolute -top-4 left-6 px-4 py-1.5 bg-amber-100 dark:bg-amber-900/80 text-amber-600 dark:text-amber-400 border-2 border-amber-200 dark:border-amber-700 rounded-full text-xs font-black tracking-wide uppercase shadow-md streak-badge">
            <i class="fas fa-fire mr-1"></i> <span id="streakCount">0</span> Beruntun!
        </div>
        
        {{-- Type badge --}}
        <div id="typeBadge" class="absolute -top-4 right-6 px-4 py-1.5 bg-[#1cb0f6] text-white rounded-full text-xs font-black tracking-wider uppercase shadow-md border-2 border-white dark:border-gray-800"></div>
        
        <div class="mt-6 mb-4">
            <p id="questionNumber" class="text-sm font-black text-[#1cb0f6] dark:text-[#1899d6] uppercase tracking-widest mb-3 text-center"></p>
            <div id="questionDisplay" class="text-center"></div>
            <p id="questionText" class="text-xl font-black text-slate-800 dark:text-white text-center mt-5 leading-relaxed"></p>
        </div>
        
        <div id="questionContent" class="mt-6"></div>
        
        <div id="hintArea" class="mt-8 pt-6 border-t-2 border-dashed border-slate-100 dark:border-gray-700 text-center">
            <button id="hintBtn" onclick="useHint()" class="inline-flex items-center justify-center px-5 py-3 text-sm font-black text-amber-500 bg-amber-50 dark:bg-amber-900/20 border-2 border-b-4 border-amber-200 dark:border-amber-800/50 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 hover:border-amber-400 active:translate-y-1 active:border-b-2 transition-all">
                <i class="fas fa-lightbulb text-lg mr-2"></i> Gunakan Bantuan <span class="kbd border-amber-300 text-amber-600 dark:text-amber-400 ml-3">H</span>
            </button>
        </div>
    </div>
</div>

{{-- RESULTS SCREEN --}}
<div id="resultsScreen" class="hidden max-w-2xl mx-auto px-4 pt-6 pb-20"></div>

{{-- BOTTOM NAV --}}
<div class="quiz-bottom bg-white dark:bg-gray-800 border-t-4 border-slate-200 dark:border-gray-700 px-4 py-4">
    <div class="max-w-3xl mx-auto flex items-center justify-between gap-4">
        
        <button id="prevBtn" onclick="prevQuestion()" disabled
                class="flex-shrink-0 px-4 h-14 flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-gray-700 rounded-2xl border-2 border-b-[6px] border-slate-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-200 dark:hover:bg-gray-600 active:translate-y-1 active:border-b-2 transition-all font-black uppercase tracking-wider text-sm hidden sm:flex">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>

        <div id="dotIndicators" class="flex-1 flex items-center gap-2 flex-wrap justify-center overflow-hidden px-2"></div>
        
        <button id="nextBtn" onclick="nextQuestion()"
                class="flex-shrink-0 px-6 h-14 flex items-center justify-center gap-3 text-white bg-[#1cb0f6] dark:bg-[#1899d6] rounded-2xl border-2 border-b-[6px] border-[#1899d6] dark:border-[#1172a1] hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all font-black uppercase tracking-wider text-sm sm:text-base">
            Lanjut <i class="fas fa-arrow-right"></i>
        </button>

    </div>
</div>

<script src="{{ asset('js/quiz-play.js') }}"></script>
@endsection