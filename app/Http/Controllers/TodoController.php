<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // Trang danh sách todo
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $todos = Todo::where('user_id', Auth::id());

        switch ($tab) {
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
            ->with('assignee') // Eager load
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline', 'asc')
            ->paginate(10);

        $users = User::all();
        return view('dashboard', compact('todos', 'tab', 'users'));
    }

    // Hiển thị form tạo công việc
    public function create()
    {
        $users = User::all();
        return view('todos.create', compact('users'));
    }

    // Xử lý thêm mới todo
    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'deadline' => 'nullable|date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'detail' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'assigned_to' => $request->assigned_to,
            'deadline' => $request->deadline,
            'priority' => $request->priority ?? 'Normal',
            'status' => $request->status,
            'detail' => $request->detail,
            'completed' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Thêm công việc thành công!');
    }

    // Hiển thị form sửa todo
    public function edit($id, Request $request)
    {
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $users = User::all();
        $tab = $request->get('tab', 'all');
        return view('todos.edit', compact('todo', 'users', 'tab'));
    }

    // Xử lý cập nhật todo
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'priority' => 'required|string',
            'deadline' => 'nullable|date',
            'status' => 'required|string',
            'detail' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->title = $request->title;
        $todo->deadline = $request->deadline;
        $todo->priority = $request->priority ?? 'Normal';
        $todo->status = $request->status;
        $todo->detail = $request->detail;
        $todo->assigned_to = $request->assigned_to;
        $todo->save();

        return redirect()->route('dashboard');
    }

    // Đánh dấu hoàn thành hoặc chưa hoàn thành
    public function markDone($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->completed = !$todo->completed;
        $todo->save();
        return redirect()->back();
    }

    // Xoá todo
    public function delete($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->delete();
        return redirect()->route('dashboard');
    }
}
