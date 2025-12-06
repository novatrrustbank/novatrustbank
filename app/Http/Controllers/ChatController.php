<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TelegramHelper; 

class ChatController extends Controller
{
    /**
     * User opens chat page ? mark unread as read
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

    $filePath = null;
    $fileExtension = null;

    // File upload
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('chat_files', 'public');
        $chat->file = $path;

        $filePath = storage_path('app/public/' . $path);
        $fileExtension = strtolower($request->file('file')->getClientOriginalExtension());
    }

    // Mark unread for admin
    $chat->is_read = 0;

    $chat->save();

    // ========== TELEGRAM NOTIFICATION ========== //
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');

    // Detect if it's an image
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if ($filePath && file_exists($filePath)) {

        $caption =
            "?? <b>New Chat Message</b>\n" .
            "?? From: " . auth()->user()->name . "\n" .
            "?? Email: " . auth()->user()->email . "\n" .
            "?? File Attached\n" .
            "?? " . now()->toDateTimeString() . "\n" .
            "?? novatrustbank.onrender.com";

        if (in_array($fileExtension, $imageExtensions)) {
            // ========== SEND PHOTO PREVIEW ==========
            \Illuminate\Support\Facades\Http::attach(
                'photo',
                file_get_contents($filePath),
                basename($filePath)
            )->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
        } else {
            // ========== SEND NORMAL FILE ==========
            \Illuminate\Support\Facades\Http::attach(
                'document',
                file_get_contents($filePath),
                basename($filePath)
            )->post("https://api.telegram.org/bot{$token}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
        }

    } else {
        // No file ? normal message
        TelegramHelper::send(
            "?? <b>New Chat Message</b>\n" .
            "?? From: " . auth()->user()->name . "\n" .
            "?? Email: " . auth()->user()->email . "\n" .
            "?? Message: " . ($request->message ?: '?? File Attached') . "\n" .
            "?? " . now()->toDateTimeString() . "\n" .
            "?? novatrustbank.onrender.com"
        );
    }

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

        return response()->json(['count' => $count]);
    }
}
