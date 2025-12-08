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
                    Analyze and Continue
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
const API_URL = '{{ url("/api") }}';
const token = localStorage.getItem('medisight_token');

// Elements
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const cameraBox = document.getElementById('cameraBox');
const cameraPlaceholder = document.getElementById('cameraPlaceholder');
const toggleCameraBtn = document.getElementById('toggleCameraBtn');
const captureBtn = document.getElementById('captureBtn');
const retakeBtn = document.getElementById('retakeBtn');
const analyzeBtn = document.getElementById('analyzeBtn');
const cameraView = document.getElementById('cameraView');
const previewView = document.getElementById('previewView');
const previewImage = document.getElementById('previewImage');

let mediaStream = null;
let capturedBlob = null;
let cameraOn = false;

// Toggle camera on/off
async function toggleCamera() {
    if (cameraOn) {
        stopCamera();
    } else {
        await startCamera();
    }
}

// Start camera
async function startCamera() {
    try {
        toggleCameraBtn.textContent = 'Starting...';
        toggleCameraBtn.disabled = true;
        
        mediaStream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 640, height: 480 } 
        });
        video.srcObject = mediaStream;
        video.style.display = 'block';
        cameraPlaceholder.style.display = 'none';
        
        cameraOn = true;
        toggleCameraBtn.textContent = 'Stop Camera';
        toggleCameraBtn.disabled = false;
        captureBtn.disabled = false;
    } catch (err) {
        console.error('Camera error:', err);
        toggleCameraBtn.textContent = 'Camera Blocked';
        toggleCameraBtn.disabled = false;
        alert('Could not access camera. Please allow camera permission and try again.');
    }
}

// Stop camera
function stopCamera() {
    if (mediaStream) {
        mediaStream.getTracks().forEach(t => t.stop());
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
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    canvas.toBlob((blob) => {
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
    cameraView.style.display = 'block';
    capturedBlob = null;
}

// Analyze and continue
async function analyzeAndContinue() {
    if (!capturedBlob) {
        window.location.href = '{{ url("/session/profile") }}';
        return;
    }
    
    analyzeBtn.disabled = true;
    analyzeBtn.textContent = 'Analyzing...';
    
    try {
        // 1. Create session first
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (token) headers['Authorization'] = `Bearer ${token}`;
        
        const sessionRes = await fetch(`${API_URL}/chat/session/start`, {
            method: 'POST',
            headers,
            body: JSON.stringify({ current_step: 'camera' })
        });
        const sessionData = await sessionRes.json();
        
        if (sessionData.success) {
            localStorage.setItem('current_session_id', sessionData.data.session.id);
            localStorage.setItem('current_session_token', sessionData.data.session.session_token);
        }
        
        // 2. Send image to AI for detection
        const formData = new FormData();
        formData.append('image', capturedBlob, 'capture.jpg');
        
        const detectRes = await fetch(`${API_URL}/detect/multi`, {
            method: 'POST',
            body: formData
        });
        
        const detectData = await detectRes.json();
        console.log('Detection result:', detectData);
        
        if (detectData.success) {
            localStorage.setItem('detection_data', JSON.stringify(detectData.data));
        }
        
        window.location.href = '{{ url("/session/profile") }}';
        
    } catch (error) {
        console.error('Analysis error:', error);
        localStorage.setItem('detection_data', JSON.stringify({}));
        window.location.href = '{{ url("/session/profile") }}';
    }
}

// Event listeners
toggleCameraBtn.addEventListener('click', toggleCamera);
captureBtn.addEventListener('click', capturePhoto);
retakeBtn.addEventListener('click', retakePhoto);
analyzeBtn.addEventListener('click', analyzeAndContinue);
</script>
@endsection
