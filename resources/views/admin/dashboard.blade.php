@extends('layouts.admin')

@section('content')

<style>
.chat-image {
    max-width: 250px;
    max-height: 250px;
    border-radius: 10px;
    object-fit: cover;
}
</style>


    <!-- ********  ALL TRANSACTIONS  ******** -->
    <h2 style="color:#1a237e; border-bottom:2px solid #1a237e; padding-bottom:8px; margin-bottom:25px;">
        📄 All Transactions
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
        📎 Recent Secure Uploads
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

                    <!-- FIXED: Same Working Chat Image Path -->
                    <td>
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
