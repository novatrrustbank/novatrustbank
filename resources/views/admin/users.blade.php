@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary">Manage User Balances</h2>

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Current Balance ($)</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>${{ number_format($user->balance, 2) }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.updateBalance', $user->id) }}" class="d-flex gap-2">
                        @csrf
                        <input type="number" name="amount" step="0.01" min="0" placeholder="Enter amount" class="form-control" required />
                </td>
                <td>
                        <input type="hidden" name="action" value="add" id="action-field-{{ $user->id }}">
                        <button type="submit" onclick="document.getElementById('action-field-{{ $user->id }}').value='add'" class="btn btn-success btn-sm">+ Add</button>
                        <button type="submit" onclick="document.getElementById('action-field-{{ $user->id }}').value='deduct'" class="btn btn-danger btn-sm">− Deduct</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
