<?php

namespace App\Http\Controllers;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class TodoController extends Controller
{

    public function index(){
        $todo = Todo::where('user_id', Auth::id())->lastest()->get();
        return view('dashboard', compact('todo')); //compact chuyen du lieu tu controller sang view

    }

    public function add(Request $request){
        $request-> validate([ 'title'=> 'required'
    ]);
        Todo::create([
            'user_id'=> Auth::id(),
            'title'=> $request -> title,
            
        ]);

        return redirect('/dashboard');
    }

    public function markDone($id){
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo ->completed = !$todo->completed;
        $todo ->save();
        return redirect('/dashboard');
    }

    public function delete($id){
        
    }
   
}
