@extends('layouts.app')

@section('content')

<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .welcome {
        font-size: 20px;
        color: #1a237e;
        font-weight: bold;
        text-align: center;
    }

    .card {
        background-color: #1a237e;
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin-top: 25px;
    }

    .card h3 {
        margin: 5px 0;
        font-weight: normal;
        font-size: 20px;
    }

    .card h2 {
        font-size: 26px;
        margin: 5px 0;
    }

    .balance {
        font-size: 32px;
        font-weight: bold;
        margin-top: 10px;
    }

    .actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .actions a {
        text-decoration: none;
        background-color: #1a237e;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.3s;
    }

    .actions a:hover {
        background-color: #0d1b63;
    }

    .chat-btn {
        background: #28a745 !important;
        color: white !important;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s;
    }

    .chat-btn:hover {
        background: #1e7e34 !important;
    }

    .instruction-btn {
        display: inline-block;
        margin: 20px auto 0;
        padding: 12px 18px;
        background: #d32f2f;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        transition: 0.3s;
    }

    .instruction-btn:hover {
        background: #b71c1c;
        transform: scale(1.03);
    }

    .instruction-wrapper {
        text-align: center;
    }

    /* PASSPORT PHOTO */
    .passport-photo{
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        margin: 0 auto 15px;
        display: block;
        background: white;
    }
</style>

<div class="welcome">
    {{ __('messages.welcome_back') }}, {{ Auth::user()->name }}
</div>

<div class="card">

```
@if(Auth::user()->passport_photo)

    <img src="{{ Auth::user()->passport_photo }}"
         class="passport-photo"
         alt="{{ __('messages.passport') }}">

@endif

<h3>{{ __('messages.account_number') }}</h3>

<h2>
    {{ Auth::user()->account_number }}
</h2>

<h3>{{ __('messages.current_balance') }}</h3>

<div class="balance">
    ${{ number_format(Auth::user()->balance, 2) }}
</div>
```

</div>

@if(session('last_transaction_id'))

<div class="instruction-wrapper">

```
<a href="{{ route('transfer.success') }}"
   class="instruction-btn">

    ⚠ {{ __('messages.view_transfer_instruction') }}

</a>
```

</div>

@endif

<div class="actions">

```
<a href="/transfer">
    {{ __('messages.make_transfer') }}
</a>

<a href="/history">
    {{ __('messages.view_history') }}
</a>

<a href="{{ route('user.chat') }}"
   class="chat-btn">

    {{ __('messages.direct_chat') }}

</a>
```

</div>

<section style="
    background: linear-gradient(135deg, #1a237e, #283593);
    color: #fff;
    text-align: center;
    padding: 40px 20px;
    margin-top: 60px;
    border-top: 5px solid #3949ab;
    border-radius: 10px;
">

```
<h2 style="font-size: 26px; margin-bottom: 10px;">
    {{ __('messages.contact_novatrust_bank') }}
</h2>

<p style="font-size: 16px; margin: 5px 0;">
    <strong>
        {{ __('messages.washington_dc_usa') }}, E-mail:
        infonovatrustbank@accountant.com
    </strong>
</p>

<p style="font-size: 16px; margin: 5px 0;">
    <strong>{{ __('messages.tel') }}:</strong>
    <a href="tel:+19793982810"
       style="color: #ffeb3b; text-decoration: none;">
        +1 979-398-2810
    </a>
</p>

<p style="font-size: 14px; color: #c5cae9; margin-top: 20px;">
    © {{ date('Y') }} NovaTrust Bank.
    {{ __('messages.all_rights_reserved') }}
</p>
```

</section>

@endsection
