<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-tr from-blue-100 to-blue-300 flex items-center justify-center min-h-screen font-sans">

    <form method="POST" action="/login" class="bg-white/80 backdrop-blur-md p-8 rounded-2xl shadow-xl w-full max-w-sm border border-blue-200">
        @csrf

        <h2 class="text-3xl font-extrabold mb-2 text-center text-blue-600 tracking-tight">Đăng nhập</h2>

        <div class="mb-5">
            <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
         
            <input 
                id="email"
                type="email" 
                name="email" 
                placeholder="your@email.com" 
                value="{{ old('email') }}" 
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition"
            >
            @error('email')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-5">
            <label for="password" class="block text-gray-700 font-semibold mb-1">Mật khẩu</label>
           
            <input 
                id="password"
                type="password" 
                name="password" 
                placeholder="••••••••"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition"
            >
            @error('password')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        @if ($errors->has('email'))
            <div class="text-red-500 text-xs mb-4">{{ $errors->first('email') }}</div>
        @endif

        <button 
            type="submit" 
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded-lg shadow transition-all duration-200"
        >
            Đăng nhập
        </button>

        <p class="mt-6 text-center text-sm text-gray-600">
            Chưa có tài khoản?
            <a href="/register" class="text-blue-500 font-semibold hover:underline">Đăng ký ngay</a>
        </p>
    </form>

</body>
</html>
