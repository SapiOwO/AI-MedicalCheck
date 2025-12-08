<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration creates a clean, simplified schema for MediSight AI
     */
    public function up(): void
    {
        // Drop old tables that are not needed
        Schema::dropIfExists('detection_logs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('password_reset_tokens');

        // Modify chat_sessions to have flexible AI data
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Patient profile data (can be from AI or manual input)
            $table->integer('age')->nullable()->after('session_token');
            $table->string('gender', 20)->nullable()->after('age');
            
            // AI Detection Results - flexible JSON for any model
            // Format: {"emotion": {"value": "sad", "confidence": 0.85}, "pain": {...}}
            $table->json('ai_detection_data')->nullable()->after('gender');
            
            // Symptom tracking from chatbot Q&A
            // Format: {"fever": true, "cough": false, "headache": null}
            $table->json('symptom_data')->nullable()->after('ai_detection_data');
            
            // Image path from camera capture
            $table->string('captured_image_path', 500)->nullable()->after('symptom_data');
            
            // Session step tracking
            $table->enum('current_step', ['camera', 'profile', 'chat', 'completed'])->default('camera')->after('captured_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender', 'ai_detection_data', 'symptom_data', 'captured_image_path', 'current_step']);
        });

        // Recreate dropped tables if needed (optional)
    }
};
