<?php

use App\Http\Controllers\ProfileController;
use App\Models\Task;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Tasks routes
    Route::get('/tasks', function () {
        return Inertia::render('Tasks/Index');
    })->name('tasks.index');

    Route::get('/tasks/create', function () {
        return Inertia::render('Tasks/Create');
    })->name('tasks.create');

    Route::get('/tasks/{task}/edit', function (Task $task) {
        return Inertia::render('Tasks/Edit', [
            'task' => $task
        ]);
    })->name('tasks.edit');
});

require __DIR__.'/auth.php';
