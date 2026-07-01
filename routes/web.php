<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [AdminController::class, 'search'])->name('search');
    Route::get('/verification', [AdminController::class, 'verification'])->name('verification');
    Route::post('/verification/{submission}', [AdminController::class, 'verify'])->name('verify');
    Route::get('/rewards', [AdminController::class, 'rewards'])->name('rewards');
    Route::post('/rewards', [AdminController::class, 'storeReward'])->name('rewards.store');
    Route::post('/rewards/{reward}/update', [AdminController::class, 'updateReward'])->name('rewards.update');
    Route::post('/rewards/{reward}/delete', [AdminController::class, 'deleteReward'])->name('rewards.delete');
    
    // Opportunities
    Route::post('/opportunities', [AdminController::class, 'storeOpportunity'])->name('opportunities.store');
    Route::post('/opportunities/{opportunity}/delete', [AdminController::class, 'deleteOpportunity'])->name('opportunities.delete');
    Route::post('/opportunities/{opportunity}/toggle', [AdminController::class, 'toggleOpportunity'])->name('opportunities.toggle');
});

// Student Routes
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('profile.update');
    Route::post('/submit', [StudentController::class, 'submit'])->name('submit');
    Route::post('/ai-recommendation', [StudentController::class, 'askAI'])->name('ai.ask');
    Route::post('/opportunities/{opportunity}/apply', [StudentController::class, 'applyOpportunity'])->name('opportunities.apply');
    Route::get('/leaderboard', [StudentController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/rewards', [StudentController::class, 'rewards'])->name('rewards');
    Route::post('/rewards/{reward}/claim', [StudentController::class, 'claimReward'])->name('rewards.claim');
});

// Deployment helper route to run migrations directly from browser
Route::get('/vercel-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);
        return '<h3>Migration and Seeding Successful!</h3><br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h3>Migration Failed:</h3><br>' . $e->getMessage();
    }
});
