<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTrust Admin Dashboard</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	
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
            padding:15px 30px;
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

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h2 {
            color:#1a237e;
            border-bottom:2px solid #1a237e;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table thead tr {
            background: #1a237e;
            color: white;
        }

        table thead th, table tbody td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .chat-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            object-fit: cover;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            thead tr {
                display: none;
            }
            tbody td {
                padding-left: 50%;
                position: relative;
                border: none;
                border-bottom: 1px solid #ddd;
            }
            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
            }
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
        </tbody>
    </table>
</div>

</body>
</html>
