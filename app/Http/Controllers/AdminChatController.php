<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    /**
     * List all users with unread chat count
     */
   public function chatUsers()
{
    $adminId = auth()->id();

    // Get all normal users (not admin)
    $users = User::where('is_admin', 0)->get();

    foreach ($users as $u) {
        // Count unread messages from user → admin
        $u->unread = Chat::where('sender_id', $u->id)
            ->where('receiver_id', $adminId)
            ->where('is_read', 0)
            ->count();
    }

    return view('admin.chat_list', compact('users'));
}


    /**
     * Admin opens chat window with a user
     * → mark that user's unread messages as read
     */
    public function chatWindow($id)
    {
        $adminId = auth()->id();

        // Mark unread as read
        Chat::where('sender_id', $id)
            ->where('receiver_id', $adminId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $user = User::findOrFail($id);

        return view('admin.chat_window', compact('user'));
    }

    /**
     * Load chat messages
     */
    public function fetchAdminMessages($id)
    {
        $admin = Auth::user();
        $user = User::findOrFail($id);

        $messages = Chat::where(function($q) use ($user, $admin) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $admin->id);
            })
            ->orWhere(function($q) use ($user, $admin) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Admin sends a message
     */
    public function sendAdminMessage(Request $request, $id)
    {
        $chat = new Chat();
        $chat->sender_id = auth()->id();   // admin
        $chat->receiver_id = $id;
        $chat->message = $request->message;

        // FILE UPLOAD
        if ($request->hasFile('file')) {
    $path = $request->file('file')->store('chat_files', 'public');
    $chat->file = $path; // only store path like chat_files/abc.png
}

        $chat->is_read = 0; // mark unread for user
        $chat->save();

        return response()->json(['success' => true]);
    }
	
	 /**
     * Admin User List
     */
	public function adminUsersList()
{
    $users = User::where('is_admin', 0)->get();

    foreach ($users as $u) {
        $u->unread = Chat::where('sender_id', $u->id)
            ->where('receiver_id', auth()->id()) // admin
            ->where('is_read', 0)
            ->count();
    }

    return view('admin.users', compact('users'));
	}

}
