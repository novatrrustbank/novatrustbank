@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h2>Edit User: {{ $user->name }}</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-2">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">

            <form action="{{ route('admin.updateUserName') }}" method="POST">
                @csrf

                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="mb-3">
                    <label class="form-label">Current Name</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Name
                </button>

            </form>

        </div>
    </div>

</div>

@endsection
