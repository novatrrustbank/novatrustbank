@extends('layouts.admin')

@section('content')

<div style="padding:20px;">

    <h2 style="
        color:#1a237e;
        border-bottom:2px solid #1a237e;
        padding-bottom:10px;
        margin-bottom:25px;
    ">
        User History Management
    </h2>

    @if($users->isEmpty())

        <p>No users found.</p>

    @else

    <table style="
        width:100%;
        border-collapse:collapse;
        background:white;
    ">

        <thead>
            <tr style="
                background:#1a237e;
                color:white;
            ">
                <th style="padding:12px;">ID</th>
                <th style="padding:12px;">Name</th>
                <th style="padding:12px;">Email</th>
                <th style="padding:12px;">Balance</th>
                <th style="padding:12px;">Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach($users as $user)

            <tr style="border-bottom:1px solid #eee;">

                <td style="padding:12px;">
                    {{ $user->id }}
                </td>

                <td style="padding:12px;">
                    {{ $user->name }}
                </td>

                <td style="padding:12px;">
                    {{ $user->email }}
                </td>

                <td style="padding:12px;">
                    ${{ number_format($user->balance, 2) }}
                </td>

                <td style="padding:12px;">

                    <a href="{{ url('/admin/history/'.$user->id) }}"
                       style="
                            background:#1a237e;
                            color:white;
                            padding:8px 15px;
                            border-radius:5px;
                            text-decoration:none;
                            font-weight:bold;
                       ">
                        Edit History
                    </a>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    @endif

</div>

@endsection