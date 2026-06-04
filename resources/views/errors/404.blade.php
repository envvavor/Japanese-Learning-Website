@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex items-center justify-center p-6" x-data>
    <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2.5rem] p-10 sm:p-16 max-w-lg w-full text-center shadow-sm relative overflow-hidden">
        
        <!-- Decorative bg circle -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-rose-100 dark:bg-rose-900/30 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 bg-sky-100 dark:bg-sky-900/30 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto bg-rose-100 dark:bg-rose-900/30 text-rose-500 rounded-[2rem] flex items-center justify-center mb-8 rotate-[-5deg] border-4 border-rose-200 dark:border-rose-800 shadow-inner">
                <i class="fas fa-ghost text-5xl sm:text-7xl drop-shadow-md"></i>
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-2">
                404
            </h1>
            <h2 class="text-xl sm:text-2xl font-black text-rose-500 uppercase tracking-widest mb-4">
                Nyasar Ya?
            </h2>
            <p class="text-sm sm:text-base font-bold text-slate-500 dark:text-slate-400 mb-10">
                Halaman yang kamu cari sepertinya tidak ada atau sudah dihapus. Ayo kembali belajar!
            </p>

            <a href="{{ url('/') }}" class="inline-block w-full sm:w-auto px-8 py-4 bg-sky-500 border-2 border-b-[6px] border-sky-600 text-white font-black text-sm sm:text-base uppercase tracking-widest rounded-2xl hover:bg-sky-400 active:border-b-2 active:translate-y-1 transition-all shadow-sm">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
