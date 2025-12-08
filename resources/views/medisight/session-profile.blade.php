@extends('layouts.medisight')

@section('title', 'MediSight AI – Profile & Chat')

@section('nav-links')
    <a href="{{ url('/session/camera') }}">Back to Camera</a>
    <a href="{{ url('/dashboard') }}">Dashboard</a>
@endsection

@section('styles')
<style>
    .profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.3fr);
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
    
    .form-group input.error,
    .form-group select.error {
        border-color: #ef4444 !important;
    }
    
    .error-text {
        color: #ef4444;
        font-size: 11px;
        margin-top: 2px;
    }
    
    .symptom-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(55, 65, 81, 0.9);
        background: rgba(15, 23, 42, 0.95);
        cursor: pointer;
        transition: border-color 0.2s, opacity 0.2s;
    }
    
    .symptom-toggle.locked {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .symptom-toggle:hover:not(.locked) {
        border-color: var(--accent);
    }
    
    .symptom-toggle.active {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.1);
    }
    
    .symptom-toggle .toggle-label {
        font-size: 14px;
    }
    
    .symptom-toggle .indicator {
        width: 36px;
        height: 20px;
        border-radius: 10px;
        background: #374151;
        position: relative;
        transition: background 0.2s;
    }
    
    .symptom-toggle.active .indicator {
        background: #22c55e;
    }
    
    .symptom-toggle .indicator::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        top: 2px;
        left: 2px;
        transition: left 0.2s;
    }
    
    .symptom-toggle.active .indicator::after {
        left: 18px;
    }
    
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 550px;
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
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .msg-bot {
        align-self: flex-start;
        background: rgba(15, 23, 42, 0.98);
        border: 1px solid rgba(55, 65, 81, 0.95);
    }
    
    .msg-bot .sender {
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 4px;
    }
    
    .msg-user {
        align-self: flex-end;
        background: var(--accent);
        color: white;
    }
    
    .quick-replies {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }
    
    .quick-reply-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid rgba(79, 70, 229, 0.5);
        background: transparent;
        color: var(--accent);
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }
    
    .quick-reply-btn:hover {
        background: var(--accent);
        color: white;
    }
    
    .finalize-section {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .lock-notice {
        font-size: 11px;
        color: var(--muted);
        font-style: italic;
        margin-top: 4px;
    }
    
    .validation-errors {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        display: none;
    }
    
    .validation-errors ul {
        margin: 0;
        padding-left: 20px;
        color: #ef4444;
        font-size: 13px;
    }
    
    /* Light mode fixes */
    body.light-mode .form-group input,
    body.light-mode .form-group select {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    
    body.light-mode .symptom-toggle {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    
    body.light-mode .symptom-toggle .toggle-label {
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
    
    body.light-mode .quick-reply-btn {
        border-color: var(--accent) !important;
        color: var(--accent) !important;
        background: white !important;
    }
    
    body.light-mode .quick-reply-btn:hover {
        background: var(--accent) !important;
        color: white !important;
    }
    
    body.light-mode .chat-input-row input {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    
    @media (max-width: 900px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
        .chat-container {
            height: 450px;
        }
    }
</style>
@endsection

@section('content')
<section>
    <div class="badge">
        <div class="badge-dot"></div>
        Step 2 of 2 - Profile & AI Chat
    </div>

    <h1 class="hero-title" style="font-size: 26px; margin-bottom: 8px;">
        Review your profile and chat.
    </h1>
    <p class="hero-subtitle">
        The AI has generated a profile for you. Review and answer the health questions below.
    </p>

    <div class="profile-layout">
        <!-- Left: Patient Profile -->
        <div class="card profile-card">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">Patient Profile</h2>
                    <p class="section-subtitle" style="margin-bottom: 0;">
                        Editable data
                    </p>
                </div>
                <span class="chat-status">
                    <span class="hero-indicator" style="background: #22c55e;"></span>
                    Active
                </span>
            </div>
            
            <form class="profile-form" id="profileForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="age">Age <span style="color: #ef4444;">*</span></label>
                        <input type="number" id="age" min="1" max="120" placeholder="Enter age" required>
                        <span class="error-text" id="ageError" style="display: none;">Age is required</span>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender <span style="color: #ef4444;">*</span></label>
                        <select id="gender" required>
                            <option value="">Select...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="error-text" id="genderError" style="display: none;">Gender is required</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="emotion">Detected Emotion</label>
                    <input type="text" id="emotion" readonly placeholder="Not detected (camera skipped)">
                </div>
                
                <div class="form-group">
                    <label>Symptom Checks</label>
                    <p class="lock-notice" id="lockNotice">Answer all chatbot questions to unlock manual editing</p>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
                        <div class="symptom-toggle locked" data-symptom="fever">
                            <span class="toggle-label">Fever</span>
                            <div class="indicator"></div>
                        </div>
                        <div class="symptom-toggle locked" data-symptom="fatigue">
                            <span class="toggle-label">Fatigue</span>
                            <div class="indicator"></div>
                        </div>
                        <div class="symptom-toggle locked" data-symptom="pain">
                            <span class="toggle-label">Pain</span>
                            <div class="indicator"></div>
                        </div>
                        <div class="symptom-toggle locked" data-symptom="cough">
                            <span class="toggle-label">Cough</span>
                            <div class="indicator"></div>
                        </div>
                        <div class="symptom-toggle locked" data-symptom="headache">
                            <span class="toggle-label">Headache</span>
                            <div class="indicator"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: AI Chatbot -->
        <div class="card chat-container">
            <div class="chat-header">
                <div>
                    <h2 class="section-title">MediSight AI Assistant</h2>
                    <p class="section-subtitle" style="margin-bottom: 0;">
                        Context-aware conversation
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
            
            <div class="chat-input-row">
                <input type="text" id="chatInput" placeholder="Type your symptoms or questions...">
                <button class="btn-primary" id="sendBtn">Send</button>
            </div>
        </div>
    </div>
    
    <!-- Validation Errors -->
    <div class="validation-errors" id="validationErrors">
        <ul id="errorList"></ul>
    </div>
    
    <!-- Finalize Session Buttons -->
    <div class="finalize-section">
        <button class="btn-primary" id="finalizeBtn" style="padding: 14px 32px;">
            Finalize Session
        </button>
        <button class="btn-secondary" id="exportPdfBtn" style="padding: 14px 24px;">
            Export as PDF
        </button>
        <button class="btn-secondary" id="exportCsvBtn" style="padding: 14px 24px;">
            Export as CSV
        </button>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function() {
    var API_URL = '{{ url("/api") }}';
    var token = localStorage.getItem('medisight_token');
    var isGuest = !token;
    var sessionId = localStorage.getItem('current_session_id');
    var sessionToken = localStorage.getItem('current_session_token');
    var detectionData = {};
    var symptomData = {};
    var currentQuestion = 0;
    var allQuestionsAnswered = false;
    var questions = [
        { key: 'fever', text: 'Are you experiencing any fever or elevated temperature?' },
        { key: 'fatigue', text: 'Do you feel fatigue or unusual tiredness?' },
        { key: 'cough', text: 'Do you have a cough?' },
        { key: 'headache', text: 'Are you experiencing any headache?' },
        { key: 'pain', text: 'Do you feel any body pain or discomfort?' }
    ];
    
    // Elements
    var chatMessages = document.getElementById('chatMessages');
    var chatInput = document.getElementById('chatInput');
    var sendBtn = document.getElementById('sendBtn');
    var finalizeBtn = document.getElementById('finalizeBtn');
    var exportPdfBtn = document.getElementById('exportPdfBtn');
    var exportCsvBtn = document.getElementById('exportCsvBtn');
    var lockNotice = document.getElementById('lockNotice');
    var validationErrors = document.getElementById('validationErrors');
    var errorList = document.getElementById('errorList');
    
    // Load detection data
    try {
        detectionData = JSON.parse(localStorage.getItem('detection_data') || '{}');
        console.log('Detection data loaded:', detectionData);
    } catch(e) {
        console.log('No detection data');
    }
    
    // Get emotion string for AI context
    var detectedEmotion = null;
    var emotionConfidence = null;
    if (detectionData && detectionData.emotion && detectionData.emotion.emotion) {
        detectedEmotion = detectionData.emotion.emotion;
        emotionConfidence = detectionData.emotion.confidence;
        var emotionText = detectedEmotion;
        if (emotionConfidence) {
            emotionText += ' (' + (emotionConfidence * 100).toFixed(0) + '% confidence)';
        }
        document.getElementById('emotion').value = emotionText;
    }
    
    // Unlock symptom toggles after all questions answered
    function unlockToggles() {
        allQuestionsAnswered = true;
        document.querySelectorAll('.symptom-toggle').forEach(function(el) {
            el.classList.remove('locked');
        });
        if (lockNotice) lockNotice.style.display = 'none';
    }
    
    // Add symptom toggle listeners
    document.querySelectorAll('.symptom-toggle').forEach(function(el) {
        el.addEventListener('click', function() {
            if (this.classList.contains('locked')) {
                alert('Please answer all chatbot questions first before editing symptoms manually.');
                return;
            }
            this.classList.toggle('active');
            var key = this.getAttribute('data-symptom');
            symptomData[key] = this.classList.contains('active');
        });
    });
    
    // Add bot message
    function addBotMessage(text, showButtons) {
        var div = document.createElement('div');
        div.className = 'msg msg-bot';
        
        var html = '<div class="sender">MediSight AI</div><div>' + text + '</div>';
        
        if (showButtons) {
            html += '<div class="quick-replies">';
            html += '<button class="quick-reply-btn" data-answer="yes">Yes</button>';
            html += '<button class="quick-reply-btn" data-answer="no">No</button>';
            html += '</div>';
        }
        
        div.innerHTML = html;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        if (showButtons) {
            div.querySelectorAll('.quick-reply-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    answerQuestion(this.getAttribute('data-answer') === 'yes');
                });
            });
        }
    }
    
    // Add user message
    function addUserMessage(text) {
        var div = document.createElement('div');
        div.className = 'msg msg-user';
        div.textContent = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Ask question
    function askQuestion() {
        if (currentQuestion < questions.length) {
            addBotMessage(questions[currentQuestion].text, true);
        } else {
            unlockToggles();
            showSummary();
        }
    }
    
    // Answer question
    function answerQuestion(isYes) {
        document.querySelectorAll('.quick-replies').forEach(function(b) { b.remove(); });
        
        addUserMessage(isYes ? 'Yes' : 'No');
        
        var key = questions[currentQuestion].key;
        symptomData[key] = isYes;
        
        var toggle = document.querySelector('.symptom-toggle[data-symptom="' + key + '"]');
        if (toggle) {
            if (isYes) toggle.classList.add('active');
            else toggle.classList.remove('active');
        }
        
        currentQuestion++;
        
        setTimeout(function() {
            askQuestion();
        }, 500);
    }
    
    // Show summary
    function showSummary() {
        var symptoms = [];
        for (var k in symptomData) {
            if (symptomData[k]) symptoms.push(k);
        }
        
        var msg = 'I have recorded your symptoms. ';
        if (symptoms.length > 0) {
            msg += 'You are experiencing: ' + symptoms.join(', ') + '. ';
        } else {
            msg += 'You do not have any concerning symptoms. ';
        }
        msg += 'You can now edit the symptom toggles manually if needed. Click Finalize Session to save your data.';
        
        addBotMessage(msg);
    }
    
    // Send message - AI aware of emotion data
    function sendMessage() {
        var message = chatInput.value.trim();
        if (!message) return;
        
        addUserMessage(message);
        chatInput.value = '';
        
        // Check if user is asking about their emotion
        var lowerMsg = message.toLowerCase();
        var response = '';
        
        if (lowerMsg.includes('sad') || lowerMsg.includes('emotion') || lowerMsg.includes('feel') || 
            lowerMsg.includes('mood') || lowerMsg.includes('happy') || lowerMsg.includes('angry')) {
            
            if (detectedEmotion) {
                var confPercent = emotionConfidence ? (emotionConfidence * 100).toFixed(0) + '%' : 'unknown';
                response = 'Based on my camera analysis, I detected that you appear to be feeling "' + detectedEmotion + 
                           '" with ' + confPercent + ' confidence. ';
                
                if (detectedEmotion === 'sad') {
                    response += 'I noticed some sadness in your expression. Would you like to talk about what might be causing this?';
                } else if (detectedEmotion === 'happy') {
                    response += 'You seem to be in a good mood! That is great for your overall wellbeing.';
                } else if (detectedEmotion === 'angry' || detectedEmotion === 'fear') {
                    response += 'This emotional state might be affecting your physical health. Let me know if you want to discuss it.';
                } else {
                    response += 'Is there anything specific you would like to discuss about how you are feeling?';
                }
            } else {
                response = 'I do not have emotion detection data for you. You may have skipped the camera step. ' +
                           'You can go back and take a photo for AI emotion analysis, or continue with the symptom assessment.';
            }
        } else {
            response = 'Thank you for your message. ';
            if (detectedEmotion) {
                response += 'By the way, I detected that you are feeling "' + detectedEmotion + '" based on your photo. ';
            }
            if (!allQuestionsAnswered) {
                response += 'Please continue answering the symptom questions above.';
            } else {
                response += 'You can click Finalize Session when you are ready to save your data.';
            }
        }
        
        setTimeout(function() {
            addBotMessage(response);
        }, 500);
    }
    
    // Validate before finalize
    function validateForm() {
        var errors = [];
        var ageInput = document.getElementById('age');
        var genderSelect = document.getElementById('gender');
        
        // Reset errors
        ageInput.classList.remove('error');
        genderSelect.classList.remove('error');
        document.getElementById('ageError').style.display = 'none';
        document.getElementById('genderError').style.display = 'none';
        
        // Check age
        if (!ageInput.value || ageInput.value < 1) {
            errors.push('Age is required');
            ageInput.classList.add('error');
            document.getElementById('ageError').style.display = 'block';
        }
        
        // Check gender
        if (!genderSelect.value) {
            errors.push('Gender is required');
            genderSelect.classList.add('error');
            document.getElementById('genderError').style.display = 'block';
        }
        
        // Check if all questions answered
        if (!allQuestionsAnswered) {
            errors.push('Please answer all symptom questions in the chatbot');
        }
        
        if (errors.length > 0) {
            errorList.innerHTML = errors.map(function(e) { return '<li>' + e + '</li>'; }).join('');
            validationErrors.style.display = 'block';
            return false;
        }
        
        validationErrors.style.display = 'none';
        return true;
    }
    
    // Finalize session
    function finalizeSession() {
        if (!validateForm()) {
            return;
        }
        
        finalizeBtn.disabled = true;
        finalizeBtn.textContent = 'Saving...';
        
        var age = document.getElementById('age').value;
        var gender = document.getElementById('gender').value;
        
        // Prepare data to save
        var sessionData = {
            age: parseInt(age),
            gender: gender,
            symptom_data: symptomData,
            ai_detection_data: detectionData,
            current_step: 'completed'
        };
        
        // If guest, save to localStorage and redirect to login
        if (isGuest) {
            localStorage.setItem('pending_session_data', JSON.stringify(sessionData));
            alert('Your session has been saved temporarily. Please login or sign up to save it permanently.');
            window.location.href = '{{ url("/login") }}?redirect=save_session';
            return;
        }
        
        // Logged in user - save to database
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        };
        
        // Create session if doesn't exist
        var createSessionIfNeeded = new Promise(function(resolve) {
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
                        sessionToken = data.data.session.session_token;
                        localStorage.setItem('current_session_id', sessionId);
                        localStorage.setItem('current_session_token', sessionToken);
                    }
                    resolve();
                })
                .catch(function() { resolve(); });
            }
        });
        
        createSessionIfNeeded.then(function() {
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
                
                alert('Session saved successfully! Redirecting to dashboard...');
                window.location.href = '{{ url("/dashboard") }}';
            } else {
                throw new Error(data.error || 'Failed to save');
            }
        })
        .catch(function(error) {
            console.error('Finalize error:', error);
            alert('Failed to save session. Please try again.');
            finalizeBtn.disabled = false;
            finalizeBtn.textContent = 'Finalize Session';
        });
    }
    
    // Export PDF
    function exportPDF() {
        var age = document.getElementById('age').value || 'Not specified';
        var gender = document.getElementById('gender').value || 'Not specified';
        var emotion = document.getElementById('emotion').value || 'Not detected';
        var date = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', month: 'long', day: 'numeric', 
            hour: '2-digit', minute: '2-digit' 
        });
        
        var symptoms = [];
        var noSymptoms = [];
        for (var k in symptomData) {
            if (symptomData[k]) {
                symptoms.push(k.charAt(0).toUpperCase() + k.slice(1));
            } else {
                noSymptoms.push(k.charAt(0).toUpperCase() + k.slice(1));
            }
        }
        
        var html = '<!DOCTYPE html><html><head><title>MediSight AI Health Report</title>';
        html += '<style>';
        html += 'body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 40px; color: #333; }';
        html += '.header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }';
        html += '.header h1 { color: #4f46e5; margin: 0; font-size: 28px; }';
        html += '.header p { color: #666; margin: 10px 0 0; }';
        html += '.section { margin-bottom: 25px; }';
        html += '.section h2 { color: #4f46e5; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 8px; }';
        html += '.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }';
        html += '.info-item { background: #f8fafc; padding: 12px; border-radius: 8px; }';
        html += '.info-item label { font-size: 12px; color: #666; display: block; margin-bottom: 4px; }';
        html += '.info-item span { font-size: 16px; font-weight: 500; }';
        html += '.symptom-list { list-style: none; padding: 0; }';
        html += '.symptom-list li { padding: 8px 12px; margin: 4px 0; border-radius: 6px; }';
        html += '.symptom-yes { background: #fef2f2; color: #dc2626; }';
        html += '.symptom-no { background: #f0fdf4; color: #16a34a; }';
        html += '.footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999; }';
        html += '@media print { body { padding: 20px; } }';
        html += '</style></head><body>';
        
        html += '<div class="header"><h1>MediSight AI</h1><p>Health Assessment Report</p></div>';
        
        html += '<div class="section"><h2>Patient Information</h2>';
        html += '<div class="info-grid">';
        html += '<div class="info-item"><label>Age</label><span>' + age + '</span></div>';
        html += '<div class="info-item"><label>Gender</label><span>' + gender + '</span></div>';
        html += '<div class="info-item"><label>Detected Emotion</label><span>' + emotion + '</span></div>';
        html += '<div class="info-item"><label>Report Date</label><span>' + date + '</span></div>';
        html += '</div></div>';
        
        html += '<div class="section"><h2>Reported Symptoms</h2><ul class="symptom-list">';
        symptoms.forEach(function(s) { html += '<li class="symptom-yes">✓ ' + s + ' - Reported</li>'; });
        noSymptoms.forEach(function(s) { html += '<li class="symptom-no">✗ ' + s + ' - Not reported</li>'; });
        if (symptoms.length === 0 && noSymptoms.length === 0) html += '<li>No symptoms recorded</li>';
        html += '</ul></div>';
        
        html += '<div class="footer"><p>This report was generated by MediSight AI for informational purposes only.</p>';
        html += '<p>For medical advice, please consult a qualified healthcare professional.</p></div>';
        html += '<script>window.onload = function() { window.print(); };<\/script></body></html>';
        
        var win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
    }
    
    // Export CSV
    function exportCSV() {
        var age = document.getElementById('age').value || '';
        var gender = document.getElementById('gender').value || '';
        var emotion = document.getElementById('emotion').value || '';
        
        var csv = 'Field,Value\n';
        csv += 'Age,' + age + '\n';
        csv += 'Gender,' + gender + '\n';
        csv += 'Detected Emotion,"' + emotion + '"\n';
        csv += 'Report Date,' + new Date().toISOString() + '\n';
        csv += '\nSymptom,Status\n';
        for (var k in symptomData) {
            csv += k.charAt(0).toUpperCase() + k.slice(1) + ',' + (symptomData[k] ? 'Yes' : 'No') + '\n';
        }
        
        var blob = new Blob([csv], { type: 'text/csv' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'medisight-report-' + Date.now() + '.csv';
        a.click();
    }
    
    // Event listeners
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    if (chatInput) chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
    if (finalizeBtn) finalizeBtn.addEventListener('click', finalizeSession);
    if (exportPdfBtn) exportPdfBtn.addEventListener('click', exportPDF);
    if (exportCsvBtn) exportCsvBtn.addEventListener('click', exportCSV);
    
    // Start conversation with emotion context
    setTimeout(function() {
        var greeting = 'Hello! I have reviewed your profile. ';
        if (detectedEmotion) {
            greeting += 'Based on my camera analysis, I can see you are feeling "' + detectedEmotion + '". ';
        }
        greeting += 'I am here to help you with your health assessment. Let me ask you a few questions about your symptoms.';
        addBotMessage(greeting);
        setTimeout(function() {
            askQuestion();
        }, 1000);
    }, 500);
})();
</script>
@endsection
