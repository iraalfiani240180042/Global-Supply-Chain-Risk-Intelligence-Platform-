<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Semua script dari @push('scripts') akan tampil di sini --}}
@stack('scripts')

</body>
</html>