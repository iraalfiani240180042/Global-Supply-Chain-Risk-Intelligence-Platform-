<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Supply Chain')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body>

<div class="app">

    @include('components.sidebar')

    <div class="main-content">

        @include('components.navbar')

        <div class="content">

            @yield('content')

        </div>

    </div>

</div>

</body>
</html>