<?php

namespace App\Console\Commands;

use App\Models\ChatSession;
use Illuminate\Console\Command;

class CleanupGuestSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sessions:cleanup-guests 
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Delete expired guest sessions and their related data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for expired guest sessions...');
        
        $expiredCount = ChatSession::expired()->count();
        
        if ($expiredCount === 0) {
            $this->info('No expired guest sessions found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredCount} expired guest session(s).");

        if ($this->option('dry-run')) {
            $this->warn('Dry run mode - no data was deleted.');
            
            // Show what would be deleted
            $expired = ChatSession::expired()->with('messages')->get();
            foreach ($expired as $session) {
                $this->line("  - Session #{$session->id}: {$session->messages->count()} messages, expired at {$session->expires_at}");
            }
            
            return Command::SUCCESS;
        }

        // Perform cleanup
        $deleted = ChatSession::cleanupExpiredGuests();
        
        $this->info("Successfully deleted {$deleted} expired guest session(s) and their related data.");
        
        return Command::SUCCESS;
    }
}
