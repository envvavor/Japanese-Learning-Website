@extends('layouts.app')

@section('title', 'Dashboard — Manabu')

@section('content')
{{-- Background Global (Warna cerah ceria) --}}
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans pb-20" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    {{-- Dark Mode Toggle removed (moved to profile dropdown) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl flex items-center justify-center p-2 shrink-0">
                    <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">
                        Halo, <span class="text-[#1cb0f6]">{{ explode(' ', Auth::user()->name)[0] }}</span>!
                    </h1>
                    <p class="text-lg font-bold text-slate-500 dark:text-slate-400">Selamat Datang Kembali di 学ぶ</p>
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div x-data="{ open: false }" class="relative z-50">
                <button @click="open = !open" @click.away="open = false" 
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-widest gap-3 shadow-sm">
                    @if(Auth::user()->google_avatar)
                        <img src="{{ Auth::user()->google_avatar }}" alt="Avatar" class="w-7 h-7 rounded-full border-2 border-[#1cb0f6] object-cover shrink-0" referrerpolicy="no-referrer" onerror="this.outerHTML='<div class=\'w-7 h-7 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-bold text-xs shrink-0\'>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>'">
                    @else
                        <div class="w-7 h-7 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    Profil
                    <i class="fas fa-chevron-down text-sm transition-transform" :class="{'rotate-180': open}"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" x-transition.opacity x-transition:enter.duration.200ms x-transition:leave.duration.150ms 
                     class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-2xl shadow-lg overflow-hidden" x-cloak>
                    
                    <div class="p-2 space-y-1">
                        {{-- Profil Saya --}}
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-[#1cb0f6] dark:hover:text-[#1cb0f6] transition-colors">
                            <i class="fas fa-id-card w-5 text-center text-lg"></i> Profil Saya
                        </a>

                        {{-- Tema Web --}}
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center gap-3 hover:text-amber-500">
                                <i class="fas fa-paint-brush w-5 text-center text-lg"></i> Tema Web
                            </div>
                            <i class="fas text-lg" :class="darkMode ? 'fa-moon text-indigo-500' : 'fa-sun text-amber-500'"></i>
                        </button>
                        
                        <div class="h-0.5 bg-slate-100 dark:bg-gray-700 my-1 mx-2"></div>

                        {{-- Logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 text-center text-lg"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Level & XP Progress (Gamification Element) --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 mb-10 flex flex-col sm:flex-row items-center gap-6 shadow-sm">
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
                <p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest"><i class="fas fa-bolt text-amber-400 mr-1"></i> Selesaikan Quiz untuk dapat XP!</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            {{-- Stat 1 --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl p-6 flex items-center justify-between shadow-sm hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Quiz Yang Dikerjakan</p>
                    <p class="text-4xl font-black text-blue-500">0</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 text-blue-500 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>

            {{-- Stat 2 --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-3xl p-6 flex items-center justify-between shadow-sm hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Huruf Dikuasai</p>
                    <p class="text-4xl font-black text-emerald-500">0</p>
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
                        0 <span class="text-sm font-black text-slate-400 uppercase tracking-widest">Hari</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 text-amber-500 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>

        {{-- Main Modules Menu --}}
        <div>
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
                <a href="https://saweria.co/envvavor" target="_blank" rel="noopener noreferrer"
                class="group flex items-center gap-3 px-6 py-3 sm:px-8 sm:py-4 bg-[#ffc82c] border-2 border-b-[6px] border-[#d8a110] text-slate-900 font-black uppercase tracking-widest text-xs sm:text-sm rounded-2xl sm:rounded-[1.25rem] hover:brightness-105 hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                    <i class="fas fa-money-bill-wave text-lg group-hover:-translate-y-1 group-hover:rotate-12 transition-transform duration-300"></i>
                    <span>Traktir Gw Kopi</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection