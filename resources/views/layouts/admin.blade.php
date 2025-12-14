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
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
        }

        /* LAYOUT */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: #0f172a;
            color: #fff;
            padding: 20px;
        }

        .sidebar h3 {
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar-section {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            margin: 20px 0 8px;
        }

        .sidebar a {
            display: block;
            padding: 12px 14px;
            color: #e5e7eb;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1e293b;
            color: #38bdf8;
        }

        /* MAIN */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* TOP BAR */
        .topbar {
            background: #1a237e;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
        }

        /* CONTENT */
        .content {
            padding: 30px;
        }

        .content-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        /* FLOATING CHAT */
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
            text-decoration: none;
            animation: floatPulse 1.8s infinite;
        }

        #floatingChatBtn:hover {
            background: #1e7e34;
        }

        @keyframes floatPulse {
            0% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
            100% { transform: translateY(0); }
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

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <h3>{{ config('app.name', 'NovaTrust Bank') }}</h3>

        <div class="sidebar-section">Admin</div>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Admin Dashboard
        </a>

        <a href="{{ route('admin.chats') }}"
           class="{{ request()->routeIs('admin.chats*') ? 'active' : '' }}">
            Admin Chats
        </a>

        <a href="{{ route('admin.users') }}"
           class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            Admin Users
        </a>

        <a href="{{ route('admin.activation_balances') }}"
           class="{{ request()->routeIs('admin.activation_balances*') ? 'active' : '' }}">
            Activation Balances
        </a>

        <div class="sidebar-section">User</div>

        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('logout') }}">
            Logout
        </a>
    </aside>

    {{-- MAIN --}}
    <div class="main">

        <header class="topbar">
            Welcome to {{ config('app.name', 'NovaTrust Bank') }}
        </header>

        <section class="content">
            <div class="content-box">
                @yield('content')
            </div>
        </section>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

{{-- FLOATING CHAT --}}
<a href="{{ route('user.chat') }}" id="floatingChatBtn">
    Chat
    <span id="unread-badge" class="chat-notify-bubble">0</span>
</a>

<script>
function loadUnreadCount() {
    fetch("{{ route('messages.unread.count') }}")
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('unread-badge');
            if (!badge) return;
            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        });
}
loadUnreadCount();
setInterval(loadUnreadCount, 5000);
</script>

</body>
</html>
