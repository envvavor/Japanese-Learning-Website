<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - 学ぶ Manabu</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#2dd4bf', 
                        primaryHover: '#14b8a6',
                        darkBg: '#050505',
                        panelBg: '#0f0f11',
                        inputBg: '#18181b',
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-darkBg text-gray-800 dark:text-gray-200 font-sans p-4 sm:p-6 transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" 
      x-init="if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">

    {{-- Dark Mode Toggle (Identik dengan Dashboard) --}}
    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)" 
            class="fixed top-5 right-5 z-50 w-11 h-11 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 bg-white dark:bg-inputBg border border-gray-200 dark:border-gray-800 hover:shadow-[0_0_15px_rgba(45,212,191,0.3)] hover:scale-110 group"
            :title="darkMode ? 'Light Mode' : 'Dark Mode'">
        <i class="fas fa-sun text-amber-500 group-hover:rotate-90 transition-transform duration-500" x-show="darkMode" x-cloak></i>
        <i class="fas fa-moon text-teal-500 group-hover:-rotate-12 transition-transform duration-500" x-show="!darkMode" x-cloak></i>
    </button>

    <div class="relative w-full max-w-md group">
        <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-blue-500 rounded-[2rem] blur opacity-20 group-hover:opacity-30 transition duration-1000 group-hover:duration-200"></div>

        <div class="relative w-full bg-white dark:bg-panelBg rounded-[2rem] shadow-2xl border border-gray-200 dark:border-gray-800 p-8 sm:p-10 flex flex-col items-center">
            
            <div class="w-full flex justify-start mb-2">
                <a href="/" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-teal-500 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Beranda
                </a>
            </div>

            <img src="{{ asset('storage/images/logo_manabu.png') }}" alt="Manabu Logo" class="w-24 h-24 object-contain drop-shadow-[0_0_15px_rgba(45,212,191,0.2)] mb-4">
            
            <h1 class="text-3xl font-bold tracking-tight mb-1 text-slate-800 dark:text-white">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-teal-400 to-blue-500">学ぶ</span> Manabu
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 text-center">Okaeri! Silakan masuk untuk melanjutkan.</p>

            @if ($errors->any())
                <div class="w-full mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl text-sm flex items-start gap-3">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="w-full space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold tracking-wide text-gray-700 dark:text-gray-400 mb-1.5 uppercase" for="email">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-50 dark:bg-inputBg border border-gray-300 dark:border-gray-700/50 text-gray-900 dark:text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition-all placeholder-gray-400 dark:placeholder-gray-600"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold tracking-wide text-gray-700 dark:text-gray-400 mb-1.5 uppercase" for="password">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-gray-50 dark:bg-inputBg border border-gray-300 dark:border-gray-700/50 text-gray-900 dark:text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition-all placeholder-gray-400 dark:placeholder-gray-600"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm mt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-teal-500 focus:ring-teal-500 bg-white dark:bg-inputBg">
                        <span class="text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-teal-600 dark:text-teal-500 hover:text-teal-500 dark:hover:text-teal-400 transition-colors font-medium">Lupa sandi?</a>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 dark:bg-gradient-to-r dark:from-teal-500 dark:to-teal-400 text-white dark:text-black transition-all py-3.5 rounded-xl font-bold hover:shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:-translate-y-0.5 mt-4">
                    Masuk ke Dashboard
                </button>
            </form>

            <p class="mt-8 text-sm text-gray-600 dark:text-gray-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-teal-600 dark:text-teal-400 hover:text-teal-500 font-bold transition-colors">Daftar sekarang</a>
            </p>
        </div>
    </div>

</body>
</html>