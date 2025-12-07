@extends('layouts.medisight')

@section('title', 'MediSight AI – Camera & Chat')

@section('nav-links')
    <a href="{{ url('/') }}">Landing</a>
    <a href="{{ url('/dashboard') }}">User dashboard</a>
@endsection

@section('content')
<section>
    <div class="badge">
        <div class="badge-dot"></div>
        Step 2 · Camera detection → AI chat
    </div>

    <h1 class="hero-title" style="font-size: 26px">
        One scan, then a personalized conversation.
    </h1>
    <p class="hero-subtitle">
        We will briefly access your camera to estimate emotion, fatigue,
        potential pain, and BMI-related risk. After that, the camera closes
        and MediSight AI continues as a chatbot.
    </p>

    <div class="chat-shell">
        <!-- Left: camera detection -->
        <div class="card camera-panel" id="cameraStep">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">Camera detection</h2>
                    <p class="section-subtitle" style="margin-bottom: 0">
                        This runs only once at the beginning of your session.
                    </p>
                </div>
                <span class="chat-status">
                    <span class="hero-indicator" id="cameraStatusDot" style="background: #6b7280"></span>
                    <span id="cameraStatusText">Idle</span>
                </span>
            </div>

            <div class="camera-preview">
                <div class="camera-video-wrapper">
                    <video id="cameraVideo" class="camera-video" autoplay playsinline muted></video>
                    <canvas id="captureCanvas" style="display: none;"></canvas>
                    <div class="camera-overlay"></div>
                </div>

                <div class="camera-footer">
                    <span>
                        We use your webcam feed only to run the models once at the start.
                    </span>
                    <button id="startScanBtn" class="btn-secondary">
                        Start scan
                    </button>
                </div>
            </div>

            <div style="margin-top: 12px; font-size: 12px; color: var(--muted); display: flex; justify-content: space-between; gap: 12px;">
                <span>
                    After the scan finishes, the webcam stops and you'll move to the chat automatically.
                </span>
                <button id="skipScanBtn" class="btn-ghost" type="button" style="font-size: 11px">
                    Skip scan & go to chat
                </button>
            </div>
        </div>

        <!-- Right: chat -->
        <div class="card chat-card" id="chatSection">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">MediSight AI assistant</h2>
                    <p class="section-subtitle" style="margin-bottom: 0">
                        Your detected signals are used as starting context.
                    </p>
                </div>

                <div class="chat-status">
                    <span class="hero-indicator" id="chatStatusDot" style="background: #22c55e"></span>
                    <span id="chatStatusText">Ready</span>
                </div>
            </div>

            <div class="chat-metrics" id="metricsRow">
                <div class="metric-pill">
                    <strong>Emotion</strong>
                    <span id="metricEmotion" class="text-muted">–</span>
                </div>
                <div class="metric-pill">
                    <strong>Fatigue</strong>
                    <span id="metricFatigue" class="text-muted">–</span>
                </div>
                <div class="metric-pill">
                    <strong>Pain</strong>
                    <span id="metricPain" class="text-muted">–</span>
                </div>
                <div class="metric-pill">
                    <strong>Confidence</strong>
                    <span id="metricConfidence" class="text-muted">–</span>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="msg msg-bot">
                    <strong>MediSight AI</strong>
                    <div style="margin-top: 4px">
                        Hi! I'll use a short camera scan to estimate your emotion,
                        fatigue, and pain level. When you're ready,
                        press <strong>Start scan</strong> on the left.
                    </div>
                </div>
            </div>

            <div class="chat-input-row">
                <input id="chatInput" type="text" placeholder="Type your message..." disabled>
                <button id="sendBtn" class="btn-primary" type="button" disabled>
                    Send
                </button>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Configuration
const API_URL = '{{ url("/api") }}';
const token = localStorage.getItem('medisight_token');
const isGuest = !token;

// Elements
const video = document.getElementById('cameraVideo');
const canvas = document.getElementById('captureCanvas');
const cameraStatusDot = document.getElementById('cameraStatusDot');
const cameraStatusText = document.getElementById('cameraStatusText');
const chatStatusDot = document.getElementById('chatStatusDot');
const chatStatusText = document.getElementById('chatStatusText');
const startScanBtn = document.getElementById('startScanBtn');
const skipScanBtn = document.getElementById('skipScanBtn');

const metricEmotion = document.getElementById('metricEmotion');
const metricFatigue = document.getElementById('metricFatigue');
const metricPain = document.getElementById('metricPain');
const metricConfidence = document.getElementById('metricConfidence');

const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');

// State
let mediaStream = null;
let scanFinished = false;
let sessionId = null;
let sessionToken = null;
let detectionResult = null;

// Check URL for existing session
const urlParams = new URLSearchParams(window.location.search);
const existingSession = urlParams.get('session');

