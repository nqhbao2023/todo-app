<!DOCTYPE html>
<html lang="en" data-theme="dracula">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-base-200">
    <form method="POST" action="{{ route('password.email') }}" class="card w-full max-w-sm bg-base-100 shadow-xl border border-base-300">
        @csrf
        <div class="card-body">
            <h2 class="text-2xl font-bold mb-4 text-center text-primary">Quên mật khẩu</h2>
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif
            <div class="mb-4">
                <label for="email" class="block mb-1 font-semibold">Email</label>
                <input type="email" id="email" name="email" required class="input input-bordered w-full" value="{{ old('email') }}">
                @error('email')
                    <div class="text-error text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-primary w-full" type="submit">Gửi liên kết đặt lại mật khẩu</button>
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:underline">Quay lại đăng nhập</a>
            </div>
        </div>
    </form>
</body>
</html>
