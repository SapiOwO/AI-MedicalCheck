<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI Medical Chatbot')</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Navigation Bar */
        .dev-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-brand {
            color: white;
            font-size: 18px;
            font-weight: bold;
            margin-right: 30px;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .nav-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            white-space: nowrap;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .nav-btn.active {
            background: rgba(255, 255, 255, 0.4);
            border-color: white;
        }

        .nav-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: white;
            font-size: 12px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ade80;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Content Area */
        .content {
            padding: 20px;
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Developer Navigation -->
    <nav class="dev-navbar">
        <div class="nav-container">
            <div class="nav-brand">🏥 AI Medical Chatbot - Dev Mode</div>
            
            <div class="nav-links">
                <a href="{{ url('/') }}" class="nav-btn {{ Request::is('/') ? 'active' : '' }}">
                    🏠 Home
                </a>
                <a href="{{ url('/health') }}" class="nav-btn {{ Request::is('health') ? 'active' : '' }}">
                    ❤️ Health
                </a>
                <a href="{{ url('/test-simple') }}" class="nav-btn {{ Request::is('test-simple') ? 'active' : '' }}">
                    🧪 Test Simple
                </a>
                <a href="{{ url('/medical-check') }}" class="nav-btn {{ Request::is('medical-check') ? 'active' : '' }}">
                    📹 Medical Check
                </a>
                <a href="{{ url('/api/health') }}" class="nav-btn" target="_blank">
                    🔌 API Health
                </a>
            </div>

            <div class="nav-status">
                <span class="status-dot"></span>
                <span>Laravel Active</span>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="content">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
