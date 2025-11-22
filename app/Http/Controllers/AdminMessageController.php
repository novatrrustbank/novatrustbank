<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AdminMessageController extends Controller
{
    // show admin send-message page (list users + form)
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.messages', compact('users'));
    }

    // store/send message to a user
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|max:4000',
        ]);

        $msg = Message::create([
            'user_id'   => $request->user_id,
            'sender_id' => Auth::id(),
            'subject'   => $request->subject,
            'content'      => $request->body,
            'is_read'   => false,
        ]);

        // optionally: you can notify via email/sms here

        return redirect()->back()->with('success', 'Message sent to user successfully.');
    }

    // view message details and optionally mark read for admin to see (optional)
    public function show($id)
    {
        $message = Message::findOrFail($id);
        return view('admin.message_show', compact('message'));
    }
} 
