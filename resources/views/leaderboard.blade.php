@extends('layouts.app')

@section('title', 'Leaderboard — Manabu')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-500 border-2 border-amber-200 dark:border-amber-800 rounded-2xl flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fas fa-trophy text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">Leaderboard</h1>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Peringkat pejuang Manabu berdasarkan Level & XP.</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-100 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest shrink-0">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Your Rank Card --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 mb-8 flex flex-col sm:flex-row items-center gap-5 sm:gap-6 shadow-sm relative overflow-hidden">
            <i class="fas fa-crown absolute -right-4 -top-4 text-7xl text-amber-400 opacity-5 rotate-12"></i>

            {{-- Rank Badge --}}
            <div class="relative shrink-0 w-20 h-20 bg-amber-100 dark:bg-amber-900/30 border-4 border-amber-400 dark:border-amber-600 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-shield-alt text-5xl text-amber-400 dark:text-amber-500 opacity-20 absolute"></i>
                <span class="text-2xl font-black text-amber-500 dark:text-amber-400 z-10">#{{ $currentRank }}</span>
                <div class="absolute -bottom-2.5 px-2.5 py-0.5 bg-amber-400 dark:bg-amber-500 text-white text-[9px] font-black rounded-full uppercase tracking-widest border-2 border-white dark:border-gray-800 shadow-sm">Rank</div>
            </div>

            <div class="flex-1 min-w-0 text-center sm:text-left">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Peringkatmu Saat Ini</p>
                <p class="text-xl font-black text-slate-800 dark:text-white truncate">{{ $currentUser->name }}</p>
                <div class="flex items-center justify-center sm:justify-start gap-3 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 rounded-xl text-xs font-black text-amber-600 dark:text-amber-400">
                        <i class="fas fa-shield-alt text-[10px]"></i> Level {{ $currentUser->level ?? 1 }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-xs font-black text-slate-500 dark:text-slate-400">
                        <i class="fas fa-bolt text-amber-400 text-[10px]"></i> {{ $currentUser->xp ?? 0 }} XP
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== PODIUM TOP 3 ===== --}}
        @if($leaderboard->count() >= 1)
        @php
            $top1 = $leaderboard->get(0);
            $top2 = $leaderboard->get(1);
            $top3 = $leaderboard->get(2);
        @endphp
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] overflow-hidden shadow-sm mb-8">

            {{-- Label --}}
            <div class="bg-slate-50 dark:bg-gray-900/50 border-b-2 border-slate-200 dark:border-gray-700 py-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <i class="fas fa-trophy text-amber-400 mr-1.5"></i> Top Peringkat
                </p>
            </div>

            {{-- Podium --}}
            <div class="flex items-end justify-center gap-2 sm:gap-4 px-4 sm:px-8 pt-10 pb-0 bg-slate-50 dark:bg-gray-900/30">

                {{-- 2nd Place --}}
                @if($top2)
                <div class="flex flex-col items-center flex-1 max-w-[160px]">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl border-[3px] border-slate-300 dark:border-slate-500 {{ $top2->google_avatar ? '' : 'bg-slate-300 dark:bg-slate-600' }} text-white flex items-center justify-center font-black text-lg sm:text-xl mb-2 overflow-hidden shrink-0">
                        @if($top2->google_avatar)
                            <img src="{{ $top2->google_avatar }}" class="w-full h-full object-cover" referrerpolicy="no-referrer"
                                onerror="this.outerHTML='<span class=\'font-black text-white text-xl\'>{{ strtoupper(substr($top2->name, 0, 1)) }}</span>'">
                        @else
                            {{ strtoupper(substr($top2->name, 0, 1)) }}
                        @endif
                    </div>
                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 truncate max-w-[110px] text-center leading-tight">{{ $top2->name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 mt-0.5"><i class="fas fa-bolt text-amber-400 text-[8px]"></i> {{ number_format($top2->xp) }} XP</p>
                    <span class="mt-1.5 text-[10px] font-black px-2 py-1 rounded-lg border-2 bg-slate-50 dark:bg-slate-700/50 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                        <i class="fas fa-shield-alt text-[8px]"></i> Lv.{{ $top2->level }}
                    </span>
                    {{-- Podium Block --}}
                    <div class="w-full mt-3 rounded-t-2xl border-t-[3px] border-x-2 border-slate-300 dark:border-slate-600 bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-700/50 dark:to-slate-800/50 h-16 flex items-center justify-center text-3xl font-black text-slate-400 dark:text-slate-500 select-none">
                        2
                    </div>
                </div>
                @endif

                {{-- 1st Place --}}
                @if($top1)
                <div class="flex flex-col items-center flex-1 max-w-[180px]">
                    <div class="relative mb-2">
                        <i class="fas fa-crown absolute -top-6 left-1/2 -translate-x-1/2 text-2xl text-amber-400 drop-shadow-sm"></i>
                        <div class="w-16 h-16 sm:w-[72px] sm:h-[72px] rounded-[20px] border-4 border-amber-400 dark:border-amber-500 {{ $top1->google_avatar ? '' : 'bg-amber-400' }} text-white flex items-center justify-center font-black text-2xl shadow-sm overflow-hidden shrink-0">
                            @if($top1->google_avatar)
                                <img src="{{ $top1->google_avatar }}" class="w-full h-full object-cover" referrerpolicy="no-referrer"
                                    onerror="this.outerHTML='<span class=\'font-black text-white text-2xl\'>{{ strtoupper(substr($top1->name, 0, 1)) }}</span>'">
                            @else
                                {{ strtoupper(substr($top1->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                    <p class="text-sm font-black text-slate-800 dark:text-white truncate max-w-[140px] text-center leading-tight">{{ $top1->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5"><i class="fas fa-bolt text-amber-400 text-[9px]"></i> {{ number_format($top1->xp) }} XP</p>
                    <span class="mt-1.5 text-xs font-black px-3 py-1.5 rounded-xl border-2 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 whitespace-nowrap">
                        <i class="fas fa-shield-alt text-[10px]"></i> Lv.{{ $top1->level }}
                    </span>
                    {{-- Podium Block --}}
                    <div class="w-full mt-3 rounded-t-2xl border-t-[3px] border-x-2 border-amber-400 dark:border-amber-500 bg-gradient-to-b from-amber-100 to-amber-200 dark:from-amber-900/40 dark:to-amber-900/20 h-28 flex items-center justify-center text-4xl font-black text-amber-500 dark:text-amber-400 select-none">
                        1
                    </div>
                </div>
                @endif

                {{-- 3rd Place --}}
                @if($top3)
                <div class="flex flex-col items-center flex-1 max-w-[160px]">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl border-[3px] border-amber-700 dark:border-amber-800 {{ $top3->google_avatar ? '' : 'bg-amber-600' }} text-white flex items-center justify-center font-black text-lg sm:text-xl mb-2 overflow-hidden shrink-0">
                        @if($top3->google_avatar)
                            <img src="{{ $top3->google_avatar }}" class="w-full h-full object-cover" referrerpolicy="no-referrer"
                                onerror="this.outerHTML='<span class=\'font-black text-white text-xl\'>{{ strtoupper(substr($top3->name, 0, 1)) }}</span>'">
                        @else
                            {{ strtoupper(substr($top3->name, 0, 1)) }}
                        @endif
                    </div>
                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 truncate max-w-[110px] text-center leading-tight">{{ $top3->name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 mt-0.5"><i class="fas fa-bolt text-amber-400 text-[8px]"></i> {{ number_format($top3->xp) }} XP</p>
                    <span class="mt-1.5 text-[10px] font-black px-2 py-1 rounded-lg border-2 bg-amber-50 dark:bg-amber-900/20 border-amber-700/40 dark:border-amber-800 text-amber-700 dark:text-amber-500 whitespace-nowrap">
                        <i class="fas fa-shield-alt text-[8px]"></i> Lv.{{ $top3->level }}
                    </span>
                    {{-- Podium Block --}}
                    <div class="w-full mt-3 rounded-t-2xl border-t-[3px] border-x-2 border-amber-600 dark:border-amber-700 bg-gradient-to-b from-amber-50 to-amber-100 dark:from-amber-900/25 dark:to-amber-900/10 h-12 flex items-center justify-center text-2xl font-black text-amber-700 dark:text-amber-600 select-none">
                        3
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif
        {{-- ===== END PODIUM ===== --}}

        {{-- Leaderboard Table --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] overflow-hidden shadow-sm">

            {{-- Table Header --}}
            <div class="flex items-center gap-4 px-6 sm:px-8 py-4 bg-slate-50 dark:bg-gray-900/50 border-b-2 border-slate-200 dark:border-gray-700">
                <div class="w-10 text-center shrink-0">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">#</span>
                </div>
                <div class="flex-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nama</span>
                </div>
                <div class="w-20 text-center shrink-0">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Level</span>
                </div>
                <div class="w-20 text-center shrink-0 hidden sm:block">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">XP</span>
                </div>
            </div>

            {{-- Rows --}}
            @forelse($leaderboard as $index => $user)
                @php
                    $rank = $index + 1;
                    $isMe = $user->id === $currentUser->id;

                    // Medal colors for top 3
                    $medalColor = match($rank) {
                        1 => 'bg-amber-400 text-white border-amber-500',
                        2 => 'bg-slate-300 text-white border-slate-400',
                        3 => 'bg-amber-600 text-white border-amber-700',
                        default => '',
                    };
                    $rowBg = $isMe
                        ? 'bg-amber-50/50 dark:bg-amber-900/10'
                        : ($rank <= 3 ? 'bg-amber-50/30 dark:bg-amber-900/5' : '');
                @endphp

                <div class="flex items-center gap-4 px-6 sm:px-8 py-4 sm:py-5 border-b-2 last:border-b-0 border-slate-100 dark:border-gray-700/50 hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors {{ $rowBg }} leaderboard-row">

                    {{-- Rank --}}
                    <div class="w-10 text-center shrink-0">
                        @if($rank <= 3)
                            <div class="w-8 h-8 mx-auto {{ $medalColor }} rounded-lg border-2 border-b-[3px] flex items-center justify-center text-xs font-black shadow-sm">
                                {{ $rank }}
                            </div>
                        @else
                            <span class="text-base font-black text-slate-300 dark:text-slate-600">{{ $rank }}</span>
                        @endif
                    </div>

                    {{-- Avatar + Name --}}
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        @if($user->google_avatar)
                            <img src="{{ $user->google_avatar }}" alt=""
                                class="w-10 h-10 rounded-xl border-2 {{ $isMe ? 'border-amber-400 dark:border-amber-500' : 'border-slate-200 dark:border-gray-600' }} object-cover shrink-0"
                                referrerpolicy="no-referrer"
                                onerror="this.outerHTML='<div class=\'w-10 h-10 rounded-xl border-2 {{ $isMe ? 'border-amber-400 dark:border-amber-500 bg-amber-400' : 'border-slate-200 dark:border-gray-600 bg-slate-300 dark:bg-slate-600' }} text-white flex items-center justify-center font-black text-sm shrink-0\'>{{ strtoupper(substr($user->name, 0, 1)) }}</div>'">
                        @else
                            <div class="w-10 h-10 rounded-xl border-2 {{ $isMe ? 'border-amber-400 dark:border-amber-500 bg-amber-400' : 'border-slate-200 dark:border-gray-600 bg-slate-300 dark:bg-slate-600' }} text-white flex items-center justify-center font-black text-sm shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-black {{ $isMe ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-200' }} truncate">
                                {{ $user->name }}
                                @if($isMe)
                                    <span class="text-[9px] font-black bg-amber-400 text-white px-1.5 py-0.5 rounded-md uppercase tracking-widest ml-1 inline-block">Kamu</span>
                                @endif
                            </p>
                            {{-- Show XP on mobile since XP column is hidden --}}
                            <p class="text-[10px] font-bold text-slate-400 sm:hidden mt-0.5">{{ $user->xp }} XP</p>
                        </div>
                    </div>

                    {{-- Level --}}
                    <div class="w-20 text-center shrink-0">
                        <span class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 rounded-xl text-xs font-black text-amber-600 dark:text-amber-400">
                            <i class="fas fa-shield-alt text-[10px]"></i> {{ $user->level }}
                        </span>
                    </div>

                    {{-- XP (desktop) --}}
                    <div class="w-20 text-center shrink-0 hidden sm:block">
                        <span class="text-sm font-black text-slate-500 dark:text-slate-400">{{ number_format($user->xp) }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 px-6">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4 border-2 border-slate-200 dark:border-gray-600">
                        <i class="fas fa-users text-2xl text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <p class="text-lg font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Belum Ada Data</p>
                    <p class="text-sm font-bold text-slate-300 dark:text-slate-600 mt-1">Jadilah yang pertama di leaderboard!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Row entrance animation */
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateX(-12px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .leaderboard-row {
        animation: fadeSlideIn 0.35s ease-out both;
    }
    .leaderboard-row:nth-child(1) { animation-delay: 0.05s; }
    .leaderboard-row:nth-child(2) { animation-delay: 0.1s; }
    .leaderboard-row:nth-child(3) { animation-delay: 0.15s; }
    .leaderboard-row:nth-child(4) { animation-delay: 0.2s; }
    .leaderboard-row:nth-child(5) { animation-delay: 0.25s; }
    .leaderboard-row:nth-child(6) { animation-delay: 0.3s; }
    .leaderboard-row:nth-child(7) { animation-delay: 0.35s; }
    .leaderboard-row:nth-child(8) { animation-delay: 0.4s; }
    .leaderboard-row:nth-child(9) { animation-delay: 0.45s; }
    .leaderboard-row:nth-child(10) { animation-delay: 0.5s; }
    .leaderboard-row:nth-child(n+11) { animation-delay: 0.55s; }

    /* Podium entrance animation */
    @keyframes podiumRise {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .podium-item-1 { animation: podiumRise 0.5s ease-out 0.1s both; }
    .podium-item-2 { animation: podiumRise 0.5s ease-out 0.25s both; }
    .podium-item-3 { animation: podiumRise 0.5s ease-out 0.4s both; }
</style>
@endpush
@endsection