<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
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
    ];

    protected $casts = [
        'emotion_probabilities' => 'array',
        'fatigue_probabilities' => 'array',
        'pain_probabilities' => 'array',
        'face_bbox' => 'array',
        'emotion_confidence' => 'float',
        'fatigue_confidence' => 'float',
        'pain_confidence' => 'float',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Boot method to generate session token automatically
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::random(32);
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
     * Scope: Guest sessions (no user_id)
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Check if session is guest
     */
    public function isGuest(): bool
    {
        return is_null($this->user_id);
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
}
