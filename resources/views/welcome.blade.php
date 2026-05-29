<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学ぶ Manabu — Belajar Bahasa Jepang</title>
    <link rel="icon" href="{{ asset('storage/images/logo_manabu.png') }}" type="image/png">
    <meta name="google-site-verification" content="Z1rsfnOxXA5mPIz0cpQP4CxmPREguJuekFq7pLq4KkY" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        html.dark { color-scheme: dark; }
        body { transition: background-color 0.3s ease, color 0.3s ease; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        @keyframes float-delay { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float { animation: float 4s ease-in-out infinite; }
        .float-delay { animation: float-delay 5s ease-in-out 1s infinite; }
        @keyframes fade-up { from { opacity:0; transform:translateY(30px) } to { opacity:1; transform:translateY(0) } }
        .fade-up { animation: fade-up 0.7s ease-out both; }
        .fade-up-d1 { animation-delay: 0.1s; }
        .fade-up-d2 { animation-delay: 0.2s; }
        .fade-up-d3 { animation-delay: 0.3s; }
        .fade-up-d4 { animation-delay: 0.4s; }
        .fade-up-d5 { animation-delay: 0.5s; }
        .fade-up-d6 { animation-delay: 0.6s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans overflow-x-hidden"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b-4 border-slate-200 dark:border-gray-800 shadow-sm">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20 sm:h-24">
            
            <a href="/" class="flex items-center gap-3 sm:gap-4 group shrink-0">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl flex items-center justify-center p-1.5 sm:p-2 group-hover:scale-110 active:scale-95 transition-all shadow-sm">
                    <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-800 dark:text-white">
                    <span class="text-[#1cb0f6]">学ぶ</span> 
                    <span class="hidden sm:inline">Manabu</span>
                </span>
            </a>

            <div class="flex items-center gap-2 sm:gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                    class="flex items-center justify-center h-11 sm:h-14 px-4 sm:px-6 bg-[#1cb0f6] border-2 border-b-[4px] sm:border-b-[6px] border-[#1899d6] text-white rounded-xl sm:rounded-2xl font-black uppercase tracking-widest text-[10px] sm:text-xs hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                        <i class="fas fa-gamepad mr-0 sm:mr-2 text-sm sm:text-base"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                    class="flex items-center justify-center h-11 sm:h-14 px-4 sm:px-6 bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl font-black uppercase tracking-widest text-[10px] sm:text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                    class="flex items-center justify-center h-11 sm:h-14 px-4 sm:px-6 bg-[#1cb0f6] border-2 border-b-[4px] sm:border-b-[6px] border-[#1899d6] text-white rounded-xl sm:rounded-2xl font-black uppercase tracking-widest text-[10px] sm:text-xs hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                        Daftar
                    </a>
                @endauth
                
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
                        class="w-11 h-11 sm:w-14 sm:h-14 shrink-0 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-sm transition-all duration-200 bg-white dark:bg-gray-800 border-2 border-b-[4px] sm:border-b-[6px] border-slate-200 dark:border-gray-700 active:border-b-2 active:translate-y-1 hover:bg-slate-100 dark:hover:bg-gray-700"
                        :title="darkMode ? 'Light Mode' : 'Dark Mode'">
                    <i class="fas fa-sun text-base sm:text-2xl text-amber-500" x-show="darkMode" x-cloak></i>
                    <i class="fas fa-moon text-base sm:text-2xl text-indigo-500" x-show="!darkMode" x-cloak></i>
                </button>
            </div>
            
        </div>
    </nav>
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <i class="fas fa-cloud absolute top-16 left-[8%] text-7xl text-slate-200 dark:text-slate-800 opacity-60 pointer-events-none float"></i>
        <i class="fas fa-star absolute top-32 right-[12%] text-5xl text-amber-300/40 dark:text-amber-500/15 pointer-events-none float-delay rotate-12"></i>
        <i class="fas fa-gamepad absolute bottom-24 left-[15%] text-6xl text-[#1cb0f6]/10 pointer-events-none float rotate-[-15deg]"></i>
        <i class="fas fa-book-open absolute bottom-32 right-[10%] text-5xl text-emerald-400/15 pointer-events-none float-delay"></i>
        <i class="fas fa-torii-gate absolute top-48 left-[45%] text-8xl text-rose-300/10 dark:text-rose-500/5 pointer-events-none float"></i>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 py-20">

            <img src="{{asset('storage/images/logo_manabu.png')}}" alt="Logo Manabu" class="w-32 mx-auto mb-4 leading-relaxed fade-up fade-up-d2 ">

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-slate-800 dark:text-white uppercase tracking-wider leading-tight mb-6 fade-up fade-up-d1">
                Belajar Bahasa<br>
                Jepang Jadi <span class="text-[#1cb0f6]">Seru!</span>
            </h1>

            <p class="text-lg sm:text-xl font-bold text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed fade-up fade-up-d2">
                Kuasai Hiragana, Katakana, dan Kanji dengan metode interaktif.
                Latihan menulis, quiz, visual novel, dan validasi AI semua dalam satu platform!
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 fade-up fade-up-d3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-10 py-5 bg-[#1cb0f6] border-2 border-b-[8px] border-[#1899d6] text-white rounded-2xl font-black uppercase tracking-widest text-base hover:brightness-110 hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-lg flex items-center gap-3">
                        <i class="fas fa-rocket text-xl"></i> Masuk ke Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="px-10 py-5 bg-[#1cb0f6] border-2 border-b-[8px] border-[#1899d6] text-white rounded-2xl font-black uppercase tracking-widest text-base hover:brightness-110 hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-lg flex items-center gap-3">
                        <i class="fas fa-rocket text-xl"></i> Mulai Belajar Gratis
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-10 py-5 bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 text-slate-700 dark:text-slate-200 rounded-2xl font-black uppercase tracking-widest text-base hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-sm flex items-center gap-3">
                        <i class="fas fa-sign-in-alt"></i> Sudah Punya Akun
                    </a>
                @endauth
            </div>

            <div class="mt-16 flex items-center justify-center gap-8 sm:gap-12 fade-up fade-up-d4">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-rose-100 dark:bg-rose-900/30 border-2 border-b-4 border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-center text-4xl font-black text-rose-500 mb-2 float">あ</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hiragana</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 border-2 border-b-4 border-blue-200 dark:border-blue-800 rounded-2xl flex items-center justify-center text-4xl font-black text-blue-500 mb-2 float-delay">ア</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Katakana</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-center text-4xl font-black text-emerald-500 mb-2 float">漢</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kanji</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-28 bg-white dark:bg-gray-800/50 border-y-2 border-slate-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1cb0f6]/10 border-2 border-b-4 border-[#1cb0f6]/20 rounded-2xl text-xs font-black uppercase tracking-widest text-[#1cb0f6] mb-6">
                    <i class="fas fa-puzzle-piece"></i> Fitur Lengkap
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Semua yang Kamu Butuhkan</h2>
                <p class="text-base font-bold text-slate-400 max-w-xl mx-auto">Satu platform, banyak cara belajar. Dari menulis huruf sampai cerita interaktif.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 border-2 border-b-4 border-indigo-200 dark:border-indigo-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-pen-nib"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Latihan Menulis</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Tulis huruf langsung di canvas interaktif. Sistem akan memvalidasi urutan goresan dan bentuk tulisanmu secara real-time.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 text-rose-500 border-2 border-b-4 border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Materi</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Akses berbagai materi pembelajaran bahasa Jepang yang terstruktur dan mudah dipahami, mulai dari dasar hingga tingkat lanjut.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-[#1cb0f6]/10 text-[#1cb0f6] border-2 border-b-4 border-[#1cb0f6]/20 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Quiz</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Sistem quiz bertahap. Pilihan ganda, hearing, dan menulis huruf. Selesaikan satu baru bisa lanjut ke berikutnya.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-violet-100 dark:bg-violet-900/30 text-violet-500 border-2 border-b-4 border-violet-200 dark:border-violet-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-theater-masks"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Visual Novel</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Belajar lewat cerita interaktif bergaya visual novel Jepang! Lengkap dengan karakter, dialog bercabang, dan voice-over AI.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500 border-2 border-b-4 border-cyan-200 dark:border-cyan-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Audio ElevenLabs</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Dengarkan pelafalan asli yang dihasilkan oleh AI text-to-speech ElevenLabs. Latihan listening jadi lebih natural!</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-500 border-2 border-b-4 border-amber-200 dark:border-amber-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Gamifikasi & XP</h3>
                    <p class="text-sm font-bold text-slate-400 leading-relaxed">Dapatkan XP setiap kali menyelesaikan quiz! Naik level, kumpulkan streak harian, dan pantau progresmu di dashboard.</p>
                </div>

            </div>
        </div>
    </section>

    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-100 dark:bg-emerald-900/30 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-6">
                    <i class="fas fa-compass"></i> Modul Belajar
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Jelajahi Semua Modul</h2>
                <p class="text-base font-bold text-slate-400 max-w-xl mx-auto">Pilih jalur belajarmu. Dari huruf dasar hingga cerita interaktif.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 text-center hover:-translate-y-1 transition-all group">
                    <div class="w-16 h-16 mx-auto bg-rose-100 dark:bg-rose-900/30 text-rose-500 border-2 border-b-4 border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">あ</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-2 group-hover:text-rose-500 transition-colors">Hiragana</h3>
                    <p class="text-xs font-bold text-slate-400">46 karakter dasar untuk menulis kata-kata Jepang asli.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 text-center hover:-translate-y-1 transition-all group">
                    <div class="w-16 h-16 mx-auto bg-blue-100 dark:bg-blue-900/30 text-blue-500 border-2 border-b-4 border-blue-200 dark:border-blue-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">ア</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-2 group-hover:text-blue-500 transition-colors">Katakana</h3>
                    <p class="text-xs font-bold text-slate-400">46 karakter untuk kata-kata serapan dari bahasa asing.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 text-center hover:-translate-y-1 transition-all group">
                    <div class="w-16 h-16 mx-auto bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">漢</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-2 group-hover:text-emerald-500 transition-colors">Kanji</h3>
                    <p class="text-xs font-bold text-slate-400">Karakter kompleks dengan arti dan urutan goresan lengkap.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 text-center hover:-translate-y-1 transition-all group">
                    <div class="w-16 h-16 mx-auto bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 border-2 border-b-4 border-indigo-200 dark:border-indigo-800 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-2 group-hover:text-indigo-500 transition-colors">Kosakata</h3>
                    <p class="text-xs font-bold text-slate-400">Perkaya perbendaharaan kata bahasa Jepangmu.</p>
                </div>

            </div>
        </div>
    </section>

    <section class="py-20 sm:py-28 bg-white dark:bg-gray-800/50 border-y-2 border-slate-200 dark:border-gray-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-100 dark:bg-violet-900/30 border-2 border-b-4 border-violet-200 dark:border-violet-800 rounded-2xl text-xs font-black uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-6">
                    <i class="fas fa-list-ol"></i> Cara Kerja
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Mulai dalam 3 Langkah</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] rounded-full flex items-center justify-center text-3xl font-black text-white mb-6 shadow-lg">1</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Buat Akun</h3>
                    <p class="text-sm font-bold text-slate-400">Daftar gratis dengan email atau langsung masuk via Google. Cuma butuh 10 detik!</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto bg-emerald-500 border-2 border-b-[6px] border-emerald-700 rounded-full flex items-center justify-center text-3xl font-black text-white mb-6 shadow-lg">2</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Pilih Modul</h3>
                    <p class="text-sm font-bold text-slate-400">Mau belajar Hiragana, Katakana, Kanji, atau langsung quiz? Semua tersedia di dashboard.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto bg-amber-500 border-2 border-b-[6px] border-amber-700 rounded-full flex items-center justify-center text-3xl font-black text-white mb-6 shadow-lg">3</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wide mb-3">Latihan & Naik Level</h3>
                    <p class="text-sm font-bold text-slate-400">Kerjakan quiz, tulis huruf, dan baca cerita. Kumpulkan XP dan lihat progresmu naik!</p>
                </div>

            </div>
        </div>
    </section>

    <!-- <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-6">
                    <i class="fas fa-microchip"></i> Tech Stack
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Dibangun dengan Teknologi Modern</h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $techs = [
                        ['icon' => 'fab fa-laravel', 'name' => 'Laravel', 'color' => 'text-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-200 dark:border-red-800'],
                        ['icon' => 'fab fa-vuejs', 'name' => 'Vue.js', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800'],
                        ['icon' => 'fab fa-css3-alt', 'name' => 'Tailwind', 'color' => 'text-cyan-500', 'bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'border' => 'border-cyan-200 dark:border-cyan-800'],
                        ['icon' => 'fab fa-python', 'name' => 'Python CNN', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-200 dark:border-yellow-800'],
                        ['icon' => 'fas fa-robot', 'name' => 'ElevenLabs', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50 dark:bg-violet-900/20', 'border' => 'border-violet-200 dark:border-violet-800'],
                        ['icon' => 'fas fa-project-diagram', 'name' => 'Inertia.js', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'border' => 'border-indigo-200 dark:border-indigo-800'],
                    ];
                @endphp
                @foreach ($techs as $tech)
                    <div class="flex flex-col items-center p-5 {{ $tech['bg'] }} border-2 border-b-[6px] {{ $tech['border'] }} rounded-2xl hover:-translate-y-1 transition-all">
                        <i class="{{ $tech['icon'] }} text-3xl {{ $tech['color'] }} mb-3"></i>
                        <span class="text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">{{ $tech['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section> -->

    <section class="py-20 sm:py-28 bg-[#1cb0f6] relative overflow-hidden">
        <i class="fas fa-star absolute top-10 left-[10%] text-6xl text-white/10 rotate-12 pointer-events-none float"></i>
        <i class="fas fa-bolt absolute bottom-10 right-[10%] text-7xl text-white/10 -rotate-12 pointer-events-none float-delay"></i>
        <i class="fas fa-graduation-cap absolute top-20 right-[25%] text-5xl text-white/10 pointer-events-none float"></i>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 border-2 border-b-4 border-white/30 rounded-2xl text-xs font-black uppercase tracking-widest text-white mb-8">
                <i class="fas fa-fire"></i> Gratis Selamanya
            </div>

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white uppercase tracking-wider mb-6">Siap Belajar<br>Bahasa Jepang?</h2>
            <p class="text-lg font-bold text-[#bae6fd] mb-10 max-w-xl mx-auto">Bergabunglah sekarang dan mulai petualangan belajar bahasa Jepangmu. Tidak perlu bayar, tidak perlu kartu kredit!</p>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-3 px-10 py-5 bg-white border-2 border-b-[8px] border-slate-200 text-[#1cb0f6] rounded-2xl font-black uppercase tracking-widest text-base hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-lg">
                    <i class="fas fa-th-large text-xl"></i> Buka Dashboard
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-3 px-10 py-5 bg-white border-2 border-b-[8px] border-slate-200 text-[#1cb0f6] rounded-2xl font-black uppercase tracking-widest text-base hover:-translate-y-1 active:border-b-2 active:translate-y-1 transition-all shadow-lg">
                    <i class="fas fa-user-plus text-xl"></i> Daftar Sekarang
                </a>
            @endauth
        </div>
    </section>

    <footer class="bg-white dark:bg-gray-900 border-t-2 border-slate-200 dark:border-gray-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white dark:bg-gray-800 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-xl flex items-center justify-center p-1.5">
                        <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="text-lg font-black uppercase tracking-wider text-slate-800 dark:text-white">
                        <span class="text-[#1cb0f6]">学ぶ</span> Manabu
                    </span>
                </div>

                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest text-center">
                    manabu &copy; {{ date('Y') }}
                </p>

                <div class="flex items-center gap-3">
                    <a href="https://mail.google.com/mail/?view=cm&to=arya.zusu@gmail.com" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-2 px-5 py-3 bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 text-slate-700 dark:text-slate-200 font-black uppercase tracking-widest text-xs rounded-2xl hover:brightness-95 dark:hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                        <i class="fas fa-envelope"></i> Support
                    </a>
                    <a href="https://trakteer.id/arya_surya_syaputra" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-2 px-5 py-3 bg-[#ffc82c] border-2 border-b-[6px] border-[#d8a110] text-slate-900 font-black uppercase tracking-widest text-xs rounded-2xl hover:brightness-105 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                        <i class="fas fa-coffee"></i> Traktir
                    </a>
                </div>

            </div>
        </div>
    </footer>

</body>
</html>
