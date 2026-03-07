<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Anti-FOUC: Apply dark mode IMMEDIATELY before any rendering
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html.dark { color-scheme: dark; }
        body { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans antialiased">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }">
    <!-- Sidebar -->
    <aside class="flex-shrink-0 w-64 bg-indigo-900 dark:bg-gray-950 text-white transition-transform transform md:translate-x-0 md:static fixed inset-y-0 left-0 z-30"
           :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
        <div class="h-16 flex items-center justify-center bg-indigo-950 dark:bg-black font-bold text-xl tracking-wider shadow-md">
            <i class="fas fa-layer-group mr-2"></i> Admin Panel
        </div>
        <div class="p-4 space-y-2 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 dark:bg-indigo-600 text-white shadow-md' : 'text-indigo-200 hover:bg-indigo-800 dark:hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <!-- Kanji Dropdown -->
            <div x-data="{ expanded: {{ request()->routeIs('admin.kanjis.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.kanjis.*') ? 'bg-indigo-800 dark:bg-indigo-700 text-white shadow-inner' : 'text-indigo-200 hover:bg-indigo-800 dark:hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-language w-5 text-center"></i>
                        <span class="font-medium">Manajemen Huruf</span>
                    </div>
                    <i class="fas fa-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                </button>
                <div x-show="expanded" x-transition.opacity class="mt-2 pl-4 pr-2 space-y-1 border-l-2 border-indigo-700 dark:border-indigo-500 ml-5 py-1" x-cloak>
                    <a href="{{ route('admin.kanjis.index') }}" class="flex items-center space-x-2 px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.kanjis.index') || request()->routeIs('admin.kanjis.edit') ? 'bg-indigo-700 dark:bg-indigo-600 text-white font-medium' : 'text-indigo-300 hover:text-white hover:bg-indigo-700 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-list-ul w-4 text-xs"></i> <span>Daftar huruf</span>
                    </a>
                    <a href="{{ route('admin.kanjis.create') }}" class="flex items-center space-x-2 px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.kanjis.create') ? 'bg-indigo-700 dark:bg-indigo-600 text-white font-medium' : 'text-indigo-300 hover:text-white hover:bg-indigo-700 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-plus w-4 text-xs"></i> <span>Tambah huruf</span>
                    </a>
                </div>
            </div>

            <!-- Materi Dropdown -->
            <div x-data="{ expanded: {{ request()->routeIs('admin.materis.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.materis.*') ? 'bg-indigo-800 dark:bg-indigo-700 text-white shadow-inner' : 'text-indigo-200 hover:bg-indigo-800 dark:hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-book-open w-5 text-center"></i>
                        <span class="font-medium">Manajemen Materi</span>
                    </div>
                    <i class="fas fa-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                </button>
                <div x-show="expanded" x-transition.opacity class="mt-2 pl-4 pr-2 space-y-1 border-l-2 border-indigo-700 dark:border-indigo-500 ml-5 py-1" x-cloak>
                    <a href="{{ route('admin.materis.index') }}" class="flex items-center space-x-2 px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.materis.index') || request()->routeIs('admin.materis.edit') ? 'bg-indigo-700 dark:bg-indigo-600 text-white font-medium' : 'text-indigo-300 hover:text-white hover:bg-indigo-700 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-list-ul w-4 text-xs"></i> <span>Daftar Materi</span>
                    </a>
                    <a href="{{ route('admin.materis.create') }}" class="flex items-center space-x-2 px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.materis.create') ? 'bg-indigo-700 dark:bg-indigo-600 text-white font-medium' : 'text-indigo-300 hover:text-white hover:bg-indigo-700 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-plus w-4 text-xs"></i> <span>Tambah Materi</span>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak x-transition.opacity></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Topbar -->
        <header class="h-16 bg-white dark:bg-gray-800 shadow-sm flex items-center justify-between px-6 z-10 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none md:hidden mr-4 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 hidden sm:block">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)" 
                        class="relative w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400"
                        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                    <i class="fas fa-sun text-lg text-amber-500" x-show="darkMode" x-transition></i>
                    <i class="fas fa-moon text-lg text-indigo-500" x-show="!darkMode" x-transition></i>
                </button>

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center mr-2 font-bold select-none cursor-pointer">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    {{ auth()->user()->name }}
                </div>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold transition-colors flex items-center space-x-1">
                        <span>Logout</span>
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6 md:p-8">
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 bg-white dark:bg-gray-800 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 rounded-lg shadow-sm flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition.opacity>
                        <div class="font-medium">
                            <i class="fas fa-check-circle mr-2 text-green-500"></i> {{ session('success') }}
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-white dark:bg-gray-800 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center mb-2 font-bold">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500"></i> Terdapat Kesalahan:
                        </div>
                        <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
