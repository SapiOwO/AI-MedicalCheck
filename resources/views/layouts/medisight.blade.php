<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MediSight AI – Camera-based Health Insights')</title>
    <link rel="stylesheet" href="{{ asset('css/medisight.css') }}">
    @yield('styles')
</head>
<body>
    <main class="page-shell">
        <header class="navbar">
            <a href="{{ url('/') }}" class="logo">
                <div class="logo-mark"></div>
                <span>MediSight AI</span>
            </a>
            <nav class="nav-links">
                @yield('nav-links')
            </nav>
        </header>

        @yield('content')
    </main>

    <script>
        // Global configuration
        window.APP_CONFIG = {
            apiUrl: '{{ url("/api") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @yield('scripts')
</body>
</html>
