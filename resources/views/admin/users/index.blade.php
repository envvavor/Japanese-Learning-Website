@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Pengguna</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola seluruh akun pengguna yang terdaftar di aplikasi.</p>
</div>

{{-- === STAT CARDS === --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-4 transition-transform hover:-translate-y-0.5 hover:shadow-md">
        <div class="w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-lg text-indigo-600 dark:text-indigo-400"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total User</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalUsers }}</p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-4 transition-transform hover:-translate-y-0.5 hover:shadow-md">
        <div class="w-11 h-11 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-shield text-lg text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Admin</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAdmin }}</p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-4 transition-transform hover:-translate-y-0.5 hover:shadow-md">
        <div class="w-11 h-11 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-lg text-green-600 dark:text-green-400"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terverifikasi</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalVerified }}</p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-4 transition-transform hover:-translate-y-0.5 hover:shadow-md">
        <div class="w-11 h-11 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fab fa-google text-lg text-red-500 dark:text-red-400"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Google OAuth</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalGoogle }}</p>
        </div>
    </div>
</div>

{{-- === SEARCH & FILTER === --}}
<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        
        {{-- Input Search --}}
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                   placeholder="Cari nama atau email pengguna...">
        </div>

        {{-- Dropdown Role --}}
        <div class="w-full sm:w-40 shrink-0">
            <select name="role" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm cursor-pointer">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>

        {{-- Dropdown Verified --}}
        <div class="w-full sm:w-44 shrink-0">
            <select name="verified" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm cursor-pointer">
                <option value="">Semua Status</option>
                <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Belum Verifikasi</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2 w-full sm:w-auto shrink-0">
            <button type="submit" class="flex-1 sm:flex-none bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            @if(request('search') || request('role') || request('verified'))
                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg border border-red-200 dark:border-red-800 transition-colors shadow-sm" title="Reset Filter">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- === USER TABLE === --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengguna</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level / XP</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terdaftar</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        {{-- User Info --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($user->google_avatar)
                                    <img src="{{ $user->google_avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center ring-2 ring-gray-200 dark:ring-gray-600">
                                        <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        @if($user->google_id)
                                            <i class="fab fa-google text-[10px] text-red-400"></i>
                                        @else
                                            <i class="fas fa-envelope text-[10px] text-gray-400"></i>
                                        @endif
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Role Badge --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800">
                                    <i class="fas fa-crown text-[10px]"></i> Admin
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800">
                                    <i class="fas fa-user text-[10px]"></i> User
                                </span>
                            @endif
                        </td>

                        {{-- Verification Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->email_verified_at)
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full border bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">
                                    <i class="fas fa-check-circle text-[10px]"></i> Terverifikasi
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-full border bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600">
                                    <i class="fas fa-clock text-[10px]"></i> Pending
                                </span>
                            @endif
                        </td>

                        {{-- Level / XP --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="text-center">
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-200">Lv. {{ $user->level ?? 1 }}</span>
                                    <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1">
                                        @php
                                            $xpPercent = ($user->next_level_xp > 0) ? min(100, ($user->xp / $user->next_level_xp) * 100) : 0;
                                        @endphp
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width: {{ $xpPercent }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $user->xp ?? 0 }} / {{ $user->next_level_xp ?? 100 }} XP</span>
                                </div>
                            </div>
                        </td>

                        {{-- Created At --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $user->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg transition-colors border border-indigo-100 dark:border-indigo-800" title="Edit">
                                    <i class="fas fa-edit w-4"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}? Semua data terkait akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg transition-colors border border-red-100 dark:border-red-800" title="Hapus">
                                            <i class="fas fa-trash-alt w-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-2 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Tidak bisa menghapus diri sendiri">
                                        <i class="fas fa-trash-alt w-4"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-user-slash text-2xl"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 font-semibold mb-1 text-lg">Pengguna tidak ditemukan</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Coba gunakan kata kunci atau filter yang berbeda.</p>
                                <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 font-medium transition-colors">
                                    Hapus Filter
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
