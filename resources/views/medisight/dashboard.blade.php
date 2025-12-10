@extends('layouts.medisight')

@section('title', 'MediSight AI – Your sessions')

@section('nav-links')
    <span class="text-muted" id="userEmail">Loading...</span>
    <a href="{{ url('/') }}">Landing</a>
    <a href="#" id="logoutBtn">Logout</a>
@endsection

@section('styles')
<style>
    .session-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid rgba(55, 65, 81, 0.9);
        background: rgba(15, 23, 42, 0.95);
        margin-bottom: 8px;
        transition: border-color 0.2s;
    }
    
    .session-item:hover {
        border-color: var(--accent);
    }
    
    .session-link {
        flex: 1;
        text-decoration: none;
        color: inherit;
    }
    
    .session-meta strong {
        font-size: 14px;
        color: var(--text);
    }
    
    .session-meta .details {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }
    
    .session-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .delete-btn {
        background: transparent;
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .delete-btn:hover {
        background: #ef4444;
        color: white;
    }
    
    .show-more-btn {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 1px dashed rgba(55, 65, 81, 0.9);
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        margin-top: 8px;
    }
    
    .show-more-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
    }
    
    .hidden-sessions {
        display: none;
    }
    
    .hidden-sessions.show {
        display: block;
    }
    
    /* Light mode */
    body.light-mode .session-item {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    
    body.light-mode .session-meta strong {
        color: #1e293b;
    }
    
    body.light-mode .session-meta .details {
        color: #64748b;
    }
    
    body.light-mode .show-more-btn {
        border-color: #cbd5e1;
        color: #64748b;
    }
</style>
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

            <div id="sessionList">
                <div class="session-item" style="justify-content: center;">
                    <span class="text-muted">Loading sessions...</span>
                </div>
            </div>
            
            <div id="hiddenSessions" class="hidden-sessions"></div>
            
            <button id="showMoreBtn" class="show-more-btn" style="display: none;">
                Show all
            </button>
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

// All sessions data
let allSessions = [];
const VISIBLE_COUNT = 3;

// Load sessions
async function loadSessions() {
    const sessionList = document.getElementById('sessionList');
    const hiddenSessions = document.getElementById('hiddenSessions');
    const showMoreBtn = document.getElementById('showMoreBtn');
    
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
            allSessions = data.data.sessions;
            
            // Split into visible and hidden
            const visibleSessions = allSessions.slice(0, VISIBLE_COUNT);
            const extraSessions = allSessions.slice(VISIBLE_COUNT);
            
            // Render visible sessions (personal numbering: newest first)
            sessionList.innerHTML = visibleSessions.map((session, index) => 
                renderSession(session, allSessions.length - index)
            ).join('');
            
            // Render hidden sessions if any
            if (extraSessions.length > 0) {
                hiddenSessions.innerHTML = extraSessions.map((session, index) => 
                    renderSession(session, allSessions.length - VISIBLE_COUNT - index)
                ).join('');
                
                showMoreBtn.textContent = `Show all (+${extraSessions.length} more)`;
                showMoreBtn.style.display = 'block';
            } else {
                showMoreBtn.style.display = 'none';
            }
            
            // Add delete event listeners
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', handleDelete);
            });
            
        } else {
            sessionList.innerHTML = `
                <div class="session-item" style="justify-content: center;">
                    <span class="text-muted">No previous sessions. Start a new one!</span>
                </div>
            `;
            showMoreBtn.style.display = 'none';
        }
    } catch (error) {
        console.error('Failed to load sessions:', error);
        sessionList.innerHTML = `
            <div class="session-item" style="justify-content: center;">
                <span class="text-muted">Failed to load sessions.</span>
            </div>
        `;
    }
}

function renderSession(session, personalNumber) {
    const date = session.started_at || session.created_at;
    const dateStr = date ? new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric' 
    }) : 'Unknown';
    const timeStr = date ? new Date(date).toLocaleTimeString('en-US', { 
        hour: '2-digit', minute: '2-digit' 
    }) : '';
    
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
        // Count boolean symptoms (true)
        if (session.symptom_data.fever === true) symptomsCount++;
        if (session.symptom_data.cough === true) symptomsCount++;
        if (session.symptom_data.fatigue === true) symptomsCount++;
        if (session.symptom_data.difficulty_breathing === true) symptomsCount++;
        // Count string symptoms (High)
        if (session.symptom_data.blood_pressure === 'High') symptomsCount++;
        if (session.symptom_data.cholesterol === 'High') symptomsCount++;
    }
    
    const isCompleted = session.status === 'completed';
    const pillText = isCompleted ? 'Completed' : 'Open';
    
    return `
        <div class="session-item" data-session-id="${session.id}">
            <a href="{{ url('/session/profile') }}?session=${session.id}" class="session-link">
                <div class="session-meta">
                    <strong>Session #${personalNumber} · ${dateStr}</strong>
                    <div class="details">
                        ${timeStr} · Emotion: ${emotion} · ${symptomsCount} symptoms · ${session.status || 'active'}
                    </div>
                </div>
            </a>
            <div class="session-actions">
                <span class="pill">${pillText}</span>
                <button class="delete-btn" data-id="${session.id}" title="Delete session">🗑️</button>
            </div>
        </div>
    `;
}

// Handle delete
async function handleDelete(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const sessionId = this.getAttribute('data-id');
    
    if (!confirm('Are you sure you want to delete this session? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`{{ url("/api/chat/session") }}/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Remove from DOM
            const sessionEl = document.querySelector(`.session-item[data-session-id="${sessionId}"]`);
            if (sessionEl) {
                sessionEl.remove();
            }
            
            // Reload to update numbering
            loadSessions();
        } else {
            alert('Failed to delete session: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Failed to delete session. Please try again.');
    }
}

// Show more toggle
document.getElementById('showMoreBtn').addEventListener('click', function() {
    const hiddenSessions = document.getElementById('hiddenSessions');
    const isShowing = hiddenSessions.classList.contains('show');
    
    if (isShowing) {
        hiddenSessions.classList.remove('show');
        this.textContent = `Show all (+${allSessions.length - VISIBLE_COUNT} more)`;
    } else {
        hiddenSessions.classList.add('show');
        this.textContent = 'Show less';
        
        // Re-attach delete listeners
        hiddenSessions.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', handleDelete);
        });
    }
});

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
