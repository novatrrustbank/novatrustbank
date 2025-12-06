<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transfer Funds - NovaTrust Bank</title>
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
      letter-spacing: 0.5px;
    }
    .navbar .menu a,
    .navbar .menu button {
      color: #fff;
      text-decoration: none;
      margin-left: 20px;
      font-weight: bold;
      background: none;
      border: none;
      cursor: pointer;
      font-family: inherit;
      font-size: 15px;
    }
    .navbar .menu a:hover,
    .navbar .menu button:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 500px;
      margin: 50px auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      padding: 30px 25px;
    }
    h2 {
      text-align: center;
      color: #1a237e;
      margin-bottom: 25px;
      font-weight: 600;
    }
    form label {
      display: block;
      font-weight: bold;
      margin-bottom: 6px;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 15px;
      transition: border 0.2s;
    }
    input:focus, select:focus {
      border-color: #1a237e;
      outline: none;
    }
    button[type="submit"] {
      background-color: #1a237e;
      color: #fff;
      border: none;
      width: 100%;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
    }
    button[type="submit"]:hover {
      background-color: #0d1b63;
    }
    .alert {
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
    }
    .alert.success {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .alert.error {
      background-color: #ffebee;
      color: #c62828;
    }
    #otherBankContainer {
      display: none;
      animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }

    /* ============================== */
    /* 🔵 PROCESSING OVERLAY STYLES  */
    /* ============================== */
    #processingOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.65);
      display: none;
      z-index: 999999;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: white;
      font-size: 20px;
      font-weight: bold;
    }

    .spinner {
      border: 6px solid rgba(255,255,255,0.3);
      border-top: 6px solid #ffffff;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
      margin-bottom: 15px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>

<!-- =============================== -->
<!-- 🔵 PROCESSING OVERLAY HTML     -->
<!-- =============================== -->
<div id="processingOverlay">
    <div class="spinner"></div>
    Processing Transfer...
</div>

  <div class="navbar">
    <div class="logo">NovaTrust Bank</div>
    <div class="menu">
      <a href="/dashboard">Dashboard</a>
      <a href="/transfer">Transfer</a>
      <a href="/history">History</a>
      <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div class="container">
    <h2>Transfer Funds</h2>

    @if(session('error'))
      <div class="alert error">{{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('transfer.process') }}" method="POST" id="transferForm">
      @csrf

      <label for="account_number">Account Number</label>
      <input type="text" id="account_number" name="account_number" placeholder="Enter recipient account number" required>

      <label for="account_name">Account Name</label>
      <input type="text" id="account_name" name="account_name" placeholder="Enter account name" required>

      <label for="bank_select">Select Bank</label>
      <select id="bank_select" name="bank_select" required>
        <option value="">-- Select Bank --</option>
        <option value="Academy Bank">Academy Bank</option>
        <option value="Ally Bank">Ally Bank</option>
        <option value="American Bank">American Bank</option>
        <option value="American Express Bank">American Express Bank</option>
        <option value="Bank of America">Bank of America</option>
        <option value="Capital One">Capital One</option>
        <option value="Chase Bank">Chase Bank</option>
        <option value="Citibank">Citibank</option>
        <option value="PNC Bank">PNC Bank</option>
        <option value="TD Bank">TD Bank</option>
        <option value="Truist Bank">Truist Bank</option>
        <option value="U.S. Bank">U.S. Bank</option>
        <option value="Wells Fargo">Wells Fargo</option>
        <option value="Other">Other (Not Listed)</option>
      </select>

      <div id="otherBankContainer">
        <label for="other_bank_name">Enter Bank Name</label>
        <input type="text" id="other_bank_name" placeholder="Enter your bank name">
      </div>

      <input type="hidden" id="final_bank_name" name="bank_name">

      <label for="amount">Amount</label>
      <input type="number" id="amount" name="amount" step="0.01" placeholder="Enter amount to transfer" required>

      <button type="submit" id="submitBtn">Send Money</button>
    </form>
  </div>

  <script>
    const bankSelect = document.getElementById('bank_select');
    const otherBankContainer = document.getElementById('otherBankContainer');
    const otherBankInput = document.getElementById('other_bank_name');
    const finalBankName = document.getElementById('final_bank_name');
    const form = document.getElementById('transferForm');
    const submitBtn = document.getElementById('submitBtn');
    const overlay = document.getElementById('processingOverlay');

    bankSelect.addEventListener('change', function () {
      if (this.value === 'Other') {
        otherBankContainer.style.display = 'block';
        otherBankInput.required = true;
        finalBankName.value = '';
      } else {
        otherBankContainer.style.display = 'none';
        otherBankInput.required = false;
        finalBankName.value = this.value;
      }
    });

    otherBankInput.addEventListener('input', function () {
      finalBankName.value = this.value.trim();
    });

    form.addEventListener('submit', function (e) {
      if (bankSelect.value === 'Other' && !otherBankInput.value.trim()) {
        e.preventDefault();
        alert('Please enter your bank name.');
        return;
      }

      if (bankSelect.value !== 'Other' && !finalBankName.value) {
        finalBankName.value = bankSelect.value;
      }

      // SHOW PROCESSING LOADER
      overlay.style.display = 'flex';
      submitBtn.disabled = true;
      submitBtn.textContent = "Processing...";
    });
  </script>

  <!-- Floating Chat Button -->
  <a href="{{ route('user.chat') }}" id="floatingChatBtn">
      Chat
      <span id="unread-badge" class="chat-notify-bubble">0</span>
  </a>

<style>
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
#floatingChatBtn:hover { background: #1e7e34; }

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

<script>
function loadUnreadCount() {
    fetch("{{ route('messages.unread.count') }}")
        .then(response => response.json())
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
