<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email - 学ぶ Manabu</title>
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
    <style>
        .code-input { letter-spacing: 0.5em; font-size: 2rem; text-align: center; }
        .code-input::placeholder { letter-spacing: 0.2em; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans p-4 transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false', countdown: 0, interval: null }"
      x-init="
        if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');
        // Start 60s cooldown on page load
        countdown = parseInt(localStorage.getItem('verifyCountdown') || '0');
        if (countdown > 0) {
            interval = setInterval(() => {
                countdown--;
                localStorage.setItem('verifyCountdown', countdown);
                if (countdown <= 0) clearInterval(interval);
            }, 1000);
        }
      ">

    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
            class="fixed top-5 right-5 z-50 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 active:border-b-2 active:translate-y-1 hover:bg-slate-100 dark:hover:bg-gray-700 transition-all">
        <i class="fas fa-sun text-2xl text-amber-500" x-show="darkMode" x-cloak></i>
        <i class="fas fa-moon text-2xl text-indigo-500" x-show="!darkMode" x-cloak></i>
    </button>

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] border-2 border-b-[8px] border-slate-200 dark:border-gray-700 p-8 sm:p-10 flex flex-col items-center shadow-sm">

            {{-- Icon --}}
            <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/30 border-2 border-indigo-200 dark:border-indigo-800 rounded-[1.5rem] flex items-center justify-center mb-6 text-4xl">
                <i class="fas fa-envelope text-indigo-500"></i>
            </div>

            <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-2 text-center">Cek Email Kamu!</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-8 leading-relaxed">
                Kami telah mengirimkan kode 6 digit ke<br>
                <strong class="text-indigo-600 dark:text-indigo-400">{{ Auth::user()->email ?? '—' }}</strong>
            </p>

            {{-- Success --}}
            @if (session('status'))
                <div class="w-full mb-6 bg-emerald-50 dark:bg-emerald-900/20 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl text-sm font-bold">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="w-full mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 px-5 py-4 rounded-2xl text-sm font-bold">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Code input form --}}
            <form action="{{ route('verification.verify') }}" method="POST" class="w-full">
                @csrf
                <div class="mb-6">
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-3 uppercase text-center">Masukkan Kode Verifikasi</label>
                    {{-- Single input for 6-digit code --}}
                    <input type="text" name="code" id="codeInput"
                        maxlength="6" inputmode="numeric" autocomplete="one-time-code" required
                        class="code-input w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl px-4 py-5 font-black focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 outline-none transition-all tracking-[0.5em]"
                        placeholder="— — — — — —"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,6)">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 border-2 border-b-[6px] border-indigo-800 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-base hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Verifikasi Sekarang
                </button>
            </form>

            {{-- Resend --}}
            <div class="mt-6 w-full text-center">
                <p class="text-xs text-slate-400 dark:text-slate-500 mb-3">Tidak menerima kode?</p>

                <form action="{{ route('verification.resend') }}" method="POST" @submit="
                    countdown = 60;
                    localStorage.setItem('verifyCountdown', 60);
                    interval = setInterval(() => {
                        countdown--;
                        localStorage.setItem('verifyCountdown', countdown);
                        if (countdown <= 0) clearInterval(interval);
                    }, 1000);
                ">
                    @csrf
                    <button type="submit"
                        :disabled="countdown > 0"
                        :class="countdown > 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400'"
                        class="w-full py-3 px-5 border-2 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 rounded-2xl text-sm font-black uppercase tracking-wider transition-all">
                        <span x-show="countdown <= 0"><i class="fas fa-paper-plane mr-1"></i> Kirim Ulang Kode</span>
                        <span x-show="countdown > 0">⏱ Kirim ulang dalam <span x-text="countdown"></span>s</span>
                    </button>
                </form>
            </div>

            {{-- Logout link --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-400 dark:text-slate-500 hover:text-rose-500 transition-colors uppercase tracking-widest">
                    <i class="fas fa-sign-out-alt mr-1"></i> Ganti Akun / Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
