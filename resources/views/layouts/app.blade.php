<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Todo App')</title>
    @viteReactRefresh
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-background min-h-screen font-sans antialiased">
    <div class="min-h-screen bg-base-100 flex flex-col">
        <!-- Header/Navbar -->
        <header class="w-full py-4 shadow-sm bg-white/90 dark:bg-base-200 border-b border-gray-200 dark:border-base-300 transition-colors">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <h1 class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">
                    <span class="mr-2">📋</span> Todo App
                </h1>
                <!-- Dark/Light Theme Switch -->
                <label class="swap swap-flip cursor-pointer text-4xl leading-none flex items-center">
                    <input id="theme-toggle" type="checkbox" class="sr-only" />
                    <span class="swap-on text-4xl">😈</span>
                    <span class="swap-off text-4xl">😇</span>
                </label>
                
            </div>
        </header>
        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-4 pt-6">
            @yield('content')
        </main>
        <!-- Footer -->
        <footer class="py-4 text-center text-xs text-muted-foreground mt-10 border-t border-base-300">
            © {{ date('Y') }} - Todo App by <a href="https://daisyui.com/" class="underline text-primary font-bold">daisyUI</a>
        </footer>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Dark/Light Mode Switch Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;

            // Default theme là light
            let saved = localStorage.getItem('theme');
            if (!saved) {
                html.setAttribute('data-theme', 'light');
                themeToggle.checked = false;
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', saved);
                themeToggle.checked = (saved === 'dracula');
            }

            themeToggle.addEventListener('change', function() {
                const next = themeToggle.checked ? 'dracula' : 'light';
                html.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
            });
        });
    </script>

    <!-- Toast Notifications (Laravel session) -->
    @if(session('success'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-init="setTimeout(() => show = false, 2500)"
        class="toast toast-top toast-end z-50"
        style="will-change: opacity, transform;"
    >
        <div class="alert alert-success flex items-center gap-2 shadow-md">
            <span>{{ session('success') }}</span>
            <button onclick="this.closest('.toast').remove()" class="btn btn-xs btn-circle btn-ghost ml-2">✕</button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 3500)"
        class="toast toast-top toast-end z-50"
    >
        <div class="alert alert-error flex items-center gap-2 shadow-md">
            <span>{{ session('error') }}</span>
            <button onclick="this.closest('.toast').remove()" class="btn btn-xs btn-circle btn-ghost ml-2">✕</button>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 6000)"
        class="toast toast-top toast-end z-50"
    >
        <div class="alert alert-error flex flex-col gap-1 shadow-md">
            @foreach($errors->all() as $error)
                <span>- {{ $error }}</span>
            @endforeach
            <button onclick="this.closest('.toast').remove()" class="btn btn-xs btn-circle btn-ghost ml-2 self-end">✕</button>
        </div>
    </div>
    @endif
</body>
</html>
