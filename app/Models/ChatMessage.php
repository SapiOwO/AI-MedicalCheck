<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id',
        'sender',
        'message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relationship: Message belongs to a ChatSession
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Scope: Only user messages
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender', 'user');
    }

    /**
     * Scope: Only bot messages
     */
    public function scopeFromBot($query)
    {
        return $query->where('sender', 'bot');
    }

    /**
     * Check if message is from user
     */
    public function isFromUser(): bool
    {
        return $this->sender === 'user';
    }

    /**
     * Check if message is from bot
     */
    public function isFromBot(): bool
    {
        return $this->sender === 'bot';
    }
}
