<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trang chủ: chuyển về login
Route::get('/', function () {
    return view('login'); 
});

// Auth routes (không cần đăng nhập)
Route::get('/register', [UserController::class, 'registerForm'])->name('register.form');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::get('/login', [UserController::class, 'LoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ------------------------
// Route cho member, leader, admin (user đã đăng nhập)
Route::middleware(['auth', 'role:admin,leader,member'])->group(function () {
    // Dashboard & Todo chức năng chính
    Route::get('/dashboard', [TodoController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/tab/{tab}', [TodoController::class, 'tabPartial'])->name('dashboard.tab');
    Route::post('/todos', [TodoController::class, 'add'])->name('todos.add');
    Route::get('/todos/create', [TodoController::class, 'create'])->name('todos.create');
    Route::get('/todos/{id}/edit', [TodoController::class, 'edit'])->name('todos.edit');
    Route::put('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [TodoController::class, 'delete'])->name('todos.delete');
    Route::post('/todos/{id}/delete', [TodoController::class, 'delete'])->name('todos.delete.ajax'); // AJAX xoá

    Route::post('/todos/{id}/toggle', [TodoController::class, 'markDone'])->name('todos.toggle');
    Route::post('/todos/{id}/toggle-importance', [TodoController::class, 'toggleImportance'])->name('todos.toggleImportance');
    Route::post('/todos/{id}/quick-update', [TodoController::class, 'quickUpdate'])->name('todos.quickUpdate');

    // Tiến độ công việc
    Route::get('/todos/{todo}/progress', [TodoController::class, 'progressForm'])->name('todos.progress.form');
    Route::post('/todos/{todo}/progress', [TodoController::class, 'storeProgress'])->name('todos.progress.store');
    Route::post('/todos/{todo}/update-status', [TodoController::class, 'updateStatus'])->name('todos.updateStatus');

    // Xuất báo cáo cá nhân
    Route::get('/report/export', [TodoController::class, 'exportReport'])->name('report.export');
});

// ------------------------
Route::middleware(['auth', 'role:admin,leader'])->group(function () {
    Route::get('/team/reports', [AdminController::class, 'teamReports'])->name('team.reports');
});

// ------------------------
// Route chỉ cho admin (quản trị hệ thống)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
Route::post('/todos/{todo}/progress', [TodoController::class, 'storeProgress'])->name('todos.progress.store');

// Form nhập email quên mật khẩu
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// Xử lý gửi mail
Route::post('forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return back()->with(['status' => __($status)]);
})->middleware('guest')->name('password.email');

// Form nhập lại mật khẩu (từ link trong mail)
Route::get('reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email')
    ]);
})->middleware('guest')->name('password.reset');

// Xử lý đổi mật khẩu
Route::post('reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        }
    );

    if ($status == Password::PASSWORD_RESET) {
        // KHÔNG được login user ở đây!
        return redirect()->route('login')
            ->with('success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới.');
    } else {
        return back()->withErrors(['email' => __($status)]);
    }
});
