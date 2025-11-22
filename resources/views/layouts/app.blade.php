<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'NovaTrust Bank') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
        }
        .nt-navbar {
            background-color: #1a237e;
            color: white;
            padding: 15px 30px;
        }
        .nt-navbar .navbar-brand {
            color: white;
            font-weight: bold;
            font-size: 22px;
        }
        .nt-navbar .nav-link {
            color: white;
            font-weight: bold;
            margin-left: 15px;
        }
        .nt-navbar .nav-link:hover {
            text-decoration: underline;
        }
        .nt-logout-btn {
            background: white;
            color: #1a237e;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
        }
        .nt-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        /* --- FLOATING BUTTON --- */
        #floatingChatBtn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 70px;
            height: 70px;
            background: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 14px rgba(0,0,0,0.28);
            cursor: pointer;
            z-index: 9999;
            animation: floatPulse 1.8s infinite;
        }
        #floatingChatBtn:hover {
            background: #1e7e34;
        }
        @keyframes floatPulse {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
            100% { transform: translateY(0px); }
        }

        .chat-notify-bubble {
            position: absolute;
            top: 6px;
            right: 6px;
            background: red;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 50%;
            font-weight: bold;
            display: none;
        }
    </style>

    @stack('styles')
</head>

<body>

<nav class="navbar navbar-expand-lg nt-navbar mb-4">
    <div class="container">
        <a class="navbar-brand"
            href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : route('login') }}">
            NovaTrust Bank
        </a>

        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                @auth
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('history') }}">History</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('user.chat') }}">Chat</a></li>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.chats') }}">Chats</a></li>
                    @endif
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="nt-logout-btn ms-3">Logout</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="nt-container">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
@auth
// Example: floating chat button opens chat box
let floatingChatBtn = document.getElementById('floatingChatBtn');
if (floatingChatBtn) {
    floatingChatBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('user.chat') }}";
    });
}

// Function to send a chat message
function sendMessage(receiverId, messageText) {
    if (!messageText.trim()) return;

    fetch("{{ route('user.chat.send') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            receiver_id: receiverId,
            message: messageText
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Clear input or append message to chat box
            const input = document.getElementById('chat-input');
            if (input) input.value = '';

            // Optionally refresh messages
            fetchMessages(receiverId);
        } else {
            console.error('Message not sent', data);
        }
    })
    .catch(err => console.error('Send message error', err));
}

// Function to fetch messages
function fetchMessages(userId) {
    fetch(`/chat/messages/${userId}`)
        .then(res => res.json())
        .then(data => {
            let chatBox = document.getElementById('chat-box');
            if (!chatBox) return;
            
            chatBox.innerHTML = '';
            data.forEach(msg => {
                const div = document.createElement('div');
                div.className = msg.sender_id === {{ auth()->id() }} ? 'my-message' : 'other-message';
                div.innerText = msg.message;
                chatBox.appendChild(div);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Optional: auto-refresh every 4 seconds
setInterval(() => {
    let chatUserId = document.getElementById('chat-user-id')?.value;
    if (chatUserId) fetchMessages(chatUserId);
}, 4000);
@endauth
</script>