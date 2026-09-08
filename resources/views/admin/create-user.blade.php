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

    <div style="position:relative;">
        <input
            name="password"
            id="password"
            type="password"
            class="form-control"
            required
            style="padding-right:45px;"
        >

        <span
            onclick="togglePassword()"
            style="
                position:absolute;
                right:12px;
                top:50%;
                transform:translateY(-50%);
                cursor:pointer;
                font-size:18px;
                color:#666;
            "
            id="passwordEye"
        >
            👁️
        </span>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const eye = document.getElementById('passwordEye');

    if (password.type === 'password') {
        password.type = 'text';
        eye.textContent = '🙈';
    } else {
        password.type = 'password';
        eye.textContent = '👁️';
    }
}
</script>

        <button class="btn btn-success">Create User</button>
    </form>

</div>
@endsection
