@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Users Messaging Support</h3>

    <ul class="list-group">
        @foreach($users as $u)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    {{ $u->name }}

                    @if($u->unread > 0)
                        <span class="badge bg-danger ms-2">
                            {{ $u->unread }} new
                        </span>
                    @endif
                </div>

                <a href="{{ route('admin.chat.open', $u->id) }}" class="btn btn-primary btn-sm">
                    Open Chat
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
