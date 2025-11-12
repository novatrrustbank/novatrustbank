<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NovaTrust Bank</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fb;
            margin: 0;
        }
        .navbar {
            background-color: #1a237e;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 400px;
            margin: 60px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #1a237e;
            margin-bottom: 25px;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-top: 12px;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            background-color: #1a237e;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #0d1b63;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #1a237e;
            text-decoration: none;
            font-weight: bold;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">NovaTrust Bank</div>

    <!-- Direct embedded Tawk.to chat (no floating bubble) -->
<iframe
  src="https://tawk.to/chat/69129b3e36dfb3195ff1a2b0/1j9oasreo"
  width="100%"
  height="450"
  style="border: none; border-radius: 10px;"
  allow="microphone; camera">
</iframe>
    
    <div class="container">
        <h2>Login</h2>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter email" autocomplete="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" autocomplete="current-password" required>

            <button type="submit">Login</button>
        </form>

        <div class="link">
             <a href="{{ route('register') }}"></a>
        </div>
    </div>
</body>
</html>
