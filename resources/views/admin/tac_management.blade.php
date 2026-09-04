<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>TAC Management - NovaTrust Bank</title>


<style>

    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
        color: #333;
    }


    .navbar {
        background: #1a237e;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }


    .navbar a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
        font-weight: bold;
    }


    .container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }


    .card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }


    h1,
    h2 {
        color: #1a237e;
    }


    label {
        display: block;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 6px;
    }


    input,
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        box-sizing: border-box;
    }


    button {
        padding: 11px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }


    .create-btn {
        margin-top: 20px;
        background: #1a237e;
        color: white;
        width: 100%;
    }


    .active-btn {
        background: #28a745;
        color: white;
    }


    .inactive-btn {
        background: #dc3545;
        color: white;
    }


    .delete-btn {
        background: #333;
        color: white;
    }


    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }


    .success {
        background: #e8f5e9;
        color: #2e7d32;
    }


    .error {
        background: #ffebee;
        color: #c62828;
    }


    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }


    th,
    td {
        padding: 13px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }


    th {
        background: #1a237e;
        color: white;
    }


    .status-active {
        color: #28a745;
        font-weight: bold;
    }


    .status-inactive {
        color: #dc3545;
        font-weight: bold;
    }


    .status-used {
        color: #777;
        font-weight: bold;
    }


    .actions {
        display: flex;
        gap: 5px;
    }


    .actions form {
        display: inline;
    }


    @media (max-width: 768px) {

        table {
            font-size: 12px;
        }


        th,
        td {
            padding: 8px;
        }

    }

</style>


</head>

<body>

<div class="navbar">


<div>

    <strong>NovaTrust Bank Admin</strong>

</div>


<div>

    <a href="{{ route('admin.dashboard') }}">

        Dashboard

    </a>


    <a href="{{ route('admin.users') }}">

        Users

    </a>

</div>


</div>

<div class="container">


@if(session('success'))

    <div class="alert success">

        {{ session('success') }}

    </div>

@endif


@if($errors->any())

    <div class="alert error">

        @foreach($errors->all() as $error)

            <div>

                {{ $error }}

            </div>

        @endforeach

    </div>

@endif



<!-- CREATE TAC -->

<div class="card">


    <h1>

        🔐 TAC Management

    </h1>


    <p>

        Create and manage Transaction Authorization Codes.

    </p>



    <form
        action="{{ route('admin.tac.create') }}"
        method="POST"
    >

        @csrf


        <label>

            Select User

        </label>


        <select
            name="user_id"
            required
        >

            <option value="">

                -- Select User --

            </option>


            @foreach($users as $user)

                <option value="{{ $user->id }}">

                    {{ $user->name }}

                    ({{ $user->email }})

                </option>

            @endforeach

        </select>



        <label>

            TAC Code

        </label>


        <input
            type="text"
            name="code"
            placeholder="Example: 483921"
            required
        >



        <label>

            Expiration Date & Time

        </label>


        <input
            type="datetime-local"
            name="expires_at"
            required
        >



        <button
            type="submit"
            class="create-btn"
        >

            Create TAC Code

        </button>


    </form>

</div>



<!-- TAC LIST -->

<div class="card">


    <h2>

        TAC Codes

    </h2>


    <div style="overflow-x:auto;">


        <table>


            <thead>

                <tr>

                    <th>ID</th>

                    <th>User</th>

                    <th>TAC Code</th>

                    <th>Expires</th>

                    <th>Status</th>

                    <th>Used At</th>

                    <th>Actions</th>

                </tr>

            </thead>



            <tbody>


                @forelse($tacs as $tac)


                    <tr>


                        <td>

                            {{ $tac->id }}

                        </td>



                        <td>

                            {{ $tac->user->name ?? 'Unknown User' }}

                        </td>



                        <td>

                            <strong>

                                {{ $tac->code }}

                            </strong>

                        </td>



                        <td>

                            {{ $tac->expires_at }}

                        </td>



                        <td>


                            @if($tac->used_at)

                                <span class="status-used">

                                    USED

                                </span>


                            @elseif($tac->is_active)

                                <span class="status-active">

                                    ACTIVE

                                </span>


                            @else

                                <span class="status-inactive">

                                    INACTIVE

                                </span>

                            @endif


                        </td>



                        <td>

                            {{ $tac->used_at ?? '-' }}

                        </td>



                        <td>


                            <div class="actions">


                                @if(!$tac->used_at)


                                    <form
                                        action="{{ route('admin.tac.toggle', $tac->id) }}"
                                        method="POST"
                                    >

                                        @csrf


                                        <button
                                            type="submit"
                                            class="{{ $tac->is_active ? 'inactive-btn' : 'active-btn' }}"
                                        >

                                            {{ $tac->is_active ? 'Deactivate' : 'Activate' }}

                                        </button>


                                    </form>


                                @endif



                                <form
                                    action="{{ route('admin.tac.delete', $tac->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this TAC Code?')"
                                >

                                    @csrf


                                    <button
                                        type="submit"
                                        class="delete-btn"
                                    >

                                        Delete

                                    </button>


                                </form>


                            </div>


                        </td>


                    </tr>


                @empty


                    <tr>

                        <td
                            colspan="7"
                            style="text-align:center;"
                        >

                            No TAC codes created yet.

                        </td>

                    </tr>


                @endforelse


            </tbody>


        </table>


    </div>


</div>

</div>

</body>

</html>
