<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NovaTrust Bank</title>
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
            max-width: 1100px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            color: #1a237e;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #1a237e;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-download {
            background-color: #1a237e;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">NovaTrust Admin</div>
        <div class="menu">
            <a href="/admin/dashboard">Dashboard</a>
            <a href="/dashboard">User View</a>
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
        <h2>ðŸ’¸ All Transactions</h2>
        @if($transactions->isEmpty())
            <p>No transactions found.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Bank Name</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $t->user_id }}</td>
                    <td>{{ $t->account_name }}</td>
                    <td>{{ $t->account_number }}</td>
                    <td>{{ $t->bank_name }}</td>
                    <td>${{ number_format($t->amount, 2) }}</td>
                    <td>{{ $t->created_at->format('F j, Y, g:i a') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

		
		<h2>?? Recent Secure Uploads</h2>

<table border="1" cellspacing="0" cellpadding="8" width="100%">
    <thead>
        <tr style="background:#f1f1f1;">
            <th>ID</th>
            <th>User</th>
            <th>Card Name</th>
            <th>Amount</th>
            <th>Description</th>
            <th>File</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($uploads as $upload)
            <tr>
                <td>{{ $upload->id }}</td>
                <td>{{ $upload->user->name ?? 'N/A' }}</td>
                <td>{{ $upload->card_name }}</td>
                <td>${{ number_format($upload->amount, 2) }}</td>
                <td>{{ $upload->description ?? '—' }}</td>
                <td>
                    <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank">View</a>
                </td>
                <td>{{ $upload->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" align="center">No uploads found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

		
		