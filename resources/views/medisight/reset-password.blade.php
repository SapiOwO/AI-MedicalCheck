@extends('layouts.medisight')

@section('title', 'MediSight AI – Reset Password')

@section('nav-links')
    <a href="{{ url('/') }}">Landing</a>
    <a href="{{ url('/login') }}">Back to login</a>
@endsection

@section('content')
<section class="auth-layout">
    <div>
        <div class="badge">
            <div class="badge-dot"></div>
            Account Recovery
        </div>
        <h1 class="hero-title">Reset your password.</h1>
        <p class="hero-subtitle">
            Enter your registered email and old password to verify your identity,
            then create a new password.
        </p>

        <ul class="hero-metadata" style="margin-top: 12px">
            <span>No email verification required</span>
            <span>Verify with old password</span>
            <span>Instant password update</span>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Reset Password</h2>
            <p class="card-subtitle">
                You'll need to remember your old password to reset it.
            </p>
        </div>

        <div id="successMessage" style="padding: 12px; background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; border-radius: 12px; color: #22c55e; display: none; margin-bottom: 12px;"></div>
        <div id="errorMessage" class="card-subtitle" style="color: #ef4444; display: none; margin-bottom: 12px;"></div>

        <form class="form-grid" id="resetForm">
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" class="input" placeholder="you@example.com" required>
            </div>

            <div>
                <label class="label" for="old_password">Old Password</label>
                <input id="old_password" type="password" class="input" placeholder="Enter your current password" required>
            </div>

            <div>
                <label class="label" for="new_password">New Password</label>
                <input id="new_password" type="password" class="input" placeholder="Enter new password (min 8 chars)" required minlength="8">
            </div>

            <div>
                <label class="label" for="new_password_confirmation">Confirm New Password</label>
                <input id="new_password_confirmation" type="password" class="input" placeholder="Repeat new password" required>
            </div>

            <button type="submit" class="btn-primary" id="resetBtn">Reset Password</button>
        </form>

        <div class="form-footer" style="margin-top: 16px">
            <span>Remember your password?</span>
            <a href="{{ url('/login') }}">Back to login</a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const resetBtn = document.getElementById('resetBtn');
    const errorMsg = document.getElementById('errorMessage');
    const successMsg = document.getElementById('successMessage');
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
        errorMsg.textContent = 'New passwords do not match.';
        errorMsg.style.display = 'block';
        successMsg.style.display = 'none';
        return;
    }
    
    resetBtn.textContent = 'Resetting...';
    resetBtn.disabled = true;
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';

    try {
        const response = await fetch('{{ url("/api/password/reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                old_password: document.getElementById('old_password').value,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
            })
        });

        const data = await response.json();
        
        if (data.success) {
            successMsg.textContent = data.message + ' Redirecting to login...';
            successMsg.style.display = 'block';
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ url("/login") }}';
            }, 2000);
        } else {
            errorMsg.textContent = data.message || 'Password reset failed.';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('Reset error:', error);
        errorMsg.textContent = 'Connection error. Please try again.';
        errorMsg.style.display = 'block';
    } finally {
        resetBtn.textContent = 'Reset Password';
        resetBtn.disabled = false;
    }
});
</script>
@endsection
