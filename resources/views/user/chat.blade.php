@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Chat with Admin</h3>
        <div>
            <span id="onlineBadge" class="badge bg-secondary">Checking…</span>
            <small id="typingIndicator" class="text-muted ms-3" style="display:none">Typing...</small>
        </div>
    </div>

    <div id="chatBox" class="border rounded p-3 mb-3"
         style="height:420px; overflow-y:auto; background:#f1f3f5;">
        @foreach($messages as $message)
            <div class="{{ $message->sender_id == Auth::id() ? 'text-end' : 'text-start' }} mb-3">
                <small class="text-muted d-block">
                    {{ $message->sender->name ?? 'User #' . $message->sender_id }} -
                    {{ $message->created_at->format('Y-m-d H:i') }}
                </small>
                @if($message->content)
                    <div class="d-inline-block p-2 rounded"
                         style="max-width:70%; background:{{ $message->sender_id == Auth::id() ? '#d0ebff' : '#e9ecef' }};">
                        {{ $message->content }}
                    </div>
                @endif
                @if($message->file_path)
                    <div>?? <a href="{{ asset('storage/'.$message->file_path) }}" target="_blank">Attachment</a></div>
                @endif
            </div>
        @endforeach
    </div>

    <form id="chatForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="receiver_id" value="{{ $admin->id }}">
        <div class="input-group">
            <input type="text" id="content" class="form-control" placeholder="Type a message..." autocomplete="off">
            <input type="file" id="file" class="form-control">
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>

<script>
(() => {
    const chatBox = document.getElementById('chatBox');
    const form = document.getElementById('chatForm');
    const content = document.getElementById('content');
    const file = document.getElementById('file');
    const receiverId = document.getElementById('receiver_id').value;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const typingIndicator = document.getElementById('typingIndicator');
    const onlineBadge = document.getElementById('onlineBadge');

    let lastId = 0;
    let typingTimeout;

    const urls = {
        fetch: `/chat/messages/${receiverId}?last_id=`,
        send: "{{ route('user.chat.send') }}",
        typing: "{{ route('chat.typing') }}",
        typingCheck: `/chat/typing/${receiverId}`,
        onlineStatus: `/chat/online/${receiverId}`,
        markRead: `/chat/mark-read`
    };

    function appendMessage(msg) {
        const wrapper = document.createElement('div');
        wrapper.className = (msg.sender_id == {{ Auth::id() }} ? "text-end mb-3" : "text-start mb-3");

        let html = `<small class="text-muted d-block">${msg.sender_name} - ${new Date(msg.created_at).toLocaleString()}</small>`;

        if (msg.content) {
            html += `<div class="d-inline-block p-2 rounded" style="max-width:70%; background:${msg.sender_id == {{ Auth::id() }} ? '#d0ebff' : '#e9ecef'}">${msg.content}</div>`;
        }

        if (msg.file_path) {
            html += `<div>?? <a href="${msg.file_path}" target="_blank">Attachment</a></div>`;
        }

        wrapper.innerHTML = html;
        chatBox.appendChild(wrapper);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function fetchMessages() {
        try {
            const res = await fetch(urls.fetch + lastId, { credentials: 'same-origin' });
            if (!res.ok) return;
            const messages = await res.json();
            if (!Array.isArray(messages) || messages.length === 0) return;

            messages.forEach(msg => {
                appendMessage(msg);
                lastId = msg.id;
            });
        } catch (err) {
            console.error('Fetch messages error:', err);
        }
    }

    async function sendMessage(formData) {
        try {
            const res = await fetch(urls.send, {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': csrf },
                body: formData,
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (data.status === 'ok') {
                appendMessage({
                    id: ++lastId,
                    sender_id: {{ Auth::id() }},
                    sender_name: "{{ Auth::user()->name }}",
                    content: content.value,
                    file_path: file.files.length ? URL.createObjectURL(file.files[0]) : null,
                    created_at: new Date().toISOString()
                });
                content.value = '';
                file.value = '';
            }
        } catch (err) {
            console.error('Send message error:', err);
        }
    }

    async function sendTyping() {
        try {
            await fetch(urls.typing, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
                body: JSON.stringify({ receiver_id: receiverId })
            });
        } catch {}
    }

    content.addEventListener('input', () => {
        clearTimeout(typingTimeout);
        sendTyping();
        typingTimeout = setTimeout(() => {}, 1500);
    });

    async function checkTyping() {
        try {
            const res = await fetch(urls.typingCheck, { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            typingIndicator.style.display = data.typing ? 'inline' : 'none';
        } catch {}
    }

    async function checkOnline() {
        try {
            const res = await fetch(urls.onlineStatus, { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            onlineBadge.className = data.online ? 'badge bg-success' : 'badge bg-secondary';
            onlineBadge.textContent = data.online ? 'Online' : 'Offline';
        } catch {}
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('content', content.value);
        if (file.files.length > 0) formData.append('file', file.files[0]);
        sendMessage(formData);
    });

    // Initial fetch & polling
    fetchMessages();
    setInterval(fetchMessages, 1000);
    setInterval(checkTyping, 1000);
    setInterval(checkOnline, 5000);

})();
</script>

@endsection
