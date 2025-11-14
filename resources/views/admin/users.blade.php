@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Manage User Balances</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Balance ($)</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <form method="POST" action="{{ route('admin.updateBalance', $user->id) }}">
                    @csrf
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <input type="number" name="balance" value="{{ $user->balance }}" step="0.01" class="form-control" />
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm">Update</button>
                    </td>
                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection