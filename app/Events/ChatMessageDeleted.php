<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $room_id;

    public function __construct(int $id, int $roomId = 0)
    {
        $this->id = $id;
        $this->room_id = $roomId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.global');
    }
}


