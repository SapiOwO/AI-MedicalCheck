@extends('layouts.medisight')

@section('title', 'MediSight AI – Camera Detection')

@section('nav-links')
    <a href="{{ url('/') }}">Landing</a>
    <a href="{{ url('/dashboard') }}">Dashboard</a>
@endsection

@section('styles')
<style>
    .camera-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .camera-box {
        position: relative;
        background: #020617;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(79, 70, 229, 0.3);
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .camera-box video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .camera-box canvas {
        display: none;
    }
    
    .camera-placeholder {
        text-align: center;
        color: var(--muted);
    }
    
    .camera-placeholder .icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    .camera-controls {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .capture-btn {
        padding: 14px 28px;
        border-radius: 999px;
        border: none;
        background: var(--accent);
        color: white;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .capture-btn:hover:not(:disabled) {
        transform: scale(1.02);
        box-shadow: 0 0 30px rgba(79, 70, 229, 0.6);
    }
    
    .capture-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .preview-image {
        max-width: 100%;
        border-radius: 16px;
        border: 2px solid var(--accent);
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }
    
    /* Detection Results Panel */
    .detection-results {
        margin-top: 20px;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid rgba(79, 70, 229, 0.3);
        background: rgba(15, 23, 42, 0.5);
    }
    
    .detection-results h3 {
        margin-bottom: 16px;
        font-size: 16px;
        color: var(--accent);
    }
    
    .detection-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(55, 65, 81, 0.5);
    }
    
    .detection-item:last-child {
        border-bottom: none;
    }
    
    .detection-label {
        font-size: 14px;
        color: var(--muted);
    }
    
    .detection-value {
        font-size: 14px;
        font-weight: 600;
    }
    
    .detection-value.success {
        color: #22c55e;
    }
    
    .detection-value.warning {
        color: #f59e0b;
    }
    
    .detection-value.error {
        color: #ef4444;
    }
    
    .confidence-bar {
        height: 6px;
        width: 100px;
        background: #374151;
        border-radius: 3px;
        overflow: hidden;
        margin-left: 12px;
    }
    
    .confidence-fill {
        height: 100%;
        background: var(--accent);
        transition: width 0.3s;
    }
    
    .detection-error {
        padding: 16px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        color: #ef4444;
        text-align: center;
        margin-bottom: 16px;
    }
    
    .confirm-buttons {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }
    
    .confirm-buttons button {
        flex: 1;
        padding: 14px 20px;
    }
    
    /* Light mode fixes */
    body.light-mode .camera-box {
        background: #e5e7eb;
        border-color: #cbd5e1;
    }
    
    body.light-mode .camera-placeholder {
        color: #475569;
    }
    
    body.light-mode .btn-secondary {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    
    body.light-mode .btn-ghost {
        background: transparent !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    
    body.light-mode .detection-results {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    
    body.light-mode .detection-results h3 {
        color: var(--accent);
    }
    
    body.light-mode .detection-item {
        border-color: #e2e8f0;
    }
    
    body.light-mode .detection-label {
        color: #64748b;
    }
    
    body.light-mode .detection-value {
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<section class="camera-container">
    <div class="badge">
        <div class="badge-dot"></div>
        Step 1 of 2 - Camera Detection
    </div>

    <h1 class="hero-title" style="font-size: 26px; margin-bottom: 8px;">
        Take a photo for AI analysis
    </h1>
    <p class="hero-subtitle" style="margin-bottom: 24px;">
        Position your face in the frame and click the capture button.
        Our AI will analyze your expression to detect emotion, fatigue, and pain levels.
    </p>

    <!-- Camera View -->
    <div class="card">
        <div id="cameraView">
            <div class="camera-box" id="cameraBox">
                <video id="video" autoplay playsinline muted style="display: none;"></video>
                <canvas id="canvas"></canvas>
                
                <!-- Placeholder when camera is off -->
                <div class="camera-placeholder" id="cameraPlaceholder">
                    <div class="icon">[ ]</div>
                    <p>Camera is off</p>
                    <p style="font-size: 12px;">Click "Start Camera" to begin</p>
                </div>
            </div>
            
            <div class="camera-controls">
                <button class="btn-secondary" id="toggleCameraBtn">
                    Start Camera
                </button>
                <button class="capture-btn" id="captureBtn" disabled>
                    Capture Photo
                </button>
            </div>
        </div>
        
        <!-- Preview View (hidden by default) -->
        <div id="previewView" style="display: none;">
            <div class="camera-box">
                <img id="previewImage" class="preview-image" alt="Captured photo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <div class="camera-controls">
                <button class="btn-secondary" id="retakeBtn">
                    Retake Photo
                </button>
                <button class="btn-primary" id="analyzeBtn">
                    Analyze Photo
                </button>
            </div>
        </div>
        
        <!-- Results View (hidden by default) -->
        <div id="resultsView" style="display: none;">
            <div class="camera-box">
                <img id="resultsImage" class="preview-image" alt="Analyzed photo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <!-- Detection Results -->
            <div class="detection-results" id="detectionResults">
                <h3>AI Detection Results</h3>
                
                <div id="detectionError" class="detection-error" style="display: none;">
                    Detection failed. Please retake the photo with better lighting and face visibility.
                </div>
                
                <div id="detectionItems">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="confirm-buttons">
                <button class="btn-secondary" id="retakeBtn2">
                    Retake Photo
                </button>
                <button class="btn-primary" id="continueBtn">
                    Continue to AI Chat
                </button>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ url('/session/profile') }}" class="btn-ghost" style="text-align: center;">
                Skip camera and enter data manually
            </a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function() {
    var API_URL = '{{ url("/api") }}';
    var token = localStorage.getItem('medisight_token');

    // Elements
    var video = document.getElementById('video');
    var canvas = document.getElementById('canvas');
    var cameraBox = document.getElementById('cameraBox');
    var cameraPlaceholder = document.getElementById('cameraPlaceholder');
    var toggleCameraBtn = document.getElementById('toggleCameraBtn');
    var captureBtn = document.getElementById('captureBtn');
    var retakeBtn = document.getElementById('retakeBtn');
    var retakeBtn2 = document.getElementById('retakeBtn2');
    var analyzeBtn = document.getElementById('analyzeBtn');
    var continueBtn = document.getElementById('continueBtn');
    var cameraView = document.getElementById('cameraView');
    var previewView = document.getElementById('previewView');
    var resultsView = document.getElementById('resultsView');
    var previewImage = document.getElementById('previewImage');
    var resultsImage = document.getElementById('resultsImage');
    var detectionError = document.getElementById('detectionError');
    var detectionItems = document.getElementById('detectionItems');

    var mediaStream = null;
    var capturedBlob = null;
    var cameraOn = false;
    var detectionData = null;

    // Toggle camera on/off
    function toggleCamera() {
        if (cameraOn) {
            stopCamera();
        } else {
            startCamera();
        }
    }

    // Start camera
    function startCamera() {
        toggleCameraBtn.textContent = 'Starting...';
        toggleCameraBtn.disabled = true;
        
        navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 640, height: 480 } 
        })
        .then(function(stream) {
            mediaStream = stream;
            video.srcObject = stream;
            video.style.display = 'block';
            cameraPlaceholder.style.display = 'none';
            
            cameraOn = true;
            toggleCameraBtn.textContent = 'Stop Camera';
            toggleCameraBtn.disabled = false;
            captureBtn.disabled = false;
        })
        .catch(function(err) {
            console.error('Camera error:', err);
            toggleCameraBtn.textContent = 'Camera Blocked';
            toggleCameraBtn.disabled = false;
            alert('Could not access camera. Please allow camera permission and try again.');
        });
    }

    // Stop camera
    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(function(t) { t.stop(); });
            mediaStream = null;
        }
        video.style.display = 'none';
        cameraPlaceholder.style.display = 'block';
        
        cameraOn = false;
        toggleCameraBtn.textContent = 'Start Camera';
        captureBtn.disabled = true;
    }

    // Capture photo
    function capturePhoto() {
        if (!cameraOn) return;
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        canvas.toBlob(function(blob) {
            capturedBlob = blob;
            previewImage.src = URL.createObjectURL(blob);
            
            cameraView.style.display = 'none';
            previewView.style.display = 'block';
            
            stopCamera();
        }, 'image/jpeg', 0.9);
    }

    // Retake photo
    function retakePhoto() {
        previewView.style.display = 'none';
        resultsView.style.display = 'none';
        cameraView.style.display = 'block';
        capturedBlob = null;
        detectionData = null;
    }

    // Analyze photo
    function analyzePhoto() {
        if (!capturedBlob) return;
        
        analyzeBtn.disabled = true;
        analyzeBtn.textContent = 'Analyzing...';
        
        // Create session first
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (token) headers['Authorization'] = 'Bearer ' + token;
        
        fetch(API_URL + '/chat/session/start', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ current_step: 'camera' })
        })
        .then(function(res) { return res.json(); })
        .then(function(sessionData) {
            if (sessionData.success) {
                localStorage.setItem('current_session_id', sessionData.data.session.id);
                localStorage.setItem('current_session_token', sessionData.data.session.session_token);
            }
            
            // Send image to AI for detection
            var formData = new FormData();
            formData.append('image', capturedBlob, 'capture.jpg');
            
            return fetch(API_URL + '/detect/multi', {
                method: 'POST',
                body: formData
            });
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            console.log('Detection result:', data);
            
            if (data.success && data.data) {
                detectionData = data.data;
                showResults(true);
            } else {
                detectionData = null;
                showResults(false);
            }
        })
        .catch(function(error) {
            console.error('Analysis error:', error);
            detectionData = null;
            showResults(false);
        });
    }

    // Show results
    function showResults(success) {
        previewView.style.display = 'none';
        resultsView.style.display = 'block';
        resultsImage.src = previewImage.src;
        
        analyzeBtn.disabled = false;
        analyzeBtn.textContent = 'Analyze Photo';
        
        if (!success || !detectionData) {
            detectionError.style.display = 'block';
            detectionItems.innerHTML = '';
            continueBtn.textContent = 'Continue Anyway';
            return;
        }
        
        detectionError.style.display = 'none';
        
        var html = '';
        
        // Emotion
        if (detectionData.emotion) {
            var emotion = detectionData.emotion.emotion || 'Unknown';
            var emotionConf = (detectionData.emotion.confidence || 0) * 100;
            var emotionClass = emotionConf > 70 ? 'success' : emotionConf > 40 ? 'warning' : 'error';
            
            html += '<div class="detection-item">';
            html += '<span class="detection-label">Detected Emotion</span>';
            html += '<div style="display: flex; align-items: center;">';
            html += '<span class="detection-value ' + emotionClass + '">' + emotion + ' (' + emotionConf.toFixed(1) + '%)</span>';
            html += '<div class="confidence-bar"><div class="confidence-fill" style="width: ' + emotionConf + '%"></div></div>';
            html += '</div></div>';
        }
        
        // Fatigue
        if (detectionData.fatigue) {
            var fatigue = detectionData.fatigue.fatigue || 'Unknown';
            var fatigueConf = (detectionData.fatigue.confidence || 0) * 100;
            var fatigueClass = fatigue === 'alert' ? 'success' : 'warning';
            
            html += '<div class="detection-item">';
            html += '<span class="detection-label">Fatigue Level</span>';
            html += '<div style="display: flex; align-items: center;">';
            html += '<span class="detection-value ' + fatigueClass + '">' + fatigue + ' (' + fatigueConf.toFixed(1) + '%)</span>';
            html += '<div class="confidence-bar"><div class="confidence-fill" style="width: ' + fatigueConf + '%"></div></div>';
            html += '</div></div>';
        }
        
        // Pain
        if (detectionData.pain) {
            var pain = detectionData.pain.pain || 'Unknown';
            var painConf = (detectionData.pain.confidence || 0) * 100;
            var painClass = pain === 'no' ? 'success' : 'warning';
            
            html += '<div class="detection-item">';
            html += '<span class="detection-label">Pain Indicators</span>';
            html += '<div style="display: flex; align-items: center;">';
            html += '<span class="detection-value ' + painClass + '">' + pain + ' (' + painConf.toFixed(1) + '%)</span>';
            html += '<div class="confidence-bar"><div class="confidence-fill" style="width: ' + painConf + '%"></div></div>';
            html += '</div></div>';
        }
        
        if (html === '') {
            detectionError.style.display = 'block';
            detectionError.textContent = 'No detection results available. The AI model may not be running.';
            continueBtn.textContent = 'Continue Anyway';
        } else {
            detectionItems.innerHTML = html;
            continueBtn.textContent = 'Continue to AI Chat';
        }
    }

    // Continue to profile
    function continueToProfile() {
        // Save detection data
        if (detectionData) {
            localStorage.setItem('detection_data', JSON.stringify(detectionData));
        } else {
            localStorage.setItem('detection_data', '{}');
        }
        
        window.location.href = '{{ url("/session/profile") }}';
    }

    // Event listeners
    toggleCameraBtn.addEventListener('click', toggleCamera);
    captureBtn.addEventListener('click', capturePhoto);
    retakeBtn.addEventListener('click', retakePhoto);
    retakeBtn2.addEventListener('click', retakePhoto);
    analyzeBtn.addEventListener('click', analyzePhoto);
    continueBtn.addEventListener('click', continueToProfile);
})();
</script>
@endsection
