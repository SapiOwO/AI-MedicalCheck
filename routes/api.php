<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\DetectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/health', [DetectionController::class, 'checkHealth']);

// Authentication Routes (No auth required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Chat Sessions (Authenticated users)
    Route::get('/chat/sessions', [ChatSessionController::class, 'index']);
    Route::get('/chat/session/{id}', [ChatSessionController::class, 'show']);
    Route::post('/chat/session/{id}/end', [ChatSessionController::class, 'end']);
});

// Guest-Friendly Routes (Optional auth)
Route::post('/detect/multi', [DetectionController::class, 'multiDetect']);
Route::post('/detect/emotion', [DetectionController::class, 'detectEmotion']);
Route::post('/detect/fatigue', [DetectionController::class, 'detectFatigue']);
Route::post('/detect/pain', [DetectionController::class, 'detectPain']);

// Chat (Works for both authenticated and guest)
Route::post('/chat/session/start', [ChatSessionController::class, 'start']);
Route::post('/chat/message', [ChatMessageController::class, 'send']);
Route::get('/chat/session/{sessionId}/messages', [ChatMessageController::class, 'index']);
