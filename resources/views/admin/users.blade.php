@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')

<h2 style="text-align:center; margin-bottom:25px; color:#333;">Manage Users</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="balance">${{ number_format($user->balance, 2) }}</td>
                <td>
                    <button class="btn-edit" onclick="openModal('{{ $user->id }}', '{{ $user->balance }}')">
                        Edit Balance
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal" id="modal">
    <div class="modal-content">
        <h3>Update User Balance</h3>

        <form method="POST" action="" id="balanceForm">
            @csrf
            <input type="hidden" name="user_id" id="user_id">

            <label>New Balance:</label>
            <input type="number" name="balance" id="balance_input" step="0.01" required>

            <button type="submit" class="btn-save">Save</button>
        </form>

        <br>
        <button onclick="closeModal()" style="width:100%; padding:10px; border:none; background:#ccc; border-radius:6px;">
            Cancel
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id, currentBalance) {
        document.getElementById("modal").style.display = "flex";
        document.getElementById("user_id").value = id;
        document.getElementById("balance_input").value = currentBalance;
    }

    function closeModal() {
        document.getElementById("modal").style.display = "none";
    }

    document.getElementById("balanceForm").addEventListener("submit", function() {
        let id = document.getElementById("user_id").value;
        this.action = `/admin/users/${id}/update-balance`;
    });

    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection