<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //dawng ky
    public function registerForm(){
        return view('register');
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email', //unique: ko trung trong bang user
            'password'=> 'required|string|min:6|confirmed'
        ]);

        User::create([
            'name' =>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),

        ]);
        return redirect('/login')->with('status', 'hell yeah! You are registered successfully');
    }


    //dang nhap
    public function loginForm(){
        return view('login');
    }

    public function login(Request $request){

        $request->validate ([
            'email'=>'required|email',
            'password'=> 'required|min:6',

        ]);

        if(Auth::attempt($request->only('email','password'))){
            return redirect('/dashboard');
        }
        return back()->withErrors([
            'email'=> 'Wrong email or password',])->onlyInput('email');

    }

        public function logout(){
            Auth::logout();
            return redirect('/login');
        }

    
}
