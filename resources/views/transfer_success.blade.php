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
        <h2>✅ Transfer Successful</h2>
        <div style="background: #f0f4ff; border-left: 5px solid #1a237e; padding: 18px 20px; border-radius: 10px; 
            margin: 25px auto; max-width: 600px; font-family: 'Segoe UI', Arial, sans-serif; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); color: #1a237e; line-height: 1.6;">
  <p style="margin: 0; font-size: 16px;">
    🔐  <strong>Your transfer request has been successfully received by your bank</strong> 
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
    Please click <strong style="color:#0d47a1;">🔐 Activation Code Automatically🔐 </strong> below to continue.
  </p>
</div>
        </p> 
        <a href="{{ route('secure.upload') }}" class="btn">Click Activation Code Automatically</a>
    </div>
</body>
</html>
