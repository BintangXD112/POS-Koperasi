<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Events\ChatMessageCreated;
use App\Events\ChatMessageDeleted;
use App\Events\ChatCleared;

class ChatController extends Controller
{
    public function index()
    {
        // Ensure a single global room exists
        $room = ChatRoom::firstOrCreate(
            ['is_global' => true],
            ['name' => 'All Users', 'created_by' => auth()->id()]
        );

        $messages = ChatMessage::with(['user.role'])
            ->where('room_id', $room->id)
            ->orderBy('created_at', 'asc')
            ->take(100)
            ->get();

        return view('chat.index', compact('room', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf',
        ]);

        $room = ChatRoom::where('is_global', true)->firstOrFail();

        $data = [
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content', ''),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat', 'public');
            $data['attachment_path'] = $path;
            $data['attachment_type'] = $file->getClientMimeType();
            $data['attachment_size'] = $file->getSize();
        }

        if (empty($data['content']) && empty($data['attachment_path'])) {
            return back();
        }

        $message = ChatMessage::create($data);
        // touch room to signal updates for polling clients
        $room->touch();
        // broadcast creation
        broadcast(new ChatMessageCreated($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('chat.index');
    }

    public function latest(Request $request)
    {
        $since = $request->integer('since', 0);
        $snapshot = $request->boolean('snapshot', false);
        $room = ChatRoom::where('is_global', true)->firstOrFail();

        $query = ChatMessage::with(['user.role'])
            ->where('room_id', $room->id)
            ->orderBy('id', 'asc');

        if ($snapshot) {
            // return a compact snapshot of latest messages for reconciliation
            $query->take(100);
        } elseif ($since > 0) {
            $query->where('id', '>', $since);
        } else {
            // when initial load for polling, send the latest snapshot
            $query->take(50);
        }

        $messages = $query->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'user' => $m->user?->name ?? 'Unknown',
                'role' => $m->user?->role?->display_name ?? null,
                'content' => $m->content,
                'attachment_url' => $m->attachment_path ? asset('storage/'.$m->attachment_path) : null,
                'attachment_type' => $m->attachment_type,
                'time' => $m->created_at?->format('H:i'),
            ];
        });

        return response()->json([
            'messages' => $messages,
            'room_updated_at' => optional($room->updated_at)->timestamp,
        ]);
    }

    public function clear(Request $request)
    {
        // Only admin users allowed
        abort_unless(auth()->user() && auth()->user()->isAdmin(), 403);
        $room = ChatRoom::where('is_global', true)->firstOrFail();
        ChatMessage::where('room_id', $room->id)->delete();
        $room->touch();
        broadcast(new ChatCleared())->toOthers();
        return back()->with('success', 'Chat berhasil dibersihkan');
    }

    public function delete(ChatMessage $message)
    {
        abort_unless(auth()->user() && auth()->user()->isAdmin(), 403);
        $message->delete();
        // Signal update
        $message->room?->touch();
        broadcast(new ChatMessageDeleted($message->id, (int) $message->room_id))->toOthers();
        return response()->json(['ok' => true, 'id' => $message->id]);
    }
}


