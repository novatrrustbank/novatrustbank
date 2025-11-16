<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // show user inbox
    public function inbox()
    {
        $user = Auth::user();
        $messages = $user->messages()->latest()->paginate(20);
        return view('messages.inbox', compact('messages'));
    }

    // show single message and mark it read
    public function show($id)
    {
        $message = Message::where('user_id', Auth::id())->findOrFail($id);

        if (!$message->is_read) {
            $message->is_read = true;
            $message->save();
        }

        return view('messages.show', compact('message'));
    }
}