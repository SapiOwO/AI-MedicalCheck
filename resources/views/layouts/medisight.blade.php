<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MediSight AI – Camera-based Health Insights')</title>
    <link rel="stylesheet" href="{{ asset('css/medisight.css') }}">
    <style>
        /* Theme Toggle Button */
        .theme-toggle {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(55, 65, 81, 0.9);
            border-radius: 999px;
            padding: 6px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--muted);
            transition: all 0.3s;
        }
        
        .theme-toggle:hover {
            background: rgba(79, 70, 229, 0.2);
            border-color: var(--accent);
        }
        
        .theme-icon {
            font-size: 14px;
        }
        
        /* Light Mode Overrides - Comprehensive Fixes */
        body.light-mode {
            --bg: #f8fafc;
            --bg-soft: #f1f5f9;
            --bg-card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
            color: var(--text);
        }
        
        /* Cards */
        body.light-mode .card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-color: #cbd5e1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        /* Navigation */
        body.light-mode .navbar a,
        body.light-mode .nav-links,
        body.light-mode .nav-links span {
            color: #475569;
        }
        
        body.light-mode .nav-links a:hover {
            color: #1e293b;
        }
        
        body.light-mode .logo span {
            color: #1e293b;
        }
        
        /* Badges and Pills - FIX VISIBILITY */
        body.light-mode .badge {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        body.light-mode .badge-small {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        body.light-mode .hero-metadata span {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        body.light-mode .pill {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        /* Hero Preview */
        body.light-mode .hero-preview {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, #f1f5f9 100%);
            border-color: #cbd5e1;
        }
        
        body.light-mode .hero-preview-inner {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-color: #e2e8f0;
        }
        
        body.light-mode .hero-preview-cam {
            border-color: #e2e8f0;
            background: #f8fafc;
        }
        
        body.light-mode .hero-preview-panel {
            color: #475569;
        }
        
        body.light-mode .hero-preview-header {
            color: #64748b;
        }
        
        body.light-mode .hero-chip {
            background: #ffffff;
            border-color: #e2e8f0;
        }
        
        body.light-mode .hero-chip-label {
            color: #64748b;
        }
        
        body.light-mode .hero-chip-value {
            color: #1e293b;
        }
        
        /* Inputs - FIX VISIBILITY */
        body.light-mode .input,
        body.light-mode .chat-input-row input,
        body.light-mode input[type="text"],
        body.light-mode input[type="email"],
        body.light-mode input[type="password"] {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        
        body.light-mode .input::placeholder,
        body.light-mode input::placeholder {
            color: #94a3b8;
        }
        
        body.light-mode .input:focus,
        body.light-mode input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        
        /* Buttons - FIX VISIBILITY */
        body.light-mode .btn-secondary {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        body.light-mode .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        
        body.light-mode .btn-ghost {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        /* History & Items */
        body.light-mode .history-item {
            background: #ffffff;
            border-color: #e2e8f0;
        }
        
        body.light-mode .history-item:hover {
            background: #f8fafc;
            border-color: #4f46e5;
        }
        
        body.light-mode .history-meta strong {
            color: #1e293b;
        }
        
        /* Chat */
        body.light-mode .metric-pill,
        body.light-mode .chat-status {
            background: #ffffff;
            border-color: #e2e8f0;
            color: #475569;
        }
        
        body.light-mode .chat-messages {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-color: #e2e8f0;
        }
        
        body.light-mode .msg-bot {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #1e293b;
        }
        
        body.light-mode .msg-bot strong {
            color: #4f46e5;
        }
        
        /* Camera */
        body.light-mode .camera-preview {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-color: #cbd5e1;
        }
        
        body.light-mode .camera-footer {
            border-color: #e2e8f0;
            color: #64748b;
        }
        
        /* Theme Toggle */
        body.light-mode .theme-toggle {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }
        
        body.light-mode .logo-mark {
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        
        /* Section Titles */
        body.light-mode .section-title {
            color: #1e293b;
        }
        
        body.light-mode .section-subtitle {
            color: #64748b;
        }
        
        body.light-mode .hero-title {
            color: #1e293b;
        }
        
        body.light-mode .hero-subtitle {
            color: #64748b;
        }
        
        body.light-mode .card-title {
            color: #1e293b;
        }
        
        body.light-mode .card-subtitle {
            color: #64748b;
        }
        
        body.light-mode .text-muted {
            color: #64748b;
        }
        
        body.light-mode .label {
            color: #475569;
        }
        
        body.light-mode .form-footer {
            color: #64748b;
        }
        
        body.light-mode .form-footer a {
            color: #4f46e5;
        }
        
        body.light-mode hr {
            border-color: #e2e8f0;
        }
        
        body.light-mode code {
            background: #e2e8f0;
            color: #4f46e5;
            padding: 2px 6px;
            border-radius: 4px;
        }
        
        /* History Table */
        body.light-mode .history-table th {
            background: #f1f5f9;
            color: #64748b;
        }
        
        body.light-mode .history-table td {
            border-color: #e2e8f0;
            color: #1e293b;
        }
        
        body.light-mode .history-table tr:hover {
            background: rgba(79, 70, 229, 0.05);
        }
        
        body.light-mode .confidence-bar {
            background: #e2e8f0;
        }
        
        /* Smooth transition for theme change */
        body {
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        .card, .input, .btn-secondary, .history-item, .chat-messages, .camera-preview, .badge, .hero-metadata span, .pill {
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
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
                <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                    <span class="theme-icon" id="themeIcon">🌙</span>
                    <span id="themeText">Dark</span>
                </button>
            </nav>
        </header>

        @yield('content')
    </main>

    <script>
        // Theme Toggle Logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        
        // Load saved theme
        const savedTheme = localStorage.getItem('medisight_theme') || 'dark';
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
            themeIcon.textContent = '☀️';
            themeText.textContent = 'Light';
        }
        
        // Toggle theme
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            const isLight = document.body.classList.contains('light-mode');
            
            themeIcon.textContent = isLight ? '☀️' : '🌙';
            themeText.textContent = isLight ? 'Light' : 'Dark';
            localStorage.setItem('medisight_theme', isLight ? 'light' : 'dark');
        });
        
        // Global configuration
        window.APP_CONFIG = {
            apiUrl: '{{ url("/api") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @yield('scripts')
</body>
</html>

