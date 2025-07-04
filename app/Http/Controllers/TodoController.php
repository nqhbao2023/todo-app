<?php

namespace App\Http\Controllers;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class TodoController extends Controller
{

    public function index(){
        $todos = Todo::where('user_id', Auth::id())->latest()->get();
        return view('dashboard', compact('todos')); //compact chuyen du lieu tu controller sang view

    }

    public function add(Request $request){

        $request-> validate([
             'title'=> 'required',
             'deadline' => 'nullable|date',

    ]);
        Todo::create([
            'user_id'=> Auth::id(),
            'title'=> $request -> title,
            'deadline' => $request->deadline,
            
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
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo ->delete();
        return redirect('/dashboard');
        
    }
    public function edit($id) {
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todos = Todo::where('user_id', Auth::id())->latest()->get(); // để render danh sách cùng lúc

        return view('dashboard', compact('todos', 'todo')); // truyền thêm $todo đang edit
    }
    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required'
        ]);

        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->title = $request->title;
        $todo->save();

        return redirect('/dashboard');
    }


    

   
}