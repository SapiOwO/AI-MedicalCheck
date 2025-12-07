<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add guest session management fields.
     * - is_guest: marks sessions without user login
     * - expires_at: when guest session data should be deleted
     */
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Guest session indicator
            $table->boolean('is_guest')->default(false)->after('user_id');
            
            // Expiration time for guest sessions (24 hours by default)
            $table->timestamp('expires_at')->nullable()->after('ended_at');
            
            // Index for cleanup queries
            $table->index(['is_guest', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropIndex(['is_guest', 'expires_at']);
            $table->dropColumn(['is_guest', 'expires_at']);
        });
    }
};
