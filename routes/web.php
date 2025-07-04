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

Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'add']);
    Route::get('/todos/{id}/toggle', [TodoController::class, 'markDone']);
    Route::delete('/todos/{id}', [TodoController::class, 'delete']);
    Route::put('/todos/{id}', [TodoController::class, 'update']);

    Route::get('/todos/{id}/edit', [TodoController::class, 'edit']);
    Route::put('/todos/{id}', [TodoController::class, 'update']);

});

//User
Route::get('/register', [UserController::class, 'registerForm']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/login', [UserController::class, 'LoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
    

// require __DIR__.'/auth.php';
