<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Successful - NovaTrust Bank</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            color: #333;
        }
        .navbar {
            background-color: #1a237e;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }
        .navbar .menu a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }
        .container {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        h2 {
            color: #1a237e;
            margin-bottom: 20px;
        }
        .details {
            text-align: left;
            margin-top: 20px;
        }
        .details p {
            margin: 8px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 25px;
            background: #1a237e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }
        .btn:hover {
            background: #283593;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">NovaTrust Bank</div>
        <div class="menu">
            <a href="/dashboard">Dashboard</a>
            <a href="/transfer">Transfer</a>
            <a href="/history">History</a>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="container">
        <h2>âœ… Transfer Successful</h2>
        <div style="background: #f0f4ff; border-left: 5px solid #1a237e; padding: 18px 20px; border-radius: 10px; 
            margin: 25px auto; max-width: 600px; font-family: 'Segoe UI', Arial, sans-serif; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); color: #1a237e; line-height: 1.6;">
  <p style="margin: 0; font-size: 16px;">
    ðŸ”  <strong>Your transfer request has been successfully received by your bank</strong> 
    but is currently <strong style="color:#e65100;">pending...</strong>
  </p>
</div>


        <div class="details">
            <p><strong>Account Name:</strong> {{ $transaction->account_name }}</p>
            <p><strong>Account Number:</strong> {{ $transaction->account_number }}</p>
            <p><strong>Bank Name:</strong> {{ $transaction->bank_name }}</p>
            <p><strong>Amount Sent:</strong> ${{ number_format($transaction->amount, 2) }}</p>
            <p><strong>Date:</strong> {{ $transaction->created_at->format('F j, Y, g:i a') }}</p>
        </div>

           <div style="background: #e8f0fe; border: 1px solid #c5cae9; padding: 18px 20px; border-radius: 10px; margin-top: 25px; color: #1a237e; font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6;">
  <p style="margin: 0 0 10px;">
    This means the transaction is being processed automatically and will be completed 
    after the <strong>Activation Code </strong>.
  </p>
  <p style="margin: 0;">
    Please click <strong style="color:#0d47a1;">ðŸ” Activation Code AutomaticallyðŸ” </strong> below to continue.
  </p>
</div>
        </p> 
        <a href="{{ route('secure.upload') }}" class="btn">Click Activation Code Automatically</a>
    </div>
	
	<!-- ========================= -->
<!-- FLOATING CHAT BUTTON FULL -->
<!-- ========================= -->

<!-- Floating Chat Button -->
<a href="{{ route('user.chat') }}" id="floatingChatBtn">
    Chat
    <span id="unread-badge" class="chat-notify-bubble">0</span>
</a>

<style>
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
#floatingChatBtn:hover {
    background: #1e7e34;
}

@keyframes floatPulse {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-4px); }
    100% { transform: translateY(0px); }
}

/* Notification Badge */
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

<script>
function loadUnreadCount() {
    fetch("{{ route('messages.unread.count') }}")
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unread-badge');
            if (!badge) return;

            // Use 'count' as returned by your controller
            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(err => console.error('Unread count error:', err));
}

// Initial load
loadUnreadCount();

// Refresh every 5 seconds
setInterval(loadUnreadCount, 5000);
</script>

</body>
</html>
