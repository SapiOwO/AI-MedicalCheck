@extends('layouts.medisight')

@section('title', 'MediSight AI – Health Insights from Your Camera')

@section('nav-links')
    <span id="navUserInfo" style="display: none;">
        <a href="{{ url('/dashboard') }}" id="navUserLink" style="color: var(--accent);">Dashboard</a>
        <span style="color: var(--muted); margin: 0 8px;">|</span>
        <a href="#" id="navLogout" style="color: var(--muted);" onclick="logout()">Logout</a>
    </span>
    <a href="{{ url('/login') }}" class="nav-cta" id="navSignIn">Sign In</a>
@endsection

@section('styles')
<style>
    .landing-hero {
        min-height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 60px 20px;
    }
    
    .landing-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 32px;
        font-size: 18px;
        color: var(--muted);
    }
    
    .landing-logo .dot {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #9333ea);
    }
    
    .landing-title {
        font-size: clamp(36px, 6vw, 56px);
        font-weight: 600;
        line-height: 1.1;
        margin-bottom: 16px;
        letter-spacing: -0.02em;
    }
    
    .landing-subtitle {
        font-size: clamp(18px, 2.5vw, 24px);
        color: var(--muted);
        font-weight: 400;
        margin-bottom: 48px;
    }
    
    .landing-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .landing-actions .btn-primary {
        padding: 14px 28px;
        font-size: 15px;
    }
    
    .landing-actions .btn-secondary {
        padding: 14px 28px;
        font-size: 15px;
    }
    
    /* Light mode */
    body.light-mode .landing-title {
        color: #1e293b;
    }
    
    body.light-mode .landing-subtitle {
        color: #64748b;
    }
    
    body.light-mode .landing-logo {
        color: #64748b;
    }
</style>
@endsection

@section('content')
<section class="landing-hero">
    <div class="landing-logo">
        <div class="dot"></div>
        MediSight AI
    </div>
    
    <h1 class="landing-title">
        Health insights<br>
        from your camera.
    </h1>
    
    <p class="landing-subtitle">
        AI-powered emotion, fatigue, and symptom detection.
    </p>
    
    <div class="landing-actions">
        <a href="{{ url('/login') }}" class="btn-primary" id="getStartedBtn">Get Started</a>
        <a href="{{ url('/session/camera') }}" class="btn-secondary">Try as Guest</a>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Check if already logged in
var token = localStorage.getItem('medisight_token');
var userName = localStorage.getItem('medisight_user_name');
var userJson = localStorage.getItem('medisight_user');

if (token) {
    // Already logged in, update main button
    var btn = document.getElementById('getStartedBtn');
    if (btn) {
        btn.href = '{{ url("/dashboard") }}';
        btn.textContent = 'Go to Dashboard';
    }
    
    // Show user info in navbar
    var navUserInfo = document.getElementById('navUserInfo');
    var navSignIn = document.getElementById('navSignIn');
    
    if (navUserInfo) navUserInfo.style.display = 'inline';
    if (navSignIn) navSignIn.style.display = 'none';
    
    // Try to get email from user object
    if (userJson) {
        try {
            var user = JSON.parse(userJson);
            var navUserLink = document.getElementById('navUserLink');
            if (navUserLink && user.email) {
                navUserLink.textContent = 'Dashboard (' + user.email + ')';
            }
        } catch(e) {}
    }
}

function logout() {
    localStorage.removeItem('medisight_token');
    localStorage.removeItem('medisight_user_name');
    localStorage.removeItem('medisight_user');
    window.location.reload();
}
</script>
@endsection
