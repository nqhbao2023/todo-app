<!DOCTYPE html>
<html lang="vi" data-theme="dracula">

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
    <div class="min-h-screen bg-muted/50 flex flex-col">
        <header class="w-full py-5 shadow-sm bg-white/80 backdrop-blur border-b border-gray-200 mb-8">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <h1 class="font-bold text-xl tracking-tight text-gray-900">Todo App</h1>
                <button
                    id="toggle-dark"
                    class="btn btn-primary"
                    type="button"
                >
                    Bật/Tắt Dark Mode
                </button>
            </div>
        </header>
        <main class="flex-1 container mx-auto px-4">
            @yield('content')
        </main>
        <footer class="py-4 text-center text-xs text-muted-foreground mt-10">
            © {{ date('Y') }} 
        </footer>
    </div>
    <!-- Import AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    document.getElementById('toggle-dark').addEventListener('click', function() {
        const html = document.documentElement;
        const current = html.getAttribute('data-theme');
        // Chuyển giữa dracula và light
        const next = current === 'dracula' ? 'light' : 'dracula';
        html.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
    });
});
</script>
</body>
</html>
