@extends('layouts.app')
@section('title', 'Riwayat Quiz — Manabu')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#1cb0f6]/10 dark:bg-[#1899d6]/20 text-[#1cb0f6] dark:text-[#1899d6] rounded-2xl flex items-center justify-center text-3xl border-2 border-[#1cb0f6]/20">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-wide uppercase">
                    Riwayat Quiz
                </h1>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">Lihat semua hasil quiz yang pernah kamu kerjakan.</p>
            </div>
        </div>
        <a href="{{ route('quiz.index') }}"
            class="inline-flex items-center justify-center px-5 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-wider">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($sessions->count() > 0)
        {{-- Stats Summary --}}
        @php
            $allSessions = \App\Models\QuizSession::where('user_id', auth()->id())->whereNotNull('completed_at')->get();
            $totalQuiz = $allSessions->count();
            $avgScore = $totalQuiz > 0 ? round($allSessions->avg('score'), 1) : 0;
            $bestScore = $totalQuiz > 0 ? round($allSessions->max('score'), 1) : 0;
            $totalCorrect = $allSessions->sum('correct_answers');
            $totalQuestions = $allSessions->sum('total_questions');
        @endphp
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 text-center shadow-sm">
                <p class="text-4xl font-black text-[#1cb0f6] dark:text-[#1899d6]"><i class="fas fa-gamepad text-2xl mr-1"></i> {{ $totalQuiz }}</p>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">Total Quiz</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 text-center shadow-sm">
                <p class="text-4xl font-black text-amber-500"><i class="fas fa-percentage text-2xl mr-1"></i> {{ $avgScore }}</p>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">Rata-rata</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 text-center shadow-sm">
                <p class="text-4xl font-black text-indigo-500"><i class="fas fa-trophy text-2xl mr-1"></i> {{ $bestScore }}</p>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">Skor Terbaik</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 text-center shadow-sm">
                <p class="text-4xl font-black text-emerald-500"><i class="fas fa-check-circle text-2xl mr-1"></i> {{ $totalCorrect }}</p>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">Jawaban Benar</p>
            </div>
        </div>

        {{-- Session List --}}
        <div class="space-y-5">
            @foreach($sessions as $session)
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-5 hover:border-[#1cb0f6]/30 dark:hover:border-[#1899d6]/30 transition-all cursor-default">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    {{-- Left Info --}}
                    <div class="flex items-center gap-4">
                        {{-- Grade Badge --}}
                        <div class="w-16 h-16 rounded-[1.2rem] flex items-center justify-center text-3xl font-black shrink-0 border-2 border-b-4
                            {{ $session->score >= 90 ? 'bg-emerald-100 border-emerald-200 text-emerald-500 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400' :
                               ($session->score >= 70 ? 'bg-[#1cb0f6]/10 border-[#1cb0f6]/20 text-[#1cb0f6] dark:bg-[#1899d6]/20 dark:border-[#1899d6]/30 dark:text-[#1899d6]' :
                               ($session->score >= 50 ? 'bg-amber-100 border-amber-200 text-amber-500 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-400' :
                               'bg-rose-100 border-rose-200 text-rose-500 dark:bg-rose-900/30 dark:border-rose-800 dark:text-rose-400')) }}">
                            {{ $session->grade }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wide">
                                    @switch($session->quiz_type)
                                        @case('multiple_choice') <i class="fas fa-list-ul mr-1 text-slate-400"></i> Pilihan Ganda @break
                                        @case('drawing') <i class="fas fa-pencil-alt mr-1 text-slate-400"></i> Menggambar @break
                                        @case('listening') <i class="fas fa-headphones mr-1 text-slate-400"></i> Mendengarkan @break
                                        @case('mixed') <i class="fas fa-random mr-1 text-slate-400"></i> Campuran @break
                                        @default {{ ucfirst($session->quiz_type) }}
                                    @endswitch
                                </span>
                                @if($session->category)
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border-2
                                    {{ $session->category === 'hiragana' ? 'border-rose-200 text-rose-500 bg-rose-50 dark:border-rose-800 dark:bg-rose-900/20' :
                                       ($session->category === 'katakana' ? 'border-blue-200 text-blue-500 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20' :
                                       'border-emerald-200 text-emerald-500 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20') }}">
                                    {{ ucfirst($session->category) }}
                                </span>
                                @else
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border-2 border-slate-200 text-slate-500 bg-slate-50 dark:border-gray-700 dark:bg-gray-800">Semua</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-2">
                                <span><i class="fas fa-calendar-alt mr-1"></i> {{ $session->created_at->format('d M Y, H:i') }}</span>
                                <span>•</span>
                                <span><i class="fas fa-layer-group mr-1"></i> {{ $session->total_questions }} soal</span>
                                @if($session->questions_with_text_revealed > 0)
                                <span>•</span>
                                <span class="text-amber-500"><i class="fas fa-lightbulb mr-1"></i> {{ $session->questions_with_text_revealed }} hint</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Right Score --}}
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-3xl font-black {{ $session->score >= 70 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ number_format($session->score, 0) }}%
                            </p>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1"><i class="fas fa-check-circle text-emerald-400 mr-1"></i> {{ $session->correct_answers }}/{{ $session->total_questions }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $sessions->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-16 text-center shadow-sm">
            <i class="fas fa-clipboard-list text-7xl text-slate-300 dark:text-gray-600 mb-6"></i>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-2">Belum Ada Riwayat</h2>
            <p class="text-slate-500 dark:text-slate-400 font-bold mb-8">Kamu belum menyelesaikan quiz apapun. Mulai quiz pertamamu sekarang!</p>
            <a href="{{ route('quiz.index') }}"
                class="inline-flex items-center px-8 py-4 rounded-2xl text-white font-black uppercase tracking-widest bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all">
                <i class="fas fa-play mr-2"></i> Mulai Quiz
            </a>
        </div>
    @endif
</div>
@endsection