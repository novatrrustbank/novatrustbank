<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NovaTrust Bank')</title>
    @yield('styles') <!-- Allow each page to insert its own CSS -->
</head>
<body>
    <!-- === Global Navigation Bar === -->
    @if(Auth::check())
        <div style="background-color:#1a237e;color:#fff;padding:12px 25px;display:flex;justify-content:space-between;align-items:center;">
            <div style="font-weight:bold;font-size:20px;">NovaTrust Bank</div>
            <div style="display:flex;align-items:center;gap:20px;">
                <a href="/dashboard" style="color:#fff;text-decoration:none;">Dashboard</a>
                <a href="/transfer" style="color:#fff;text-decoration:none;">Transfer</a>
                <a href="/history" style="color:#fff;text-decoration:none;">History</a>
                
                <!-- 💬 Global Messages Link with Unread Badge -->
                <a href="{{ route('messages.inbox') }}" id="messages-link" style="color:#fff;text-decoration:none;position:relative;">
                    Messages
                    @php
                        $unread = \Auth::user()->messages()->where('is_read', false)->count();
                    @endphp
                    @if($unread > 0)
                        <span id="msg-badge" style="background:#e3342f;color:#fff;padding:3px 7px;border-radius:12px;font-size:12px;margin-left:6px;">{{ $unread }}</span>
                    @endif
                </a>

                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="color:#fff;text-decoration:none;">
                   Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
    @endif

    <!-- === Page Content === -->
    @yield('content')

    <!-- ✅ Real-Time Message Notifications (Pusher / Laravel Echo) -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        @if(Auth::check())
            const userId = {{ Auth::id() }};
            const badge = document.getElementById("msg-badge");
            const msgLink = document.getElementById("messages-link");

            if (window.Echo) {
                window.Echo.private(`user.${userId}`)
                    .listen('.message.created', (e) => {
                        let count = parseInt(badge?.textContent || 0) + 1;

                        if (badge) {
                            badge.textContent = count;
                        } else {
                            const newBadge = document.createElement("span");
                            newBadge.id = "msg-badge";
                            newBadge.style.background = "#e3342f";
                            newBadge.style.color = "#fff";
                            newBadge.style.padding = "3px 7px";
                            newBadge.style.borderRadius = "12px";
                            newBadge.style.fontSize = "12px";
                            newBadge.style.marginLeft = "6px";
                            newBadge.textContent = count;
                            msgLink.appendChild(newBadge);
                        }

                        // 🔔 Optional sound alert
                        const audio = new Audio("{{ asset('sounds/notify.mp3') }}");
                        audio.play().catch(() => {});
                    });
            }
        @endif
    });
    </script>

    <!-- ✅ Tawk.to Live Chat only for Guests -->
    @guest
    <script type="text/javascript">
        var Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/69129b3e36dfb3195ff1a2b0/1j9oasreo';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    @endguest
</body>
</html>