@extends('layouts.medisight')

@section('title', 'MediSight AI – Your sessions')

@section('nav-links')
    <span class="text-muted" id="userEmail">Loading...</span>
    <a href="{{ url('/chat') }}" class="nav-cta">Start new session</a>
    <a href="#" id="logoutBtn">Logout</a>
@endsection

@section('content')
<section>
    <h1 class="hero-title" style="font-size: 26px">
        Your MediSight AI activity
    </h1>
    <p class="hero-subtitle">
        Choose a previous chat session to resume, or start a new detection and conversation.
    </p>

    <div class="dashboard-layout">
        <div class="card">
            <h2 class="section-title">Session history</h2>
            <p class="section-subtitle">
                Your previous MediSight AI sessions are listed below.
            </p>

            <div class="history-list" id="sessionList">
                <div class="history-item" style="cursor: default; justify-content: center;">
                    <span class="text-muted">Loading sessions...</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="section-title">Start a new analysis</h2>
            <p class="section-subtitle">
                We'll open the camera, run the AI models once, and then move
                you directly into the chatbot with your latest readings.
            </p>

            <ul class="history-list">
                <li class="history-item" style="cursor: default">
                    <div class="history-meta">
                        <strong>1. Camera & detection</strong>
                        <span class="text-muted">
                            Browser asks for camera permission and captures a frame.
                        </span>
                    </div>
                </li>
                <li class="history-item" style="cursor: default">
                    <div class="history-meta">
                        <strong>2. Laravel → Python</strong>
                        <span class="text-muted">
                            Backend runs emotion detection model and receives JSON.
                        </span>
                    </div>
                </li>
                <li class="history-item" style="cursor: default">
                    <div class="history-meta">
                        <strong>3. AI health chat</strong>
                        <span class="text-muted">
                            JSON values are used as context for the chatbot.
                        </span>
                    </div>
                </li>
            </ul>

            <a href="{{ url('/chat') }}" class="btn-primary" style="margin-top: 16px; display: inline-flex">
                Start new MediSight session
            </a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
const token = localStorage.getItem('medisight_token');
const user = JSON.parse(localStorage.getItem('medisight_user') || '{}');

// Check if logged in
if (!token) {
    window.location.href = '{{ url("/login") }}';
}

// Display user email
document.getElementById('userEmail').textContent = `Signed in as ${user.email || 'user'}`;

// Load sessions
async function loadSessions() {
    const sessionList = document.getElementById('sessionList');
    
    try {
        const response = await fetch('{{ url("/api/chat/sessions") }}', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data.sessions.length > 0) {
            sessionList.innerHTML = data.data.sessions.map(session => `
                <a href="{{ url('/chat') }}?session=${session.id}" class="history-item">
                    <div class="history-meta">
                        <strong>Session · ${new Date(session.started_at).toLocaleDateString()} · ${new Date(session.started_at).toLocaleTimeString()}</strong>
                        <span class="text-muted">
                            Emotion: ${session.initial_emotion || 'N/A'} · 
                            ${session.message_count || 0} messages · 
                            ${session.status}
                        </span>
                    </div>
                    <span class="pill">Open</span>
                </a>
            `).join('');
        } else {
            sessionList.innerHTML = `
                <div class="history-item" style="cursor: default; justify-content: center;">
                    <span class="text-muted">No previous sessions. Start a new one!</span>
                </div>
            `;
        }
    } catch (error) {
        console.error('Failed to load sessions:', error);
        sessionList.innerHTML = `
            <div class="history-item" style="cursor: default; justify-content: center;">
                <span class="text-muted">Failed to load sessions.</span>
            </div>
        `;
    }
}

// Logout
document.getElementById('logoutBtn').addEventListener('click', async function(e) {
    e.preventDefault();
    
    try {
        await fetch('{{ url("/api/logout") }}', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
    } catch (error) {
        console.error('Logout error:', error);
    }
    
    localStorage.removeItem('medisight_token');
    localStorage.removeItem('medisight_user');
    window.location.href = '{{ url("/login") }}';
});

// Load sessions on page load
loadSessions();
</script>
@endsection
