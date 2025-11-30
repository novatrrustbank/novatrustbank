@extends('layouts.app')

@section('content')

<div class="navbar" style="background:#1a237e; color:white; padding:15px 30px; display:flex; justify-content:space-between;">
    <div class="logo" style="font-size:22px; font-weight:bold;">NovaTrust Admin</div>
    <div class="menu">
        <a href="/admin/dashboard" style="color:white; margin-right:20px;">Dashboard</a>
        <a href="/dashboard" style="color:white; margin-right:20px;">User View</a>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="color:white;">Logout</a>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>
    </div>
</div>


<div class="container" style="max-width:1100px; margin:40px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.1);">

    <!-- Admin Balance Options -->
    <div style="display:flex; gap:20px; margin-bottom:25px;">
        <a href="/admin/users"
           style="background:#1a237e; color:white; padding:10px 20px; border-radius:6px; text-decoration:none;">
            User Edit
        </a>

        <a href="/admin/activation-balances"
           style="background:#01579b; color:white; padding:10px 20px; border-radius:6px; text-decoration:none;">
            Users Activation Balance
        </a>
    </div>

    <!-- ********  ALL TRANSACTIONS  ******** -->
    <h2 style="color:#1a237e; border-bottom:2px solid #1a237e; padding-bottom:8px; margin-bottom:25px;">
        ?? All Transactions
    </h2>

    @if($transactions->isEmpty())
        <p>No transactions found.</p>
    @else
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#1a237e; color:white;">
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
            <tr style="border-bottom:1px solid #eee;">
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



    <!-- ********  RECENT SECURE UPLOADS  ******** -->
    <h2 style="color:#1a237e; border-bottom:2px solid #1a237e; padding-bottom:8px; margin-top:40px;">
        ?? Recent Secure Uploads
    </h2>

    <table style="width:100%; border-collapse:collapse;">
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
                <tr style="border-bottom:1px solid #eee;">
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

</div>

@endsection
