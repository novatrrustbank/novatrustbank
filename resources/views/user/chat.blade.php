@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Chat with Admin ({{ $admin->name }})</h3>

        <div>
            <span id="onlineBadge" class="badge bg-secondary">Checking…</span>
            <small id="typingIndicator" class="text-muted ms-3" style="display:none">Typing...</small>
        </div>
    </div>

    <!-- CHAT AREA -->
    <div id="chatBox" class="border rounded p-3 mb-3"
         style="height:420px; overflow-y:auto; background:#f7f7f7;">
    </div>

    <!-- SEND MESSAGE -->
    <form id="chatForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="receiver_id" value="{{ $admin->id }}">

        <div class="input-group">
            <input type="text" id="content" class="form-control" placeholder="Type a message..." autocomplete="off">
            <input type="file" id="file" class="form-control">
            <button class="btn btn-success" type="submit">Send</button>
        </div>
    </form>
</div>


<script>
(() => {

    const authId = {{ Auth::id() }};
    const otherId = {{ $admin->id }};
    const chatBox = document.getElementById("chatBox");

    const urls = {
        fetch: "{{ route('user.chat.messages', ['userId' => 'USER_ID']) }}"
            .replace('USER_ID', otherId) + "?last_id=",

        send: "{{ route('user.chat.send') }}",

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


    /* ---------------------- RENDER ---------------------- */

    function appendMessage(m) {
        const wrap = document.createElement("div");
        wrap.className = (m.sender_id == authId) ? "text-end mb-3" : "text-start mb-3";

        let html = `
            <small class="text-muted d-block">
                ${m.sender_name} - ${new Date(m.created_at).toLocaleString()}
            </small>
        `;

        if (m.content) {
            html += `
                <div class="d-inline-block p-2 rounded"
                     style="max-width:70%; background:${m.sender_id == authId ? '#d0ebff' : '#e9ecef'};">
                    ${m.content}
                </div>
            `;
        }

        if (m.file_path) {
            html += `
                <div>📎 <a href="${m.file_path}" target="_blank">Attachment</a></div>
            `;
        }

        wrap.innerHTML = html;
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
    }


    /* ---------------------- FETCH NEW MESSAGES ---------------------- */

    async function fetchMessages() {
        try {
            const res = await fetch(urls.fetch + lastId, { credentials: "same-origin" });
            if (!res.ok) return;

            const data = await res.json();
            if (!Array.isArray(data) || !data.length) return;

            data.forEach(m => {
                appendMessage(m);
                lastId = m.id;
            });

            markAsRead();

        } catch (e) {
            console.error("Fetch error:", e);
        }
    }


    /* ---------------------- SEND MESSAGE ---------------------- */

    document.getElementById("chatForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const fd = new FormData();
        fd.append("receiver_id", otherId);
        fd.append("content", content.value);

        const file = document.getElementById("file");
        if (file.files.length > 0) fd.append("file", file.files[0]);

        await fetch(urls.send, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            body: fd,
            credentials: "same-origin"
        });

        content.value = "";
        file.value = "";

        fetchMessages();
    });


    /* ---------------------- MARK READ ---------------------- */

    async function markAsRead() {
        try {
            await fetch(urls.markRead, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf
                },
                body: JSON.stringify({ sender_id: otherId }),
                credentials: "same-origin"
            });
        } catch {}
    }


    /* ---------------------- TYPING ---------------------- */

    content.addEventListener("input", () => {
        clearTimeout(typingTimeout);

        fetch(urls.typing, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({ receiver_id: otherId })
        });

        typingTimeout = setTimeout(() => {}, 1200);
    });

    async function checkTyping() {
        try {
            const res = await fetch(urls.typingCheck);
            const data = await res.json();

            document.getElementById("typingIndicator").style.display =
                data.typing ? "inline" : "none";

        } catch {}
    }


    /* ---------------------- ONLINE ---------------------- */

    async function checkOnline() {
        try {
            const res = await fetch(urls.online);
            const data = await res.json();

            const badge = document.getElementById("onlineBadge");
            badge.className = data.online ? "badge bg-success" : "badge bg-secondary";
            badge.textContent = data.online ? "Online" : "Offline";

        } catch {}
    }


    /* ---------------------- INIT ---------------------- */

    (async function init() {
        await fetchMessages();

        setInterval(fetchMessages, 1200);
        setInterval(checkTyping, 1200);
        setInterval(checkOnline, 6000);
    })();

})();
</script>

@endsection