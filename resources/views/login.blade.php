<!DOCTYPE html>
<html lang="en" data-theme="dracula">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-base-200">
    @if(session('success'))
    <div class="toast toast-top toast-center">
        <div class="alert alert-success">
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif
    <form method="POST" action="/login" class="card w-full max-w-sm bg-base-100 shadow-xl border border-base-300">
        @csrf
        <div class="card-body">
            <h2 class="text-3xl font-extrabold mb-2 text-center text-primary tracking-tight">Đăng nhập</h2>
            <div class="mb-4">
                <label for="email" class="block text-base-content font-semibold mb-1">Email</label>
            <input 
                id="email"
                type="email" 
                name="email" 
                placeholder="your@email.com" 
                value="{{ old('email') }}" 
                required
                    class="input input-bordered w-full"
            >
            @error('email')
                    <div class="text-error text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>
            <div class="mb-4">
                <label for="password" class="block text-base-content font-semibold mb-1">Mật khẩu</label>
            <input 
                id="password"
                type="password" 
                name="password" 
                placeholder="••••••••"
                required
                    class="input input-bordered w-full"
            >
            @error('password')
                    <div class="text-error text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>
        @if ($errors->has('email'))
                <div class="text-error text-xs mb-2">{{ $errors->first('email') }}</div>
        @endif
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:underline">Quên mật khẩu?</a>
        </div>
        <button 
            type="submit" 
                class="btn btn-primary w-full mt-2"
        >
            Đăng nhập
        </button>
        
            <p class="mt-6 text-center text-sm text-base-content/70">
            Chưa có tài khoản?
                <a href="/register" class="text-primary font-semibold hover:underline">Đăng ký ngay</a>
        </p>
        </div>
    </form>


</body>
</html>
