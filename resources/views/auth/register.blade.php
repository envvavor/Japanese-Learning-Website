<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - 学ぶ Manabu</title>
    <link rel="icon" href="{{ asset('storage/images/logo_manabu.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script>
        if (localStorage.getItem('darkMode') !== 'false') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen flex bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans p-4 sm:p-6 transition-colors duration-300 relative overflow-x-hidden overflow-y-auto"
      x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }"
      x-init="if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">

    <i class="fas fa-meteor absolute top-10 left-20 text-6xl text-rose-300/30 dark:text-rose-500/10 pointer-events-none -z-10 -rotate-45"></i>
    <i class="fas fa-puzzle-piece absolute bottom-20 left-10 text-7xl text-[#1cb0f6]/10 dark:text-[#1cb0f6]/5 pointer-events-none -z-10 rotate-12"></i>
    <i class="fas fa-trophy absolute top-40 right-10 text-5xl text-amber-300/30 dark:text-amber-500/10 pointer-events-none -z-10 rotate-[20deg] animate-pulse"></i>
    <i class="fas fa-headphones absolute bottom-10 right-20 text-6xl text-emerald-400/10 dark:text-emerald-500/10 pointer-events-none -z-10 -rotate-12"></i>

    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
            class="fixed top-5 right-5 z-50 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm transition-all duration-200 bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 active:border-b-2 active:translate-y-1 hover:bg-slate-100 dark:hover:bg-gray-700"
            :title="darkMode ? 'Light Mode' : 'Dark Mode'">
        <i class="fas fa-sun text-2xl text-amber-500" x-show="darkMode" x-cloak></i>
        <i class="fas fa-moon text-2xl text-indigo-500" x-show="!darkMode" x-cloak></i>
    </button>

    <div class="relative w-full max-w-md z-10 m-auto py-8">
        <div class="relative w-full bg-white dark:bg-gray-800 rounded-[2rem] border-2 border-b-[8px] border-slate-200 dark:border-gray-700 p-8 sm:p-10 flex flex-col items-center shadow-sm">

            <!-- <div class="w-full flex justify-start mb-4 mt-2">
                <a href="/" class="px-4 py-2 bg-slate-50 dark:bg-gray-900 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white active:border-b-2 active:translate-y-1 transition-all">
                    <i class="fas fa-arrow-left mr-1"></i> Beranda
                </a>
            </div> -->

            <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 border-2 border-emerald-200 dark:border-emerald-800 rounded-[1.5rem] flex items-center justify-center p-3 mb-5 shadow-sm">
                <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Manabu Logo" class="w-full h-full object-contain">
            </div>

            <h1 class="text-3xl font-black tracking-wider mb-2 text-slate-800 dark:text-white uppercase text-center">
                Buat Akun <span class="text-emerald-500">Manabu</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 mb-8 text-center uppercase tracking-widest">Mulai petualangan belajarmu hari ini.</p>

            @if ($errors->any())
                <div class="w-full mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 px-5 py-4 rounded-2xl text-sm font-bold flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1 text-lg"></i>
                    <ul class="list-disc list-inside leading-relaxed">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── GOOGLE REGISTER BUTTON ── --}}
            <a href="{{ route('auth.google') }}"
               class="w-full flex items-center justify-center gap-3 py-4 px-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-slate-700 dark:text-slate-200 font-black text-sm uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-gray-800 active:border-b-2 active:translate-y-1 transition-all shadow-sm mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>

            <div class="w-full flex items-center gap-3 mb-6">
                <div class="flex-1 h-px bg-slate-200 dark:bg-gray-700"></div>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">atau daftar manual</span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-gray-700"></div>
            </div>

            <form action="{{ route('register') }}" method="POST" class="w-full space-y-5">
                @csrf

                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase" for="name">Nama</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 text-sm font-bold focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="Taro Yamada">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase" for="email">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 text-sm font-bold focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase" for="password">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 text-sm font-bold focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="Minimal 8 karakter">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" onclick="togglePasswordVisibility('password', this)">
                            <i class="fas fa-eye text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase" for="password_confirmation">Konfirmasi Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 text-sm font-bold focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="Ulangi sandi">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" onclick="togglePasswordVisibility('password_confirmation', this)">
                            <i class="fas fa-eye text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-500 border-2 border-b-[6px] border-emerald-700 text-white transition-all py-4 rounded-2xl font-black uppercase tracking-widest text-lg hover:brightness-110 active:border-b-2 active:translate-y-1 mt-6 flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>

            <div class="mt-8 pt-6 border-t-2 border-dashed border-slate-100 dark:border-gray-700 w-full text-center">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    Sudah Punya Akun?
                    <a href="{{ route('login') }}" class="text-emerald-500 hover:text-emerald-400 ml-1 transition-colors"><i class="fas fa-sign-in-alt mr-1"></i> Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
<script>
    function togglePasswordVisibility(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
</html>
