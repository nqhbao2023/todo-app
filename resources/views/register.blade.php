<form method="POST" action="/register">
    @csrf
    <input type="text" name="name" placeholder="Name" value="{{old('name')  }}" required>
   @error('name')
   <div style="color:red"> {{ $message }}
   </div>
   @enderror

    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
    
    @error('email')
    <div style="color:red">
        {{ $message }}
    </div>
    @enderror
    
    
    <input type="password" name="password" placeholder="password" required>
    @error('password')
    <div style="color:red"> {{ $message }}

    </div>
    @enderror
    <input type="password" name="password_confirmation" placeholder="comfirm password">

    <button type="submit">Register</button>
    <p> 
        <a a href="/login"> already have an account? login now</a>
    </p>
</form>