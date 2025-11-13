@extends('layouts.app')

@section('title', 'Send Message - Admin')

@section('content')
<div class="container">
    <h2>Send message to user</h2>

    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.messages.store') }}">
        @csrf
        <div style="margin-bottom:10px;">
            <label for="user_id"><strong>Select user</strong></label>
            <select name="user_id" id="user_id" required style="width:100%;padding:8px;border-radius:6px;">
                <option value="">-- Select user --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Subject</strong></label>
            <input name="subject" type="text" maxlength="255" style="width:100%;padding:8px;border-radius:6px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Message</strong></label>
            <textarea name="body" rows="6" required style="width:100%;padding:8px;border-radius:6px;"></textarea>
        </div>

        <button type="submit" style="background:#1a237e;color:#fff;padding:10px 16px;border-radius:6px;border:none;">Send Message</button>
    </form>

    <hr style="margin:20px 0;">

    <h3>Recent messages</h3>
    @php $recent = \App\Models\Message::latest()->take(10)->get(); @endphp
    <ul>
        @foreach($recent as $msg)
            <li>
                <strong>{{ $msg->user->name }}</strong> — {{ \Str::limit($msg->body, 80) }} 
                <a href="{{ route('admin.messages.show', $msg->id) }}">View</a>
            </li>
        @endforeach
    </ul>
</div> 
@endsection