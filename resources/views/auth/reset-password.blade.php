<!DOCTYPE html>
<html lang="en" data-theme="dracula">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-base-200">
    <form method="POST" action="/reset-password" class="card w-full max-w-sm bg-base-100 shadow-xl border border-base-300">
        @csrf
        <div class="card-body">
            <h2 class="text-2xl font-bold mb-4 text-center text-primary">Đặt lại mật khẩu</h2>
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', $email ?? request('email')) }}">
            <div class="mb-4">
                <label for="password" class="block mb-1 font-semibold">Mật khẩu mới</label>
                <input type="password" id="password" name="password" required class="input input-bordered w-full">
                @error('password')
                    <div class="text-error text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block mb-1 font-semibold">Nhập lại mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="input input-bordered w-full">
            </div>
            <button class="btn btn-primary w-full" type="submit">Đặt lại mật khẩu</button>
        </div>
    </form>
</body>
</html>
