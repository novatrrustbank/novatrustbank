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

    const urls = {
    fetch: "{{ route('admin.chat.refresh', ['user_id' => 'USER_ID']) }}"
        .replace('USER_ID', otherId) + "?last_id=",

    send: "{{ route('admin.chat.send') }}",

    typing: "{{ route('chat.typing') }}",

    typingCheck: "{{ route('chat.typing.status', ['userId' => 'USER_ID']) }}"
        .replace('USER_ID', otherId),

    markRead: "{{ route('chat.mark.read') }}",

    online: "{{ route('chat.online.status', ['userId' => 'USER_ID']) }}"
        .replace('USER_ID', otherId)
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
            html += `<div>?? <a href="${m.file_path}" target="_blank">Attachment</a></div>`;
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

        const fd = new FormData();
        fd.append("receiver_id", otherId);
        fd.append("content", content.value);
        if (file.files.length) fd.append("file", file.files[0]);

        sendMessage(fd).then(() => {
            content.value = "";
            file.value = "";
            fetchMessages();
        });
    });

    /* ------------------- TYPING ------------------- */

    async function sendTyping() {
        try {
            await fetch(urls.typing, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
                body: JSON.stringify({ receiver_id: otherId })
            });
        } catch {}
    }

    content.addEventListener("input", () => {
        clearTimeout(typingTimeout);
        sendTyping();
        typingTimeout = setTimeout(() => {}, 1500);
    });

    async function checkTyping() {
        try {
            const res = await fetch(urls.typingCheck, { credentials: "same-origin" });
            if (!res.ok) return;

            const data = await res.json();
            document.getElementById('typingIndicator').style.display =
                data.typing ? "inline" : "none";

        } catch {}
    }

    /* ------------------- ONLINE STATUS ------------------- */

    async function checkOnline() {
        try {
            const res = await fetch(urls.online, { credentials: "same-origin" });
            if (!res.ok) return;

            const data = await res.json();
            const badge = document.getElementById("onlineBadge");

            badge.className = data.online ? "badge bg-success" : "badge bg-secondary";
            badge.textContent = data.online ? "Online" : "Offline";

        } catch {}
    }

    /* ------------------- INIT ------------------- */

    (async function init() {
        await fetchMessages();

        await fetch(urls.markRead, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ sender_id: otherId })
        });

        setInterval(fetchMessages, 1200);
        setInterval(checkTyping, 1200);
        setInterval(checkOnline, 6000);
    })();

})();
</script>

@endsection