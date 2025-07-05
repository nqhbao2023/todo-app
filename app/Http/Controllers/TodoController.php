<?php

namespace App\Http\Controllers;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class TodoController extends Controller
{

public function index(Request $request)
{
    $tab = $request->get('tab', 'all'); 
    $todos = Todo::where('user_id', Auth::id());

    switch($tab) {
        case 'today':
            $todos = $todos->where('completed', false)
                           ->whereDate('deadline', now()->toDateString());
            break;
        case 'upcoming':
            $todos = $todos->where('completed', false)
                           ->whereDate('deadline', '>', now()->toDateString());
            break;
        case 'done':
            $todos = $todos->where('completed', true);
            break;
        default: // 'all' (Trang chủ)
            $todos = $todos->where('completed', false);
            break;
    }

    $todos = $todos
        ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
        ->orderBy('deadline', 'asc')
        ->get();

    return view('dashboard', compact('todos', 'tab'));
}



    public function add(Request $request){

        $request-> validate([
             'title'=> 'required',
             'deadline' => 'nullable|date',
             'priority' => 'required|string',
             'status' => 'required|string',
             'detail' => 'nullable|string',

    ]);
        Todo::create([
            'user_id'=> Auth::id(), 
            'title'=> $request -> title,
            'deadline' => $request->deadline,
            'priority' => $request->priority ?? 'Normal',
            'status' => $request->status,
            
        ]);

        return redirect('/dashboard');
    }

public function markDone($id){
    $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    $todo->completed = !$todo->completed;
    $todo->save();
    return redirect()->back();
}



    public function delete($id){
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo ->delete();
        return redirect('/dashboard');
        
    }
public function edit($id) {
    $todo = Todo::where('id', operator: $id)->where('user_id', Auth::id())->firstOrFail();
    $todos = Todo::where('user_id', Auth::id())
        ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
        ->orderBy('deadline', 'asc')
        ->get();

    return view('dashboard', compact('todos', 'todo'));
}
    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required',
            'priority' => 'required|string',
            'deadline' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->title = $request->title;
        $todo->deadline = $request->deadline;
        $todo->priority = $request->priority ?? 'Normal';
        $todo->status = $request->status;
        $todo->detail = $request->detail;

        $todo->save();

        return redirect('/dashboard');
    }


    

   
}