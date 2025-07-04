<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <form method="POST" action="/login" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        @csrf

        <h2 class="text-2xl font-semibold mb-6 text-center">Đăng nhập</h2>

        {{-- Email --}}
        <div class="mb-4">
            <input 
                type="email" 
                name="email" 
                placeholder="Email" 
                value="{{ old('email') }}" 
                required
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
            @error('email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
            @error('password')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- General error --}}
        @if ($errors->has('email'))
            <div class="text-red-500 text-sm mb-4">{{ $errors->first('email') }}</div>
        @endif

        {{-- Submit --}}
        <button 
            type="submit" 
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
        >
            Login
        </button>

        {{-- Register link --}}
        <p class="mt-4 text-center text-sm text-gray-600">
            Bạn chưa có tài khoản?
            <a href="/register" class="text-blue-500 hover:underline">Đăng ký ngay</a>
        </p>
    </form>

</body>
</html>
