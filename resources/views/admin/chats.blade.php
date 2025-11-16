@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h3 class="mb-4">All Users</h3>

    @if($users->count() == 0)
        <p class="text-muted">No users available.</p>
    @else
        <div class="list-group">
            @foreach($users as $user)
                <a href="{{ route('admin.chat.window', $user->id) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">

                    <div>
                        <strong>{{ $user->name }}</strong>
                        <div class="text-muted">{{ $user->email }}</div>
                    </div>

                    <span class="badge bg-primary rounded-pill">Open Chat</span>

                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
