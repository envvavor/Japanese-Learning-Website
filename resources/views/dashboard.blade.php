@extends('layouts.app')

@section('title', 'Dashboard — Manabu')

@section('content')
{{-- Background Global (Warna cerah ceria) --}}
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section --}}
        <div id="onboard-header" class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 sm:mb-10 gap-4 sm:gap-6">
            
            {{-- Wrapper Logo & Sapaan --}}
            <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl sm:rounded-3xl flex items-center justify-center p-1.5 sm:p-2 shrink-0 shadow-sm">
                    <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-0.5 sm:mb-1 leading-tight">
                        Halo, <span class="text-[#1cb0f6]">{{ explode(' ', Auth::user()->name)[0] }}</span>!
                    </h1>
                    <p class="text-sm sm:text-lg font-bold text-slate-500 dark:text-slate-400">Selamat Datang Kembali di 学ぶ</p>
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div id="onboard-profile" x-data="{ open: false }" class="relative z-50 w-full sm:w-auto mt-2 sm:mt-0">
                <button @click="open = !open" @click.away="open = false" 
                    class="w-full sm:w-auto flex items-center justify-center px-5 sm:px-6 py-3 sm:py-3.5 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-black text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest gap-2 sm:gap-3 shadow-sm">
                    
                    @if(Auth::user()->google_avatar)
                        <img src="{{ Auth::user()->google_avatar }}" alt="Avatar" class="w-6 h-6 sm:w-7 sm:h-7 rounded-full border-2 border-[#1cb0f6] object-cover shrink-0" referrerpolicy="no-referrer" onerror="this.outerHTML='<div class=\'w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-bold text-[10px] sm:text-xs shrink-0\'>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>'">
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-bold text-[10px] sm:text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <span class="flex-1 text-left sm:flex-none">Profil</span>
                    
                    <i class="fas fa-chevron-down text-[10px] sm:text-sm transition-transform" :class="{'rotate-180': open}"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" x-transition.opacity x-transition:enter.duration.200ms x-transition:leave.duration.150ms 
                     class="absolute right-0 left-0 sm:left-auto mt-2 sm:mt-3 w-full sm:w-56 bg-white dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-2xl shadow-lg overflow-hidden" x-cloak>
                    
                    <div class="p-2 space-y-1">
                        {{-- Profil Saya --}}
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-[#1cb0f6] dark:hover:text-[#1cb0f6] transition-colors">
                            <i class="fas fa-id-card w-5 text-center text-base sm:text-lg"></i> Profil Saya
                        </a>

                        {{-- Tema Web --}}
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center gap-3 hover:text-amber-500">
                                <i class="fas fa-paint-brush w-5 text-center text-base sm:text-lg"></i> Tema Web
                            </div>
                            <i class="fas text-base sm:text-lg" :class="darkMode ? 'fa-moon text-indigo-500' : 'fa-sun text-amber-500'"></i>
                        </button>
                        
                        <div class="h-0.5 bg-slate-100 dark:bg-gray-700 my-1 mx-2"></div>

                        {{-- Logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 text-center text-base sm:text-lg"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Level & XP Progress (Gamification Element) --}}
        <div id="onboard-xp" class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 mb-10 flex flex-col sm:flex-row items-center gap-6 shadow-sm">
            <div class="relative shrink-0 w-24 h-24 bg-amber-100 dark:bg-amber-900/30 border-4 border-amber-400 dark:border-amber-600 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-shield-alt text-6xl text-amber-400 dark:text-amber-500 opacity-20 absolute"></i>
                <span class="text-4xl font-black text-amber-500 dark:text-amber-400 z-10">{{ Auth::user()->level ?? 1 }}</span>
                <div class="absolute -bottom-3 px-3 py-1 bg-amber-400 dark:bg-amber-500 text-white text-[10px] font-black rounded-full uppercase tracking-widest border-2 border-white dark:border-gray-800 shadow-sm">Level</div>
            </div>
            
            <div class="flex-1 w-full">
                <div class="flex justify-between items-end mb-3">
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wider text-lg">Progres Belajar</h3>
                    <span class="text-sm font-black text-amber-500">
                        {{ Auth::user()->xp ?? 0 }} / {{ Auth::user()->next_level_xp ?? 100 }} XP
                    </span>
                </div>
                <div class="h-6 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden border-2 border-slate-200 dark:border-gray-600 shadow-inner p-1">
                    @php
                        $xp = Auth::user()->xp ?? 0;
                        $nextLvl = Auth::user()->next_level_xp ?? 100;
                        $pct = min(100, ($xp / $nextLvl) * 100);
                    @endphp
                    <div class="h-full bg-amber-400 rounded-full transition-all duration-1000 relative" style="width: {{ $pct }}%">
                        <div class="absolute top-0.5 left-2 right-2 h-1 bg-white/40 rounded-full"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest"><i class="fas fa-bolt text-amber-400 mr-1"></i> Selesaikan Quiz untuk dapat XP!</p>
                    <a href="{{ route('leaderboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border-2 border-b-[4px] border-amber-200 dark:border-amber-700 rounded-xl text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest hover:bg-amber-100 dark:hover:bg-amber-900/40 active:border-b-2 active:translate-y-0.5 transition-all shrink-0">
                        <i class="fas fa-trophy"></i> Leaderboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div id="onboard-stats" class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            {{-- Stat 1 --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl p-6 flex items-center justify-between shadow-sm hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Quiz Yang Dikerjakan</p>
                    <p class="text-4xl font-black text-blue-500">{{ $quizCount }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 text-blue-500 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>

            {{-- Stat 2 --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl p-6 flex items-center justify-between shadow-sm hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Huruf Dikuasai</p>
                    <p class="text-4xl font-black text-emerald-500">{{ $masteredCount }}</p>
                </div>
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>

            {{-- Stat 3 --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl p-6 flex items-center justify-between shadow-sm hover:border-amber-200 dark:hover:border-amber-800 transition-colors">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Streak</p>
                    <p class="text-4xl font-black text-amber-500 flex items-baseline gap-1">
                        {{ $streak }} <span class="text-sm font-black text-slate-400 uppercase tracking-widest">Hari</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 text-amber-500 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>

        {{-- Main Modules Menu --}}
        <div id="onboard-modules">
            <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-3">
                <i class="fas fa-compass text-slate-400"></i> Pilih Modul
                <span class="h-1 flex-1 bg-slate-200 dark:bg-gray-800 rounded-full"></span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Modul: Quiz (Roadmap) --}}
                <a href="{{ route('quizzes.index') }}" class="block bg-[#1cb0f6] border-2 border-b-[8px] border-[#1899d6] dark:border-[#1172a1] rounded-[2rem] p-6 hover:brightness-110 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-7xl text-white opacity-20 group-hover:rotate-12 transition-transform">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="w-16 h-16 bg-white/20 text-white border-2 border-b-4 border-white/30 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform relative z-10">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase tracking-wide relative z-10">Peta Quiz</h3>
                    <p class="text-xs font-bold text-[#bae6fd] mt-2 line-clamp-2 relative z-10">Uji kemampuanmu di mode petualangan!</p>
                </a>

                {{-- Modul: Semua Huruf --}}
                <a href="/list" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-500 border-2 border-b-4 border-amber-200 dark:border-amber-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-font"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-amber-500 transition-colors">Semua Huruf</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Jelajahi Kanji, Hiragana, dan Katakana lengkap.</p>
                </a>

                {{-- Modul: Hiragana --}}
                <a href="/hiragana" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 text-rose-500 border-2 border-b-4 border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        あ
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-rose-500 transition-colors">Hiragana</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Latihan membaca & menulis dasar Hiragana.</p>
                </a>

                {{-- Modul: Katakana --}}
                <a href="/katakana" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-500 border-2 border-b-4 border-blue-200 dark:border-blue-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        ア
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-blue-500 transition-colors">Katakana</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Pelajari karakter untuk kata serapan asing.</p>
                </a>

                {{-- Modul: Kanji --}}
                <a href="/kanji" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        漢
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-emerald-500 transition-colors">Kanji</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Latihan karakter Kanji & urutan goresannya.</p>
                </a>

                {{-- Modul: Vocabulary --}}
                <a href="{{ route('vocabulary.index') }}" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 border-2 border-b-4 border-indigo-200 dark:border-indigo-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-indigo-500 transition-colors">Kosakata</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Perkaya perbendaharaan kata bahasa Jepangmu.</p>
                </a>

                {{-- Modul: Materi --}}
                <a href="{{ route('materi.index') }}" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500 border-2 border-b-4 border-cyan-200 dark:border-cyan-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-cyan-500 transition-colors">Materi</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Baca materi pelajaran bahasa Jepang terbaru.</p>
                </a>

                {{-- Modul: Visual Novel --}}
                <a href="{{ route('vn.scenes') }}" class="block bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 hover:bg-slate-50 dark:hover:bg-gray-700/50 hover:-translate-y-1 active:translate-y-1 active:border-b-2 transition-all group">
                    <div class="w-16 h-16 bg-violet-100 dark:bg-violet-900/30 text-violet-500 border-2 border-b-4 border-violet-200 dark:border-violet-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-theater-masks"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide group-hover:text-violet-500 transition-colors">Visual Novel</h3>
                    <p class="text-xs font-bold text-slate-400 mt-2 line-clamp-2">Belajar bahasa Jepang via cerita interaktif!</p>
                </a>

            </div>
            {{-- Alert Tugas Akhir & GForm --}}
            <div class="bg-indigo-100 dark:bg-indigo-900/30 border-2 border-b-[8px] border-indigo-200 dark:border-indigo-800 rounded-[2rem] p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6 shadow-sm mt-10 mb-8 relative overflow-hidden">
                
                {{-- Ornamen Background --}}
                <i class="fas fa-bullhorn absolute -right-4 -bottom-4 text-7xl text-indigo-500 opacity-10 -rotate-12"></i>
                
                {{-- Icon Toga --}}
                <div class="w-16 h-16 shrink-0 bg-indigo-500 text-white border-2 border-b-4 border-indigo-700 rounded-2xl flex items-center justify-center text-3xl shadow-sm rotate-[-10deg] hover:rotate-0 transition-transform z-10">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                
                {{-- Teks Pengumuman --}}
                <div class="flex-1 w-full text-center sm:text-left z-10">
                    <h3 class="font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-wider text-lg mb-1">
                        Hai Minna-san! Bantu Pembuat Manabu Yuk!
                    </h3>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400 leading-relaxed">
                        Website ini dikembangkan untuk keperluan <span class="text-indigo-600 dark:text-indigo-400 font-black">Tugas Akhir</span>. Saya butuh saran dan masukan dari kalian agar aplikasi ini menjadi lebih baik lagi! ありがとうございます。よろしくお願いいたします。⸜(｡˃ ᵕ ˂ )⸝♡
                    </p>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400 leading-relaxed">Dan jika kalian ingin membantu saya melengkapi materi, huruf, quiz, story dan lain lain. feel free to dm me on instagram @envvavor</p>
                </div>
                
                {{-- Tombol CTA ke GForm --}}
                <div class="w-full sm:w-auto shrink-0 z-10">
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSeYMIs2BYbEhI_ySnVbS-1_8y7T9eIeFZk5cilJ6qMQfjPcWA/viewform?usp=publish-editor" target="_blank" rel="noopener noreferrer"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-indigo-500 border-2 border-b-[6px] border-indigo-700 text-white transition-all rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 active:border-b-2 active:translate-y-1 shadow-sm">
                        <i class="fas fa-clipboard-check mr-2 text-lg"></i> Isi Kuesioner
                    </a>
                </div>
            </div>
            {{-- Footer Buy Me Coffee --}}
            <div class="flex items-center justify-center mt-12 mb-4">
                <a href="https://trakteer.id/arya_surya_syaputra" target="_blank" rel="noopener noreferrer"
                class="group flex items-center gap-3 px-6 py-3 sm:px-8 sm:py-4 bg-[#ffc82c] border-2 border-b-[6px] border-[#d8a110] text-slate-900 font-black uppercase tracking-widest text-xs sm:text-sm rounded-2xl sm:rounded-[1.25rem] hover:brightness-105 hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-coffee text-lg group-hover:-translate-y-1 group-hover:rotate-12 transition-transform duration-300"></i>
                    <span>Traktir Gw Kopi</span>
                </a>
            </div>
        </div>
    </div>
</div>

@if(!Auth::user()->has_seen_onboarding)
<div id="onboarding-overlay" style="display:none;">
    <div id="onboard-backdrop"></div>
    <div id="onboarding-tooltip">
        <div class="onboard-card">
            <div class="onboard-head">
                <div id="tooltip-icon" class="onboard-icon-box">
                    <i class="fas fa-hand-sparkles"></i>
                </div>
                <div>
                    <p id="tooltip-step" class="onboard-step-label"></p>
                    <h3 id="tooltip-title" class="onboard-title"></h3>
                </div>
            </div>
            <p id="tooltip-desc" class="onboard-desc"></p>
            <div class="onboard-footer">
                <button id="onboard-skip" class="onboard-btn-skip">Lewati</button>
                <div class="onboard-actions">
                    <div id="onboard-dots" class="onboard-dots"></div>
                    <button id="onboard-prev" class="onboard-btn-prev" style="display:none;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button id="onboard-next" class="onboard-btn-next">Lanjut</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #onboarding-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        pointer-events: none;
    }
    #onboard-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.7);
        z-index: 1;
        pointer-events: auto;
        transition: opacity 0.3s ease;
    }
    .onboard-highlight {
        position: relative !important;
        z-index: 99999 !important;
        box-shadow: 0 0 0 4px #1cb0f6, 0 0 0 9999px rgba(15,23,42,0.7) !important;
        border-radius: 1.5rem !important;
        pointer-events: none !important;
        transition: box-shadow 0.4s ease;
    }
    #onboarding-tooltip {
        position: fixed;
        z-index: 100000;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: calc(100vw - 2rem);
        max-width: 420px;
        pointer-events: auto;
        box-sizing: border-box;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    #onboarding-tooltip.tooltip-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #onboarding-tooltip.tooltip-visible {
        opacity: 1;
        visibility: visible;
    }
    .onboard-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-bottom-width: 8px;
        border-radius: 2rem;
        padding: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    html.dark .onboard-card {
        background: #1e293b;
        border-color: #334155;
    }
    .onboard-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .onboard-icon-box {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        border: 2px solid;
        border-bottom-width: 4px;
    }
    .onboard-step-label {
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin: 0;
    }
    .onboard-title {
        font-size: 1.1rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #1e293b;
        margin: 0;
    }
    html.dark .onboard-title { color: #f1f5f9; }
    .onboard-desc {
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }
    html.dark .onboard-desc { color: #94a3b8; }
    .onboard-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .onboard-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .onboard-dots {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-right: 0.5rem;
    }
    .onboard-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: all 0.2s;
    }
    html.dark .onboard-dot { background: #475569; }
    .onboard-dot.active {
        width: 10px;
        height: 10px;
        background: #1cb0f6;
    }
    .onboard-btn-skip {
        padding: 0.5rem 1rem;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        background: none;
        border: none;
        cursor: pointer;
    }
    .onboard-btn-skip:hover { color: #64748b; }
    .onboard-btn-prev {
        padding: 0.5rem 0.75rem;
        font-size: 12px;
        font-weight: 900;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-bottom-width: 4px;
        border-radius: 0.75rem;
        color: #64748b;
        cursor: pointer;
    }
    html.dark .onboard-btn-prev { background: #334155; border-color: #475569; color: #94a3b8; }
    .onboard-btn-prev:active { border-bottom-width: 2px; transform: translateY(2px); }
    .onboard-btn-next {
        padding: 0.5rem 1.25rem;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: #1cb0f6;
        border: 2px solid #1899d6;
        border-bottom-width: 4px;
        border-radius: 0.75rem;
        color: #fff;
        cursor: pointer;
    }
    .onboard-btn-next:hover { filter: brightness(1.1); }
    .onboard-btn-next:active { border-bottom-width: 2px; transform: translateY(2px); }
    @media (max-width: 640px) {
        #onboarding-tooltip { max-width: calc(100vw - 1.5rem); width: calc(100vw - 1.5rem); }
        .onboard-card { padding: 1rem; border-radius: 1.25rem; }
        .onboard-icon-box { width: 2.5rem; height: 2.5rem; font-size: 1rem; }
        .onboard-title { font-size: 0.9rem; }
        .onboard-desc { font-size: 0.8rem; margin-bottom: 1rem; }
        .onboard-dots { display: none; }
        .onboard-footer { gap: 0.25rem; }
        .onboard-btn-next { padding: 0.5rem 1rem; font-size: 11px; }
        .onboard-btn-skip { padding: 0.4rem 0.5rem; font-size: 10px; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var steps = [
        { target: null, icon: 'fas fa-door-open', color: '#1cb0f6', title: 'Yokoso! (ようこそ!)', desc: 'Selamat datang di Manabu! Ini adalah dashboard utama tempat kamu memulai semua aktivitas belajar. Yuk, saya tunjukkan fitur-fitur pentingnya!' },
        { target: '#onboard-header', icon: 'fas fa-user-circle', color: '#1cb0f6', title: 'Halaman Utamamu', desc: 'Ini adalah header dashboard. Di sini kamu bisa melihat sapaan personal dan mengakses menu profil.' },
        { target: '#onboard-xp', icon: 'fas fa-bolt', color: '#f59e0b', title: 'Level & XP', desc: 'Setiap kali kamu menyelesaikan quiz, kamu akan mendapatkan XP! Kumpulkan XP untuk naik level. Semakin tinggi levelmu, semakin jago!' },
        { target: '#onboard-stats', icon: 'fas fa-chart-bar', color: '#3b82f6', title: 'Statistik Belajar', desc: 'Pantau progresmu di sini! Lihat berapa quiz yang sudah dikerjakan, huruf yang dikuasai, dan streak harianmu.' },
        { target: '#onboard-modules', icon: 'fas fa-compass', color: '#10b981', title: 'Modul Belajar', desc: 'Pilih modul yang mau kamu pelajari! Ada Peta Quiz, Hiragana, Katakana, Kanji, Kosakata, Materi pelajaran, dan Visual Novel. Semuanya gratis!' },
        { target: '#onboard-profile', icon: 'fas fa-cog', color: '#8b5cf6', title: 'Menu Profil', desc: 'Klik tombol ini untuk mengatur profil, mengganti tema (dark/light mode), atau logout dari akunmu.' },
        { target: null, icon: 'fas fa-rocket', color: '#1cb0f6', title: 'Siap Belajar!', desc: 'Sekarang kamu sudah tahu semua fiturnya. Mulai petualangan belajar bahasa Jepangmu sekarang! Ganbare! (がんばれ!)' }
    ];

    var currentStep = 0;
    var prevHighlight = null;
    var stepping = false;

    var overlay = document.getElementById('onboarding-overlay');
    var backdrop = document.getElementById('onboard-backdrop');
    var tooltip = document.getElementById('onboarding-tooltip');
    var tooltipIcon = document.getElementById('tooltip-icon');
    var tooltipStep = document.getElementById('tooltip-step');
    var tooltipTitle = document.getElementById('tooltip-title');
    var tooltipDesc = document.getElementById('tooltip-desc');
    var dotsContainer = document.getElementById('onboard-dots');
    var btnNext = document.getElementById('onboard-next');
    var btnPrev = document.getElementById('onboard-prev');
    var btnSkip = document.getElementById('onboard-skip');

    function buildDots() {
        dotsContainer.innerHTML = '';
        for (var i = 0; i < steps.length; i++) {
            var dot = document.createElement('div');
            dot.className = 'onboard-dot' + (i === currentStep ? ' active' : '');
            dotsContainer.appendChild(dot);
        }
    }

    function hideTooltip() {
        tooltip.classList.remove('tooltip-visible');
        tooltip.classList.add('tooltip-hidden');
    }

    function showTooltipVisible() {
        tooltip.classList.remove('tooltip-hidden');
        tooltip.classList.add('tooltip-visible');
    }

    function positionTooltip(target) {
        if (!target) {
            tooltip.style.position = 'fixed';
            tooltip.style.left = '50%';
            tooltip.style.top = '50%';
            tooltip.style.transform = 'translate(-50%, -50%)';
            return;
        }

        tooltip.style.position = 'fixed';
        tooltip.style.transform = 'none';

        var rect = target.getBoundingClientRect();
        var vw = window.innerWidth;
        var vh = window.innerHeight;
        var tw = tooltip.offsetWidth || (vw - 32);
        var th = tooltip.offsetHeight || 200;
        var gap = 12;
        var safe = 8;

        var spaceBelow = vh - rect.bottom;
        var spaceAbove = rect.top;
        var top;

        if (spaceBelow >= th + gap + safe) {
            top = rect.bottom + gap;
        } else if (spaceAbove >= th + gap + safe) {
            top = rect.top - th - gap;
        } else {
            top = vh - th - safe;
        }
        top = Math.max(safe, Math.min(top, vh - th - safe));

        var left = rect.left + rect.width / 2 - tw / 2;
        left = Math.max(safe, Math.min(left, vw - tw - safe));

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    }

    function scrollToTarget(el, callback) {
        var rect = el.getBoundingClientRect();
        var vh = window.innerHeight;
        var elH = el.offsetHeight;
        var margin = 80;
        var isVisible = rect.top >= -margin && rect.top < vh - margin;

        if (isVisible) {
            callback();
            return;
        }

        var scrollBlock = elH > vh * 0.5 ? 'start' : 'center';
        el.scrollIntoView({ behavior: 'smooth', block: scrollBlock });

        var settled = false;
        var lastY = window.scrollY;
        var stableCount = 0;
        var checkInterval = setInterval(function() {
            var nowY = window.scrollY;
            if (Math.abs(nowY - lastY) < 1) {
                stableCount++;
            } else {
                stableCount = 0;
            }
            lastY = nowY;
            if (stableCount >= 3) {
                clearInterval(checkInterval);
                if (!settled) { settled = true; callback(); }
            }
        }, 50);

        setTimeout(function() {
            clearInterval(checkInterval);
            if (!settled) { settled = true; callback(); }
        }, 1200);
    }

    function updateContent(index) {
        var step = steps[index];
        tooltipIcon.innerHTML = '<i class="' + step.icon + '"></i>';
        tooltipIcon.style.background = step.color + '1a';
        tooltipIcon.style.color = step.color;
        tooltipIcon.style.borderColor = step.color + '33';
        tooltipStep.textContent = 'Langkah ' + (index + 1) + ' dari ' + steps.length;
        tooltipTitle.textContent = step.title;
        tooltipDesc.textContent = step.desc;
        btnPrev.style.display = index > 0 ? '' : 'none';
        btnNext.innerHTML = index === steps.length - 1
            ? '<i class="fas fa-check" style="margin-right:4px"></i> Mulai!'
            : 'Lanjut <i class="fas fa-arrow-right" style="margin-left:4px"></i>';
        btnSkip.style.display = index === steps.length - 1 ? 'none' : '';
        buildDots();
    }

    function showStep(index) {
        if (stepping) return;
        stepping = true;
        currentStep = index;
        var step = steps[index];

        hideTooltip();

        if (prevHighlight) {
            prevHighlight.classList.remove('onboard-highlight');
            prevHighlight = null;
        }

        updateContent(index);

        if (!step.target) {
            backdrop.style.display = '';
            positionTooltip(null);
            requestAnimationFrame(function() {
                showTooltipVisible();
                stepping = false;
            });
            return;
        }

        var target = document.querySelector(step.target);
        if (!target) {
            backdrop.style.display = '';
            positionTooltip(null);
            requestAnimationFrame(function() {
                showTooltipVisible();
                stepping = false;
            });
            return;
        }

        backdrop.style.display = '';

        scrollToTarget(target, function() {
            target.classList.add('onboard-highlight');
            prevHighlight = target;
            backdrop.style.display = 'none';

            requestAnimationFrame(function() {
                positionTooltip(target);
                requestAnimationFrame(function() {
                    showTooltipVisible();
                    stepping = false;
                });
            });
        });
    }

    function finishOnboarding() {
        if (prevHighlight) prevHighlight.classList.remove('onboard-highlight');
        overlay.style.display = 'none';

        fetch('{{ route("onboarding.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }

    btnNext.addEventListener('click', function() {
        if (currentStep < steps.length - 1) showStep(currentStep + 1);
        else finishOnboarding();
    });
    btnPrev.addEventListener('click', function() {
        if (currentStep > 0) showStep(currentStep - 1);
    });
    btnSkip.addEventListener('click', finishOnboarding);

    var resizeTimer = null;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (prevHighlight) positionTooltip(prevHighlight);
            else if (!steps[currentStep].target) positionTooltip(null);
        }, 100);
    });

    var scrollTimer = null;
    window.addEventListener('scroll', function() {
        if (stepping || !prevHighlight) return;
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            if (prevHighlight) positionTooltip(prevHighlight);
        }, 50);
    });

    setTimeout(function() {
        overlay.style.display = '';
        hideTooltip();
        showStep(0);
    }, 500);
});
</script>
@endif

@endsection