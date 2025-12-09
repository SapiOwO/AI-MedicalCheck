<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\DetectionController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\PasswordController;
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

// Password Reset (No auth required - uses old password verification)
Route::post('/password/reset', [PasswordController::class, 'reset']);

// Protected Routes (Require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Password Change (for logged-in users)
    Route::post('/password/change', [PasswordController::class, 'change']);
    
    // Chat Sessions (Authenticated users)
    Route::get('/chat/sessions', [ChatSessionController::class, 'index']);
    Route::get('/chat/session/{id}', [ChatSessionController::class, 'show']);
    Route::post('/chat/session/{id}/end', [ChatSessionController::class, 'end']);
    Route::delete('/chat/session/{id}', [ChatSessionController::class, 'destroy']);
    
    // Session Start and Update (inside auth so user_id is captured)
    Route::post('/chat/session/start', [ChatSessionController::class, 'start']);
    Route::post('/session/update', [ChatSessionController::class, 'updateProfile']);
    
    // Detection History
    Route::get('/detection/history', [DetectionController::class, 'history']);
    
    // Export (Authenticated users)
    Route::get('/export/chat/{sessionId}/pdf', [ExportController::class, 'exportPDF']);
    Route::get('/export/chat/{sessionId}/csv', [ExportController::class, 'exportCSV']);
    Route::get('/export/history/csv', [ExportController::class, 'exportHistoryCSV']);
});

// Guest-Friendly Routes (No auth required)
Route::post('/detect/multi', [DetectionController::class, 'multiDetect']);
Route::post('/detect/emotion', [DetectionController::class, 'detectEmotion']);
Route::post('/detect/fatigue', [DetectionController::class, 'detectFatigue']);
Route::post('/detect/pain', [DetectionController::class, 'detectPain']);

// Chat messages (Works for both authenticated and guest via session_token)
Route::post('/chat/message', [ChatMessageController::class, 'send']);
Route::get('/chat/session/{sessionId}/messages', [ChatMessageController::class, 'index']);

// Guest session routes (for users without login)
Route::post('/guest/session/start', [ChatSessionController::class, 'start']);
Route::post('/guest/session/update', [ChatSessionController::class, 'updateProfile']);

