@extends('layouts.app')

@section('content')
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