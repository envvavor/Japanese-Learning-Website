<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Kata Sandi - 学ぶ Manabu</title>
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
      x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false', showPass: false, showConfirm: false }"
      x-init="if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">

    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
            class="fixed top-5 right-5 z-50 w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 active:border-b-2 active:translate-y-1 hover:bg-slate-100 dark:hover:bg-gray-700 transition-all">
        <i class="fas fa-sun text-2xl text-amber-500" x-show="darkMode" x-cloak></i>
        <i class="fas fa-moon text-2xl text-indigo-500" x-show="!darkMode" x-cloak></i>
    </button>

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] border-2 border-b-[8px] border-slate-200 dark:border-gray-700 p-8 sm:p-10 flex flex-col items-center shadow-sm">

            <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/30 border-2 border-indigo-200 dark:border-indigo-800 rounded-[1.5rem] flex items-center justify-center mb-6 text-4xl">
                <i class="fas fa-lock text-indigo-500"></i>
            </div>

            <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-2 text-center">Buat Kata Sandi Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-8">Pastikan kata sandi baru kamu mudah diingat tapi sulit ditebak.</p>

            @if ($errors->any())
                <div class="w-full mb-6 bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 px-5 py-4 rounded-2xl text-sm font-bold flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1"></i>
                    <div>@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="w-full space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email (pre-filled, readonly) --}}
                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                            class="w-full bg-slate-100 dark:bg-gray-900/50 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-500 dark:text-slate-400 rounded-2xl pl-12 pr-4 py-4 text-base font-bold outline-none cursor-not-allowed">
                    </div>
                </div>

                {{-- New password --}}
                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase">Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-12 py-4 text-base font-bold focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="Minimal 8 karakter">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm password --}}
                <div>
                    <label class="block text-[10px] font-black tracking-widest text-slate-400 dark:text-slate-500 mb-2 uppercase">Konfirmasi Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-slate-400 dark:text-slate-500 text-lg"></i>
                        </div>
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                            class="w-full bg-slate-50 dark:bg-gray-900 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white rounded-2xl pl-12 pr-12 py-4 text-base font-bold focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="Ulangi sandi baru">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 border-2 border-b-[6px] border-indigo-800 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-base hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all flex items-center justify-center gap-2 mt-2">
                    <i class="fas fa-save"></i> Simpan Kata Sandi Baru
                </button>
            </form>
        </div>
    </div>
</body>
</html>
