<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Đăng ký
    public function registerForm(){
        return view('register');
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|string|min:6|confirmed'
        ]);

        User::create([
            'name'    => $request->input('name'),
            'email'   => $request->input('email'),
            'password'=> Hash::make($request->input('password')),
        ]);
        return redirect('/login')->with('status', 'hell yeah! You are registered successfully');
    }

    // Đăng nhập
    public function loginForm(){
        return view('login');
    }

    public function login(Request $request){
        $request->validate ([
            'email'   => 'required|email',
            'password'=> 'required|min:6',
        ]);

        if(Auth::attempt($request->only('email','password'))){
            return redirect('/dashboard');
        }
        return back()->withErrors([
            'email'=> 'Wrong email or password',
        ])->onlyInput('email');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
}
