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

        /* NAVBAR */
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

        /* CONTENT */
        .nt-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        /* Floating Chat Button */
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
            text-decoration: none;
        }
        #floatingChatBtn:hover { background: #1e7e34; }

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

{{-- TOP NAVBAR (VISIBLE TO EVERYONE) --}}
<nav class="navbar navbar-expand-lg nt-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            {{ config('app.name', 'NovaTrust Bank') }}
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'text-warning' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        Admin Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chats*') ? 'text-warning' : '' }}"
                       href="{{ route('admin.chats') }}">
                        Admin Chats
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'text-warning' : '' }}"
                       href="{{ route('admin.users') }}">
                        Admin Users
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.activation_balances*') ? 'text-warning' : '' }}"
                       href="{{ route('admin.activation_balances') }}">
                        Activation Balances
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-warning' : '' }}"
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
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unread-badge');
            if (!badge) return;
            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(err => console.error(err));
}
loadUnreadCount();
setInterval(loadUnreadCount, 5000);
</script>

<!-- Tawk.to -->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/69129b3e36dfb3195ff1a2b0/1j9oasreo';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>

</body>
</html>
