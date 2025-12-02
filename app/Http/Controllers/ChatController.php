<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * User opens chat page → mark unread as read
     */
    public function userChat()
    {
        Chat::where('receiver_id', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('user.chat');
    }

    /**
     * Fetch chat messages between user and admin
     */
    public function fetchMessages()
    {
        $user = Auth::user();
        $admin = User::where('is_admin', 1)->first();

        $messages = Chat::where(function ($q) use ($admin, $user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $admin->id);
            })
            ->orWhere(function ($q) use ($admin, $user) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * User sends message to admin
     */
    public function sendMessage(Request $request)
    {
        $chat = new Chat();
        $chat->sender_id = auth()->id();
        $chat->receiver_id = 1; // Admin ID
        $chat->message = $request->message;

        // File upload
       if ($request->hasFile('file')) {
    $path = $request->file('file')->store('chat_files', 'public');
    $chat->file = $path; // only store path like chat_files/abc.png
	}


        // Mark unread for admin
        $chat->is_read = 0;

        $chat->save();

        return response()->json(['success' => true]);
    }

    /**
     * Floating button unread badge count
     */
    public function unreadCount()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['count' => 0]);
        }

        $count = Chat::where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();

        // ❗ Do NOT mark messages as read here
        return response()->json(['count' => $count]);
    }
}
