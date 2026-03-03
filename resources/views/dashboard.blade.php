@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">
                Dashboard
            </h1>
            <p class="text-base text-slate-500 mt-1">
                Selamat datang kembali, <span class="font-semibold text-indigo-600">{{ Auth::user()->name }}</span>! Mari lanjutkan belajarmu.
            </p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-100 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-1">Kanji Learned</p>
                    <p class="text-4xl font-extrabold text-slate-800">0</p>
                </div>
                <div class="p-3 bg-blue-500 text-white rounded-xl shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-100 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1">Kanji Mastered</p>
                    <p class="text-4xl font-extrabold text-slate-800">0</p>
                </div>
                <div class="p-3 bg-emerald-500 text-white rounded-xl shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-100 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-1">Daily Streak</p>
                    <p class="text-4xl font-extrabold text-slate-800 flex items-baseline gap-1">
                        0 <span class="text-lg font-medium text-amber-600">Hari</span>
                    </p>
                </div>
                <div class="p-3 bg-amber-500 text-white rounded-xl shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            Materi Pembelajaran
            <span class="ml-3 h-px flex-1 bg-slate-200"></span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <img src="data:image/jpeg;base64,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" alt="Semua Huruf" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">Semua Huruf</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Jelajahi Kanji, Hiragana, dan Katakana secara lengkap.</p>
                    <a href="/list" class="inline-flex items-center text-sm font-semibold text-amber-400 hover:text-amber-300 w-max">
                        Buka Modul <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <img src="https://teamjapanese.com/wp-content/uploads/2018/10/how-to-read-japanese-768x576.jpg" alt="Hiragana" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-pink-300 transition-colors">Hiragana</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Latihan membaca dan menulis dasar karakter Hiragana.</p>
                    <a href="/hiragana" class="inline-flex items-center text-sm font-semibold text-pink-400 hover:text-pink-300 w-max">
                        Buka Modul <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <img src="https://teamjapanese.com/wp-content/uploads/2018/10/how-to-read-japanese-768x576.jpg" alt="Katakana" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-300 transition-colors">Katakana</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Pelajari karakter Katakana untuk kata serapan asing.</p>
                    <a href="/katakana" class="inline-flex items-center text-sm font-semibold text-blue-400 hover:text-blue-300 w-max">
                        Buka Modul <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <img src="https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&q=80&w=600" alt="Kanji" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-emerald-300 transition-colors">Kanji</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Latihan karakter Kanji lengkap dengan urutan goresan.</p>
                    <a href="/kanji" class="inline-flex items-center text-sm font-semibold text-emerald-400 hover:text-emerald-300 w-max">
                        Buka Modul <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ring-2 ring-indigo-500 ring-offset-2">
                <div class="absolute top-4 left-4 z-30">
                    <span class="px-3 py-1 bg-indigo-500 text-white text-xs font-bold rounded-full shadow-lg">Rekomendasi</span>
                </div>
                <img src="https://teamjapanese.com/wp-content/uploads/2018/10/how-to-read-japanese-768x576.jpg" alt="Practice Writing" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-gray-900/30 z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">Vocabulary</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Tingkatkan akurasi goresan dan otot memori tulisan Anda.</p>
                    <a href="#" class="inline-flex items-center text-sm font-semibold text-indigo-400 hover:text-indigo-300 w-max">
                        Mulai Latihan <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <div class="group relative rounded-2xl h-56 overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <img src="https://teamjapanese.com/wp-content/uploads/2018/10/how-to-read-japanese-768x576.jpg" alt="Quiz" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent z-10"></div>
                
                <div class="absolute inset-0 z-20 p-6 flex flex-col justify-end">
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">Quiz</h3>
                    <p class="text-sm text-gray-300 mb-4 line-clamp-2">Uji pemahaman Anda dengan kuis interaktif yang seru.</p>
                    <a href="#" class="inline-flex items-center text-sm font-semibold text-purple-400 hover:text-purple-300 w-max">
                        Mulai Kuis <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection