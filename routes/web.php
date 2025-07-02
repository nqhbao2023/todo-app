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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::get('/todos/{id}/toggle', [TodoController::class, 'toggle']);
    Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
});

//User
Route::get('/register', [UserController::class, 'registerForm']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/login', [UserController::class, 'LoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout', [UserComtroller::class, 'logout']);

Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard',[TodoController::class,'index']);
    });

// require __DIR__.'/auth.php';
