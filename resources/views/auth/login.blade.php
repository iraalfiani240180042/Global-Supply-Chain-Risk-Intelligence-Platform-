<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - Global Supply Chain</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:linear-gradient(135deg,#0f172a,#1e3a8a);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    width:430px;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 20px 45px rgba(0,0,0,.25);
}

.logo{
    width:70px;
    height:70px;
    margin:auto;
    margin-bottom:20px;
    background:#2563eb;
    color:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
}

h2{
    text-align:center;
    margin-bottom:8px;
}

.subtitle{
    text-align:center;
    color:#64748b;
    margin-bottom:30px;
    font-size:14px;
}

input{
    width:100%;
    padding:14px;
    margin-bottom:18px;
    border:1px solid #dbeafe;
    border-radius:10px;
    background:#f8fafc;
    transition:.3s;
    font-size:15px;
}

input:focus{
    outline:none;
    border-color:#2563eb;
    background:white;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:#2563eb;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#1d4ed8;
}

.error{
    color:#dc2626;
    background:#fee2e2;
    padding:10px;
    border-radius:8px;
    margin-bottom:20px;
    text-align:center;
}

.register{
    margin-top:25px;
    text-align:center;
    color:#64748b;
}

.register a{
    color:#2563eb;
    text-decoration:none;
    font-weight:bold;
}

.register a:hover{
    text-decoration:underline;
}

</style>

</head>

<body>

<div class="card">

    <div class="logo">
        🌍
    </div>

    <h2>Global Supply Chain</h2>

    <p class="subtitle">
        Risk Intelligence Platform
    </p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/login" method="POST">

        @csrf

        <input
            type="email"
            name="email"
            placeholder="Email Address"
            required>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="register">
        Don't have an account?
        <a href="{{ route('register') }}">
            Register
        </a>
    </div>

</div>

</body>
</html>