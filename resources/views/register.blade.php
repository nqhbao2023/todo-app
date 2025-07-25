<!DOCTYPE html>
<html lang="en" data-theme="dracula">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-base-200">
    <form method="POST" action="/register" class="card w-full max-w-sm bg-base-100 shadow-xl border border-base-300">
        @csrf
        <div class="card-body">
            <h2 class="text-3xl font-extrabold mb-2 text-center text-primary tracking-tight">Đăng ký tài khoản</h2>
            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-base-content font-semibold mb-1">Tên</label>
                <input 
                    id="name"
                    type="text" 
                    name="name" 
                    placeholder="Nhập tên của bạn" 
                    value="{{ old('name') }}" 
                    required
                    class="input input-bordered w-full"
                >
                @error('name')
                    <div class="text-error text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            {{-- Email --}}
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
            {{-- Password --}}
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
            {{-- Confirm Password --}}
            <div class="mb-4">
                <label for="password_confirmation" class="block text-base-content font-semibold mb-1">Xác nhận mật khẩu</label>
                <input 
                    id="password_confirmation"
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Nhập lại mật khẩu"
                    required
                    class="input input-bordered w-full"
                >
            </div>
            <button 
                type="submit" 
                class="btn btn-primary w-full mt-2"
            >
                Đăng ký
            </button>
            <p class="mt-6 text-center text-sm text-base-content/70">
                Đã có tài khoản? 
                <a href="/login" class="text-primary font-semibold hover:underline">Đăng nhập ngay</a>
            </p>
        </div>
    </form>
</body>
</html>
