<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Visual Novel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script>
        // Anti-FOUC: Apply dark mode IMMEDIATELY before any rendering
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        html.dark { color-scheme: dark; }
        body { transition: background-color 0.3s ease, color 0.3s ease; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/vn-app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950 text-gray-800 dark:text-white">
    @inertia
</body>
</html>
