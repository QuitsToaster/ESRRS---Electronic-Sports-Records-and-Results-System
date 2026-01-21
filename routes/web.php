<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('schools', SchoolController::class);
    Route::resource('sports', SportController::class);
    Route::resource('events', EventController::class);
    Route::resource('athletes', AthleteController::class);
});

Route::resource('athletes', AthleteController::class)->middleware('auth');
Route::resource('coaches', CoachController::class)->middleware('auth');
Route::resource('games', GameController::class)->middleware('auth');

Route::resource('audit-logs', AuditLogController::class)->only(['index']);
Route::get('audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])
    ->name('audit-logs.export')
    ->middleware('auth');

Route::prefix('users')->group(function() {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');




Route::resource('announcements', AnnouncementController::class);



require __DIR__.'/auth.php';
