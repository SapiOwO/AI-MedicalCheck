@extends('layouts.medisight')

@section('title', 'MediSight AI – Health Assessment')

@section('nav-links')
    <a href="{{ url('/session/camera') }}" id="backToCameraLink">Back to Camera</a>
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
    
    /* Symptom Picker Styles */
    .symptom-add-btn {
        background: rgba(79, 70, 229, 0.2);
        border: 1px solid rgba(79, 70, 229, 0.5);
        color: #818cf8;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
    
    .symptom-picker {
        margin-top: 8px;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(55, 65, 81, 0.8);
        border-radius: 8px;
        padding: 8px;
    }
    
    .symptom-select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid rgba(55, 65, 81, 0.9);
        background: rgba(15, 23, 42, 0.95);
        color: var(--text);
        font-size: 13px;
        margin-bottom: 8px;
    }
    
    .symptom-list {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(55, 65, 81, 0.5);
        border-radius: 12px;
        padding: 12px;
        min-height: 60px;
        font-size: 14px;
        color: #cbd5e1;
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: flex-start;
    }
    
    .symptom-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(79, 70, 229, 0.2);
        color: #818cf8;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 12px;
    }
    
    .symptom-tag .remove-btn {
        background: none;
        border: none;
        color: #818cf8;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        line-height: 1;
        opacity: 0.7;
    }
    
    .symptom-tag .remove-btn:hover {
        opacity: 1;
        color: #ef4444;
    }
    
    /* Light Mode for Symptom UI */
    body.light-mode .symptom-add-btn {
        background: rgba(79, 70, 229, 0.15);
        border-color: rgba(79, 70, 229, 0.4);
        color: #4f46e5;
    }
    
    body.light-mode .symptom-picker {
        background: #ffffff;
        border-color: #e2e8f0;
    }
    
    body.light-mode .symptom-select {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    
    body.light-mode .symptom-list {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #475569;
    }
    
    body.light-mode .symptom-tag {
        background: rgba(79, 70, 229, 0.15);
        color: #4f46e5;
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

                <!-- Symptom List -->
                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: flex; align-items: center; justify-content: space-between;">
                        Identified Symptoms
                        <button type="button" id="addSymptomBtn" onclick="toggleSymptomPicker()" class="symptom-add-btn" style="display: none;">+</button>
                    </label>
                    
                    <!-- Symptom Picker Dropdown -->
                    <div id="symptomPicker" class="symptom-picker" style="display: none;">
                        <select id="symptomSelect" class="symptom-select">
                            <option value="">Select a symptom...</option>
                            <option value="Fever" data-msg="I have a fever">Fever</option>
                            <option value="Cough" data-msg="I have a cough">Cough</option>
                            <option value="Fatigue" data-msg="I feel tired and fatigued">Fatigue</option>
                            <option value="Difficulty Breathing" data-msg="I have difficulty breathing">Difficulty Breathing</option>
                            <option value="Blood Pressure" data-msg="I have high blood pressure">High Blood Pressure</option>
                            <option value="Cholesterol Level" data-msg="I have high cholesterol">High Cholesterol</option>
                        </select>
                        <button type="button" onclick="addSelectedSymptom()" class="btn-primary" style="width: 100%; padding: 8px; font-size: 13px;">
                            Add to Chat
                        </button>
                    </div>
                    
                    <div id="symptomList" class="symptom-list">
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
            
            <div class="chat-input-row" id="chatInputRow" style="display: none;">
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
    
    // Load detection data from localStorage (saved by camera page)
    var storedDetection = localStorage.getItem('detection_data');
    var detectionData = storedDetection ? JSON.parse(storedDetection) : {};
    
    var isReadOnly = false;
    
    // Chatbot state
    var chatbotProfile = null;
    var lastContext = '';
    var diagnosisGiven = false;
    var guestMessages = []; // Store messages for guests to save later
    
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
    
    // Helper: Get auth headers
    function getHeaders() {
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }
        return headers;
    }
    
    // Helper: Ensure session exists in database
    function ensureSession() {
        return new Promise(function(resolve, reject) {
            if (sessionId) {
                resolve(sessionId);
                return;
            }
            
            // For guests, don't create session now - will be created on finalize/login
            if (isGuest) {
                resolve(null);
                return;
            }
            
            // Create new session for registered users
            fetch(API_URL + '/chat/session/start', {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({
                    emotion: detectionData.emotion ? detectionData.emotion.emotion : null,
                    emotion_confidence: detectionData.emotion ? detectionData.emotion.confidence : null,
                    current_step: 'profile'
                })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    sessionId = data.data.session.id;
                    sessionToken = data.data.session.session_token;
                    localStorage.setItem('current_session_id', sessionId);
                    localStorage.setItem('current_session_token', sessionToken);
                    resolve(sessionId);
                } else {
                    reject(new Error('Failed to create session'));
                }
            })
            .catch(reject);
        });
    }
    
    // Helper: Save message to database
    function saveMessageToDB(sender, message) {
        // For guests, store messages locally to save later when they login
        if (isGuest) {
            guestMessages.push({ sender: sender, message: message });
            return Promise.resolve();
        }
        
        if (!sessionId) return Promise.resolve();
        
        return fetch(API_URL + '/chat/message/store', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                session_id: parseInt(sessionId),
                sender: sender,
                message: message
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) {
                console.warn('Failed to save message:', data.error);
            }
            return data;
        })
        .catch(function(err) {
            console.warn('Error saving message:', err);
        });
    }
    
    // Helper: Save symptom data to database (for session resume)
    function saveSymptomData() {
        if (isGuest || !sessionId) return;
        
        var symptomData = {
            fever: chatbotProfile.Fever === 'Yes',
            cough: chatbotProfile.Cough === 'Yes',
            fatigue: chatbotProfile.Fatigue === 'Yes',
            difficulty_breathing: chatbotProfile['Difficulty Breathing'] === 'Yes',
            blood_pressure: chatbotProfile['Blood Pressure'] || 'Normal',
            cholesterol: chatbotProfile['Cholesterol Level'] || 'Normal'
        };
        
        fetch(API_URL + '/session/update', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                session_id: parseInt(sessionId),
                symptom_data: symptomData,
                age: parseInt(document.getElementById('age').value) || null,
                gender: document.getElementById('gender').value || null
            })
        })
        .catch(function(err) {
            console.warn('Error saving symptom data:', err);
        });
    }
    
    // Helper: Save initial session data including ai_detection_data (emotion)
    function saveInitialSessionData() {
        if (isGuest || !sessionId) return;
        
        fetch(API_URL + '/session/update', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                session_id: parseInt(sessionId),
                ai_detection_data: detectionData,
                age: parseInt(document.getElementById('age').value) || null,
                gender: document.getElementById('gender').value || null
            })
        })
        .catch(function(err) {
            console.warn('Error saving initial session data:', err);
        });
    }
    
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
                
                // Load patient info
                if (session.age) document.getElementById('age').value = session.age;
                if (session.gender) document.getElementById('gender').value = session.gender;
                
                // Load emotion from ai_detection_data
                if (session.ai_detection_data && session.ai_detection_data.emotion) {
                    var emotionData = session.ai_detection_data.emotion;
                    updateEmotionDisplay(emotionData.emotion, emotionData.confidence);
                    detectionData = session.ai_detection_data;
                }
                
                // Initialize chatbot profile with saved symptom data
                initChatbotProfile();
                
                // Load saved symptoms from symptom_data (only set values that were actually saved)
                if (session.symptom_data) {
                    // Only set if explicitly defined (true/false, not undefined)
                    if (session.symptom_data.fever === true) chatbotProfile.Fever = 'Yes';
                    else if (session.symptom_data.fever === false) chatbotProfile.Fever = 'No';
                    
                    if (session.symptom_data.cough === true) chatbotProfile.Cough = 'Yes';
                    else if (session.symptom_data.cough === false) chatbotProfile.Cough = 'No';
                    
                    if (session.symptom_data.fatigue === true) chatbotProfile.Fatigue = 'Yes';
                    else if (session.symptom_data.fatigue === false) chatbotProfile.Fatigue = 'No';
                    
                    if (session.symptom_data.difficulty_breathing === true) chatbotProfile['Difficulty Breathing'] = 'Yes';
                    else if (session.symptom_data.difficulty_breathing === false) chatbotProfile['Difficulty Breathing'] = 'No';
                    
                    if (session.symptom_data.blood_pressure) chatbotProfile['Blood Pressure'] = session.symptom_data.blood_pressure;
                    if (session.symptom_data.cholesterol) chatbotProfile['Cholesterol Level'] = session.symptom_data.cholesterol;
                    
                    // Update symptom viewer
                    updateSymptomViewer();
                }
                
                // Load chat messages
                if (session.messages && session.messages.length > 0) {
                    var lastBotMessage = '';
                    session.messages.forEach(function(msg) {
                        if (msg.sender === 'bot') {
                            addBotMessage(msg.message);
                            lastBotMessage = msg.message; // Track last bot message
                        } else {
                            addUserMessage(msg.message);
                        }
                    });
                    
                    // Set lastContext to last bot message so chatbot knows where to continue
                    lastContext = lastBotMessage;
                    
                    // Show chat input since conversation has started
                    document.getElementById('chatInputRow').style.display = 'flex';
                    document.getElementById('startChatBtn').style.display = 'none';
                    
                    // Check if diagnosis was given in any message
                    session.messages.forEach(function(msg) {
                        if (msg.message && (msg.message.includes('Diagnosis') || msg.message.includes('Match'))) {
                            diagnosisGiven = true;
                            var addSymptomBtn = document.getElementById('addSymptomBtn');
                            if (addSymptomBtn) addSymptomBtn.style.display = 'inline-block';
                        }
                    });
                }
                
                // Enable read-only mode LAST (after all data is loaded)
                if (session.status === 'completed') {
                    enableReadOnlyMode();
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
        document.getElementById('pageSubtitle').textContent = 'This session has been archived and is now read-only.';
        document.getElementById('statusText').textContent = 'Archived';
        readonlyNotice.style.display = 'block';
        
        // Hide "Back to Camera" link in navbar for archived sessions
        var backToCameraLink = document.getElementById('backToCameraLink');
        if (backToCameraLink) backToCameraLink.style.display = 'none';
        
        // Disable all inputs
        document.getElementById('age').disabled = true;
        document.getElementById('gender').disabled = true;
        
        // Hide chat input and finalize button
        chatInputRow.style.display = 'none';
        if (finalizeBtn) finalizeBtn.style.display = 'none';
        
        // Hide Start AI Consultation button
        var startChatBtn = document.getElementById('startChatBtn');
        if (startChatBtn) startChatBtn.style.display = 'none';
        
        // Hide symptom add button and picker
        var addSymptomBtn = document.getElementById('addSymptomBtn');
        if (addSymptomBtn) addSymptomBtn.style.display = 'none';
        var symptomPicker = document.getElementById('symptomPicker');
        if (symptomPicker) symptomPicker.style.display = 'none';
        
        // Disable quick buttons
        document.querySelectorAll('.quick-btn').forEach(function(btn) {
            btn.disabled = true;
        });
        
        // Hide remove buttons in symptom tags
        document.querySelectorAll('.symptom-tag .remove-btn').forEach(function(btn) {
            btn.style.display = 'none';
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
            // Check main symptoms
            ['Fever', 'Cough', 'Fatigue', 'Difficulty Breathing'].forEach(function(k) {
                if (chatbotProfile[k] === 'Yes') {
                    symptoms.push(k);
                }
            });
            // Check vitals
            if (chatbotProfile['Blood Pressure'] === 'High') {
                symptoms.push('Blood Pressure');
            }
            if (chatbotProfile['Cholesterol Level'] === 'High') {
                symptoms.push('Cholesterol Level');
            }
        }
        
        if (symptoms.length === 0) {
            container.innerHTML = '<em style="opacity: 0.5;">No symptoms identified yet...</em>';
        } else {
            container.innerHTML = symptoms.map(function(s) {
                // Only show remove button if diagnosis is given and not in read-only mode
                var removeBtn = (isReadOnly || !diagnosisGiven) ? '' : '<button type="button" class="remove-btn" onclick="removeSymptom(\'' + s + '\')">&times;</button>';
                return '<span class="symptom-tag">' + s + removeBtn + '</span>';
            }).join('');
        }
        
        // Update dropdown to hide already-selected symptoms
        updateSymptomDropdown(symptoms);
    }
    
    function updateSymptomDropdown(selectedSymptoms) {
        var select = document.getElementById('symptomSelect');
        if (!select) return;
        
        var options = select.querySelectorAll('option');
        options.forEach(function(opt) {
            if (opt.value && selectedSymptoms.indexOf(opt.value) !== -1) {
                opt.style.display = 'none';
            } else {
                opt.style.display = '';
            }
        });
    }
    
    // Symptom Picker Functions
    window.toggleSymptomPicker = function() {
        // Only allow if diagnosis has been given
        if (!diagnosisGiven) {
            alert('Please complete the AI consultation first to modify symptoms.');
            return;
        }
        
        var picker = document.getElementById('symptomPicker');
        if (picker) {
            picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
            
            // Update dropdown to hide already-selected symptoms
            var symptoms = [];
            if (chatbotProfile) {
                ['Fever', 'Cough', 'Fatigue', 'Difficulty Breathing'].forEach(function(k) {
                    if (chatbotProfile[k] === 'Yes') symptoms.push(k);
                });
                if (chatbotProfile['Blood Pressure'] === 'High') symptoms.push('Blood Pressure');
                if (chatbotProfile['Cholesterol Level'] === 'High') symptoms.push('Cholesterol Level');
            }
            updateSymptomDropdown(symptoms);
        }
    };
    
    window.addSelectedSymptom = function() {
        var select = document.getElementById('symptomSelect');
        var selectedOption = select.options[select.selectedIndex];
        
        if (!select || !select.value) {
            alert('Please select a symptom first.');
            return;
        }
        
        var symptomKey = select.value; // e.g., "Fever", "Blood Pressure"
        var chatMsg = selectedOption.getAttribute('data-msg'); // e.g., "I have a fever"
        
        // Hide picker
        document.getElementById('symptomPicker').style.display = 'none';
        select.value = '';
        
        // Send to chat
        sendMessage(chatMsg);
    };
    
    window.removeSymptom = function(symptomKey) {
        if (!chatbotProfile) return;
        if (!diagnosisGiven) return; // Prevent removal before diagnosis
        
        // Determine the chat message for removal
        var removeMessages = {
            'Fever': "I don't have a fever",
            'Cough': "I don't have a cough",
            'Fatigue': "I don't feel fatigued",
            'Difficulty Breathing': "I don't have difficulty breathing",
            'Blood Pressure': "I don't have high blood pressure",
            'Cholesterol Level': "I don't have high cholesterol"
        };
        
        // Clear from profile
        if (symptomKey === 'Blood Pressure') {
            chatbotProfile['Blood Pressure'] = 'Normal';
        } else if (symptomKey === 'Cholesterol Level') {
            chatbotProfile['Cholesterol Level'] = 'Normal';
        } else {
            chatbotProfile[symptomKey] = 'No';
        }
        
        // Update UI
        updateSymptomViewer();
        
        // Send removal message to chat
        var msg = removeMessages[symptomKey] || ("I don't have " + symptomKey.toLowerCase());
        sendMessage(msg);
    };
    
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
        
        // Hide Start button, show chat input
        document.getElementById('startChatBtn').style.display = 'none';
        document.getElementById('chatInputRow').style.display = 'flex';
        
        // Show typing
        showTyping();
        
        // Ensure session exists first, then get greeting
        ensureSession()
        .then(function() {
            return fetch(PYTHON_API + '/chatbot/greeting', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ profile: chatbotProfile })
            });
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            hideTyping();
            var greetingMsg = data.success ? data.greeting : 'Hello! I\'m your MediSight AI. Please describe your symptoms.';
            addBotMessage(greetingMsg);
            lastContext = greetingMsg;
            
            // Save greeting to database
            saveMessageToDB('bot', greetingMsg);
            
            // Save initial data including ai_detection_data (emotion)
            saveInitialSessionData();
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
        
        // Save user message to DB first
        saveMessageToDB('user', message);
        
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
            sendBtn.disabled = false;
            
            if (data.success) {
                chatbotProfile = data.profile;
                lastContext = data.response;
                addBotMessage(data.response);
                
                // Save bot response to DB
                saveMessageToDB('bot', data.response);
                
                updateSymptomViewer();
                
                // Auto-save symptom data so session can be resumed properly
                saveSymptomData();
                
                if (data.response.includes('Diagnosis') || data.response.includes('Match')) {
                    diagnosisGiven = true;
                    // Show symptom add button after diagnosis
                    var addSymptomBtn = document.getElementById('addSymptomBtn');
                    if (addSymptomBtn) addSymptomBtn.style.display = 'inline-block';
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

    // Guest Handling - only show login prompt, don't interfere with finalize
    // The actual finalize listener is at the bottom of the script

    // Clear all guest session data (only called on explicit End Session)
    function clearGuestData() {
        localStorage.removeItem('current_session_id');
        localStorage.removeItem('current_session_token');
        localStorage.removeItem('detection_data');
        localStorage.removeItem('pending_session_data');
        localStorage.removeItem('pending_chat_messages');
    }

    if (isGuest && document.getElementById('finalizeSection')) {
        var endBtn = document.createElement('button');
        endBtn.className = 'btn-secondary';
        endBtn.textContent = 'End Session';
        endBtn.style.padding = '14px 24px';
        endBtn.style.marginLeft = '12px';
        endBtn.onclick = function() {
            if (confirm('Are you sure? All session data will be discarded.')) {
                clearGuestData();
                window.location.href = '{{ url("/") }}';
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
        
        // Require diagnosis to be given first (user must complete AI chat questions)
        if (!diagnosisGiven) {
            alert('Please complete the AI consultation first. Answer the 4 symptom questions to get a diagnosis before finalizing.');
            return;
        }
        
        finalizeBtn.disabled = true;
        finalizeBtn.textContent = 'Saving...';
        
        var symptomData = {
            fever: chatbotProfile.Fever === 'Yes',
            cough: chatbotProfile.Cough === 'Yes',
            fatigue: chatbotProfile.Fatigue === 'Yes',
            difficulty_breathing: chatbotProfile['Difficulty Breathing'] === 'Yes',
            blood_pressure: chatbotProfile['Blood Pressure'] || 'Normal',
            cholesterol: chatbotProfile['Cholesterol Level'] || 'Normal'
        };
        
        var sessionData = {
            age: parseInt(age),
            gender: gender,
            symptom_data: symptomData,
            ai_detection_data: detectionData,
            current_step: 'completed'
        };
        
        if (isGuest) {
            // Save session data and chat messages for after login
            localStorage.setItem('pending_session_data', JSON.stringify(sessionData));
            localStorage.setItem('pending_chat_messages', JSON.stringify(guestMessages));
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
            if (chatbotProfile['Blood Pressure'] === 'High') symptoms.push('High Blood Pressure');
            if (chatbotProfile['Cholesterol Level'] === 'High') symptoms.push('High Cholesterol');
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
            csv += 'Blood Pressure,' + (chatbotProfile['Blood Pressure'] === 'High' ? 'High' : 'Normal') + '\n';
            csv += 'Cholesterol Level,' + (chatbotProfile['Cholesterol Level'] === 'High' ? 'High' : 'Normal') + '\n';
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
