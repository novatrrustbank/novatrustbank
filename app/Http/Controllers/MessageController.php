<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    /**
     * ==================================================
     * SEND MESSAGE  (Admin → User OR User → Admin)
     * ==================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'content'     => 'nullable|string',
            'file'        => 'nullable|file|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_uploads', 'public');
        }

        // Only use sender_id and receiver_id, not user_id
        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $request->content,
            'file_path'   => $filePath,
            'is_read'     => 0,
        ]);

        // Update online heartbeat
        Cache::put("last_seen_" . Auth::id(), Carbon::now()->timestamp, now()->addSeconds(30));

        return response()->json(['status' => 'ok']);
    }

    /**
     * ==================================================
     * FETCH MESSAGES (Live Reload / JSON)
     * ==================================================
     */
    public function fetchMessages($userId)
    {
        $authId = Auth::id();

        $messages = Message::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        $payload = $messages->map(function ($m) {
            return [
                'id'          => $m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => $m->sender ? $m->sender->name : 'User #' . $m->sender_id,
                'content'     => $m->content,
                'file_path'   => $m->file_path ? asset('storage/' . $m->file_path) : null,
                'is_read'     => $m->is_read,
                'created_at'  => $m->created_at->toDateTimeString(),
            ];
        });

        return response()->json($payload);
    }

    /**
     * ==================================================
     * TYPING INDICATOR (User → Admin OR Admin → User)
     * ==================================================
     */
    public function typing(Request $request)
    {
        $request->validate(['receiver_id' => 'required|integer']);
        $sender   = Auth::id();
        $receiver = (int) $request->receiver_id;

        Cache::put("typing_{$sender}_{$receiver}", true, now()->addSeconds(4));

        // Update online heartbeat
        Cache::put("last_seen_{$sender}", Carbon::now()->timestamp, now()->addSeconds(30));

        return response()->json(['ok' => true]);
    }

    public function typingStatus($userId)
    {
        $authId   = Auth::id();
        $isTyping = Cache::get("typing_{$userId}_{$authId}", false);

        return response()->json(['typing' => (bool) $isTyping]);
    }

    /**
     * ==================================================
     * MARK MESSAGE AS READ
     * ==================================================
     */
    public function markRead(Request $request)
    {
        $request->validate(['sender_id' => 'required|integer']);

        $sender = (int) $request->sender_id;
        $me     = Auth::id();

        Message::where('sender_id', $sender)
            ->where('receiver_id', $me)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['ok' => true]);
    }

    /**
     * ==================================================
     * ONLINE STATUS (Green Dot)
     * ==================================================
     */
    public function onlineStatus($userId)
    {
        $ts = Cache::get("last_seen_{$userId}", null);

        $online = false;
        if ($ts) {
            $online = (Carbon::now()->timestamp - (int)$ts) <= 30;
        }

        return response()->json(['online' => $online]);
    }

    /**
     * ==================================================
     * ADMIN LIST USERS
     * ==================================================
     */
    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $users = User::where('role', 'user')->get();
        return view('admin.chats', compact('users'));
    }

    /**
     * ==================================================
     * ADMIN OPEN CHAT WITH USER
     * ==================================================
     */
    public function adminChat($user_id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $admin_id = Auth::id();
        $user = User::findOrFail($user_id);

        $messages = Message::where(function ($q) use ($admin_id, $user_id) {
                $q->where('sender_id', $admin_id)
                  ->where('receiver_id', $user_id);
            })
            ->orWhere(function ($q) use ($admin_id, $user_id) {
                $q->where('sender_id', $user_id)
                  ->where('receiver_id', $admin_id);
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('admin.chat_window', compact('user', 'messages'));
    }

    /**
     * ==================================================
     * USER OPEN CHAT WITH ADMIN
     * ==================================================
     */
    public function userChat()
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $user_id = Auth::id();

        $messages = Message::where(function ($q) use ($user_id, $admin) {
                $q->where('sender_id', $user_id)
                  ->where('receiver_id', $admin->id);
            })
            ->orWhere(function ($q) use ($user_id, $admin) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', $user_id);
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('user.chat', compact('messages', 'admin'));
    }
	
	public function checkUnread()
{
    $count = Message::where('receiver_id', Auth::id())
                    ->where('is_read', 0)
                    ->count();

    return response()->json(['count' => $count]);
}

}
