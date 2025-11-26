
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

    .table-container {
        width: 100%;
        overflow-x: auto;
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

    .btn-edit {
        background: #1a73e8;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #0059c1;
    }

    /* Popup modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        width: 90%;
        max-width: 400px;
        border-radius: 10px;
    }

    .modal-content h3 {
        margin-top: 0;
    }

    input[type="number"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .btn-save {
        background: #0a7a2d;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
    }

    .btn-save:hover {
        background: #086621;
    }

<div class="container mt-4">

    <h2>Users</h2>

    <a href="{{ route('admin.createUserPage') }}" class="btn btn-success mb-3">Create User</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Phone</th><th>Balance</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>${{ $user->balance }}</td>
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

</div>
@endsection