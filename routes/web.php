<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicalCheckController;

Route::get('/', function () {
    return view('welcome');
});

// Health Check Route (accessible at port 8000)
Route::get('/health', [MedicalCheckController::class, 'status'])->name('health');

// Simple Test Page (for debugging)
Route::get('/test-simple', function () {
    return view('test-simple');
})->name('test-simple');

// Medical Check Routes
Route::get('/medical-check', [MedicalCheckController::class, 'index'])->name('medical-check');
Route::post('/medical-check/analyze', [MedicalCheckController::class, 'analyze'])->name('medical-check.analyze');
Route::get('/medical-check/status', [MedicalCheckController::class, 'status'])->name('medical-check.status');
