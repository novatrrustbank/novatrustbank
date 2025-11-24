<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction History - NovaTrust Bank</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f7f9fb;
      margin: 0;
      color: #333;
    }
    .navbar {
      background-color: #1a237e;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar .logo {
      font-size: 20px;
      font-weight: bold;
    }
    .navbar .menu a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }
    .navbar .menu a:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      padding: 25px;
    }
    h2 {
      color: #1a237e;
      text-align: center;
      margin-bottom: 25px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #1a237e;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .credit {
      color: #2e7d32;
      font-weight: bold;
    }
    .debit {
      color: #c62828;
      font-weight: bold;
    }
    .empty {
      text-align: center;
      padding: 30px;
      color: #888;
      font-style: italic;
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
      <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button style="background:none;border:none;color:white;cursor:pointer;">Logout</button>
      </form>
    </div>
  </div>

  <div class="container">
    <h2>Transaction History</h2>

    @if($transactions->isEmpty())
      <div class="empty">No transactions yet.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Balance After</th>
            <th>Description</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactions as $transaction)
            <tr>
              <td>{{ $transaction->created_at->format('M d, Y - h:i A') }}</td>
              <td>{{ ucfirst($transaction->type) }}</td>
              <td class="{{ $transaction->type == 'credit' ? 'credit' : 'debit' }}">
                ${{ number_format($transaction->amount, 2) }}
              </td>
              <td>${{ number_format($transaction->balance_after, 2) }}</td>
              <td>{{ $transaction->description ?? 'N/A' }}</td>
              <td>{{ ucfirst($transaction->status) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
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
