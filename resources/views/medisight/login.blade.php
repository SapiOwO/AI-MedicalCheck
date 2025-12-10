@extends('layouts.medisight')

@section('title', 'MediSight AI – Login')

@section('nav-links')
    <a href="{{ url('/') }}">Back to landing</a>
    <a href="{{ url('/register') }}">Create account</a>
@endsection

@section('content')
<section class="auth-layout">
    <div>
        <div class="badge">
            <div class="badge-dot"></div>
            Step 1 · Sign in or continue as guest
        </div>
        <h1 class="hero-title">Welcome back.</h1>
        <p class="hero-subtitle">
            Sign in to access your previous MediSight AI sessions, or try it
            once as a guest to see how camera detection and chat work together.
        </p>

        <ul class="hero-metadata" style="margin-top: 12px">
            <span>Save chat history</span>
            <span>Compare emotion/fatigue over time</span>
            <span>Guest mode for quick demos</span>
        </ul>
    </div>

    <div class="card" aria-label="Login form">
        <div class="card-header">
            <h2 class="card-title">Sign in</h2>
            <p class="card-subtitle">
                Use your email account to access saved sessions.
            </p>
        </div>

        <div id="errorMessage" class="card-subtitle" style="color: #ef4444; display: none; margin-bottom: 12px;"></div>

        <form class="form-grid" id="loginForm">
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" class="input" placeholder="you@example.com" required>
            </div>

            <div>
                <label class="label" for="password">Password</label>
                <input id="password" type="password" class="input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary" id="loginBtn">Login</button>
        </form>

        <div class="form-footer">
            <span>
                New here?
                <a href="{{ url('/register') }}">Create account</a>
            </span>
            <a href="{{ url('/reset-password') }}" style="color: #94a3b8;">Forgot password?</a>
        </div>

        <hr style="border-color: rgba(31, 41, 55, 0.9); margin: 16px 0">

        <p class="card-subtitle" style="margin-bottom: 10px">
            Just want to try the models once?
        </p>

        <a href="{{ url('/chat') }}" class="btn-secondary" style="width: 100%" id="guestBtn">
            Continue as guest
        </a>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const loginBtn = document.getElementById('loginBtn');
    const errorMsg = document.getElementById('errorMessage');
    
    loginBtn.textContent = 'Signing in...';
    loginBtn.disabled = true;
    errorMsg.style.display = 'none';

    try {
        const response = await fetch('{{ url("/api/login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Store token in localStorage
            localStorage.setItem('medisight_token', data.data.token);
            localStorage.setItem('medisight_user', JSON.stringify(data.data.user));
            localStorage.setItem('medisight_user_name', data.data.user.name); // For navbar
            
            // Check if there's pending guest session to save
            var pendingData = localStorage.getItem('pending_session_data');
            if (pendingData) {
                try {
                    var sessionData = JSON.parse(pendingData);
                    
                    // Create new session for this user
                    var sessionRes = await fetch('{{ url("/api/chat/session/start") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + data.data.token
                        },
                        body: JSON.stringify({ current_step: 'profile' })
                    });
                    var sessionResult = await sessionRes.json();
                    
                    if (sessionResult.success) {
                        var newSessionId = sessionResult.data.session.id;
                        sessionData.session_id = newSessionId;
                        
                        // Save the session data
                        await fetch('{{ url("/api/session/update") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + data.data.token
                            },
                            body: JSON.stringify(sessionData)
                        });
                        
                        // Save pending chat messages
                        var pendingMessages = localStorage.getItem('pending_chat_messages');
                        if (pendingMessages) {
                            var messages = JSON.parse(pendingMessages);
                            for (var i = 0; i < messages.length; i++) {
                                await fetch('{{ url("/api/chat/message/store") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'Authorization': 'Bearer ' + data.data.token
                                    },
                                    body: JSON.stringify({
                                        session_id: newSessionId,
                                        sender: messages[i].sender,
                                        message: messages[i].message
                                    })
                                });
                            }
                            localStorage.removeItem('pending_chat_messages');
                        }
                    }
                    
                    localStorage.removeItem('pending_session_data');
                    alert('Your guest session has been saved to your account!');
                } catch(e) {
                    console.error('Failed to save pending session:', e);
                }
            }
            
            // Redirect to dashboard
            window.location.href = '{{ url("/dashboard") }}';
        } else {
            errorMsg.textContent = data.message || 'Login failed. Please check your credentials.';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorMsg.textContent = 'Connection error. Please try again.';
        errorMsg.style.display = 'block';
    } finally {
        loginBtn.textContent = 'Login';
        loginBtn.disabled = false;
    }
});

// Check if there's a pending session from guest
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('redirect') === 'save_session') {
    document.getElementById('errorMessage').style.display = 'block';
    document.getElementById('errorMessage').style.color = '#22c55e';
    document.getElementById('errorMessage').textContent = 'Please login or create an account to save your session data.';
}

// Check if already logged in
if (localStorage.getItem('medisight_token')) {
    window.location.href = '{{ url("/dashboard") }}';
}
</script>
@endsection
