@extends('layouts.app')

@section('content')

<title>Manage Users - NovaTrust Bank Admin</title>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f7f9fc;
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    th {
        background: #1a73e8;
        color: white;
        font-size: 14px;
    }

    tr:hover {
        background: #f1f5ff;
    }

    .balance {
        font-weight: bold;
        color: #0a7a2d;
    }

    .btn-primary, .btn-danger, .btn-success {
        padding: 6px 12px;
        border-radius: 5px;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-primary { background: #1a73e8; }
    .btn-danger { background: #d93025; }
    .btn-success { background: #0a7a2d; }
</style>

<h2>Users</h2>

<a href="{{ route('admin.createUserPage') }}" class="btn btn-success mb-3">Create User</a>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Balance</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="balance">${{ number_format($user->balance, 2) }}</td>

            <td>
                <a href="{{ route('admin.editUserPage', $user->id) }}" class="btn btn-primary btn-sm">Edit</a>

                <form action="{{ route('admin.deleteUser') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection