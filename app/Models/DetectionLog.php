<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectionLog extends Model
{
    protected $fillable = [
        'chat_session_id',
        'detection_type',
        'result',
        'confidence',
        'all_probabilities',
        'face_bbox',
        'image_path',
    ];

    protected $casts = [
        'all_probabilities' => 'array',
        'face_bbox' => 'array',
        'confidence' => 'float',
    ];

    /**
     * Relationship: Log belongs to a ChatSession
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Scope: Only emotion detections
     */
    public function scopeEmotion($query)
    {
        return $query->where('detection_type', 'emotion');
    }

    /**
     * Scope: Only fatigue detections
     */
    public function scopeFatigue($query)
    {
        return $query->where('detection_type', 'fatigue');
    }

    /**
     * Scope: Only pain detections
     */
    public function scopePain($query)
    {
        return $query->where('detection_type', 'pain');
    }
}
