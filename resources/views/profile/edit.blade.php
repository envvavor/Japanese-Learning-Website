@extends('layouts.app')

@section('title', 'Edit Profil — Manabu')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans pb-20 pt-10" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('dashboard') }}" class="text-indigo-500 hover:text-indigo-600 font-bold mb-2 inline-flex items-center gap-2 uppercase tracking-widest text-xs">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mt-2">
                    Edit <span class="text-[#1cb0f6]">Profil</span>
                </h1>
            </div>
        </div>

        {{-- Profile Info & Edit Form --}}
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-10 mb-10 shadow-sm flex flex-col md:flex-row gap-10">
            
            {{-- Avatar Section (Read Only) --}}
            <div class="shrink-0 flex flex-col items-center md:border-r-2 border-slate-100 dark:border-gray-700 md:pr-10">
                @if(Auth::user()->google_avatar)
                    <img src="{{ Auth::user()->google_avatar }}" alt="Avatar" class="w-32 h-32 rounded-full border-4 border-[#1cb0f6] object-cover shadow-md" referrerpolicy="no-referrer" onerror="this.outerHTML='<div class=\'w-32 h-32 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-black text-5xl border-4 border-blue-200 shadow-md\'>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>'">
                @else
                    <div class="w-32 h-32 rounded-full bg-[#1cb0f6] text-white flex items-center justify-center font-black text-5xl border-4 border-blue-200 shadow-md">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <span class="mt-4 px-4 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-500 border-2 border-amber-200 dark:border-amber-800 rounded-full text-xs font-black uppercase tracking-widest">
                    Level {{ Auth::user()->level ?? 1 }}
                </span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-6">Bergabung Sejak</p>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ Auth::user()->created_at->translatedFormat('d M Y') }}</p>
            </div>

            {{-- Edit Form Section --}}
            <div class="flex-1 w-full">
                <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-6 flex items-center gap-3">
                    <i class="fas fa-user-edit text-[#1cb0f6]"></i> Informasi Dasar
                </h2>

                @if (session('status') === 'profile-updated')
                    <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 text-emerald-600 rounded-2xl font-bold text-sm">
                        <i class="fas fa-check-circle mr-2"></i> Profil berhasil diperbarui!
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input id="name" name="name" type="text" class="w-full px-4 py-3 bg-slate-50 dark:bg-gray-900 border-2 border-slate-200 dark:border-gray-700 rounded-xl focus:border-[#1cb0f6] focus:ring-0 text-slate-700 dark:text-white font-bold transition-colors" value="{{ old('name', Auth::user()->name) }}" required autofocus autocomplete="name" />
                        @error('name')
                            <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Alamat Email</label>
                        <input id="email" name="email" type="email" class="w-full px-4 py-3 bg-slate-50 dark:bg-gray-900 border-2 border-slate-200 dark:border-gray-700 rounded-xl focus:border-[#1cb0f6] focus:ring-0 text-slate-700 dark:text-white font-bold transition-colors" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username" />
                        @error('email')
                            <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                        @enderror

                        @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail() && !Auth::user()->isGoogleUser())
                            <div class="mt-3 text-sm text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-900/30 p-3 rounded-lg border border-amber-200 dark:border-amber-800">
                                Email Anda belum diverifikasi.
                                <button form="send-verification" class="underline hover:text-amber-700 dark:hover:text-amber-300">
                                    Klik di sini untuk mengirim ulang email.
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#1cb0f6] text-white font-black uppercase tracking-widest rounded-xl border-2 border-b-[6px] border-[#1899d6] hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
                
                <form id="send-verification" method="post" action="{{ route('verification.resend') }}">
                    @csrf
                </form>
            </div>
        </div>

        {{-- Update Password --}}
        @if(!Auth::user()->isGoogleUser())
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-10 shadow-sm">
            <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-6 flex items-center gap-3">
                <i class="fas fa-lock text-rose-500"></i> Ubah Password
            </h2>

            @if (session('status') === 'password-updated')
                <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 text-emerald-600 rounded-2xl font-bold text-sm">
                    <i class="fas fa-check-circle mr-2"></i> Password berhasil diperbarui!
                </div>
            @endif

            <form method="post" action="{{ route('password.update.profile') }}" class="space-y-6">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-gray-900 border-2 border-slate-200 dark:border-gray-700 rounded-xl focus:border-rose-500 focus:ring-0 text-slate-700 dark:text-white font-bold transition-colors" autocomplete="current-password" />
                    @error('current_password')
                        <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Password Baru</label>
                    <input id="password" name="password" type="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-gray-900 border-2 border-slate-200 dark:border-gray-700 rounded-xl focus:border-rose-500 focus:ring-0 text-slate-700 dark:text-white font-bold transition-colors" autocomplete="new-password" />
                    @error('password')
                        <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-gray-900 border-2 border-slate-200 dark:border-gray-700 rounded-xl focus:border-rose-500 focus:ring-0 text-slate-700 dark:text-white font-bold transition-colors" autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-rose-500 text-white font-black uppercase tracking-widest rounded-xl border-2 border-b-[6px] border-rose-700 hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
