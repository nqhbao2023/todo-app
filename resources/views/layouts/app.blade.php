<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Todo App')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-background min-h-screen font-sans antialiased">
    <div class="min-h-screen bg-muted/50 flex flex-col">
        <header class="w-full py-5 shadow-sm bg-white/80 backdrop-blur border-b border-gray-200 mb-8">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <h1 class="font-bold text-xl tracking-tight text-gray-900">Todo App</h1>
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


</body>
</html>
