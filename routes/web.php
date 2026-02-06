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
use App\Http\Controllers\ResultDivisionController;
use App\Http\Controllers\ViewerController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\AccountRequestController;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [ViewerController::class, 'home'])->name('landing');

// Account requests
Route::post('/account-request', [AccountRequestController::class, 'store'])->name('account-request.store');

Route::post('/account-requests/{accountRequest}/done', 
    [UserController::class, 'markAccountRequestDone']
)->name('account-requests.done');


// Switch account
Route::post('/switch-account', [SwitchAccountController::class, 'store'])->name('switch.account');

// Reset user password
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
    ->name('users.reset-password');

/*
|--------------------------------------------------------------------------
| Viewer Routes (Public / No Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('viewer')->name('viewer.')->group(function() {
    Route::get('home', [ViewerController::class, 'home'])->name('home');
    Route::get('participants', [ViewerController::class, 'participants'])->name('participants');
    Route::get('participants/{division}', [ViewerController::class, 'participantShow'])->name('participants.show');
    Route::get('events', [ViewerController::class, 'events'])->name('events');
    Route::get('games', [ViewerController::class, 'games'])->name('games');
    Route::get('announcements', [ViewerController::class, 'announcements'])->name('announcements');
    Route::get('feedbacks', [ViewerController::class, 'feedbacks'])->name('feedbacks');
    Route::post('feedbacks/send', [ViewerController::class, 'sendFeedback'])->name('feedbacks.send');

    // Medal Tally
    Route::get('medal-tally', [ViewerController::class, 'medalTally'])->name('medalTally');

    // Live scores & modals
    Route::get('live-scores', [ViewerController::class, 'liveScores'])->name('liveScores');
    Route::get('game/{game}/live', [ViewerController::class, 'liveGame']);
    Route::get('game/{game}/live-score', [ViewerController::class, 'liveGame'])->name('liveGame');
    Route::get('game/{game}/live-timer', [ViewerController::class, 'liveTimer']);
    Route::get('game/{game}/live-periods', [ViewerController::class, 'livePeriodScores']);
    Route::get('game/{game}', [ViewerController::class, 'gameModal'])->name('game.modal');

    // Gallery
    Route::get('gallery', [ViewerController::class, 'gallery'])->name('gallery');
});

    /*
|--------------------------------------------------------------------------
| Attendance Scanner (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/check-attendance', [AttendanceController::class, 'index'])
    ->name('attendance.check');

Route::post('/check-attendance', [AttendanceController::class, 'store'])
    ->name('attendance.store');
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function() {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('users.updateProfile');

    /*
    |--------------------------------------------------------------------------
    | Admin Attendance
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        Route::get('attendance', [\App\Http\Controllers\AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/generate', [\App\Http\Controllers\AdminAttendanceController::class, 'generateQr'])->name('attendance.generate');
        Route::get('attendance/export', [\App\Http\Controllers\AdminAttendanceController::class, 'exportPdf'])->name('attendance.export');
    });


    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    */
    Route::resource('divisions', DivisionController::class);
    Route::resource('schools', SchoolController::class);
    Route::resource('sports', SportController::class);
    Route::resource('events', EventController::class);
    Route::resource('coaches', CoachController::class);
    Route::get('coaches/{coach}/qr', [CoachController::class, 'qr'])->name('coaches.qr');
    Route::resource('officials', OfficialController::class)->only(['index','create','store','destroy','edit','update']);
    Route::resource('announcements', AnnouncementController::class);

    /*
    |--------------------------------------------------------------------------
    | Athletes
    |--------------------------------------------------------------------------
    */
    Route::resource('athletes', AthleteController::class)->except(['show']);
    Route::get('athletes/{athlete}/qr', [AthleteController::class, 'qr'])->name('athletes.qr');
    Route::post('athletes/bulk-upload', [AthleteController::class, 'bulkUpload'])->name('athletes.bulkUpload');
    Route::get('/athletes/export-id-pdf', [AthleteController::class, 'exportIdPdf'])
    ->name('athletes.exportIdPdf');


    /*
    |--------------------------------------------------------------------------
    | Games
    |--------------------------------------------------------------------------
    */
    Route::resource('games', GameController::class);
    Route::get('games/{game}/manage', [GameController::class, 'manage'])->name('games.manage');
    Route::post('games/{game}/update-score', [GameController::class, 'updateScore'])->name('games.updateScore');
    Route::post('games/{game}/update-period/{periodIndex}', [GameController::class, 'updatePeriodScore'])->name('games.updatePeriodScore');
    Route::post('games/{game}/live-score', [GameController::class, 'liveScore'])->name('games.liveScore');
    Route::post('games/{game}/update-timer', [GameController::class, 'updateTimer'])->name('games.updateTimer');
    Route::post('games/{game}/update-live-video', [GameController::class, 'updateLiveVideo'])->name('games.updateLiveVideo');
    Route::post('games/{game}/start', [GameController::class, 'startGame'])->name('games.start');
    Route::post('games/{game}/winner', [GameController::class, 'setWinner'])->name('games.winner');
    Route::post('games/{game}/finish', [GameController::class, 'finishGame'])->name('games.finish');

    /*
    |--------------------------------------------------------------------------
    | Brackets
    |--------------------------------------------------------------------------
    */
    Route::get('brackets/create', [BracketController::class, 'create'])->name('brackets.create');
    Route::post('brackets', [BracketController::class, 'store'])->name('brackets.store');

    /*
    |--------------------------------------------------------------------------
    | Result Divisions
    |--------------------------------------------------------------------------
    */
    Route::prefix('results-divisions')->name('results_divisions.')->group(function () {
        Route::get('/', [ResultDivisionController::class, 'index'])->name('index');
        Route::get('/create', [ResultDivisionController::class, 'create'])->name('create');
        Route::post('/store', [ResultDivisionController::class, 'store'])->name('store');

        // PDF export - add export() method to controller
        Route::get('/export', [ResultDivisionController::class, 'export'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    */
    Route::resource('audit-logs', AuditLogController::class)->only(['index']);
    Route::get('audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit-logs.export');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Gallery
    |--------------------------------------------------------------------------
    */
    // Route::prefix('gallery')->group(function() {
    //     Route::get('/', [GalleryController::class, 'index'])->name('gallery.index');
    //     Route::get('/create', [GalleryController::class, 'create'])->name('gallery.create');
    //     Route::post('/', [GalleryController::class, 'store'])->name('gallery.store');
    //     Route::get('/{photo}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
    //     Route::put('/{photo}', [GalleryController::class, 'update'])->name('gallery.update');
    //     Route::delete('/{photo}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    // });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/layout', [\App\Http\Controllers\SettingsController::class, 'layout'])->name('layout');
        Route::post('/layout', [\App\Http\Controllers\SettingsController::class, 'saveLayout'])->name('layout.save');
    });
});

Route::get('events/{event}/manage', [EventController::class, 'manage'])
    ->name('events.manage');


require __DIR__.'/auth.php';
