@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stat Card Kanji -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-4 group-hover:bg-red-100 dark:group-hover:bg-red-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-red-600 dark:text-red-400">漢</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Kanji</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalKanji ?? 0 }}</p>
    </div>

    <!-- Stat Card Hiragana -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">あ</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Hiragana</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalHiragana ?? 0 }}</p>
    </div>

    <!-- Stat Card Katakana -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mb-4 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-green-600 dark:text-green-400">ア</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Katakana</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalKatakana ?? 0 }}</p>
    </div>

    <!-- Stat Card Materi -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mb-4 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors duration-300">
            <i class="fas fa-book-open text-2xl text-purple-600 dark:text-purple-400"></i>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Materi</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalMateri ?? 0 }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Selamat datang di Admin Panel</h3>
        <span class="text-sm text-gray-500 dark:text-gray-400"><i class="far fa-clock mr-1"></i> Hari ini: {{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="text-gray-600 dark:text-gray-300 space-y-4">
        <p class="leading-relaxed">Gunakan menu di sebelah kiri untuk mengelola berbagai data aplikasi Anda.</p>
        <div class="bg-indigo-50 dark:bg-indigo-950/50 rounded-lg p-5 border border-indigo-100 dark:border-indigo-800">
            <h4 class="font-bold text-indigo-800 dark:text-indigo-300 mb-2 flex items-center"><i class="fas fa-info-circle mr-2"></i> Akses Cepat</h4>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.kanjis.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-plus-circle mr-2 opacity-75 group-hover:opacity-100"></i> Tambah Kanji Baru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kanjis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-list-ul mr-2 opacity-75 group-hover:opacity-100"></i> Lihat Daftar Kanji
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materis.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-plus-circle mr-2 opacity-75 group-hover:opacity-100"></i> Tambah Materi Baru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-list-ul mr-2 opacity-75 group-hover:opacity-100"></i> Lihat Daftar Materi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
