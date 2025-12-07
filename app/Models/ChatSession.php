<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChatSession extends Model
{
    // Guest sessions expire after 24 hours
    public const GUEST_EXPIRATION_HOURS = 24;

    protected $fillable = [
        'user_id',
        'is_guest',
        'session_token',
        'initial_emotion',
        'initial_fatigue',
        'initial_pain',
        'emotion_confidence',
        'fatigue_confidence',
        'pain_confidence',
        'emotion_probabilities',
        'fatigue_probabilities',
        'pain_probabilities',
        'face_bbox',
        'face_image_path',
        'status',
        'started_at',
        'ended_at',
        'expires_at',
    ];

    protected $casts = [
        'emotion_probabilities' => 'array',
        'fatigue_probabilities' => 'array',
        'pain_probabilities' => 'array',
        'face_bbox' => 'array',
        'emotion_confidence' => 'float',
        'fatigue_confidence' => 'float',
        'pain_confidence' => 'float',
        'is_guest' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($session) {
            // Generate session token
            if (empty($session->session_token)) {
                $session->session_token = Str::random(32);
            }
            
            // Set guest flag and expiration
            if (is_null($session->user_id)) {
                $session->is_guest = true;
                $session->expires_at = Carbon::now()->addHours(self::GUEST_EXPIRATION_HOURS);
            }
        });
    }

    /**
     * Relationship: Session belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Session has many ChatMessages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Relationship: Session has many DetectionLogs
     */
    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class);
    }

    /**
     * Scope: Only active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Guest sessions only
     */
    public function scopeGuest($query)
    {
        return $query->where('is_guest', true);
    }

    /**
     * Scope: Logged-in user sessions only
     */
    public function scopeRegistered($query)
    {
        return $query->where('is_guest', false);
    }

    /**
     * Scope: Expired guest sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('is_guest', true)
                     ->where('expires_at', '<', Carbon::now());
    }

    /**
     * Scope: Not expired sessions
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('is_guest', false)
              ->orWhere('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Check if session is guest
     */
    public function isGuest(): bool
    {
        return $this->is_guest || is_null($this->user_id);
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        if (!$this->is_guest) {
            return false; // Registered user sessions never expire
        }
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }

    /**
     * Mark session as completed
     */
    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }

    /**
     * Delete all expired guest sessions
     */
    public static function cleanupExpiredGuests(): int
    {
        $expired = self::expired()->get();
        $count = $expired->count();

        foreach ($expired as $session) {
            // Delete related messages first
            $session->messages()->delete();
            $session->detectionLogs()->delete();
            $session->delete();
        }

        return $count;
    }
}