// Helper functions
function appendMessage(sender, text) {
    const div = document.createElement('div');
    div.classList.add('msg');
    div.classList.add(sender === 'bot' ? 'msg-bot' : 'msg-user');

    if (sender === 'bot') {
        div.innerHTML = `<strong>MediSight AI</strong><div style='margin-top:4px'>${text}</div>`;
    } else {
        div.textContent = text;
    }

    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function enableChat() {
    chatInput.disabled = false;
    sendBtn.disabled = false;
    chatInput.focus();
}

async function startCamera() {
    try {
        cameraStatusText.textContent = 'Requesting camera…';
        cameraStatusDot.style.background = '#eab308';

        mediaStream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = mediaStream;
        
        cameraStatusText.textContent = 'Live';
        cameraStatusDot.style.background = '#22c55e';
    } catch (err) {
        cameraStatusText.textContent = 'Camera blocked';
        cameraStatusDot.style.background = '#ef4444';
        console.error('Camera error:', err);
        appendMessage('bot', '⚠️ Could not access camera. You can still chat with me, but I won\'t have your health signals.');
        enableChat();
    }
}

function stopCamera() {
    if (mediaStream) {
        mediaStream.getTracks().forEach(t => t.stop());
    }
    cameraStatusText.textContent = 'Stopped';
    cameraStatusDot.style.background = '#6b7280';
}

async function runScan() {
    if (!mediaStream) {
        await startCamera();
        if (!mediaStream) return; // Camera blocked
    }

    cameraStatusText.textContent = 'Scanning…';
    cameraStatusDot.style.background = '#eab308';
    startScanBtn.disabled = true;
    startScanBtn.textContent = 'Scanning...';

    try {
        // Capture frame
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        // Convert to blob
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.9));
        
        if (!blob) {
            throw new Error('Failed to capture image');
        }

        // Send to detection API
        const formData = new FormData();
        formData.append('image', blob, 'frame.jpg');

        chatStatusText.textContent = 'Analyzing...';
        chatStatusDot.style.background = '#eab308';

        const response = await fetch(`${API_URL}/detect/emotion`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        console.log('Detection result:', data);

        if (data.success && data.data) {
            detectionResult = data.data;
            
            // Update metrics
            const emotion = detectionResult.emotion || 'neutral';
            const confidence = (detectionResult.confidence * 100).toFixed(1);
            
            metricEmotion.textContent = `${emotion} · ${confidence}%`;
            metricFatigue.textContent = 'Low · 0.30';  // Placeholder until fatigue model
            metricPain.textContent = 'None · 0.10';   // Placeholder until pain model
            metricConfidence.textContent = `${confidence}%`;

            // Start chat session
            await startChatSession(emotion, confidence);

            // Show success message
            appendMessage('bot', `✅ Scan complete! I detected: <strong>${emotion}</strong> (${confidence}% confidence). Based on this, I'll tailor my responses to your current state. How can I help you today?`);
            
            cameraStatusText.textContent = 'Done';
            cameraStatusDot.style.background = '#22c55e';
            chatStatusText.textContent = 'Ready';
            chatStatusDot.style.background = '#22c55e';
        } else {
            throw new Error(data.error || 'Detection failed');
        }

    } catch (error) {
        console.error('Scan error:', error);
        appendMessage('bot', `⚠️ Detection error: ${error.message}. You can still chat with me.`);
        await startChatSession('neutral', 0);
        
        cameraStatusText.textContent = 'Error';
        cameraStatusDot.style.background = '#ef4444';
    }

    // Finish up
    stopCamera();
    scanFinished = true;
    startScanBtn.textContent = '✓ Complete';
    enableChat();
}

async function startChatSession(emotion, confidence) {
    try {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(`${API_URL}/chat/session/start`, {
            method: 'POST',
            headers,
            body: JSON.stringify({
                emotion: emotion,
                fatigue: 'low',
                pain: 'none',
                emotion_confidence: confidence / 100,
                fatigue_confidence: 0.3,
                pain_confidence: 0.1
            })
        });

        const data = await response.json();
        
        if (data.success) {
            sessionId = data.data.session.id;
            sessionToken = data.data.session.session_token;
            console.log('Chat session started:', sessionId);
        }
    } catch (error) {
        console.error('Failed to start session:', error);
    }
}

async function sendMessage() {
    const value = chatInput.value.trim();
    if (!value) return;

    appendMessage('user', value);
    chatInput.value = '';
    chatInput.disabled = true;
    sendBtn.disabled = true;

    try {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const body = {
            session_id: sessionId,
            message: value
        };
        
        if (!token) {
            body.session_token = sessionToken;
        }

        const response = await fetch(`${API_URL}/chat/message`, {
            method: 'POST',
            headers,
            body: JSON.stringify(body)
        });

        const data = await response.json();
        
        if (data.success) {
            appendMessage('bot', data.data.bot_response.message);
        } else {
            appendMessage('bot', '⚠️ Sorry, I encountered an error. Please try again.');
        }
    } catch (error) {
        console.error('Message error:', error);
        appendMessage('bot', '⚠️ Connection error. Please try again.');
    }

    chatInput.disabled = false;
    sendBtn.disabled = false;
    chatInput.focus();
}

// Event listeners
startScanBtn.addEventListener('click', () => {
    runScan();
});

skipScanBtn.addEventListener('click', async () => {
    stopCamera();
    scanFinished = true;
    
    await startChatSession('neutral', 0);
    
    appendMessage('bot', 'Okay, we\'ll skip camera detection for now. You can still chat with me, but my recommendations will be more generic.');
    enableChat();
});

sendBtn.addEventListener('click', sendMessage);

chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
    }
});

// Load existing session if provided
async function loadExistingSession() {
    if (existingSession && token) {
        try {
            const response = await fetch(`${API_URL}/chat/session/${existingSession}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                sessionId = data.data.session.id;
                
                // Update metrics
                if (data.data.session.initial_emotion) {
                    metricEmotion.textContent = `${data.data.session.initial_emotion} · ${(data.data.session.emotion_confidence * 100).toFixed(1)}%`;
                }
                
                // Load messages
                if (data.data.session.messages) {
                    chatMessages.innerHTML = ''; // Clear default message
                    data.data.session.messages.forEach(msg => {
                        appendMessage(msg.sender, msg.message);
                    });
                }
                
                scanFinished = true;
                enableChat();
                
                // Hide camera panel
                document.getElementById('cameraStep').style.display = 'none';
            }
        } catch (error) {
            console.error('Failed to load session:', error);
        }
    }
}

// Initialize
if (existingSession) {
    loadExistingSession();
} else if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    startCamera();
}
</script>
@endsection
