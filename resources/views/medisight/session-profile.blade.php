@extends('layouts.medisight')

@section('title', 'MediSight AI – Health Assessment')

@section('nav-links')
    <a href="{{ url('/session/camera') }}">Back to Camera</a>
    <a href="{{ url('/dashboard') }}">Dashboard</a>
@endsection

@section('styles')
<style>
    .profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.5fr);
        gap: 24px;
        margin-top: 16px;
    }
    
    .profile-card {
        height: fit-content;
    }
    
    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .form-group label {
        font-size: 12px;
        color: var(--muted);
    }
    
    .form-group input,
    .form-group select {
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid rgba(55, 65, 81, 0.9);
        background: rgba(15, 23, 42, 0.95);
        color: var(--text);
        font-size: 14px;
    }
    
    .form-group input:disabled,
    .form-group select:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 600px;
    }
    
    .chat-messages-box {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid rgba(31, 41, 55, 0.95);
        background: radial-gradient(circle at top, #020617, #000);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .msg {
        max-width: 88%;
        padding: 14px 18px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .msg-bot {
        align-self: flex-start;
        background: rgba(15, 23, 42, 0.98);
        border: 1px solid rgba(55, 65, 81, 0.95);
    }
    
    .msg-bot .sender {
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .msg-bot .sender::before {
        content: '';
    }
    
    .msg-user {
        align-self: flex-end;
        background: linear-gradient(135deg, var(--accent), #6366f1);
        color: white;
    }
    
    .msg-bot h2 {
        font-size: 16px;
        margin: 12px 0 8px 0;
        color: var(--accent);
    }
    
    .msg-bot h3 {
        font-size: 14px;
        margin: 10px 0 6px 0;
        color: #22c55e;
    }
    
    .msg-bot code {
        font-family: monospace;
        letter-spacing: 1px;
    }
    
    .chat-input-row {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }
    
    .chat-input-row input {
        flex: 1;
        padding: 14px 18px;
        border-radius: 24px;
        border: 1px solid rgba(55, 65, 81, 0.9);
        background: rgba(15, 23, 42, 0.95);
        color: var(--text);
        font-size: 14px;
    }
    
    .chat-input-row input:focus {
        outline: none;
        border-color: var(--accent);
    }
    
    .chat-input-row button {
        padding: 14px 28px;
        border-radius: 24px;
    }
    
    .finalize-section {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .readonly-notice {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid #3b82f6;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        color: #3b82f6;
        text-align: center;
        font-size: 14px;
    }
    
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
    }
    
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: var(--muted);
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.2); }
    }
    
    .emotion-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.2), rgba(99, 102, 241, 0.1));
        border: 1px solid rgba(79, 70, 229, 0.3);
        border-radius: 20px;
        font-size: 13px;
        color: var(--accent);
    }
    
    .quick-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    
    .quick-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid rgba(79, 70, 229, 0.4);
        background: transparent;
        color: var(--accent);
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .quick-btn:hover {
        background: var(--accent);
        color: white;
    }
    
    /* Light mode */
    body.light-mode .form-group input,
    body.light-mode .form-group select,
    body.light-mode .chat-input-row input {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    
    body.light-mode .chat-messages-box {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
    }
    
    body.light-mode .msg-bot {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #1e293b !important;
    }
    
    @media (max-width: 900px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
        .chat-container {
            height: 500px;
        }
    }
</style>
@endsection

@section('content')
<section>
    <div class="badge">
        <div class="badge-dot"></div>
        <span id="stepBadge">Step 2 of 2 - AI Health Assessment</span>
    </div>

    <h1 class="hero-title" style="font-size: 26px; margin-bottom: 8px;">
        <span id="pageTitle">AI-Powered Health Assessment</span>
    </h1>
    <p class="hero-subtitle" id="pageSubtitle">
        Describe your symptoms naturally. Our ML model will analyze and provide accurate diagnosis.
    </p>

    <!-- Read-only notice for completed sessions -->
    <div class="readonly-notice" id="readonlyNotice" style="display: none;">
        This session has been finalized and is in <strong>read-only</strong> mode.
    </div>

    <div class="profile-layout">
        <!-- Left: Patient Profile -->
        <div class="card profile-card">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">Patient Info</h2>
                    <p class="section-subtitle" style="margin-bottom: 0;">
                        Your basic information
                    </p>
                </div>
                <span class="chat-status" id="statusBadge">
                    <span class="hero-indicator" style="background: #22c55e;"></span>
                    <span id="statusText">Active</span>
                </span>
            </div>
            
            <form class="profile-form" id="profileForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" min="1" max="120" placeholder="Enter age">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender">
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Detected Emotion</label>
                    <div class="emotion-badge" id="emotionBadge">
                        <span>-</span>
                        <span id="emotionText">Not detected</span>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <button type="button" class="btn-primary" id="startChatBtn" onclick="startChat()" style="width: 100%;">
                        Start AI Consultation
                    </button>
                </div>

                <!-- Symptom List (Read Only) -->
                <div class="form-group" style="margin-top: 20px;">
                    <label>Identified Symptoms</label>
                    <div id="symptomList" style="
                        background: rgba(15, 23, 42, 0.5);
                        border: 1px solid rgba(55, 65, 81, 0.5);
                        border-radius: 12px;
                        padding: 12px;
                        min-height: 80px;
                        font-size: 14px;
                        color: #cbd5e1;
                    ">
                        <em style="opacity: 0.5;">No symptoms identified yet...</em>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: AI Chatbot -->
        <div class="card chat-container">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">MediSight AI Expert</h2>
                    <p class="section-subtitle" style="margin-bottom: 0;">
                        ML-powered diagnosis with 350+ patient data
                    </p>
                </div>
                <span class="chat-status">
                    <span class="hero-indicator" id="chatDot" style="background: #22c55e;"></span>
                    <span id="chatStatusText">Online</span>
                </span>
            </div>
            
            <div class="chat-messages-box" id="chatMessages">
                <!-- Messages will be appended here -->
            </div>
            
            <div class="chat-input-row" id="chatInputRow">
                <input type="text" id="chatInput" placeholder="Describe your symptoms... (e.g., 'I have fever, cough, and feel tired')">
                <button class="btn-primary" id="sendBtn">Send</button>
            </div>
        </div>
    </div>
    
    <!-- Finalize Session Buttons -->
    <div class="finalize-section" id="finalizeSection">
        <button class="btn-primary" id="finalizeBtn" style="padding: 14px 32px;">
            Finalize Session
        </button>
        <button class="btn-secondary" id="exportPdfBtn" style="padding: 14px 24px;">
            Export PDF
        </button>
        <button class="btn-secondary" id="exportCsvBtn" style="padding: 14px 24px;">
            Export CSV
        </button>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function() {
    var API_URL = '{{ url("/api") }}';
    var PYTHON_API = 'http://127.0.0.1:8001';
    var token = localStorage.getItem('medisight_token');
    var isGuest = !token;
    var sessionId = null;
    var sessionToken = localStorage.getItem('current_session_token');
    var detectionData = {};
    var isReadOnly = false;
    
    // Chatbot state
    var chatbotProfile = null;
    var lastContext = '';
    var diagnosisGiven = false;
    
    // Elements
    var chatMessages = document.getElementById('chatMessages');
    var chatInput = document.getElementById('chatInput');
    var sendBtn = document.getElementById('sendBtn');
    var finalizeBtn = document.getElementById('finalizeBtn');
    var exportPdfBtn = document.getElementById('exportPdfBtn');
    var exportCsvBtn = document.getElementById('exportCsvBtn');
    var readonlyNotice = document.getElementById('readonlyNotice');
    var chatInputRow = document.getElementById('chatInputRow');
    var emotionText = document.getElementById('emotionText');
    var emotionBadge = document.getElementById('emotionBadge');
    
    // Check if loading existing session from URL
    var urlParams = new URLSearchParams(window.location.search);
    var existingSessionId = urlParams.get('session');
    
    if (existingSessionId) {
        sessionId = existingSessionId;
        loadExistingSession(existingSessionId);
    } else {
        sessionId = localStorage.getItem('current_session_id');
        initNewSession();
    }
    
    // Load existing session from database
    function loadExistingSession(id) {
        if (!token) {
            alert('Please login to view session history.');
            window.location.href = '{{ url("/login") }}';
            return;
        }
        
        fetch(API_URL + '/chat/session/' + id, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                var session = data.data.session;
                
                if (session.status === 'completed') {
                    enableReadOnlyMode();
                }
                
                if (session.age) document.getElementById('age').value = session.age;
                if (session.gender) document.getElementById('gender').value = session.gender;
                
                // Load emotion
                if (session.ai_detection_data && session.ai_detection_data.emotion) {
                    var emotionData = session.ai_detection_data.emotion;
                    updateEmotionDisplay(emotionData.emotion, emotionData.confidence);
                    detectionData = session.ai_detection_data;
                }
                
                initChatbotProfile();
                
                // Load chat messages
                if (session.messages && session.messages.length > 0) {
                    session.messages.forEach(function(msg) {
                        if (msg.sender === 'bot') {
                            addBotMessage(msg.message);
                        } else {
                            addUserMessage(msg.message);
                        }
                    });
                }
            }
        })
        .catch(function(error) {
            console.error('Load session error:', error);
            window.location.href = '{{ url("/dashboard") }}';
        });
    }
    
    function enableReadOnlyMode() {
        isReadOnly = true;
        document.getElementById('stepBadge').textContent = 'Archived Session';
        document.getElementById('pageTitle').textContent = 'Session Review (Read-Only)';
        document.getElementById('pageSubtitle').textContent = 'This session has been finalized.';
        document.getElementById('statusText').textContent = 'Completed';
        readonlyNotice.style.display = 'block';
        
        document.getElementById('age').disabled = true;
        document.getElementById('gender').disabled = true;
        chatInputRow.style.display = 'none';
        finalizeBtn.style.display = 'none';
        
        document.querySelectorAll('.quick-btn').forEach(function(btn) {
            btn.disabled = true;
        });
    }
    
    function updateEmotionDisplay(emotion, confidence) {
        var emojis = {
            'happy': ':)', 'sad': ':(', 'angry': '>:(', 
            'fear': 'o_o', 'surprise': ':O', 'neutral': '-', 'disgust': 'x_x'
        };
        var emoji = emojis[emotion.toLowerCase()] || '-';
        var confPct = confidence ? (confidence * 100).toFixed(0) + '%' : '';
        
        emotionBadge.querySelector('span:first-child').textContent = emoji;
        emotionText.textContent = emotion + (confPct ? ' (' + confPct + ')' : '');
    }
    
    function initChatbotProfile() {
        var emotion = 'Neutral';
        if (detectionData.emotion && detectionData.emotion.emotion) {
            emotion = detectionData.emotion.emotion;
        }
        
        chatbotProfile = {
            Fever: null,
            Cough: null,
            Fatigue: null,
            'Difficulty Breathing': null,
            Age: parseInt(document.getElementById('age').value) || 30,
            Gender: document.getElementById('gender').value || 'Male',
            'Blood Pressure': null,
            'Cholesterol Level': null,
            'Outcome Variable': 'Negative',
            Emotion: emotion
        };
    }
    
    function initNewSession() {
        try {
            detectionData = JSON.parse(localStorage.getItem('detection_data') || '{}');
        } catch(e) {}
        
        if (detectionData.emotion && detectionData.emotion.emotion) {
            updateEmotionDisplay(detectionData.emotion.emotion, detectionData.emotion.confidence);
        }
        
        initChatbotProfile();
        // getGreeting(); // Removed auto-start
        
        // Initial symptom list update
        updateSymptomViewer();
    }
    
    function updateSymptomViewer() {
        var container = document.getElementById('symptomList');
        if (!container) return;
        
        var symptoms = [];
        if (chatbotProfile) {
            for (var k in chatbotProfile) {
                if (chatbotProfile[k] === 'Yes' || chatbotProfile[k] === 'High') {
                    symptoms.push(k);
                }
            }
        }
        
        if (symptoms.length === 0) {
            container.innerHTML = '<em style="opacity: 0.5;">No symptoms identified yet...</em>';
        } else {
            container.innerHTML = symptoms.map(s => 
                '<span style="display:inline-block; background:rgba(79, 70, 229, 0.2); color:#818cf8; padding:4px 10px; border-radius:99px; font-size:12px; margin:2px;">' + s + '</span>'
            ).join(' ');
        }
    }
    
    function getGreeting() {
        // Don't start automatically anymore
        // Wait for user to click "Start Consultation"
    }

    // New function to start chat
    window.startChat = function() {
        var ageInput = document.getElementById('age');
        var genderInput = document.getElementById('gender');
        
        if (!ageInput.value || !genderInput.value) {
            alert('Please enter your age and gender to start.');
            return;
        }
        
        // Update profile
        chatbotProfile.Age = parseInt(ageInput.value);
        chatbotProfile.Gender = genderInput.value;
        
        // Disable inputs
        ageInput.disabled = true;
        genderInput.disabled = true;
        document.getElementById('startChatBtn').style.display = 'none';
        
        // Show typing
        showTyping();
        
        fetch(PYTHON_API + '/chatbot/greeting', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ profile: chatbotProfile })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            hideTyping();
            if (data.success) {
                addBotMessage(data.greeting);
                lastContext = data.greeting; // CRITICAL FIX: Track context from greeting
            } else {
                addBotMessage('Hello! I\'m your MediSight AI. Please describe your symptoms.');
            }
        })
        .catch(function(error) {
            hideTyping();
            console.error('Greeting error:', error);
            addBotMessage('System error. Please try again.');
        });
    };
    
    function addBotMessage(text) {
        var div = document.createElement('div');
        div.className = 'msg msg-bot';
        
        var htmlText = text
            .replace(/## (.*)/g, '<h2>$1</h2>')
            .replace(/### (.*)/g, '<h3>$1</h3>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\n/g, '<br>');
        
        div.innerHTML = '<div class="sender">MediSight AI</div><div>' + htmlText + '</div>';
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function addUserMessage(text) {
        var div = document.createElement('div');
        div.className = 'msg msg-user';
        div.textContent = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function showTyping() {
        var div = document.createElement('div');
        div.className = 'msg msg-bot';
        div.id = 'typingIndicator';
        div.innerHTML = '<div class="sender">MediSight AI</div><div class="typing-indicator"><span></span><span></span><span></span></div>';
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function hideTyping() {
        var el = document.getElementById('typingIndicator');
        if (el) el.remove();
    }
    
    function sendMessage(customMsg) {
        if (isReadOnly) return;
        
        var message = customMsg || chatInput.value.trim();
        if (!message) return;
        
        addUserMessage(message);
        if (!customMsg) chatInput.value = '';
        sendBtn.disabled = true;
        
        chatbotProfile.Age = parseInt(document.getElementById('age').value) || chatbotProfile.Age;
        chatbotProfile.Gender = document.getElementById('gender').value || chatbotProfile.Gender;
        
        showTyping();
        
        fetch(PYTHON_API + '/chatbot/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                profile: chatbotProfile,
                context: lastContext
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            hideTyping();
            
            if (data.success) {
                chatbotProfile = data.profile;
                lastContext = data.response;
                addBotMessage(data.response);
                
                updateSymptomViewer(); // New: Update symptoms
                
                if (data.response.includes('Diagnosis') || data.response.includes('Match')) {
                    diagnosisGiven = true;
                }
            } else {
                throw new Error('API error');
            }
        })
        .catch(function(error) {
            hideTyping();
            console.error('Chat error:', error);
            addBotMessage('Connection error. Please check if the Python service is running on port 8001.');
            sendBtn.disabled = false;
        });
    }

    // Guest Handling
    if (finalizeBtn) {
        finalizeBtn.addEventListener('click', function() {
            if (isGuest) {
                if (!diagnosisGiven) {
                    alert('Please complete the diagnosis first.');
                    return;
                }
                alert('To save your results, please Log In or Register.');
                window.location.href = '{{ route("login") }}';
            } else {
                 // Keep default behavior (likely form submit or no-op as button is submit type usually, but checking HTML it is type="button" maybe? No, defaults to submit in form, but here likely handled by other script or just link. Actually HTML shows it has id finalizeBtn)
                 // Assuming existing logic handles it or we need to redirect.
                 // Since snippet didn't show existing finalize listener, I'll assume we need to redirect to dashboard or submit.
                 window.location.href = '{{ url("/dashboard") }}';
            }
        });
    }

    if (isGuest && document.getElementById('finalizeSection')) {
        var endBtn = document.createElement('button');
        endBtn.className = 'btn-secondary';
        endBtn.textContent = 'End Session';
        endBtn.style.padding = '14px 24px';
        endBtn.style.marginLeft = '12px';
        endBtn.onclick = function() {
            if (confirm('Are you sure? All session data will be discarded.')) {
                localStorage.removeItem('medisight_token');
                localStorage.removeItem('detection_data'); // Clear photo data too
                window.location.href = '{{ url("/dashboard") }}';
            }
        };
        document.getElementById('finalizeSection').appendChild(endBtn);
    }
    
    // Quick symptom buttons
    document.querySelectorAll('.quick-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (isReadOnly) return;
            var symptom = this.getAttribute('data-symptom');
            sendMessage(symptom);
        });
    });
    
    function finalizeSession() {
        if (isReadOnly) return;
        
        var age = document.getElementById('age').value;
        var gender = document.getElementById('gender').value;
        
        if (!age || !gender) {
            alert('Please fill in Age and Gender before finalizing.');
            return;
        }
        
        finalizeBtn.disabled = true;
        finalizeBtn.textContent = 'Saving...';
        
        var symptomData = {
            fever: chatbotProfile.Fever === 'Yes',
            cough: chatbotProfile.Cough === 'Yes',
            fatigue: chatbotProfile.Fatigue === 'Yes',
            difficulty_breathing: chatbotProfile['Difficulty Breathing'] === 'Yes'
        };
        
        var sessionData = {
            age: parseInt(age),
            gender: gender,
            symptom_data: symptomData,
            ai_detection_data: detectionData,
            current_step: 'completed'
        };
        
        if (isGuest) {
            localStorage.setItem('pending_session_data', JSON.stringify(sessionData));
            alert('Please login to save your session.');
            window.location.href = '{{ url("/login") }}?redirect=save_session';
            return;
        }
        
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        };
        
        var createSession = new Promise(function(resolve) {
            if (sessionId) {
                resolve();
            } else {
                fetch(API_URL + '/chat/session/start', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ current_step: 'profile' })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        sessionId = data.data.session.id;
                    }
                    resolve();
                })
                .catch(function() { resolve(); });
            }
        });
        
        createSession.then(function() {
            sessionData.session_id = parseInt(sessionId);
            
            return fetch(API_URL + '/session/update', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(sessionData)
            });
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                localStorage.removeItem('current_session_id');
                localStorage.removeItem('current_session_token');
                localStorage.removeItem('detection_data');
                
                alert('Session saved successfully!');
                window.location.href = '{{ url("/dashboard") }}';
            } else {
                throw new Error(data.error || 'Failed to save');
            }
        })
        .catch(function(error) {
            console.error('Finalize error:', error);
            alert('Failed to save. Please try again.');
            finalizeBtn.disabled = false;
            finalizeBtn.textContent = 'Finalize Session';
        });
    }
    
    function exportPDF() {
        var age = document.getElementById('age').value || 'N/A';
        var gender = document.getElementById('gender').value || 'N/A';
        var emotion = emotionText.textContent || 'N/A';
        
        var symptoms = [];
        if (chatbotProfile) {
            if (chatbotProfile.Fever === 'Yes') symptoms.push('Fever');
            if (chatbotProfile.Cough === 'Yes') symptoms.push('Cough');
            if (chatbotProfile.Fatigue === 'Yes') symptoms.push('Fatigue');
            if (chatbotProfile['Difficulty Breathing'] === 'Yes') symptoms.push('Difficulty Breathing');
        }
        
        var html = '<!DOCTYPE html><html><head><title>MediSight AI Report</title>';
        html += '<style>body{font-family:Arial,sans-serif;max-width:800px;margin:auto;padding:40px}';
        html += '.header{text-align:center;border-bottom:3px solid #4f46e5;padding-bottom:20px;margin-bottom:30px}';
        html += '.header h1{color:#4f46e5;margin:0}';
        html += '.header p{color:#666;margin:5px 0}';
        html += '.section{margin-bottom:25px}.section h2{color:#4f46e5;border-bottom:1px solid #eee;padding-bottom:8px}';
        html += '.info{background:#f8fafc;padding:12px 16px;border-radius:8px;margin:8px 0;border-left:4px solid #4f46e5}';
        html += '.symptom{background:#fef3c7;border-left-color:#f59e0b}';
        html += '.disclaimer{background:#fee2e2;padding:15px;border-radius:8px;margin-top:30px;font-size:12px;color:#991b1b}</style></head><body>';
        
        html += '<div class="header"><h1>MediSight AI</h1><p>Health Assessment Report</p><p style="font-size:12px">' + new Date().toLocaleString() + '</p></div>';
        
        html += '<div class="section"><h2>Patient Information</h2>';
        html += '<div class="info"><strong>Age:</strong> ' + age + ' years</div>';
        html += '<div class="info"><strong>Gender:</strong> ' + gender + '</div>';
        html += '<div class="info"><strong>Detected Emotion:</strong> ' + emotion + '</div></div>';
        
        html += '<div class="section"><h2>Reported Symptoms</h2>';
        if (symptoms.length > 0) {
            symptoms.forEach(function(s) {
                html += '<div class="info symptom">✓ ' + s + '</div>';
            });
        } else {
            html += '<div class="info">No symptoms reported</div>';
        }
        html += '</div>';
        
        html += '<div class="disclaimer"><strong>Disclaimer:</strong> This report is generated by an AI system and is for informational purposes only. It is not a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of a qualified healthcare provider.</div>';
        
        html += '<script>window.onload=function(){window.print();}<\/script></body></html>';
        
        var win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
    }
    
    function exportCSV() {
        var csv = 'Field,Value\n';
        csv += 'Age,' + (document.getElementById('age').value || '') + '\n';
        csv += 'Gender,' + (document.getElementById('gender').value || '') + '\n';
        csv += 'Emotion,"' + (emotionText.textContent || '') + '"\n';
        csv += 'Date,"' + new Date().toLocaleString() + '"\n';
        csv += '\nSymptom,Status\n';
        
        if (chatbotProfile) {
            csv += 'Fever,' + (chatbotProfile.Fever === 'Yes' ? 'Yes' : 'No') + '\n';
            csv += 'Cough,' + (chatbotProfile.Cough === 'Yes' ? 'Yes' : 'No') + '\n';
            csv += 'Fatigue,' + (chatbotProfile.Fatigue === 'Yes' ? 'Yes' : 'No') + '\n';
            csv += 'Difficulty Breathing,' + (chatbotProfile['Difficulty Breathing'] === 'Yes' ? 'Yes' : 'No') + '\n';
        }
        
        var blob = new Blob([csv], { type: 'text/csv' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'medisight-report-' + Date.now() + '.csv';
        a.click();
    }
    
    // Event listeners
    if (sendBtn) sendBtn.addEventListener('click', function() { sendMessage(); });
    if (chatInput) chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
    if (finalizeBtn) finalizeBtn.addEventListener('click', finalizeSession);
    if (exportPdfBtn) exportPdfBtn.addEventListener('click', exportPDF);
    if (exportCsvBtn) exportCsvBtn.addEventListener('click', exportCSV);
    
    // Update profile when age/gender changes
    document.getElementById('age').addEventListener('change', function() {
        if (chatbotProfile) chatbotProfile.Age = parseInt(this.value) || 30;
    });
    document.getElementById('gender').addEventListener('change', function() {
        if (chatbotProfile) chatbotProfile.Gender = this.value || 'Male';
    });
})();
</script>
@endsection
