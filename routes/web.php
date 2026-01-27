<?php

use App\Http\Controllers\Auth\SwitchAccountController;
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
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ViewerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BracketController;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Athlete;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::post('/switch-account', [SwitchAccountController::class, 'store'])
    ->name('switch.account');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Viewer pages — no login required
Route::prefix('viewer')->name('viewer.')->group(function() {
    Route::get('home', [ViewerController::class, 'home'])->name('home');
    Route::get('participants', [ViewerController::class, 'participants'])->name('participants');
    Route::get('participants/{school}', [ViewerController::class, 'participantShow'])->name('participants.show');
    Route::get('events', [ViewerController::class, 'events'])->name('events');
    Route::get('games', [ViewerController::class, 'games'])->name('games');
    Route::get('announcements', [ViewerController::class, 'announcements'])->name('announcements');
    Route::get('feedbacks', [ViewerController::class, 'feedbacks'])->name('feedbacks');
});

Route::get('viewer/feedbacks', [App\Http\Controllers\ViewerController::class, 'feedbacks'])->name('viewer.feedbacks');
Route::post('viewer/feedbacks/send', [App\Http\Controllers\ViewerController::class, 'sendFeedback'])->name('viewer.feedbacks.send');


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('attendance', [\App\Http\Controllers\AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/generate', [\App\Http\Controllers\AdminAttendanceController::class, 'generateQr'])->name('attendance.generate');
    Route::get('attendance/export', [\App\Http\Controllers\AdminAttendanceController::class, 'exportPdf'])->name('attendance.export');
});

Route::get('/check-attendance', [AttendanceController::class, 'index'])
    ->name('attendance.check');

Route::post('/check-attendance', [AttendanceController::class, 'store'])
    ->name('attendance.store');

Route::get('/athletes/{athlete}/qr', [AthleteController::class, 'qr'])->name('athletes.qr');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Logged-in user profile update
Route::put('/profile/update', [UserController::class, 'updateProfile'])
     ->name('users.updateProfile')
     ->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::resource('schools', SchoolController::class);
    Route::resource('sports', SportController::class);
    Route::resource('events', EventController::class);
    Route::resource('athletes', AthleteController::class);
});

Route::resource('athletes', AthleteController::class)->middleware('auth');
Route::post('/athletes/bulk-upload', [AthleteController::class, 'bulkUpload'])
     ->name('athletes.bulk-upload');


Route::get('athletes/{athlete}/qr', [AthleteController::class, 'qr'])->name('athletes.qr');
Route::get('/athletes/{athlete}/qr', function (Athlete $athlete) {

    $data = json_encode([
        'type' => 'athlete',
        'qr_code' => $athlete->qr_code,
    ]);

    return response(
        QrCode::format('png')->size(150)->generate($data)
    )->header('Content-Type', 'image/png');

})->name('athletes.qr');
Route::resource('coaches', CoachController::class)->middleware('auth');
Route::get('coaches/{coach}/qr', [CoachController::class, 'qr'])->name('coaches.qr');

Route::resource('games', GameController::class)->middleware('auth');
Route::get('/games/{game}/manage', [GameController::class, 'manage'])->name('games.manage');
Route::post('/games/{game}/update-score', [GameController::class, 'updateScore'])->name('games.updateScore');
Route::post('/games/{game}/update-period/{periodIndex}', [GameController::class, 'updatePeriodScore'])
    ->name('games.updatePeriodScore');

    // Admin live update
Route::post('/games/{game}/live-score', [GameController::class, 'liveScore'])
    ->name('games.liveScore');

Route::post('/games/{game}/update-timer', [GameController::class, 'updateTimer'])->name('games.updateTimer');

Route::post('/games/{game}/update-live-video', [GameController::class, 'updateLiveVideo'])
    ->name('games.updateLiveVideo');

Route::post('games/{game}/start', [GameController::class, 'startGame'])->name('games.start');
Route::post('games/{game}/winner', [GameController::class, 'setWinner'])->name('games.winner');


Route::post('/games/{game}/finish', [GameController::class, 'finishGame'])
     ->name('games.finish');

Route::middleware(['auth'])->group(function () {
    Route::get('brackets/create', [BracketController::class, 'create'])->name('brackets.create');
    Route::post('brackets', [BracketController::class, 'store'])->name('brackets.store');
});


// Route::get('/viewer/game/{game}/live-timer', [GameController::class, 'liveTimer'])->name('viewer.liveTimer');
Route::get('/viewer/game/{game}/live-timer', [ViewerController::class, 'liveTimer']);
Route::get('/viewer/game/{game}/live-periods', [ViewerController::class, 'livePeriodScores']);


// Viewer polling
Route::get('/viewer/live-scores', [ViewerController::class, 'liveScores'])
    ->name('viewer.liveScores');

Route::get('/viewer/game/{game}', [ViewerController::class, 'gameModal']);
Route::get('/viewer/game/{game}/live', [ViewerController::class, 'liveGame']);



Route::middleware(['auth'])->group(function () {
    Route::resource('officials', OfficialController::class)->only([
        'index', 'create', 'store', 'destroy', 'edit', 'update'
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('results', ResultController::class)->only([
        'index', 'create', 'store'
    ]);
});
Route::get('/results/export', [ResultController::class, 'export'])->name('results.export');



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

Route::prefix('gallery')->group(function() {
    Route::get('/', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/create', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('/', [GalleryController::class, 'store'])->name('gallery.store');
    Route::get('/{photo}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
    Route::put('/{photo}', [GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/{photo}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
});

// Viewer Gallery
Route::get('/viewer/gallery', [ViewerController::class, 'gallery'])->name('viewer.gallery');



Route::resource('announcements', AnnouncementController::class);


Route::prefix('settings')->name('settings.')->middleware('auth')->group(function () {
    Route::get('/layout', [\App\Http\Controllers\SettingsController::class, 'layout'])->name('layout');
    Route::post('/layout', [\App\Http\Controllers\SettingsController::class, 'saveLayout'])->name('layout.save');
});



require __DIR__.'/auth.php';
