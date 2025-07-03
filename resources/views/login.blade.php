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

    @if ($error->has('email'))
    <div style="color:red">
        {{ $error->first('email') }}

    </div>
    @endif

    <button type="submit">Login </button>

    <p> 
        <a href =" /register "> don't have account? Register now </a>
    </p>
</form>