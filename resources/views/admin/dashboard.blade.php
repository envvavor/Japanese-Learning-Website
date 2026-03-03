@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Stat Card Kanji -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mb-4 group-hover:bg-red-100 transition-colors duration-300">
            <span class="text-2xl font-bold text-red-600">漢</span>
        </div>
        <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Kanji</h3>
        <p class="text-4xl font-bold text-gray-800">{{ $totalKanji ?? 0 }}</p>
    </div>

    <!-- Stat Card Hiragana -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors duration-300">
            <span class="text-2xl font-bold text-blue-600">あ</span>
        </div>
        <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Hiragana</h3>
        <p class="text-4xl font-bold text-gray-800">{{ $totalHiragana ?? 0 }}</p>
    </div>

    <!-- Stat Card Katakana -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mb-4 group-hover:bg-green-100 transition-colors duration-300">
            <span class="text-2xl font-bold text-green-600">ア</span>
        </div>
        <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-1">Total Katakana</h3>
        <p class="text-4xl font-bold text-gray-800">{{ $totalKatakana ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
        <h3 class="text-lg font-bold text-gray-800">Selamat datang di Admin Panel</h3>
        <span class="text-sm text-gray-500"><i class="far fa-clock mr-1"></i> Hari ini: {{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="text-gray-600 space-y-4">
        <p class="leading-relaxed">Gunakan menu di sebelah kiri untuk mengelola berbagai data aplikasi Anda.</p>
        <div class="bg-indigo-50 rounded-lg p-5 border border-indigo-100">
            <h4 class="font-bold text-indigo-800 mb-2 flex items-center"><i class="fas fa-info-circle mr-2"></i> Akses Cepat</h4>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.kanjis.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-plus-circle mr-2 opacity-75 group-hover:opacity-100"></i> Tambah Kanji Baru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kanjis.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-list-ul mr-2 opacity-75 group-hover:opacity-100"></i> Lihat Daftar Kanji
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
