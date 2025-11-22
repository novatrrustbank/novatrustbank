@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Chat with {{ $user->name }}</h3>

        <div>
            <span id="onlineBadge" class="badge bg-secondary">Checking…</span>
            <small id="typingIndicator" class="text-muted ms-3" style="display:none">Typing...</small>
        </div>
    </div>

    <div id="chatBox" class="border rounded p-3 mb-3"
         style="height:420px; overflow-y:auto; background:#f1f3f5;">
    </div>

    <form id="chatForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="receiver_id" value="{{ $user->id }}">

        <div class="input-group">
            <input type="text" id="content" class="form-control" placeholder="Type a message..." autocomplete="off">
            <input type="file" id="file" class="form-control">
            <button class="btn btn-primary" id="sendBtn" type="submit">Send</button>
        </div>
    </form>
</div>


<script>
(() => {

    const authId = {{ Auth::id() }};
    const otherId = {{ $user->id }};
    const chatBox = document.getElementById('chatBox');
    const content = document.getElementById('content');
    const file = document.getElementById('file');

    const urls = {

        // --- Fetch + last_id
        fetch: "/admin/chat/" + otherId + "/refresh?last_id=",

        // --- Send message
        send: "{{ route('admin.chat.send') }}",

        // --- Typing system
        typing: "{{ route('chat.typing') }}",

        typingCheck: "{{ route('chat.typing.status', ['userId' => $user->id]) }}",

        // --- Mark messages read
        markRead: "{{ route('chat.mark.read') }}",

        // --- Online check
        online: "{{ route('chat.online.status', ['userId' => $user->id]) }}"
    };

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let lastId = 0;
    let typingTimeout;

    /* ------------------- MESSAGE RENDER ------------------- */

    function appendMessage(m) {
        const wrapper = document.createElement('div');
        wrapper.className = (m.sender_id == authId) ? "text-end mb-3" : "text-start mb-3";

        let html = `<small class="text-muted d-block">
                        ${m.sender_name} - ${new Date(m.created_at).toLocaleString()}
                    </small>`;

        if (m.content) {
            html += `<div class="d-inline-block p-2 rounded"
                       style="max-width:70%; background:${m.sender_id == authId ? '#d0ebff' : '#e9ecef'};">
                       ${m.content}
                     </div>`;
        }

        if (m.file_path) {
            html += `<div>📎 <a href="${m.file_path}" target="_blank">Attachment</a></div>`;
        }

        wrapper.innerHTML = html;
        chatBox.appendChild(wrapper);

        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function fetchMessages() {
        try {
            const res = await fetch(urls.fetch + lastId, { credentials: "same-origin" });
            if (!res.ok) return;

            const data = await res.json();
            if (!Array.isArray(data) || data.length === 0) return;

            data.forEach(m => {
                appendMessage(m);
                lastId = m.id;
            });

        } catch (e) {
            console.error("Fetch error:", e);
        }
    }

    /* ------------------- SEND MESSAGE ------------------- */

    async function sendMessage(formData) {
        try {
            await fetch(urls.send, {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': csrf },
                body: formData,
                credentials: "same-origin"
            });
        } catch (e) {
            console.error("Send error:", e);
        }
    }

    document.getElementById("chatForm").addEventListener("submit", e => {
        e.preventDefault();

        if (!content.value.trim() && file.files.length === 0) {