<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>

<style>
body{
    font-family:Arial,sans-serif;
    background:#f5f5f5;
}

.container{
    width:100%;
    max-width:400px;
    margin:80px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    color:#1a237e;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    width:100%;
    margin-top:20px;
    padding:12px;
    background:#1a237e;
    color:white;
    border:none;
    border-radius:5px;
    font-weight:bold;
    cursor:pointer;
}

.error{
    background:#ffebee;
    color:#c62828;
    padding:10px;
    border-radius:5px;
    margin-bottom:15px;
    text-align:center;
}

.success{
    background:#e8f5e9;
    color:#2e7d32;
    padding:10px;
    border-radius:5px;
    margin-bottom:15px;
    text-align:center;
}
</style>
</head>

<body>

<div class="container">

<h2>Reset Password</h2>

@if(session('error'))
<div class="error">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="success">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('password.update') }}">

    @csrf

    <input type="email"
           name="email"
           placeholder="Enter Email"
           required>

    <input type="password"
           name="password"
           placeholder="New Password"
           required>

    <input type="password"
           name="password_confirmation"
           placeholder="Confirm Password"
           required>

    <button type="submit">
        Change Password
    </button>

</form>

</div>

</body>
</html>