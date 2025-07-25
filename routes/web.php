<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('/login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {


    
    Route::post('/todos/{todo}/progress', [TodoController::class, 'saveProgress'])->name('todos.progress.save');

    // web.php

    Route::get('/dashboard', [TodoController::class, 'index'])->name('dashboard');
    Route::post('/todos', [TodoController::class, 'add'])->name('todos.add');
    Route::post('/todos/{id}/toggle', [TodoController::class, 'markDone'])->name('todos.toggle');
    Route::delete('/todos/{id}', [TodoController::class, 'delete'])->name('todos.delete');
    // Thêm mới route này
    Route::post('/todos/{id}/delete', [TodoController::class, 'delete'])->name('todos.delete.ajax');

    Route::put('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
    Route::get('/todos/{id}/edit', [TodoController::class, 'edit'])->name('todos.edit');
   
    Route::get('/todos/{todo}/progress', [TodoController::class, 'progressForm'])->name('todos.progress.form');
    Route::post('/todos/{todo}/progress', [TodoController::class, 'storeProgress'])->name('todos.progress.store');
    Route::post('/todos/{todo}/update-status', [TodoController::class, 'updateStatus'])->name('todos.updateStatus');

    // Route POST đổi tầm quan trọng (ngôi sao)
    Route::post('/todos/{id}/toggle-importance', [TodoController::class, 'toggleImportance'])
    ->name('todos.toggleImportance');

    Route::post('/todos/{id}/quick-update', [TodoController::class, 'quickUpdate'])->name('todos.quickUpdate');

    Route::get('/dashboard/tab/{tab}', [TodoController::class, 'tabPartial'])->name('dashboard.tab');

});

//User
Route::get('/register', [UserController::class, 'registerForm'])->name('register.form');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::get('/login', [UserController::class, 'LoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    

// Trang tạo mới công việc (hiển thị form)
Route::get('/todos/create', [TodoController::class, 'create'])->name('todos.create');
//update trạng thái

Route::get('/report/export', [App\Http\Controllers\TodoController::class, 'exportReport'])->name('report.export');
Route::get('/dashboard/tab/{tab}', [TodoController::class, 'tabPartial'])->name('dashboard.tabPartial');
