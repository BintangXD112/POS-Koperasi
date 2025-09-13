<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = [
            'room_id' => $message->room_id,
            'id' => $message->id,
            'user' => $message->user?->name ?? 'Unknown',
            'role' => $message->user?->role?->display_name ?? null,
            'content' => $message->content,
            'attachment_url' => $message->attachment_path ? asset('storage/'.$message->attachment_path) : null,
            'attachment_type' => $message->attachment_type,
            'has_image' => $message->attachment_type && str_starts_with($message->attachment_type, 'image/'),
            'attachment_label' => $message->attachment_type ? (str_starts_with($message->attachment_type, 'image/') ? 'Mengirim gambar' : 'Mengirim lampiran') : null,
            'time' => optional($message->created_at)->format('H:i'),
        ];
    }

    public function broadcastOn(): Channel
    {
        // Public channel for global chat
        return new Channel('chat.global');
    }
}


