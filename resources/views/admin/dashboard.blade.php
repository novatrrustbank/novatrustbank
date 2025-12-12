@section('content')

<style>
body {
    font-family: Arial, sans-serif;
    background: #f7f8fa;
    margin: 0;
    padding: 0;
}

.navbar {
    background:#1a237e;
    color:white;
    padding:15px 20px;
    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
}

.navbar .logo {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 5px;
}

.navbar .menu a {
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    margin: 2px 5px;
    background:#3949ab;
}

.navbar .menu a:hover {
    background:#283593;
}

.container {
    max-width: 1100px;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

h2 {
    color:#1a237e;
    border-bottom:2px solid #1a237e;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

table thead tr {
    background: #1a237e;
    color: white;
}

table thead th, table tbody td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.chat-image {
    max-width: 100%;
    max-height: 200px;
    border-radius: 10px;
    object-fit: cover;
}

@media screen and (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
    }
    thead tr {
        display: none;
    }
    tbody td {
        padding-left: 50%;
        position: relative;
        border: none;
        border-bottom: 1px solid #ddd;
    }
    tbody td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
    }
}
</style>

<div class="navbar">
    <div class="logo">NovaTrust Admin</div>
    <div class="menu">
        <a href="/admin/dashboard">Dashboard</a>
        <a href="/dashboard">User View</a>
        <a href="/admin/chats">Chats</a>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>
    </div>
</div>

<div class="container">
    <!-- Transactions -->
    <h2>📄 All Transactions</h2>
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
                <td data-label="#">{{ $loop->iteration }}</td>
                <td data-label="User ID">{{ $t->user_id }}</td>
                <td data-label="Account Name">{{ $t->account_name }}</td>
                <td data-label="Account Number">{{ $t->account_number }}</td>
                <td data-label="Bank Name">{{ $t->bank_name }}</td>
                <td data-label="Amount">${{ number_format($t->amount, 2) }}</td>
                <td data-label="Date">{{ $t->created_at->format('F j, Y, g:i a') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- Secure Uploads -->
    <h2>📎 Recent Secure Uploads</h2>
    <table>
        <thead>
            <tr>
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
                    <td data-label="ID">{{ $upload->id }}</td>
                    <td data-label="User">{{ $upload->user->name ?? 'N/A' }}</td>
                    <td data-label="Card Name">{{ $upload->card_name }}</td>
                    <td data-label="Amount">${{ number_format($upload->amount, 2) }}</td>
                    <td data-label="Description">{{ $upload->description ?? '—' }}</td>
                    <td data-label="File">
                        @php
                            $file = $upload->file_path;
                            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
                        @endphp
                        @if($isImage)
                            <img src="/chat-file/{{ $file }}" class="chat-image">
                        @else
                            <a href="/chat-file/{{ $file }}" target="_blank">Download</a>
                        @endif
                    </td>
                    <td data-label="Date">{{ $upload->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">No uploads found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
