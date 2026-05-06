@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pengguna
    </a>
</div>

<div class="max-w-2xl">
    {{-- User Header Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center gap-4">
            @if($user->google_avatar)
                <img src="{{ $user->google_avatar }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-indigo-900/50">
            @else
                <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center ring-4 ring-indigo-100 dark:ring-indigo-900/50">
                    <span class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
            @endif
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-0.5">
                    {{ $user->email }}
                    @if($user->google_id)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800"><i class="fab fa-google mr-1"></i>Google</span>
                    @endif
                </p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500"><i class="fas fa-calendar-alt mr-1"></i>Terdaftar: {{ $user->created_at->format('d M Y, H:i') }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500"><i class="fas fa-star mr-1"></i>Level {{ $user->level ?? 1 }} &middot; {{ $user->xp ?? 0 }} XP</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
            <i class="fas fa-user-edit text-indigo-500"></i> Edit Data Pengguna
        </h4>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm @error('name') border-red-500 ring-red-500 @enderror">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm @error('email') border-red-500 ring-red-500 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
                <select name="role" id="role" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm cursor-pointer @error('role') border-red-500 ring-red-500 @enderror">
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                @if($user->id === auth()->id())
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> Ini adalah akun Anda. Hati-hati mengubah role.
                    </p>
                @endif
            </div>

            {{-- Password --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password Baru <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="password" name="password" id="password" autocomplete="new-password"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm @error('password') border-red-500 ring-red-500 @enderror"
                       placeholder="Kosongkan jika tidak ingin mengubah password">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                       placeholder="Ulangi password baru">
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
