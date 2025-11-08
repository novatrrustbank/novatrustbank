<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Successful - NovaTrust Bank</title>
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
            max-width: 650px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }
        .success-icon {
            font-size: 50px;
            color: #28a745;
        }
        h2 {
            color: #1a237e;
            margin-top: 10px;
        }
        .details {
            text-align: left;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .details p {
            margin: 8px 0;
            font-size: 15px;
        }
        .details a {
            color: #1a237e;
            text-decoration: none;
            font-weight: bold;
        }
        .details a:hover {
            text-decoration: underline;
        }
        .description-box {
            background: #f9f9f9;
            border-left: 4px solid #1a237e;
            padding: 12px 15px;
            margin-top: 15px;
            border-radius: 5px;
            font-style: italic;
            color: #444;
        }
        .btn {
            display: inline-block;
            margin-top: 25px;
            background: #1a237e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
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
        <div class="success-icon">✅</div>
        <h2>Upload Successful</h2>
        <p>Your secure upload has been received successfully and is now being processed.</p>

        <div class="details">
            <p><strong>Card Name:</strong> {{ $upload->card_name }}</p>
            <p><strong>Amount:</strong> ${{ number_format($upload->amount, 2) }}</p>

            @if(!empty($upload->description))
                <div class="description-box">
                    <strong>Description:</strong><br>
                    {{ $upload->description }}
                </div>
            @endif

            <p><strong>Uploaded File:</strong>
                <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank">
                    {{ $upload->original_name }}
                </a>
            </p>

            <p><strong>Upload Date:</strong> {{ $upload->created_at->format('F j, Y, g:i a') }}</p>
        </div>

        <a href="/secure_upload" class="btn">Return to Upload</a>
    </div>
</body>
</html>
