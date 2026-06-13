<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\PomodoroController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ExportController;

Route::get('/', fn() => redirect()->route('dashboard'));

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/roadmap',   [RoadmapController::class, 'index'])->name('roadmap');
    Route::get('/stats',     [StatsController::class, 'index'])->name('stats');
    Route::get('/kanban',    [KanbanController::class, 'index'])->name('kanban');
    Route::get('/pomodoro',  [PomodoroController::class, 'index'])->name('pomodoro');

    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/progress', [ProjectController::class, 'updateProgress'])->name('projects.progress');

    Route::get('/tasks',                 [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks',                [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::patch('/tasks/{task}',        [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',       [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/reminders',                   [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminders',                  [ReminderController::class, 'store'])->name('reminders.store');
    Route::patch('/reminders/{reminder}/read', [ReminderController::class, 'markRead'])->name('reminders.read');
    Route::delete('/reminders/{reminder}',     [ReminderController::class, 'destroy'])->name('reminders.destroy');

    Route::patch('/kanban/{task}/status', [KanbanController::class, 'updateStatus'])->name('kanban.status');
    Route::post('/pomodoro',              [PomodoroController::class, 'store'])->name('pomodoro.store');

    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
});
