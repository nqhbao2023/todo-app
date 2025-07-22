<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use App\Models\TodoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    // Trang dashboard - danh sách todo với các tab dạng "My Day", "Important",...
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'myday');
        $userId = Auth::id();
        

        $todosQuery = Todo::with('assignee')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('assigned_to', $userId);
            });

            
        // Filter theo từng tab
        switch ($tab) {
            case 'myday':
                $todosQuery->whereDate('deadline', now()->toDateString());
                break;
            case 'important':
                $todosQuery->where('important', true);
                break;
            case 'planned':
                $todosQuery->whereNotNull('deadline');
                break;
            case 'assigned':
                $todosQuery->where('assigned_to', $userId);
                break;
            case 'tasks':
            default:
                // không filter thêm, lấy tất cả tasks liên quan user
                break;
        }
        if ($tab === 'kpi') {
        $todosQuery->whereNotNull('kpi_target');
}

        $todos = $todosQuery->orderBy('deadline')->paginate(10);
        

        // Đếm số lượng cho badge ở các tab
        $countTabs = [
            'myday'     => Todo::where(function($q) use($userId){ $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                                ->whereDate('deadline', now())->count(),
            'important' => Todo::where(function($q) use($userId){ $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                                ->where('important', true)->count(),
            'planned'   => Todo::where(function($q) use($userId){ $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                                ->whereNotNull('deadline')->count(),
            'assigned'  => Todo::where('assigned_to', $userId)->count(),
            'tasks'     => Todo::where(function($q) use($userId){ $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })->count(),
        ];

        $users = User::all();

        return view('dashboard', compact('todos', 'tab', 'countTabs', 'users'));
    }

    // Form tạo todo
    public function create()
    {
        $users = User::all();
        return view('todos.create', [
            'users' => $users,
            'deadlineVal' => old('deadline', null),
            'repeatVal' => old('repeat', null),
            'repeatCustom' => old('repeat_custom', null),
        ]);
    }

    // Xử lý tạo mới todo
    public function add(Request $request)
    {
        if ($request->input('deadline') === 'none' || empty($request->input('deadline'))) {
            $request->merge(['deadline' => null]);
        }

        $request->validate([
            'title'           => 'required|string|max:255',
            'deadline'        => 'nullable|date',
            'priority'        => 'required|string',
            'status'          => 'required|string',
            'detail'          => 'nullable|string',
            'assigned_to'     => 'nullable|exists:users,id',
            'kpi_target'      => 'nullable|integer|min:1',
            'attachment_link' => 'nullable|string',
            'repeat'          => 'nullable|string|max:50',
            'repeat_custom'   => 'nullable|string|max:100',
        ]);

        $repeat = $request->input('repeat');
        if ($repeat === 'custom') {
            $repeat = $request->input('repeat_custom');
        }

        Todo::create([
            'user_id'         => Auth::id(),
            'title'           => $request->input('title'),
            'assigned_to'     => $request->input('assigned_to'),
            'kpi_target'      => $request->input('kpi_target'),
            'deadline'        => $request->input('deadline'),
            'priority'        => $request->input('priority') ?? 'Normal',
            'status'          => $request->input('status'),
            'detail'          => $request->input('detail'),
            'completed'       => false,
            'important'       => $request->boolean('important'), // checkbox hoặc hidden field
            'attachment_link' => $request->input('attachment_link'),
            'repeat'          => $repeat,
        ]);

        return redirect()->route('dashboard')->with('success', 'Thêm công việc thành công!');
    }

    // Form sửa todo
    public function edit($id, Request $request)
    {
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $users = User::all();
        $tab = $request->get('tab', 'tasks');
        return view('todos.edit', [
            'todo' => $todo,
            'users' => $users,
            'tab' => $tab,
            'deadlineVal' => old('deadline', $todo->deadline ?? null),
            'repeatVal' => old('repeat', $todo->repeat ?? null),
            'repeatCustom' => old('repeat_custom', null),
        ]);
    }

    // Xử lý update todo
    public function update(Request $request, $id)
    {
        if ($request->input('deadline') === 'none' || empty($request->input('deadline'))) {
            $request->merge(['deadline' => null]);
        }

        $request->validate([
            'title'           => 'required|string|max:255',
            'priority'        => 'required|string',
            'deadline'        => 'nullable|date',
            'status'          => 'required|string',
            'detail'          => 'nullable|string',
            'assigned_to'     => 'nullable|exists:users,id',
            'kpi_target'      => 'nullable|integer|min:1',
            'attachment_link' => 'nullable|url|max:500',
            'repeat'          => 'nullable|string|max:50',
            'repeat_custom'   => 'nullable|string|max:100',
        ]);

        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();

        $repeat = $request->input('repeat');
        if ($repeat === 'custom') {
            $repeat = $request->input('repeat_custom');
        }

        $todo->fill([
            'title'           => $request->input('title'),
            'deadline'        => $request->input('deadline'),
            'priority'        => $request->input('priority') ?? 'Normal',
            'status'          => $request->input('status'),
            'detail'          => $request->input('detail'),
            'assigned_to'     => $request->input('assigned_to'),
            'kpi_target'      => $request->input('kpi_target'),
            'attachment_link' => $request->input('attachment_link'),
            'repeat'          => $repeat,
            'important'       => $request->boolean('important'),
        ]);
        $todo->save();

        return redirect()->route('dashboard');
    }

    // Đổi trạng thái (AJAX)
    public function updateStatus(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo); // policy, nếu có
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

    // Đánh dấu hoàn thành/chưa hoàn thành
    public function markDone($id)
    {
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->completed = !$todo->completed;
        $todo->save();
        return redirect()->back();
    }

    // Đánh dấu/cởi đánh dấu công việc quan trọng
    public function toggleImportance($id)
    {
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->important = !$todo->important;
        $todo->save();
        return redirect()->back();
    }

    // Xoá todo
    public function delete($id)
    {
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->delete();

        return redirect()->route('dashboard')->with('success', 'Đã xoá thành công!');

    }

    // Hiện form nhập tiến độ
    public function progressForm($id)
    {
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $progresses = $todo->progresses()->orderBy('progress_date')->get();
        return view('todos.progress', compact('todo', 'progresses'));
    }

    // Lưu tiến độ
    public function storeProgress(Request $request, $id)
    {
        $request->validate([
            'progress_date' => 'required|date',
            'quantity'      => 'required|integer|min:1'
        ]);
        $todo = Todo::where('id', $id)->where(function($q){
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();

        TodoProgress::updateOrCreate(
            ['todo_id' => $todo->id, 'progress_date' => $request->input('progress_date')],
            ['quantity' => $request->input('quantity')]
        );
        return redirect()->route('todos.progress.form', $todo->id)->with('success', 'Đã ghi nhận tiến độ!');
    }
}
