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

    Route::get('/dashboard', [TodoController::class, 'index'])->name('dashboard');
    Route::post('/todos', [TodoController::class, 'add'])->name('todos.add');
    Route::post('/todos/{id}/toggle', [TodoController::class, 'markDone'])->name('todos.toggle');
    Route::delete('/todos/{id}', [TodoController::class, 'delete'])->name('todos.delete');
    Route::put('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
    Route::get('/todos/{id}/edit', [TodoController::class, 'edit'])->name('todos.edit');
   
    Route::get('/todos/{todo}/progress', [TodoController::class, 'progressForm'])->name('todos.progress.form');
    Route::post('/todos/{todo}/progress', [TodoController::class, 'storeProgress'])->name('todos.progress.store');
    Route::post('/todos/{todo}/update-status', [TodoController::class, 'updateStatus'])->name('todos.updateStatus');

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

