@foreach($messages as $msg)
    <div class="{{ $msg->sender == 'admin' ? 'text-end' : 'text-start' }}">
        <p class="p-2 rounded mb-1 {{ $msg->sender == 'admin' ? 'bg-primary text-white' : 'bg-light' }}">
            {{ $msg->message }}
        </p>
        <small class="text-muted">{{ $msg->created_at->format('h:i A') }}</small>
    </div>
@endforeach
