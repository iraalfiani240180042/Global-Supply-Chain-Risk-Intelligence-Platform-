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
            background:#0f172a;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .card{
            width:380px;
            background:white;
            padding:35px;
            border-radius:15px;
            box-shadow:0 10px 30px rgba(0,0,0,.3);
        }

        h2{
            text-align:center;
            margin-bottom:25px;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border-radius:8px;
            border:1px solid #ddd;
        }

        button{
            width:100%;
            padding:12px;
            border:none;
            background:#2563eb;
            color:white;
            border-radius:8px;
            cursor:pointer;
        }

        button:hover{
            background:#1d4ed8;
        }

        .error{
            color:red;
            margin-bottom:15px;
        }

    </style>
</head>
<body>

<div class="card">

    <h2>Global Supply Chain</h2>

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
            placeholder="Email"
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

</div>
<div class="text-center mt-3">

    Don't have an account?

    <a href="{{ route('register') }}">
        Register
    </a>

</div>
</body>
</html>