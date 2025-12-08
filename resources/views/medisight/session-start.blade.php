@extends('layouts.medisight')

@section('title', 'MediSight AI – Start New Session')

@section('nav-links')
    <a href="{{ url('/') }}">Landing</a>
    <a href="{{ url('/dashboard') }}">Dashboard</a>
@endsection

@section('content')
<section style="max-width: 600px; margin: 0 auto; text-align: center;">
    <div class="badge">
        <div class="badge-dot"></div>
        Step 1 · Start your health assessment
    </div>

    <h1 class="hero-title" style="font-size: 32px; margin-bottom: 16px;">
        Health Check Session
    </h1>
    <p class="hero-subtitle" style="max-width: 500px; margin: 0 auto 32px;">
        We'll use your camera to detect facial expressions, then our AI will 
        ask you some health questions to provide personalized insights.
    </p>

    <div class="card" style="text-align: left; margin-bottom: 24px;">
        <div class="card-header">
            <h2 class="card-title">What happens next?</h2>
        </div>
        
        <div class="history-list">
            <div class="history-item" style="cursor: default;">
                <div class="history-meta">
                    <strong>📸 Camera Detection</strong>
                    <span class="text-muted">
                        Take a photo for AI analysis (emotion, fatigue, pain)
                    </span>
                </div>
                <span class="pill">Step 2</span>
            </div>
            
            <div class="history-item" style="cursor: default;">
                <div class="history-meta">
                    <strong>📋 Profile Review</strong>
                    <span class="text-muted">
                        Review detected data and answer health questions
                    </span>
                </div>
                <span class="pill">Step 3</span>
            </div>
            
            <div class="history-item" style="cursor: default;">
                <div class="history-meta">
                    <strong>💬 AI Chat</strong>
                    <span class="text-muted">
                        Get personalized health advice based on your profile
                    </span>
                </div>
                <span class="pill">Step 4</span>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <a href="{{ url('/session/camera') }}" class="btn-primary" style="font-size: 16px; padding: 14px 28px;">
            🚀 Start Session
        </a>
        
        <a href="{{ url('/dashboard') }}" class="btn-ghost">
            ← Back to Dashboard
        </a>
    </div>

    <p class="text-muted" style="margin-top: 24px; font-size: 12px;">
        🔒 Your data is processed securely. Images are not stored permanently.
    </p>
</section>
@endsection
