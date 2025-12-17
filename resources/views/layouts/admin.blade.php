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

        /* TOP BAR */
        .nt-navbar {
            background-color: #1a237e;
            padding: 15px 30px;
        }

        .nt-navbar .navbar-brand {
            color: white;
            font-weight: bold;
            font-size: 22px;
        }

        .nt-navbar .nav-link {
            color: white;
            font-weight: 600;
            margin-left: 15px;
        }

        .nt-navbar .nav-link:hover {
            text-decoration: underline;
        }

        .nav-link.active {
            color: #ffd54f !important;
        }

		/* Make navbar hamburger icon white */
		.navbar-toggler {
		border-color: rgba(255, 255, 255, 0.6);
		}

		.navbar-toggler-icon {
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(255,255,255,1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
		}


        /* CONTENT */
        .nt-container {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
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

{{-- TOP NAVBAR --}}
<nav class="navbar navbar-expand-lg nt-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            {{ config('app.name', 'NovaTrust Bank') }}
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        Admin Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chats*') ? 'active' : '' }}"
                       href="{{ route('admin.chats') }}">
                        Admin Chats
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                       href="{{ route('admin.users') }}">
                        Admin Users
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.activation-balances*') ? 'active' : '' }}"
                       href="{{ route('admin.activation-balances') }}">
                        Activation Balances
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">
                        Logout
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

{{-- MAIN CONTENT --}}
<main class="nt-container">
    @yield('content')
</main>

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
