<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TodoProgress;

class TodoController extends Controller
{
    // Trang danh sách todo
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        // Lấy tất cả todo liên quan tới user (là người tạo hoặc được giao)
        $todos = Todo::with('assignee')
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('assigned_to', Auth::id());
            });
            

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
            default:
                $todos = $todos->where('completed', false);
                break;
        }

        $todos = $todos
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline', 'asc')
            ->paginate(10);

        $users = User::all(); // Để dùng cho dropdown filter hoặc phân công
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
            'kpi_target' => 'nullable|integer|min:1',
        ]);

        Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'assigned_to' => $request->assigned_to,
            'kpi_target' => $request->kpi_target,
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
            'kpi_target' => 'nullable|integer|min:1',
        ]);

        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $todo->title = $request->title;
        $todo->deadline = $request->deadline;
        $todo->priority = $request->priority ?? 'Normal';
        $todo->status = $request->status;
        $todo->detail = $request->detail;
        $todo->assigned_to = $request->assigned_to;
        $todo->kpi_target = $request->kpi_target;
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

    // Hiện form nhập tiến độ
    public function progressForm($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $progresses = $todo->progresses()->orderBy('progress_date')->get();
        return view('todos.progress', compact('todo', 'progresses'));
    }

    // Xử lý lưu tiến độ
    public function storeProgress(Request $request, $id)
    {
        $request->validate([
            'progress_date' => 'required|date',
            'quantity' => 'required|integer|min:1'
        ]);
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Nếu 1 ngày đã nhập rồi thì cập nhật, chưa có thì thêm mới
        TodoProgress::updateOrCreate(
            ['todo_id' => $todo->id, 'progress_date' => $request->progress_date],
            ['quantity' => $request->quantity]
        );
        return redirect()->route('todos.progress.form', $todo->id)->with('success', 'Đã ghi nhận tiến độ!');
    }
}
