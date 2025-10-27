<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'message',
        'context',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function scopeUserMessages($query)
    {
        return $query->where('sender_type', 'user');
    }

    public function scopeAiMessages($query)
    {
        return $query->where('sender_type', 'ai');
    }

    public function scopeWithContext($query, $context)
    {
        return $query->where('context', $context);
    }

    public function getIsUserAttribute()
    {
        return $this->sender_type === 'user';
    }

    public function getIsAiAttribute()
    {
        return $this->sender_type === 'ai';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }
}

