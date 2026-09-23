<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SAKALA — Sistem Akademik, Kampus Aman, Layanan Aspirasi">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SAKALA') — Sistem Akademik Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 p-0 antialiased font-sans">
    <div class="login-bg min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
