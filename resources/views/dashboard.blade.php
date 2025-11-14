@extends('layouts.app')

@section('title', 'Dashboard - NovaTrust Bank')

@section('styles')
<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background-color: #f4f7fc;
        margin: 0;
    }
    .navbar {
        background-color: #1a237e;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .navbar h1 {
        margin: 0;
        font-size: 22px;
    }
    .navbar a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
        font-weight: bold;
        position: relative;
    }
    .navbar a .badge {
        background: #e3342f;
        color: #fff;
        padding: 3px 7px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 6px;
    }
    .container {
        max-width: 900px;
        margin: 40px auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        padding: 30px;
    }
    .welcome {
        font-size: 20px;
        color: #1a237e;
        font-weight: bold;
        text-align: center;
    }
    .card {
        background-color: #1a237e;
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin-top: 25px;
    }
    .card h3 {
        margin: 5px 0;
        font-weight: normal;
    }
    .balance {
        font-size: 32px;
        font-weight: bold;
        margin-top: 10px;
    }
    .actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .actions a {
        text-decoration: none;
        background-color: #1a237e;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.3s;
    }
    .actions a:hover {
        background-color: #0d1b63;
    }
    .footer {
        text-align: center;
        color: #777;
        font-size: 14px;
        margin-top: 40px;
    }
</style>
@endsection

@section('content')
<div class="navbar">
    <h1>NovaTrust Bank</h1>
    <div>
        <a href="/dashboard">Dashboard</a>
        <a href="/transfer">Transfer</a>
        <a href="/history">History</a>
    
<div> 
<--💬 Direct Contact Chat Button (Centered + Green) -->
<div style="text-align: center; margin: 25px 0;">
    <a href="https://tawk.to/chat/69129b3e36dfb3195ff1a2b0/1j9oasreo"
       target="_blank"
       style="
          display: inline-block;
          background-color: #00b300;
          color: #fff;
          padding: 12px 28px;
          border-radius: 8px;
          text-decoration: none;
          font-weight: bold;
          font-family: 'Segoe UI', Arial, sans-serif;
          transition: background-color 0.3s ease;
          box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
       "
       onmouseover="this.style.backgroundColor='#009900'"
       onmouseout="this.style.backgroundColor='#00b300'">
       💬 Direct Contact Chat
    </a>
</div>

<div class="container">
    <p class="welcome">Welcome back, {{ Auth::user()->name }} 👋</p>

    <div class="card">
        <h3>Account Number</h3>
        <h2>{{ Auth::user()->account_number }}</h2>
        <h3>Current Balance</h3>
        <div class="balance">${{ number_format(Auth::user()->balance, 2) }}</div>
    </div>

    <div class="actions">
        <a href="/transfer">Make Transfer</a>
        <a href="/history">View History</a>
    </div>
</div>

<section style="
    background: linear-gradient(135deg, #1a237e, #283593);
    color: #fff;
    text-align: center;
    padding: 40px 20px;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin-top: 60px;
    border-top: 5px solid #3949ab;
">
    <h2 style="font-size: 26px; margin-bottom: 10px;">Contact NovaTrust Bank</h2>
    <p><strong>Washington DC, USA</strong></p>
    <p><strong>E-mail:</strong> infonovatrustbank@accountant.com</p>
    <p><strong>Tel:</strong> 
        <a href="tel:+19793982810" style="color: #ffeb3b; text-decoration: none;">
            +1 979-398-2810
        </a>
    </p>
    <p style="font-size: 14px; color: #c5cae9; margin-top: 20px;">
        © {{ date('Y') }} NovaTrust Bank. All Rights Reserved.
    </p>
</section>

<!-- Real-Time Pusher Listener -->
<script src="{{ asset('js/app.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const userId = {{ Auth::id() }};
    const msgLink = document.getElementById("messages-link");
    let badge = msgLink.querySelector(".badge");

    if (window.Echo) {
        window.Echo.channel(`user.${userId}`)
            .listen('.message.created', (e) => {
                if (!badge) {
                    badge = document.createElement("span");
                    badge.className = "badge";
                    badge.style.background = "#e3342f";
                    badge.style.color = "#fff";
                    badge.style.padding = "3px 7px";
                    badge.style.borderRadius = "12px";
                    badge.style.fontSize = "12px";
                    badge.style.marginLeft = "6px";
                    msgLink.appendChild(badge);
                }

                let count = parseInt(badge.textContent || 0);
                badge.textContent = count + 1;

                // Optional notification sound
                const audio = new Audio("{{ asset('sounds/notify.mp3') }}");
                audio.play().catch(() => {});
            });
    } else {
        console.warn("Echo not loaded — real-time updates unavailable.");
    }
});
</script>
@endsection