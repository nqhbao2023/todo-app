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
        $sort = $request->input('sort', 'deadline');
        $direction = strtolower($request->input('direction', 'asc')) == 'desc' ? 'desc' : 'asc';

        // Lấy tất cả todo liên quan tới user (là người tạo hoặc được giao)
        $todos = Todo::with('assignee')
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('assigned_to', Auth::id());
            });

        // Filter theo tab
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

        // Các cột được phép sort
        $sortable = [
            'title',
            'assigned_to',
            'status',
            'priority',
            'deadline'
        ];

        // Áp dụng sort
        if ($sort == 'priority') {
            $todos = $todos->orderByRaw(
                "FIELD(priority, 'Urgent', 'High', 'Normal', 'Low') $direction"
            );
        } elseif (in_array($sort, $sortable)) {
            $todos = $todos->orderBy($sort, $direction);
        } else {
            $todos = $todos->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
                           ->orderBy('deadline', 'asc');
        }

        $todos = $todos->paginate(10);

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
        if ($request->input('deadline') === 'none' || empty($request->input('deadline'))) {
            $request->merge(['deadline' => null]);
        }
    
        $request->validate([
            'title' => 'required',
            'deadline' => 'nullable|date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'detail' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'kpi_target' => 'nullable|integer|min:1',
            'attachment_link' => 'nullable|string',
            'repeat' => 'nullable|string|max:50',
            'repeat_custom' => 'nullable|string|max:100',
        ]);
     $repeat = $request->input('repeat');
        if ($repeat === 'custom') {
            $repeat = $request->input('repeat_custom');
        }


        Todo::create([
            'user_id'     => Auth::id(),
            'title'       => $request->input('title'),
            'assigned_to' => $request->input('assigned_to'),
            'kpi_target'  => $request->input('kpi_target'),
            'deadline'    => $request->input('deadline'),
            'priority'    => $request->input('priority') ?? 'Normal',
            'status'      => $request->input('status'),
            'detail'      => $request->input('detail'),
            'completed'   => false,
            'attachment_link' => $request->input('attachment_link'),
            'repeat'      => $repeat,
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
        if ($request->input('deadline') === 'none' || empty($request->input('deadline'))) {
            $request->merge(['deadline' => null]);
        }
    
        $request->validate([
            'title' => 'required',
            'priority' => 'required|string',
            'deadline' => 'nullable|date',
            'status' => 'required|string',
            'detail' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'kpi_target' => 'nullable|integer|min:1',
            'attachment_link' => 'nullable|url|max:500',
            'repeat' => 'nullable|string|max:50',
            'repeat_custom' => 'nullable|string|max:100',
        ]);

        $deadline = $request->input('deadline');
        if ($deadline === 'none' || empty($deadline)) {
            $deadline = null;
        }


        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $repeat = $request->input('repeat');
        if ($repeat === 'custom') {
            $repeat = $request->input('repeat_custom');
        }
        $todo->title = $request->input('title');
        $todo->deadline = $deadline;
        $todo->priority = $request->input('priority') ?? 'Normal';
        $todo->status = $request->input('status');
        $todo->detail = $request->input('detail');
        $todo->assigned_to = $request->input('assigned_to');
        $todo->kpi_target = $request->input('kpi_target');
        $todo->attachment_link = $request->input('attachment_link');
        $todo->repeat = $repeat; 
        $todo->save();

        return redirect()->route('dashboard');
    }
    
    public function updateStatus(Request $request, Todo $todo)
{
    $request->validate([
        'status' => 'required|string',
    ]);
    $todo->status = $request->input('status');
    $todo->save();

    return response()->json([
        'success' => true,
        'status' => $todo->status
    ]);
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
        /** @var \App\Models\Todo $todo */
        $todo = Todo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    
        TodoProgress::updateOrCreate(
            ['todo_id' => $todo->id, 'progress_date' => $request->input('progress_date')],
            ['quantity' => $request->input('quantity')]
        );
        return redirect()->route('todos.progress.form', $todo->id)->with('success', 'Đã ghi nhận tiến độ!');
    }
    
    
    
}
