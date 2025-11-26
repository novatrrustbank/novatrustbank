@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2>Edit User: {{ $user->name }}</h2>

    <form action="{{ route('admin.updateUser') }}" method="POST">
        @csrf

        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Balance</label>
            <input type="number" step="0.01" name="balance" value="{{ $user->balance }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>New Password (optional)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button class="btn btn-primary">Update</button>

    </form>

</div>
@endsection
