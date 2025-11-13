@extends('layouts.app')

@section('title', $message->subject ?: 'Message')

@section('content')
<div class="container">
    <h2>{{ $message->subject ?: 'Message from NovaTrust' }}</h2>
    <p><small>From: {{ $message->sender ? $message->sender->name : 'NovaTrust Support' }} — {{ $message->created_at->format('F j, Y g:ia') }}</small></p>

    <div style="background:#fff;padding:16px;border-radius:8px;border:1px solid #eee;">
        {!! nl2br(e($message->body)) !!}
    </div>

    <div style="margin-top:12px;">
        <a href="{{ route('messages.inbox') }}" style="text-decoration:none;color:#1a237e;">← Back to inbox</a>
    </div>
</div> 
@endsection