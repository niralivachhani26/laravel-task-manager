<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/projects', ProjectController::class);
    Route::resource('/tasks', TaskController::class);
    Route::post('/tasks/toggle-status/{task}', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
    Route::get('/filter-tasks', [TaskController::class, 'filter'])->name('tasks.filter');
    Route::get('/search-tasks', [TaskController::class, 'search'])->name('tasks.search');
});

require __DIR__.'/auth.php';
