<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Kata Sandi - 学ぶ Manabu</title>
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
<body class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans p-4 transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }"
      x-init="if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">

    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
            class="fixed top-5 right-5 z-50 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 active:border-b-2 active:translate-y-1 hover:bg-slate-100 dark:hover:bg-gray-700 transition-all">
        <i class="fas fa-sun text-2xl text-amber-500" x-show="darkMode" x-cloak></i>
        <i class="fas fa-moon text-2xl text-indigo-500" x-show="!darkMode" x-cloak></i>
    </button>

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] border-2 border-b-[8px] border-slate-200 dark:border-gray-700 p-8 sm:p-10 flex flex-col items-center shadow-sm">

            <div class="w-full flex justify-start mb-6">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-slate-50 dark:bg-gray-900 border-2 border-b-4 border-slate-200 dark:border-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white active:border-b-2 active:translate-y-1 transition-all">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali Login
                </a>
            </div>

            <div class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-200 dark:border-amber-800 rounded-[1.5rem] flex items-center justify-center mb-6 text-4xl">
                <i class="fas fa-key text-amber-500"></i>
            </div>

            <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-2 text-center">Lupa Kata Sandi?</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-8 leading-relaxed">
                Masukkan email akunmu dan kami akan mengirimkan link untuk membuat kata sandi baru.
            </p>

            {{-- Success --}}
            @if (session('status'))
                <div class="w-full mb-6 bg-emerald-50 dark:bg-emerald-900/20 border-2 border-b-4 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl text-sm font-bold flex items-start gap-2">
                    <i class="fas fa-check-circle mt-0.5"></i>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="w-full mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 px-5 py-4 rounded-2xl text-sm font-bold flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="w-full">
                @csrf
                <div class="mb-6">
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase" for="email">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 text-base font-bold focus:border-amber-500 dark:focus:border-amber-400 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-amber-500 border-2 border-b-[6px] border-amber-700 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-base hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
            </form>
        </div>
    </div>
</body>
</html>
