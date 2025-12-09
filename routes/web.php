<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicalCheckController;

/*
|--------------------------------------------------------------------------
| MediSight AI - Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page (New Homepage)
Route::get('/', function () {
    return view('medisight.landing');
})->name('home');

// Authentication Pages
Route::get('/login', function () {
    return view('medisight.login');
})->name('login');

Route::get('/register', function () {
    return view('medisight.register');
})->name('register');

// User Dashboard (requires login via JS)
Route::get('/dashboard', function () {
    return view('medisight.dashboard');
})->name('dashboard');

// Chat Page - Redirect to new session flow
Route::get('/chat', function () {
    return redirect('/session/camera');
})->name('chat');

// Password Reset Page
Route::get('/reset-password', function () {
    return view('medisight.reset-password');
})->name('reset-password');

// Detection History Page
Route::get('/history', function () {
    return view('medisight.history');
})->name('history');

// New Multi-Step Session Flow
Route::get('/session/start', function () {
    return view('medisight.session-start');
})->name('session.start');

Route::get('/session/camera', function () {
    return view('medisight.session-camera');
})->name('session.camera');

Route::get('/session/profile', function () {
    return view('medisight.session-profile');
})->name('session.profile');

/*
|--------------------------------------------------------------------------
| Legacy/Test Routes (kept for debugging)
|--------------------------------------------------------------------------
*/

// Health Check Route
Route::get('/health', [MedicalCheckController::class, 'status'])->name('health');

// Simple Test Page (for debugging)
Route::get('/test-simple', function () {
    return view('test-simple');
})->name('test-simple');

// Old Medical Check Routes (deprecated, kept for reference)
Route::get('/medical-check', [MedicalCheckController::class, 'index'])->name('medical-check');
Route::post('/medical-check/analyze', [MedicalCheckController::class, 'analyze'])->name('medical-check.analyze');
Route::get('/medical-check/status', [MedicalCheckController::class, 'status'])->name('medical-check.status');

