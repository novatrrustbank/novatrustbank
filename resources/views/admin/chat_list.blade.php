<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NovaTrust Admin Chats List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f8fa;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background:#1a237e;
            color:white;
            padding:15px 20px;
            display:flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .navbar .menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 2px 5px;
            background:#3949ab;
        }

        .navbar .menu a:hover {
            background:#283593;
        }
        
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">NovaTrust Admin</div>
    <div class="menu">
        <a href="/admin/dashboard">Dashboard</a>
        <a href="/dashboard">User View</a>
        <a href="/admin/chats">Chats List</a>
		<a href="/admin/activation-balances">Activation Balance</a>
		<a href="/admin/users">Users List</a>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>
    </div>
</div>

<div class="container">
    <h3>Users Messaging Support</h3>

    <ul class="list-group">
        @foreach($users as $u)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    {{ $u->name }}

                    @if($u->unread > 0)
                        <span class="badge bg-danger ms-2">
                            {{ $u->unread }} new
                        </span>
                    @endif
                </div>

                <a href="{{ route('admin.chat.open', $u->id) }}" class="btn btn-primary btn-sm">
                    Open Chat
                </a>
            </li>
        @endforeach
    </ul>
</div>

</body>
</html>