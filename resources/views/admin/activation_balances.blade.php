@extends('layouts.app')

@section('content')

<div style="max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:10px;">
    <h2>User Activation Balances</h2>

    @if(session('success'))
        <p style="color:green; font-weight:bold;">{{ session('success') }}</p>
    @endif

    <table width="100%" border="1" cellpadding="8" style="border-collapse:collapse;">
        <tr style="background:#1a237e; color:white;">
            <th>ID</th>
            <th>Name</th>
            <th>Activation Amount</th>
            <th>Action</th>
        </tr>

        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>
                    {{ number_format(\App\Helpers\ActivationBalanceHelper::get($user->id), 2) }}

                </td>
                <td>
                    <form method="POST" action="/admin/activation-balances/update">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="number" name="amount" value="{{ \App\Helpers\ActivationBalanceHelper::get($user->id) }}" required>
                        <button type="submit">Save</button>
                    </form>
                </td>
            </tr>
        @endforeach

    </table>
</div>

@endsection
