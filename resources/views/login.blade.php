<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NovaTrust Bank</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { height: 100vh; overflow: hidden; }
        .bg-image { position: fixed; width: 100%; height: 100%; object-fit: cover; top: 0; left: 0; z-index: -2; }
        .overlay { position: fixed; width: 100%; height: 100%; background: rgba(0,0,0,0.55); top: 0; left: 0; z-index: -1; }
        .navbar { color: white; padding: 20px 40px; font-size: 22px; font-weight: bold; }
        .main { display: flex; height: calc(100vh - 80px); align-items: center; justify-content: space-between; padding: 40px; }
        .hero-text { color: white; max-width: 600px; }
        .hero-text span { background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 14px; }
        .hero-text h1 { font-size: 48px; margin-top: 15px; line-height: 1.2; }
        .container { width: 100%; max-width: 380px; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        h2 { text-align: center; color: #1a237e; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; }
        button { width: 100%; margin-top: 20px; padding: 12px; border: none; border-radius: 6px; background-color: #1a237e; color: white; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #0d1b63; }
        .link { text-align: center; margin-top: 15px; }
        .error { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
        .logo-image { position: absolute; top: 20px; left: 40px; height: 40px; }
        @media(max-width: 768px){
            .main { flex-direction: column; justify-content: center; text-align: center; }
            .hero-text h1 { font-size: 28px; }
            .container { margin-top: 20px; }
        }
    </style>
</head>
<body>

    <img src="{{ asset('images/banking-bg.jpg') }}" class="bg-image">
    <div class="overlay"></div>
    <img src="{{ asset('images/logo.jpg') }}" class="logo-image">

    <div class="main">
        <div class="hero-text">
            <span>Simple, Quick, Secure Banking System</span>
            <h1>Send Funds From Us To 130+ Countries Within Minutes</h1>
        </div>

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

                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit">Login</button>
            </form>

            <div class="link">
    <a href="{{ route('register') }}"
       style="color: #1a237e; text-decoration: none; font-size: 14px;">
       Don't have an account? Register
    </a>
</div>

<div class="link" style="margin-top:10px;">
    <a href="{{ route('password.request') }}"
       style="color:#c62828; text-decoration:none; font-size:14px; font-weight:bold;">
       Forgot Password?
    </a>
</div>