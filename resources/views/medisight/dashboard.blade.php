@extends('layouts.medisight')

@section('title', 'MediSight AI – Your sessions')

@section('nav-links')
    <span class="text-muted" id="userEmail">Loading...</span>
    <a href="{{ url('/') }}">Landing</a>
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
                We'll guide you through a simple 2-step process:
                Camera Detection → Profile & AI Chat
            </p>

            <ul class="history-list">
                <li class="history-item" style="cursor: default">
                    <div class="history-meta">
                        <strong>Step 1: Camera Detection</strong>
                        <span class="text-muted">
                            Take a photo for AI analysis (emotion, fatigue, pain)
                        </span>
                    </div>
                </li>
                <li class="history-item" style="cursor: default">
                    <div class="history-meta">
                        <strong>Step 2: Profile & AI Chat</strong>
                        <span class="text-muted">
                            Review results, answer questions, and chat with AI
                        </span>
                    </div>
                </li>
            </ul>

            <a href="{{ url('/session/camera') }}" class="btn-primary" style="margin-top: 16px; display: inline-flex; width: 100%;">
                Start New Session
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
        console.log('Sessions data:', data);
        
        if (data.success && data.data.sessions.length > 0) {
            sessionList.innerHTML = data.data.sessions.map(session => {
                const date = session.started_at || session.created_at;
                const dateStr = date ? new Date(date).toLocaleDateString() : 'Unknown';
                const timeStr = date ? new Date(date).toLocaleTimeString() : '';
                
                // Get emotion from ai_detection_data or initial_emotion
                let emotion = 'N/A';
                if (session.ai_detection_data && session.ai_detection_data.emotion) {
                    emotion = session.ai_detection_data.emotion.emotion || session.ai_detection_data.emotion;
                } else if (session.initial_emotion) {
                    emotion = session.initial_emotion;
                }
                
                // Get symptoms count
                let symptomsCount = 0;
                if (session.symptom_data) {
                    symptomsCount = Object.values(session.symptom_data).filter(v => v === true).length;
                }
                
                // Status badge color
                const statusClass = session.status === 'completed' ? 'success' : 'active';
                
                return `
                    <a href="{{ url('/session/profile') }}?session=${session.id}" class="history-item">
                        <div class="history-meta">
                            <strong>Session #${session.id} · ${dateStr} ${timeStr}</strong>
                            <span class="text-muted">
                                Emotion: ${emotion} · 
                                ${symptomsCount} symptoms · 
                                ${session.status || 'active'}
                            </span>
                        </div>
                        <span class="pill">${session.status === 'completed' ? 'Completed' : 'Open'}</span>
                    </a>
                `;
            }).join('');
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
