<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f7fb;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .register-card{
            width:430px;
            background:#fff;
            border-radius:15px;
            padding:35px;
            box-shadow:0 10px 25px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>

<div class="register-card">

    <h1 class="text-center mb-4">Register</h1>

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password"
                   name="password"
                   class="form-control">
        </div>

        <div class="mb-4">
            <label>Confirm Password</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control">
        </div>

        <button class="btn btn-primary w-100">
            Register
        </button>

        <div class="text-center mt-3">
            Sudah punya akun?
            <a href="{{ route('login') }}">Login</a>
        </div>

    </form>

</div>

</body>
</html>