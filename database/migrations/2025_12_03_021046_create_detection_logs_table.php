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
        Schema::create('detection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('detection_type', ['emotion', 'fatigue', 'pain']);
            $table->string('result'); // The detected class
            $table->float('confidence');
            $table->json('all_probabilities');
            $table->json('face_bbox')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('chat_session_id');
            $table->index('detection_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detection_logs');
    }
};
