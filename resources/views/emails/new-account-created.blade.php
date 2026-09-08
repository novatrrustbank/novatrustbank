<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to NovaTrust Bank</title>
</head>

<body style="font-family:Arial, sans-serif; background:#f5f7fa; padding:30px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:white;
        padding:30px;
        border-radius:10px;
        box-shadow:0 3px 10px rgba(0,0,0,0.1);
    ">

        <h2 style="color:#1a237e;">
            Welcome to NovaTrust Bank
        </h2>

        <p>
            Hello {{ $user->name }},
        </p>

        <p>
            Your NovaTrust Bank account has been successfully created
            and is now ready to use.
        </p>

        <div style="
            background:#f1f3f9;
            padding:20px;
            border-radius:8px;
            margin:20px 0;
        ">

            <p>
                <strong>Account Name:</strong>
                {{ $user->name }}
            </p>

            <p>
                <strong>Account Number:</strong>
                {{ $user->account_number }}
            </p>

            <p>
                <strong>Currency:</strong>
                {{ $user->currency ?? 'USD' }}
            </p>

            <p>
                <strong>Available Balance:</strong>
                {{ $user->currency === 'EUR' ? '€' : ($user->currency === 'GBP' ? '£' : '$') }}{{ number_format($user->balance, 2) }}
            </p>

        </div>

        <p>
            You can now log in to your account using the credentials
            provided to you separately.
        </p>

        <p>
            <a href="{{ url('/login') }}"
               style="
                   display:inline-block;
                   background:#1a237e;
                   color:white;
                   padding:12px 20px;
                   text-decoration:none;
                   border-radius:5px;
                   font-weight:bold;
               ">
                Login to Your Account
            </a>
        </p>

        <p style="margin-top:30px; color:#777;">
            For your security, please do not share your login credentials
            with anyone.
        </p>

        <p>
            Regards,<br>
            <strong>NovaTrust Bank</strong>
        </p>

    </div>

</body>
</html>
