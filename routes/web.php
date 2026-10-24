<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CultureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MysteryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\VenueController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// Landing
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Materi budaya (publik, dengan batasan guest di CultureController)
Route::prefix('budaya')->name('culture.')->group(function () {
    Route::get('/', [CultureController::class, 'islands'])->name('islands');
    Route::post('/pilih-provinsi', [CultureController::class, 'chooseProvince'])->middleware('guest')->name('choose-province');
    Route::get('/{island:slug}/filosofi', [CultureController::class, 'philosophy'])->name('philosophy');
    Route::get('/{island:slug}/{province:slug}', [CultureController::class, 'regencies'])->name('province');
    Route::get('/{island:slug}/{province:slug}/{regency:slug}', [CultureController::class, 'regency'])->name('regency');
    Route::get('/{island:slug}/{province:slug}/{regency:slug}/{culture:slug}', [CultureController::class, 'show'])->name('detail');
    Route::get('/{island:slug}', [CultureController::class, 'provinces'])->name('island');
});

// Pertunjukan
Route::get('/pertunjukan', [ShowController::class, 'index'])->name('shows.index');
Route::get('/pertunjukan/{show:slug}', [ShowController::class, 'show'])->name('shows.show');

// Tempat latihan (sanggar)
Route::get('/sanggar', [VenueController::class, 'index'])->name('venues.index');
Route::get('/sanggar/permintaanku', [VenueController::class, 'myRequests'])->middleware('auth')->name('venues.requests');
Route::post('/sanggar/{venue:slug}/tertarik', [VenueController::class, 'interested'])->middleware('auth')->name('venues.interested');

// Kuis harian & mingguan
Route::get('/kuis', [QuestController::class, 'index'])->name('quests.index');
Route::middleware('auth')->group(function () {
    Route::post('/kuis/{quest}/mulai', [QuestController::class, 'start'])->name('quests.start');
    Route::get('/attempt/{attempt}/main', [QuestController::class, 'play'])->name('quests.play');
    Route::post('/attempt/{attempt}/jawab', [QuestController::class, 'submitAnswer'])->name('quests.answer');
    Route::post('/attempt/{attempt}/timeout', [QuestController::class, 'timeout'])->name('quests.timeout');
    Route::post('/attempt/{attempt}/selesai', [QuestController::class, 'finish'])->name('quests.finish');
    Route::get('/attempt/{attempt}/hasil', [QuestController::class, 'result'])->name('quests.result');
});

// Mystery Budaya
Route::get('/misteri', [MysteryController::class, 'index'])->name('mystery.index');
Route::middleware('auth')->group(function () {
    Route::get('/misteri/{mystery}', [MysteryController::class, 'play'])->name('mystery.play');
    Route::post('/misteri/{mystery}/petunjuk', [MysteryController::class, 'reveal'])->name('mystery.reveal');
    Route::post('/misteri/{mystery}/jawab', [MysteryController::class, 'guess'])->name('mystery.guess');
});

// Leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Profil
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin
Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('quests', Admin\QuestAdminController::class)->except('show');
        Route::get('/quests/{quest}/soal', [Admin\QuestionAdminController::class, 'index'])->name('quests.questions');
        Route::post('/quests/{quest}/soal', [Admin\QuestionAdminController::class, 'store'])->name('questions.store');
        Route::post('/soal/{question}', [Admin\QuestionAdminController::class, 'update'])->name('questions.update');
        Route::delete('/soal/{question}', [Admin\QuestionAdminController::class, 'destroy'])->name('questions.destroy');

        Route::resource('shows', Admin\ShowAdminController::class)->except('show')->parameters(['shows' => 'show']);
        Route::resource('venues', Admin\VenueAdminController::class)->except('show');

        Route::get('/permintaan', [Admin\InterestRequestAdminController::class, 'index'])->name('requests.index');
        Route::patch('/permintaan/{interestRequest}', [Admin\InterestRequestAdminController::class, 'update'])->name('requests.update');
    });
