@extends('layouts.app')

@section('title', 'Messages - NovaTrust Bank')

@section('content')
<div class="container">
    <h2>Your Messages</h2>

    @if($messages->count() == 0)
        <div style="padding:12px;background:#fff3cd;border-radius:6px;">You have no messages.</div>
    @else
        <ul style="list-style:none;padding:0;">
            @foreach($messages as $m)
                <li style="border-bottom:1px solid #eee;padding:12px 0;">
                    <a href="{{ route('messages.show', $m->id) }}" style="text-decoration:none;color:inherit;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <strong>{{ $m->subject ?: 'No subject' }}</strong><br>
                                <small>From: {{ $m->sender ? $m->sender->name : 'NovaTrust Support' }} — {{ $m->created_at->diffForHumans() }}</small>
                            </div>
                            <div style="text-align:right;">
                                @if(!$m->is_read)
                                    <span style="background:#00b300;color:#fff;padding:6px;border-radius:20px;font-size:12px;">New</span>
                                @endif
                            </div>
                        </div>
                        <div style="color:#555;margin-top:6px;">{{ \Str::limit($m->body, 150) }}</div>
                    </a>
                </li>
            @endforeach
        </ul>

        <div style="margin-top:16px;">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection