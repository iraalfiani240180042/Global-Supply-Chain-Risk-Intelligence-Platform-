<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite([
        'resources/js/app.js'
    ])
</head>

<body>

<div class="wrapper">

    @include('components.sidebar')

    <div class="main-content">

        @include('components.navbar')

        <main class="content">

            @yield('content')

        </main>

    </div>

</div>

{{-- Semua script dari @push('scripts') akan tampil di sini --}}
@stack('scripts')

</body>
</html>