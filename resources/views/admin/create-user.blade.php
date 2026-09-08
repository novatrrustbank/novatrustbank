```blade
@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <h2>Create User</h2>

    <form action="{{ route('admin.storeUser') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Balance</label>
            <input name="balance" type="number" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Currency</label>

            <select name="currency" class="form-control" required>
                <option value="USD" selected>
                    USD - US Dollar ($)
                </option>

                <option value="EUR">
                    EUR - Euro (€)
                </option>

                <option value="GBP">
                    GBP - British Pound (£)
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>

        <button class="btn btn-success">Create User</button>
    </form>

</div>
@endsection
```
