@extends('layouts.dev')

@section('title', 'Home - AI Medical Chatbot')

@section('styles')
<style>
    .home-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .hero {
        text-align: center;
        margin-bottom: 50px;
    }

    .hero h1 {
        font-size: 42px;
        color: #667eea;
        margin-bottom: 10px;
    }

    .hero p {
        font-size: 18px;
        color: #666;
    }

    .nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .nav-card {
        padding: 30px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .nav-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border-color: #667eea;
    }

    .nav-card h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #667eea;
    }

    .nav-card p {
        font-size: 14px;
        color: #555;
        margin: 0;
    }

    .status-banner {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
        padding: 20px;
        background: #f0f4ff;
        border-radius: 8px;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-icon {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .status-ok {
        background: #4ade80;
    }

    .status-pending {
        background: #fb923c;
    }
</style>
@endsection

@section('content')
<div class="home-container">
    <div class="hero">
        <h1>🏥 AI Medical Chatbot</h1>
        <p>Emotion Detection & Medical Assistance</p>
    </div>

    <div class="nav-grid">
        <a href="{{ url('/health') }}" class="nav-card">
            <h3>❤️ System Health</h3>
            <p>Check if all services are running properly</p>
        </a>

        <a href="{{ url('/test-simple') }}" class="nav-card">
            <h3>🧪 Test Simple</h3>
            <p>Basic testing page with camera and API tests</p>
        </a>

        <a href="{{ url('/medical-check') }}" class="nav-card">
            <h3>📹 Medical Check</h3>
            <p>Full realtime emotion detection interface</p>
        </a>

        <a href="{{ url('/api/health') }}" class="nav-card" target="_blank">
            <h3>🔌 API Health</h3>
            <p>Direct Python AI service health check</p>
        </a>
    </div>

    <div class="status-banner">
        <div class="status-item">
            <span class="status-icon status-ok"></span>
            <span>Laravel Server</span>
        </div>
        <div class="status-item">
            <span class="status-icon status-ok"></span>
            <span>Python API</span>
        </div>
        <div class="status-item">
            <span class="status-icon status-ok"></span>
            <span>Emotion Model</span>
        </div>
        <div class="status-item">
            <span class="status-icon status-pending"></span>
            <span>Fatigue Model (Coming Soon)</span>
        </div>
        <div class="status-item">
            <span class="status-icon status-pending"></span>
            <span>Pain Model (Coming Soon)</span>
        </div>
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 4px;">
        <h4 style="margin: 0 0 10px 0; color: #92400e;">💡 Quick Start</h4>
        <ol style="margin: 0; padding-left: 20px; color: #78350f;">
            <li>Make sure both services are running (use <code>start-services.bat</code>)</li>
            <li>Test the system health first</li>
            <li>Try the simple test page for basic functionality</li>
            <li>Use medical check for full realtime detection</li>
        </ol>
    </div>
</div>
@endsection
