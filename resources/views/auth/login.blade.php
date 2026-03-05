<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7c5cff'
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-900 p-6">

    <div class="w-full max-w-5xl bg-[#2b2740] rounded-2xl shadow-2xl overflow-hidden flex">

        <!-- LEFT PANEL -->
        <div class="hidden md:flex w-1/2 relative text-white p-10 flex-col justify-between overflow-hidden">

            <!-- Background Image -->
            <div class="absolute inset-0">
                <img 
                    src="https://teamjapanese.com/wp-content/uploads/2018/10/how-to-read-japanese-768x576.jpg"
                    class="w-full h-full object-cover"
                    alt="Background"
                >
            </div>

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-purple-900/70 to-black/70"></div>

            <!-- Content -->
            <div class="relative z-10">
                <h1 class="text-2xl font-bold tracking-wider">Japanese Learning</h1>
                <a href="/" class="mt-4 inline-block text-xs bg-white/20 px-3 py-1 rounded-full hover:bg-white/30 transition backdrop-blur-sm">
                    Back to website →
                </a>
            </div>

            <div class="relative z-10">
                <h2 class="text-xl font-semibold leading-relaxed">
                    Belajar Bahasa Jepang<br>
                    Jadi Lebih Menyenangkan
                </h2>

                <div class="flex gap-2 mt-6">
                    <span class="w-8 h-1 bg-white rounded"></span>
                    <span class="w-4 h-1 bg-white/40 rounded"></span>
                    <span class="w-4 h-1 bg-white/40 rounded"></span>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL (LOGIN FORM) -->
        <div class="w-full md:w-1/2 bg-[#1e1b2e] text-white p-10">

            <h2 class="text-2xl font-semibold mb-2">Welcome Back</h2>
            <p class="text-gray-400 text-sm mb-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary hover:underline">Daftar</a>
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <input 
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email"
                    required
                    class="w-full bg-[#2b2740] border border-gray-600 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none"
                >

                <!-- Password -->
                <input 
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    class="w-full bg-[#2b2740] border border-gray-600 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none"
                >

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-600 bg-[#2b2740]">
                        Remember me
                    </label>
                </div>

                <!-- Button -->
                <button 
                    type="submit"
                    class="w-full bg-primary hover:bg-indigo-600 transition py-3 rounded-lg font-medium"
                >
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-6">
                <div class="flex-grow h-px bg-gray-700"></div>
                <span class="px-4 text-gray-500 text-sm">or</span>
                <div class="flex-grow h-px bg-gray-700"></div>
            </div>

            <!-- Social -->
            <div class="grid grid-cols-2 gap-4">
                <button class="border border-gray-600 rounded-lg py-3 text-sm hover:bg-[#2b2740] transition">
                    Google
                </button>
                <button class="border border-gray-600 rounded-lg py-3 text-sm hover:bg-[#2b2740] transition">
                    Apple
                </button>
            </div>

        </div>

    </div>

</body>
</html>