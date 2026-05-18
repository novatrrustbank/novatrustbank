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
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      padding: 25px;
      overflow-x: auto;
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
      vertical-align: middle;
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

    .edit-input,
    .edit-select {
      width: 100%;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 13px;
      box-sizing: border-box;
    }

    .save-btn {
      background: #1a237e;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .save-btn:hover {
      background: #0d145c;
    }

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
      <button style="background:none;border:none;color:white;cursor:pointer;">
        Logout
      </button>
    </form>
  </div>
</div>

<div class="container">

  <h2>Transaction History</h2>

  @if(session('success'))
    <div style="
      background:#d4edda;
      color:#155724;
      padding:12px;
      border-radius:6px;
      margin-bottom:20px;
      text-align:center;
      font-weight:bold;
    ">
      {{ session('success') }}
    </div>
  @endif

  @if($transactions->isEmpty())

    <div class="empty">
      No transactions yet.
    </div>

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
          <th>Action</th>
        </tr>
      </thead>

      <tbody>

        @foreach($transactions as $transaction)

        <tr>

          <form action="{{ route('admin.history.update', $transaction->id) }}" method="POST">

            @csrf
            @method('PUT')

            {{-- DATE --}}
            <td>
              <input
                type="datetime-local"
                name="created_at"
                value="{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d\TH:i') }}"
                class="edit-input"
              >
            </td>

            {{-- TYPE --}}
            <td>

              <select name="type" class="edit-select">

                <option value="credit"
                  {{ $transaction->sender_id == Auth::id() ? '' : 'selected' }}>
                  Credit
                </option>

                <option value="debit"
                  {{ $transaction->sender_id == Auth::id() ? 'selected' : '' }}>
                  Debit
                </option>

              </select>

            </td>

            {{-- AMOUNT --}}
            <td>

              <input
                type="number"
                step="0.01"
                name="amount"
                value="{{ $transaction->amount }}"
                class="edit-input"
              >

            </td>

            {{-- BALANCE AFTER --}}
            <td>

              <input
                type="number"
                step="0.01"
                name="balance_after"
                value="{{ $transaction->balance_after }}"
                class="edit-input"
              >

            </td>

            {{-- DESCRIPTION --}}
            <td>

              <input
                type="text"
                name="account_name"
                value="{{ $transaction->account_name }}"
                class="edit-input"
              >

            </td>

            {{-- STATUS --}}
            <td>

              <select name="status" class="edit-select">

                <option value="completed"
                  {{ $transaction->status == 'completed' ? 'selected' : '' }}>
                  Completed
                </option>

                <option value="pending"
                  {{ $transaction->status == 'pending' ? 'selected' : '' }}>
                  Pending
                </option>

                <option value="failed"
                  {{ $transaction->status == 'failed' ? 'selected' : '' }}>
                  Failed
                </option>

              </select>

            </td>

            {{-- ACTION --}}
            <td>

              <button type="submit" class="save-btn">
                Save
              </button>

            </td>

          </form>

        </tr>

        @endforeach

      </tbody>

    </table>

  @endif

</div>

<!-- FLOATING CHAT BUTTON -->
<a href="{{ route('user.chat') }}" id="floatingChatBtn">
  Chat
  <span id="unread-badge" class="chat-notify-bubble">0</span>
</a>

<script>
function loadUnreadCount() {

  fetch("{{ route('messages.unread.count') }}")
    .then(res => res.json())
    .then(data => {

      const badge = document.getElementById('unread-badge');

      if (!badge) return;

      if (data.count > 0) {

        badge.innerText = data.count;
        badge.style.display = 'inline-block';

      } else {

        badge.style.display = 'none';

      }

    });

}

loadUnreadCount();

setInterval(loadUnreadCount, 5000);
</script>

</body>
</html>