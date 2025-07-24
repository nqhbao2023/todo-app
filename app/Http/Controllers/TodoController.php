<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use App\Models\TodoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TodoController extends Controller
{
    // Trang dashboard - danh sách todo với các tab dạng "My Day", "Important",...
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'myday');
        $user = Auth::user();
        $userId = $user->id;

        // Nếu là leader thì không filter theo user_id/assigned_to
        if ($user->role === 'leader') {
            $todosQuery = Todo::with(['assignee', 'progresses']);
        } else {
            $todosQuery = Todo::with(['assignee', 'progresses'])
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->orWhere('assigned_to', $userId);
                });
        }

        // Đếm số lượng cho badge ở các tab
        $countTabs = [
            'myday'     => $user->role === 'leader'
                ? Todo::whereDate('deadline', now())->count()
                : Todo::where(function ($q) use ($userId) { $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                    ->whereDate('deadline', now())->count(),
            'important' => $user->role === 'leader'
                ? Todo::where('important', true)->count()
                : Todo::where(function ($q) use ($userId) { $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                    ->where('important', true)->count(),
            'planned'   => $user->role === 'leader'
                ? Todo::whereNotNull('deadline')->count()
                : Todo::where(function ($q) use ($userId) { $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })
                    ->whereNotNull('deadline')->count(),
            'assigned'  => $user->role === 'leader'
                ? Todo::whereNotNull('assigned_to')->count()
                : Todo::where('assigned_to', $userId)->count(),
            'tasks'     => $user->role === 'leader'
                ? Todo::count()
                : Todo::where(function ($q) use ($userId) { $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })->count(),
            'flagged' => $user->role === 'leader'
                ? Todo::where('flagged', true)->count()
                : Todo::where(function ($q) use ($userId) { $q->where('user_id', $userId)->orWhere('assigned_to', $userId); })->where('flagged', true)->count(),
        ];

        $users = User::all();

        // Các tab khác giữ nguyên paginate như cũ
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
            case 'completed':
                $todosQuery->where('completed', true);
                break;
            case 'flagged':
                $todosQuery->where('flagged', true);
                break;
            case 'tasks':
            default:
                // không filter thêm, lấy tất cả tasks liên quan user
                break;
        }
        if ($tab === 'kpi') {
            $todosQuery->whereNotNull('kpi_target');
        }

        $todos = $todosQuery->with('progresses')->orderBy('deadline')->paginate(10);

        if ($tab === 'myday') {
            foreach ($todos as $todo) {
                $todo->suggest_today = $this->getTodayKpiSuggestion($todo);
                $todo->remaining     = max(0, ($todo->kpi_target ?? 0) - ($todo->total_progress ?? 0));
            }
        }

        // Nếu là tab kpi, tính daySuggestions cho từng todo KPI
        if ($tab === 'kpi') {
            foreach ($todos as $todo) {
                if ($todo->kpi_target && $todo->deadline) {
                    $totalProgress = $todo->progresses->sum('quantity');
                    $remaining = max(0, $todo->kpi_target - $totalProgress);
                    $from = now()->startOfDay();
                    $to = \Carbon\Carbon::parse($todo->deadline)->startOfDay();

                    $workDates = collect(\Carbon\CarbonPeriod::create($from, $to))
                        ->filter(function ($date) {
                            return $date->dayOfWeek !== \Carbon\Carbon::SUNDAY;
                        })
                        ->values();
                    if ($workDates->isEmpty()) {
                        $workDates = collect(); // Không có ngày nào hợp lệ
                    }

                    // === Đoạn SỬA MỚI chia nhỏ KPI ===
                    $datesArr = $workDates->map(fn($d) => $d->format('d/m'))->values()->all();
                    $progressByDate = $todo->progresses->groupBy(fn($p) => \Carbon\Carbon::parse($p->progress_date)->format('d/m'));
                    $uncompletedDates = [];
                    foreach ($datesArr as $d) {
                        $done = $progressByDate->get($d, collect())->sum('quantity');
                        if ($done < 1) $uncompletedDates[] = $d;
                    }
                    $numUncompleted = count($uncompletedDates) ?: 1;
                    $suggest = $remaining > 0 ? (int)ceil($remaining / $numUncompleted) : 0;

                    $daySuggestions = [];
                    foreach ($datesArr as $d) {
                        $done = $progressByDate->get($d, collect())->sum('quantity');
                        if ($done >= $suggest && $suggest > 0) {
                            $daySuggestions[$d] = '✔';
                        } elseif ($done > 0 && $done < $suggest) {
                            $daySuggestions[$d] = "$done/$suggest";
                        } else {
                            $daySuggestions[$d] = $suggest;
                        }
                    }
                    $todo->daySuggestions = $daySuggestions;
                    // === Hết đoạn sửa ===
                } else {
                    $todo->daySuggestions = [];
                }
            }
        }

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
        $todo = Todo::where('id', $id)->where(function ($q) {
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
        $user = Auth::user();
        // Nếu không phải leader thì không cho sửa assigned_to
        if ($user->role !== 'leader') {
            $request->request->remove('assigned_to');
        }
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

        $todo = Todo::where('id', $id)->where(function ($q) {
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

    public function quickUpdate(Request $request, $id)
    {
        $user = Auth::user();
        // Nếu không phải leader thì không cho sửa assigned_to
        if ($user->role !== 'leader') {
            $request->request->remove('assigned_to');
        }
        $todo = Todo::where('id', $id)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
            })->firstOrFail();

        // Chuyển đổi deadline nếu có
        if ($request->filled('deadline')) {
            $deadline = $request->input('deadline');
            $deadline = str_replace('T', ' ', $deadline);
            if (strlen($deadline) == 16) { // Nếu thiếu giây
                $deadline .= ':00';
            }
            $request->merge(['deadline' => $deadline]);
        }

        foreach (['assigned_to', 'kpi_target', 'attachment_link', 'deadline'] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        $request->validate([
            'title'           => 'required|string|max:255',
            'priority'        => 'required|string',
            'deadline'        => 'nullable|date',
            'status'          => 'required|string',
            'detail'          => 'nullable|string',
            'assigned_to'     => 'nullable|exists:users,id',
            'kpi_target'      => 'nullable|integer|min:1',
            'attachment_link' => 'nullable|string|max:500', // Cho phép để trống hoặc không phải url
        ]);

        $todo->fill([
            'title'           => $request->input('title'),
            'deadline'        => $request->input('deadline'),
            'priority'        => $request->input('priority') ?? 'Normal',
            'status'          => $request->input('status'),
            'detail'          => $request->input('detail'),
            'assigned_to'     => $request->input('assigned_to'),
            'kpi_target'      => $request->input('kpi_target'),
            'attachment_link' => $request->input('attachment_link'),
        ]);
        $todo->save();

        return response()->json(['success' => true]);
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
        $todo = Todo::where('id', $id)->where(function ($q) {
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->completed = !$todo->completed;
        $todo->save();
        return redirect()->back();
    }

    // Đánh dấu/cởi đánh dấu công việc quan trọng
    public function toggleImportance($id)
    {
        $todo = Todo::where('id', $id)->where(function ($q) {
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->important = !$todo->important;
        $todo->save();
        return redirect()->back();
    }

    // Xoá todo
    public function delete($id)
    {
        $todo = Todo::where('id', $id)->where(function ($q) {
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();
        $todo->delete();

        return redirect()->route('dashboard')->with('success', 'Đã xoá thành công!');
    }

    // Hiện form nhập tiến độ
    public function progressForm($id)
    {
        $todo = Todo::where('id', $id)->where(function ($q) {
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
        $todo = Todo::where('id', $id)->where(function ($q) {
            $q->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id());
        })->firstOrFail();

        TodoProgress::updateOrCreate(
            ['todo_id' => $todo->id, 'progress_date' => $request->input('progress_date')],
            ['quantity' => $request->input('quantity')]
        );
        return redirect()->route('todos.progress.form', $todo->id)->with('success', 'Đã ghi nhận tiến độ!');
    }

    private function getTodayKpiSuggestion($todo, $forDate = null)
    {
        if (!$forDate) $forDate = now()->startOfDay();

        $target = $todo->kpi_target ?? 0;
        $done   = $todo->progresses->sum('quantity');
        $remain = max(0, $target - $done);

        if ($remain <= 0) return 0; // Xong rồi thì không cần làm nữa

        // Xác định số ngày còn lại (loại T7, CN nếu muốn, hoặc giữ nguyên)
        $deadline = $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->startOfDay() : null;
        if (!$deadline || $forDate->gt($deadline)) return 0;

        // Xử lý repeat: daily (mỗi ngày, không kể T7, CN), none (chỉ deadline)
        $dates = [];
        $cur = $forDate->copy();
        while ($cur->lte($deadline)) {
            if ($todo->repeat === 'daily' && $cur->isWeekend()) {
                $cur->addDay();
                continue;
            }
            $dates[] = $cur->copy();
            $cur->addDay();
        }
        $daysLeft = count($dates);

        // Nếu chỉ còn 1 ngày thì phải làm hết số còn lại
        if ($daysLeft <= 1) return $remain;

        // Ngược lại chia đều, làm tròn lên để kịp tiến độ
        return ceil($remain / $daysLeft);
    }

    public function tabPartial($tab, Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $todosQuery = Todo::with('assignee');

        // Nếu là leader thì không filter theo user_id/assigned_to
        if ($user->role === 'leader') {
            $todosQuery = Todo::with('assignee');
        } else {
            $todosQuery = Todo::with('assignee')
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->orWhere('assigned_to', $userId);
                });
        }

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
            case 'completed':
                $todosQuery->where('completed', true);
                break;
            case 'flagged':
                $todosQuery->where('flagged', true);
                break;
            case 'tasks':
                $todosQuery->where('completed', false);
                break;
            case 'kpi':
                $todosQuery->whereNotNull('kpi_target');
                break;
            default:
                // không filter thêm, lấy tất cả tasks liên quan user
                break;
        }
        $todos = $todosQuery->with('progresses')->orderBy('deadline')->paginate(10);
        $users = User::all();

        if ($tab === 'kpi') {
            foreach ($todos as $todo) {
                if ($todo->kpi_target && $todo->deadline) {
                    $totalProgress = $todo->progresses->sum('quantity');
                    $remaining = max(0, $todo->kpi_target - $totalProgress);
                    $from = now()->startOfDay();
                    $to = \Carbon\Carbon::parse($todo->deadline)->startOfDay();

                    $workDates = collect(\Carbon\CarbonPeriod::create($from, $to))
                        ->filter(function ($date) use ($to) {
                            return $date->dayOfWeek !== \Carbon\Carbon::SUNDAY || $date->eq($to);
                        })
                        ->values();
                    if ($workDates->isEmpty()) {
                        $workDates = collect([$to]);
                    }

                    // === Đoạn SỬA MỚI chia nhỏ KPI ===
                    $datesArr = $workDates->map(fn($d) => $d->format('d/m'))->values()->all();
                    $progressByDate = $todo->progresses->groupBy(fn($p) => \Carbon\Carbon::parse($p->progress_date)->format('d/m'));
                    $uncompletedDates = [];
                    foreach ($datesArr as $d) {
                        $done = $progressByDate->get($d, collect())->sum('quantity');
                        if ($done < 1) $uncompletedDates[] = $d;
                    }
                    $numUncompleted = count($uncompletedDates) ?: 1;
                    $suggest = $remaining > 0 ? (int)ceil($remaining / $numUncompleted) : 0;

                    $daySuggestions = [];
                    foreach ($datesArr as $d) {
                        $done = $progressByDate->get($d, collect())->sum('quantity');
                        if ($done >= $suggest && $suggest > 0) {
                            $daySuggestions[$d] = '✔';
                        } elseif ($done > 0 && $done < $suggest) {
                            $daySuggestions[$d] = "$done/$suggest";
                        } else {
                            $daySuggestions[$d] = $suggest;
                        }
                    }
                    $todo->daySuggestions = $daySuggestions;
                    // === Hết đoạn sửa ===

                } else {
                    $todo->daySuggestions = [];
                }
                // Tự động đánh dấu hoàn thành nếu đã đủ KPI
                if ($todo->kpi_target && $todo->progresses->sum('quantity') >= $todo->kpi_target && !$todo->completed) {
                    $todo->completed = true;
                    $todo->save();
                }
            }
        }

        return view('partials.todo_table', compact('todos', 'tab', 'users'))->render();
    }

    // Hiển thị tab "Đã lên kế hoạch" (planned) với các todo KPI còn hạn, chưa đủ KPI
    public function plannedTasks()
    {
        $userId = Auth::id();
        $today = now()->startOfDay();

        // Lấy todo chưa đủ KPI, deadline còn hạn
        $todos = Todo::with('progresses')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('assigned_to', $userId);
            })
            ->where('deadline', '>=', $today)
            ->whereRaw('(kpi_target IS NOT NULL AND kpi_target > 0)')
            ->get()
            ->filter(function ($todo) {
                return ($todo->total_progress ?? 0) < ($todo->kpi_target ?? 0);
            })
            ->values();

        foreach ($todos as $todo) {
            $from = $today;
            $to = $todo->deadline ? \Carbon\Carbon::parse($todo->deadline) : $today;
            $workDates = collect(\Carbon\CarbonPeriod::create($from, $to))
                ->filter(function ($date) {
                    return $date->dayOfWeek !== \Carbon\Carbon::SUNDAY;
                })
                ->values();
            if ($workDates->isEmpty()) {
                $workDates = collect([$to]);
            }

            // === Đoạn SỬA MỚI chia nhỏ KPI ===
            $totalProgress = $todo->progresses->sum('quantity');
            $remaining = max(0, ($todo->kpi_target ?? 0) - $totalProgress);
            $datesArr = $workDates->map(fn($d) => $d->format('d/m'))->values()->all();
            $progressByDate = $todo->progresses->groupBy(fn($p) => \Carbon\Carbon::parse($p->progress_date)->format('d/m'));
            $uncompletedDates = [];
            foreach ($datesArr as $d) {
                $done = $progressByDate->get($d, collect())->sum('quantity');
                if ($done < 1) $uncompletedDates[] = $d;
            }
            $numUncompleted = count($uncompletedDates) ?: 1;
            $suggest = $remaining > 0 ? (int)ceil($remaining / $numUncompleted) : 0;

            $daySuggestions = [];
            foreach ($datesArr as $d) {
                $done = $progressByDate->get($d, collect())->sum('quantity');
                if ($done >= $suggest && $suggest > 0) {
                    $daySuggestions[$d] = '✔';
                } elseif ($done > 0 && $done < $suggest) {
                    $daySuggestions[$d] = "$done/$suggest";
                } else {
                    $daySuggestions[$d] = $suggest;
                }
            }
            $todo->daySuggestions = $daySuggestions;
            // === Hết đoạn sửa ===
        }

        return view('dashboard', compact('todos'))->with('tab', 'planned');
    }

    // Đếm số ngày làm việc còn lại (bỏ T7, CN nếu repeat=daily)
    private function countWorkdaysLeft($todo, $fromDate)
    {
        $deadline = $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->endOfDay() : null;
        if (!$deadline) return 0;
        $workdays = 0;
        $cur = $fromDate->copy();
        while ($cur->lte($deadline)) {
            if ($todo->repeat === 'daily' && ($cur->isSaturday() || $cur->isSunday())) {
                $cur->addDay();
                continue;
            }
            $workdays++;
            $cur->addDay();
        }
        return $workdays;
    }

    // Gợi ý số lượng nên làm hôm nay (chia đều cho các ngày còn lại)
    private function getDailyKpiSuggestion($todo, $forDate)
    {
        $done = $todo->progresses->sum('quantity');
        $target = $todo->kpi_target ?? 0;
        $deadline = $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->endOfDay() : null;
        if ($done >= $target || !$deadline || $deadline->lt($forDate)) return 0;
        $workdaysLeft = $this->countWorkdaysLeft($todo, $forDate);
        if ($workdaysLeft <= 1) return $target - $done;
        return ceil(($target - $done) / $workdaysLeft);
    }
}

