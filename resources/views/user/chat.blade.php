@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        width: 100%;
        max-width: 900px;
        margin: auto;
        background: #f0f2f5;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .chat-header {
        padding: 15px;
        background: #075E54;
        color: white;
        font-size: 18px;
        font-weight: bold;
        border-radius: 12px 12px 0 0;
    }

    .chat-box {
        height: 480px;
        overflow-y: scroll;
        padding: 15px;
        background: #e5ddd5;
    }

	.chat-image {
    width: 250px;       /* WhatsApp-style width */
    height: auto;       /* Keep correct aspect ratio */
    max-height: 250px;  /* Limit very tall images */
    border-radius: 12px;
    object-fit: cover;
    display: block;
	}

    .msg-right {
        max-width: 75%;
        background: #dcf8c6;
        padding: 10px 12px;
        border-radius: 12px 0 12px 12px;
        margin-bottom: 12px;
        float: right;
        clear: both;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        display: inline-block;
    }

    .msg-left {
        max-width: 75%;
        background: white;
        padding: 10px 12px;
        border-radius: 0 12px 12px 12px;
        margin-bottom: 12px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        display: inline-block;
    }

    .timestamp {
        font-size: 11px;
        color: gray;
        margin-top: 4px;
        text-align: right;
    }

    .chat-footer {
        padding: 12px;
        background: #fff;
        border-top: 1px solid #ddd;
        border-radius: 0 0 12px 12px;
    }
</style>

<div class="chat-container">
    <div class="chat-header">Chat with Support</div>

    <div id="chatBox" class="chat-box"></div>

    <div class="chat-footer">
        <form id="chatForm" enctype="multipart/form-data">
            @csrf
            <textarea name="message" id="message" rows="2" class="form-control" placeholder="Type message..."></textarea>

            <input type="file" name="file" id="fileInput" class="form-control mt-2">

            <button type="submit" class="btn btn-primary w-100 mt-2">Send</button>
        </form>
    </div>
</div>

<script>
function loadMessages() {
    fetch("/chat/fetch")
        .then(res => res.json())
        .then(messages => {
            let html = "";

            messages.forEach(msg => {
                let isUser = msg.sender_id == {{ auth()->id() }};
                let bubble = isUser ? "msg-right" : "msg-left";

                let time = new Date(msg.created_at).toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit"
                });

                html += `<div class="${bubble}">
                            ${msg.message ?? ""}`;

                if (msg.file) {
                    let f = msg.file.toLowerCase();

                    if (f.match(/\.(jpg|jpeg|png|gif)$/)) {
                        html += `<br><img src="/chat-file/${msg.file}" class="chat-image">`;
                    } else {
                        html += `<br><a href="/storage/${msg.file}" target="_blank">📎 Download file</a>`;
                    }
                }

                html += `<div class="timestamp">${time}</div></div>
                         <div style="clear:both;"></div>`;
            });

            let box = document.getElementById("chatBox");
            box.innerHTML = html;
            box.scrollTop = box.scrollHeight;
        });
}

setInterval(loadMessages, 2000);
loadMessages();

document.getElementById("chatForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let msg = document.getElementById("message").value.trim();
    let file = document.getElementById("fileInput");

    if (msg === "" && file.files.length === 0) return;

    let formData = new FormData(this);

    fetch("/chat/send", {
        method: "POST",
        body: formData
    }).then(() => {
        document.getElementById("message").value = "";
        file.value = "";
        loadMessages();
    });
});
</script>

@endsection
