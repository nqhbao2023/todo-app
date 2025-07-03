<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite ('resources/css/app.css ')
    
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">{{-- flex item va justify-center--}}


<form method="POST" action="/login">
    @csrf
    

    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
    @error('email')
    <div style="color:red">
        {{ $message }}
    </div>
    @enderror

    <input type="password" name="password" placeholder="password" required>
    @error('password')
    <div style="color:red">
        {{ $message }}
    </div>
    @enderror

    @if ($errors->has('email'))
    <div style="color:red">
        {{ $errors->first('email') }}
    </div>
    @endif

    <button type="submit">Login</button>

    <p>
        <a href="/register">Bạn chưa có tài khoản? Đăng ký ngay</a>
    </p>
</form>

</body>
</html>
    

