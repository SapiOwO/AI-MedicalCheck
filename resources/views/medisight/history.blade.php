@extends('layouts.medisight')

@section('title', 'MediSight AI – Detection History')

@section('nav-links')
    <span class="text-muted" id="userEmail">Loading...</span>
    <a href="{{ url('/dashboard') }}">Dashboard</a>
    <a href="{{ url('/chat') }}" class="nav-cta">New Session</a>
    <a href="#" id="logoutBtn">Logout</a>
@endsection

@section('styles')
<style>
    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .history-table th,
    .history-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid rgba(31, 41, 55, 0.9);
    }
    
    .history-table th {
        background: rgba(15, 23, 42, 0.95);
        color: var(--muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .history-table tr:hover {
        background: rgba(79, 70, 229, 0.1);
    }
    
    .emotion-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .emotion-happy { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .emotion-sad { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .emotion-angry { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .emotion-fear { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
    .emotion-surprise { background: rgba(234, 179, 8, 0.2); color: #eab308; }
    .emotion-disgust { background: rgba(249, 115, 22, 0.2); color: #f97316; }
    .emotion-neutral { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
    
    .datetime-cell {
        font-family: monospace;
        font-size: 13px;
    }
    
    .confidence-bar {
        height: 6px;
        background: rgba(31, 41, 55, 0.9);
        border-radius: 3px;
        overflow: hidden;
        width: 80px;
    }
    
    .confidence-fill {
        height: 100%;
        background: var(--accent);
        border-radius: 3px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }
</style>
@endsection

@section('content')
<section>
    <h1 class="hero-title" style="font-size: 26px">
        Detection History
    </h1>
    <p class="hero-subtitle">
        View all your past emotion detection sessions with detailed timestamps.
    </p>

    <div class="card" style="margin-top: 20px; overflow-x: auto;">
        <div class="chat-header">
            <div>
                <h2 class="section-title">All Detections</h2>
                <p class="section-subtitle" style="margin-bottom: 0">
                    Total: <span id="totalCount">0</span> sessions
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="#" class="btn-secondary" id="exportCSVBtn" style="display: none;">📥 Export CSV</a>
                <button class="btn-secondary" id="refreshBtn">↻ Refresh</button>
            </div>
        </div>

        <div id="historyContainer">
            <div class="empty-state">
                <p>Loading history...</p>
            </div>
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
document.getElementById('userEmail').textContent = `${user.email || 'user'}`;

// Load history
async function loadHistory() {
    const container = document.getElementById('historyContainer');
    const totalCount = document.getElementById('totalCount');
    
    container.innerHTML = '<div class="empty-state"><p>Loading history...</p></div>';
    
    try {
        const response = await fetch('{{ url("/api/detection/history") }}', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data.history.length > 0) {
            totalCount.textContent = data.data.total;
            document.getElementById('exportCSVBtn').style.display = 'inline-flex';
            
            container.innerHTML = `
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time (H:M:S)</th>
                            <th>Day</th>
                            <th>Emotion</th>
                            <th>Confidence</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.data.history.map((item, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td class="datetime-cell">${item.date}</td>
                                <td class="datetime-cell">${item.time}</td>
                                <td>${item.day}</td>
                                <td>
                                    <span class="emotion-badge emotion-${(item.emotion || 'neutral').toLowerCase()}">
                                        ${item.emotion || 'N/A'}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="confidence-bar">
                                            <div class="confidence-fill" style="width: ${(item.emotion_confidence || 0) * 100}%"></div>
                                        </div>
                                        <span style="font-size: 12px;">${((item.emotion_confidence || 0) * 100).toFixed(1)}%</span>
                                    </div>
                                </td>
                                <td>${item.message_count}</td>
                                <td>
                                    <span class="pill">${item.status}</span>
                                </td>
                                <td>
                                    <a href="{{ url('/chat') }}?session=${item.id}" class="btn-ghost" style="padding: 4px 12px; font-size: 12px;">
                                        View
                                    </a>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        } else {
            totalCount.textContent = '0';
            container.innerHTML = `
                <div class="empty-state">
                    <p style="font-size: 48px; margin-bottom: 16px;">📊</p>
                    <p>No detection history yet.</p>
                    <p style="margin-top: 8px;">Start a new session to see your data here.</p>
                    <a href="{{ url('/chat') }}" class="btn-primary" style="margin-top: 20px; display: inline-flex;">
                        Start New Session
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Failed to load history:', error);
        container.innerHTML = `
            <div class="empty-state">
                <p>Failed to load history. Please try again.</p>
            </div>
        `;
    }
}

// Refresh button
document.getElementById('refreshBtn').addEventListener('click', loadHistory);

// Export CSV button
document.getElementById('exportCSVBtn').addEventListener('click', function(e) {
    e.preventDefault();
    // Open export URL in new window with auth token
    const exportUrl = `{{ url('/api/export/history/csv') }}`;
    
    // We need to use fetch with auth header and download
    fetch(exportUrl, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'text/csv'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `medisight_history_${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Failed to export. Please try again.');
    });
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

// Load on page load
loadHistory();
</script>
@endsection
