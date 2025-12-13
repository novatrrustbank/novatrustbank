<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NovaTrust Admin Dashboard</title>
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

        
    </style>
	
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
