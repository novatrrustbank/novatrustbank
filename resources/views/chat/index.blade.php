@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="row">
        <!-- Users List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Users
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($users as $user)
                        <li class="list-group-item">
                            <a href="{{ route('messages.chat', $user->id) }}">
                                {{ $user->name }}
                                @if($user->unread > 0)
                                    <span class="badge bg-danger float-end">{{ $user->unread }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8">
            @if(isset($receiver))
            <div class="card">
                <div class="card-header bg-success text-white">
                    Chat with {{ $receiver->name }}
                </div>

                <div class="card-body" style="height: 350px; overflow-y: auto;">
                    @foreach($messages as $msg)
                        <div class="mb-3 {{ $msg->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">

                            <div class="p-2 rounded {{ $msg->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                {{ $msg->content }}

                                @if($msg->file_path)
                                    <br>
                                    <a href="{{ asset('storage/' . $msg->file_path) }}" target="_blank">
                                        📄 Download Attachment
                                    </a>
                                @endif
                            </div>

                            <small class="text-muted">
                                {{ $msg->created_at->diffForHumans() }}
                            </small>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    <form action="{{ route('messages.send') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">

                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Type a message...">

                            <input type="file" name="file" class="form-control">

                            <button class="btn btn-success">Send</button>
                        </div>
                    </form>
                </div>

            </div>
            @else
                <div class="text-center mt-5">
                    <h4>Select a user to start chatting</h4>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
