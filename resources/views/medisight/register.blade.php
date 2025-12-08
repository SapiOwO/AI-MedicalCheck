@extends('layouts.medisight')

@section('title', 'MediSight AI – Create Account')

@section('nav-links')
    <a href="{{ url('/') }}">Back to landing</a>
    <a href="{{ url('/login') }}">Sign in</a>
@endsection

@section('content')
<section class="auth-layout">
    <div>
        <div class="badge">
            <div class="badge-dot"></div>
            Step 1 · Create your account
        </div>
        <h1 class="hero-title">Join MediSight AI.</h1>
        <p class="hero-subtitle">
            Create an account to save your chat history, track emotions over time,
            and get personalized health insights from our AI assistant.
        </p>

        <ul class="hero-metadata" style="margin-top: 12px">
            <span>Save chat history</span>
            <span>Track emotion changes</span>
            <span>Export reports</span>
        </ul>
    </div>

    <div class="card" aria-label="Register form">
        <div class="card-header">
            <h2 class="card-title">Create account</h2>
            <p class="card-subtitle">
                Fill in your details to get started.
            </p>
        </div>

        <div id="successMessage" style="padding: 12px; background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; border-radius: 12px; color: #22c55e; display: none; margin-bottom: 12px;"></div>
        <div id="errorMessage" class="card-subtitle" style="color: #ef4444; display: none; margin-bottom: 12px;"></div>

        <form class="form-grid" id="registerForm">
            <div>
                <label class="label" for="name">Full Name</label>
                <input id="name" type="text" class="input" placeholder="John Doe" required>
            </div>

            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" class="input" placeholder="you@example.com" required>
            </div>

            <div>
                <label class="label" for="password">Password</label>
                <input id="password" type="password" class="input" placeholder="Min 8 characters" required minlength="8">
            </div>

            <div>
                <label class="label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" class="input" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn-primary" id="registerBtn">Create Account</button>
        </form>

        <div class="form-footer" style="margin-top: 16px">
            <span>Already have an account? <a href="{{ url('/login') }}">Sign in</a></span>
        </div>

        <hr style="border-color: rgba(31, 41, 55, 0.9); margin: 16px 0">

        <p class="card-subtitle" style="margin-bottom: 10px">Just want to try the models once?</p>

        <a href="{{ url('/chat') }}" class="btn-secondary" style="width: 100%">Continue as guest</a>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const registerBtn = document.getElementById('registerBtn');
    const errorMsg = document.getElementById('errorMessage');
    const successMsg = document.getElementById('successMessage');
    
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password !== confirmPassword) {
        errorMsg.textContent = 'Passwords do not match.';
        errorMsg.style.display = 'block';
        successMsg.style.display = 'none';
        return;
    }
    
    registerBtn.textContent = 'Creating account...';
    registerBtn.disabled = true;
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';

    try {
        const response = await fetch('{{ url("/api/register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: password,
                password_confirmation: confirmPassword
            })
        });

        const data = await response.json();
        
        if (data.success) {
            successMsg.textContent = 'Account created! Redirecting to login...';
            successMsg.style.display = 'block';
            
            // Check if there's pending session - redirect to login with param
            var pendingData = localStorage.getItem('pending_session_data');
            if (pendingData) {
                setTimeout(() => { window.location.href = '{{ url("/login") }}?redirect=save_session'; }, 2000);
            } else {
                setTimeout(() => { window.location.href = '{{ url("/login") }}'; }, 2000);
            }
        } else {
            let errorText = data.message || 'Registration failed.';
            if (data.errors) { errorText = Object.values(data.errors).flat().join(' '); }
            errorMsg.textContent = errorText;
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('Register error:', error);
        errorMsg.textContent = 'Connection error. Please try again.';
        errorMsg.style.display = 'block';
    } finally {
        registerBtn.textContent = 'Create Account';
        registerBtn.disabled = false;
    }
});

// Check if there's a pending session from guest
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('redirect') === 'save_session' || localStorage.getItem('pending_session_data')) {
    document.getElementById('successMessage').style.display = 'block';
    document.getElementById('successMessage').textContent = 'Create an account to save your session data permanently.';
}

if (localStorage.getItem('medisight_token')) { window.location.href = '{{ url("/dashboard") }}'; }
</script>
@endsection
