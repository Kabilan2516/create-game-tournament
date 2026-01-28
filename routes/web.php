<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\OrganizerResultController;
use App\Http\Controllers\PlayerDashboardController;
use App\Http\Controllers\PlayerTournamentController;
use App\Http\Controllers\PlayerRoomController;
use App\Http\Controllers\PlayerNotificationController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\PlayerPaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 LANDING PAGE

Route::get('/', [HomeController::class, 'index'])->name('home');

// 🔹 AUTH UI PAGES (GET ROUTES – FOR YOUR CUSTOM VIEWS)

// Login Page
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

// Register Page
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

// Forgot Password Page
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// Verify Email Page (after login)
Route::get('/verify-email', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

// 🔹 DASHBOARD (After Login)
Route::middleware(['auth', 'role:organizer'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard'); // we will create this later
    })->name('dashboard');
});

Route::get('/verify-email', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/password-reset/{token}', function (Request $request, $token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->email,
    ]);
})->middleware('guest')->name('password.reset');

// 🔹 TOURNAMENT ROUTES (PUBLIC VIEW)
Route::get('/tournaments', [TournamentController::class, 'index'])
    ->name('tournaments.index');

Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])
    ->name('tournaments.show');
// Public Join Page (No Login Required)
Route::get('/tournaments/{tournament}/join', [TournamentController::class, 'joinForm'])
    ->name('tournaments.join.form');

Route::post('/tournaments/{tournament}/join', [TournamentController::class, 'joinStore'])
    ->name('tournaments.join.store');
// Public organizer profile
Route::get('/organizers/{user}', [OrganizerController::class, 'publicProfile'])
    ->name('organizers.public');

// 🔹 ORGANIZER ROUTES (ONLY LOGGED IN + ORGANIZER ROLE)
Route::middleware(['auth', 'role:organizer'])->group(function () {
    // Create Tournament
    Route::get('/dashboard/tournaments/create', [TournamentController::class, 'create'])
        ->name('tournaments.create');

    Route::post('/organizer/tournaments', [TournamentController::class, 'store'])
        ->name('organizer.tournaments.store');


    // Edit Tournament
    Route::get('/dashboard/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])
        ->name('tournaments.edit');

    Route::put('/dashboard/tournaments/{tournament}', [TournamentController::class, 'update'])
        ->name('tournaments.update');

    Route::delete('/dashboard/tournaments/{tournament}', [TournamentController::class, 'destroy'])
        ->name('tournaments.destroy');

    // My Tournaments Page
    Route::get('/dashboard/tournaments', [TournamentController::class, 'myTournaments'])
        ->name('tournaments.my');
    // Join Requests Page
    Route::get('/dashboard/join-requests', [TournamentController::class, 'joinRequests'])

        ->name('tournaments.requests');

    Route::post('/organizer/requests/{join}/approve', [TournamentController::class, 'approve'])
        ->name('organizer.requests.approve');

    Route::post('/organizer/requests/{join}/reject', [TournamentController::class, 'reject'])
        ->name('organizer.requests.reject');
    Route::get('/organizer/joins/{join}', [TournamentController::class, 'showdetials'])
        ->name('organizer.joins.showdetials');

    Route::get('/organizer/tournaments/{tournament}/requests', [TournamentController::class, 'requests'])
        ->name('organizer.requests');
    Route::get(
        '/organizer/tournaments/{tournament}/release-room',
        [TournamentController::class, 'releaseRoom']
    )
        ->name('tournaments.release-room');
    Route::post(
        '/organizer/tournaments/{tournament}/send-room',
        [TournamentController::class, 'sendRoomDetails']
    )
        ->name('organizer.tournaments.sendRoom');
    Route::get(
        '/organizer/tournaments/{tournament}/results',
        [OrganizerResultController::class, 'create']
    )->name('organizer.results.upload');

    Route::post(
        '/organizer/tournaments/{tournament}/results',
        [OrganizerResultController::class, 'store']
    )->name('organizer.results.store');
    // Analytics Page
    Route::get('/dashboard/analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index');
    // Organizer Profile Page
    Route::get('/dashboard/profile', [OrganizerController::class, 'profile'])
        ->name('organizer.profile');
    // Organizer Settings Page
    Route::get('/dashboard/settings', [OrganizerController::class, 'settings'])

        ->name('organizer.settings');
});



Route::middleware(['auth', 'role:player,organizer'])
    ->prefix('player')
    ->name('player.')
    ->group(function () {

        /* =========================
           PLAYER DASHBOARD
        ========================== */
        Route::get('/dashboard', [PlayerDashboardController::class, 'index'])
            ->name('dashboard');

        /* =========================
           MY TOURNAMENTS
        ========================== */
        Route::get('/tournaments', [PlayerTournamentController::class, 'index'])
            ->name('tournaments');

        Route::get('/tournaments/{join}', [PlayerTournamentController::class, 'show'])
            ->name('tournaments.show');

        /* =========================
           MATCH ROOMS
        ========================== */
        Route::get('/rooms', [PlayerRoomController::class, 'index'])
            ->name('rooms');

        Route::get('/rooms/{join}', [PlayerRoomController::class, 'show'])
            ->name('rooms.show');

        /* =========================
           NOTIFICATIONS
        ========================== */
        Route::get('/notifications', [PlayerNotificationController::class, 'index'])
            ->name('notifications');

        Route::post('/notifications/{id}/read', [PlayerNotificationController::class, 'markRead'])
            ->name('notifications.read');

        /* =========================
           PLAYER PROFILE
        ========================== */
        Route::get('/profile', [PlayerProfileController::class, 'edit'])
            ->name('profile');

        Route::put('/profile', [PlayerProfileController::class, 'update'])
            ->name('profile.update');

        /* =========================
           PAYMENTS
        ========================== */
        Route::get('/payments', [PlayerPaymentController::class, 'index'])
            ->name('payments');

        Route::get('/payments/{join}', [PlayerPaymentController::class, 'show'])
            ->name('payments.show');

        /* =========================
           WALLET (FUTURE)
        ========================== */
        Route::get('/wallet', function () {
            abort(403, 'Wallet feature coming soon');
        })->name('wallet');
    });

// 🔹 PLAYER JOIN TOURNAMENT
Route::middleware(['auth'])->post('/tournaments/{id}/join', [TournamentController::class, 'join'])
    ->name('tournaments.join');



require __DIR__ . '/auth.php';
