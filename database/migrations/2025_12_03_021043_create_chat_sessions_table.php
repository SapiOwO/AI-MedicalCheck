<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_token')->unique();
            
            // Detection results
            $table->string('initial_emotion')->nullable();
            $table->string('initial_fatigue')->nullable();
            $table->string('initial_pain')->nullable();
            
            // Confidence scores
            $table->float('emotion_confidence')->nullable();
            $table->float('fatigue_confidence')->nullable();
            $table->float('pain_confidence')->nullable();
            
            // Probability distributions (JSON)
            $table->json('emotion_probabilities')->nullable();
            $table->json('fatigue_probabilities')->nullable();
            $table->json('pain_probabilities')->nullable();
            
            // Face detection data
            $table->json('face_bbox')->nullable();
            $table->string('face_image_path')->nullable();
            
            // Session status
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            
            // Timestamps
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('session_token');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
