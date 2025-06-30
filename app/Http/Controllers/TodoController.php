<?php
namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())->latest()->get();
        return view('dashboard', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        Todo::create([
            'title' => $request->title,
            'user_id' => Auth::id(),
            'completed' => false,
        ]);

        return redirect()->route('dashboard');
    }

    public function update(Todo $todo)
    {
        $this->authorize('update', $todo);
        $todo->update(['completed' => !$todo->completed]);
        return redirect()->route('dashboard');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);
        $todo->delete();
        return redirect()->route('dashboard');
    }
}
