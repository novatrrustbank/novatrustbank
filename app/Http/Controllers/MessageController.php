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

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $request->content,
            'file_path'   => $filePath,
            'is_read'     => 0,
            'read_at'     => null,
        ]);

        // Mark sender as active
        Cache::put("last_seen_" . Auth::id(), now()->timestamp, 30);

        return response()->json(['status' => 'sent']);
    }



    /**
     * ==================================================
     * FETCH MESSAGES (Live Reload / JSON)
     * ==================================================
     */
    public function fetchMessages($userId)
    {
        $authId = Auth::id();
        $lastId = request('last_id', 0);

        $messages = Message::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $authId);
            })
            ->where('id', '>', $lastId)
            ->orderBy('id', 'ASC')
            ->get();

        // Convert to JSON format UI needs
        $payload = $messages->map(function ($m) {
            return [
                'id'          => $m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => $m->sender ? $m->sender->name : 'User ' . $m->sender_id,
                'content'     => $m->content,
                'file_path'   => $m->file_path ? asset('storage/'.$m->file_path) : null,
                'is_read'     => $m->is_read,
                'created_at'  => $m->created_at->toDateTimeString(),
            ];
        });

        return response()->json($payload);
    }



    /**
     * ==================================================
     * TYPING INDICATOR
     * ==================================================
     */
    public function typing(Request $request)
    {
        $request->validate(['receiver_id' => 'required|integer']);

        $sender   = Auth::id();
        $receiver = $request->receiver_id;

        Cache::put("typing_{$sender}_{$receiver}", true, 4);

        Cache::put("last_seen_{$sender}", now()->timestamp, 30);

        return response()->json(['ok' => true]);
    }

    public function typingStatus($userId)
    {
        $authId   = Auth::id();
        $isTyping = Cache::get("typing_{$userId}_{$authId}", false);

        return response()->json(['typing' => $isTyping]);
    }



    /**
     * ==================================================
     * MARK READ
     * ==================================================
     */
    public function markRead(Request $request)
    {
        $request->validate(['sender_id' => 'required|integer']);

        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        return response()->json(['ok' => true]);
    }



    /**
     * ==================================================
     * ONLINE STATUS
     * ==================================================
     */
    public function onlineStatus($userId)
    {
        $ts = Cache::get("last_seen_{$userId}", null);

        $online = $ts && (now()->timestamp - $ts <= 30);

        return response()->json(['online' => $online]);
    }



    /**
     * ==================================================
     * ADMIN LIST USERS
     * ==================================================
     */
    public function adminIndex()
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $users = User::where('role', 'user')->get();
        return view('admin.chats', compact('users'));
    }



    /**
     * ==================================================
     * ADMIN OPEN CHAT WINDOW
     * ==================================================
     */
    public function adminChat($user_id)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $admin_id = Auth::id();
        $user     = User::findOrFail($user_id);

        $messages = Message::where(function ($q) use ($admin_id, $user_id) {
                $q->where('sender_id', $admin_id)->where('receiver_id', $user_id);
            })
            ->orWhere(function ($q) use ($admin_id, $user_id) {
                $q->where('sender_id', $user_id)->where('receiver_id', $admin_id);
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('admin.chat_window', compact('user', 'messages'));
    }



    /**
     * ==================================================
     * USER OPENS CHAT WITH ADMIN
     * ==================================================
     */
    public function userChat()
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $user  = Auth::id();

        // Mark admin→user messages read
        Message::where('receiver_id', $user)
            ->where('sender_id', $admin->id)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        return view('user.chat', ['admin' => $admin]);
    }



    /**
     * ==================================================
     * CHECK UNREAD COUNT (Float Widget Badge)
     * ==================================================
     */
    public function checkUnread()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', 0)
            ->count();

        return response()->json(['unread' => $count]);
    }



    /* ============================================================
     *   WIDGET CHAT (LOAD / SEND / FETCH)
     * ============================================================
     */

    public function widgetLoad()
    {
        $authId = Auth::id();
        $admin  = User::where('role', 'admin')->firstOrFail();

        $partnerId = ($authId == $admin->id) ? request('user_id') : $admin->id;

        Message::where('receiver_id', $authId)
            ->where('sender_id', $partnerId)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        return $this->widgetFetch(new Request(['last_id' => 0, 'receiver_id' => $partnerId]));
    }


    public function widgetSend(Request $request)
    {
        $authId = Auth::id();
        $admin  = User::where('role', 'admin')->firstOrFail();
        $partnerId = ($authId == $admin->id) ? $request->receiver_id : $admin->id;

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        Message::create([
            'sender_id'   => $authId,
            'receiver_id' => $partnerId,
            'content'     => $request->message,
            'file_path'   => $filePath,
            'is_read'     => 0,
        ]);

        return response()->json(['status' => 'ok']);
    }


    public function widgetFetch(Request $request)
    {
        $authId = Auth::id();
        $admin  = User::where('role', 'admin')->firstOrFail();
        $partnerId = ($authId == $admin->id) ? $request->receiver_id : $admin->id;
        $lastId = $request->input('last_id', 0);

        $messages = Message::where(function ($q) use ($authId, $partnerId) {
                $q->where('sender_id', $authId)->where('receiver_id', $partnerId);
            })
            ->orWhere(function ($q) use ($authId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $authId);
            })
            ->where('id', '>', $lastId)
            ->orderBy('id', 'ASC')
            ->get();

        return response()->json($messages);
    }
}