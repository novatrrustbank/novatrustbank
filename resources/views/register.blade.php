<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NovaTrust Bank</title>
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
        input[type="text"],
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
    </style>
</head>
<body>
    <div class="navbar">NovaTrust Bank</div>

    <div class="container">
        <h2>Create Account</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter full name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>

            <button type="submit">Register</button>
        </form>

        <div class="link">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
</body>
</html>
