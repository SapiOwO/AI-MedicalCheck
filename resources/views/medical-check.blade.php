<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Medical Check - Realtime Emotion Detection</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            --dark-bg: #0f0f23;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 10%, rgba(56, 239, 125, 0.05) 0%, transparent 50%);
            animation: backgroundMove 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes backgroundMove {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-10px, -10px); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 968px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .video-container {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid var(--border-color);
            margin-bottom: 20px;
        }

        #videoElement {
            width: 100%;
            height: auto;
            display: block;
        }

        .video-overlay {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0, 0, 0, 0.8);
            padding: 12px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .recording-indicator {
            display: none;
            align-items: center;
            gap: 8px;
            color: #fc4a1a;
            font-weight: 600;
        }

        .recording-indicator.active {
            display: flex;
        }

        .recording-dot {
            width: 10px;
            height: 10px;
            background: #fc4a1a;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .controls {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
gap: 8px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-danger:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(252, 74, 26, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover:not(:disabled) {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .emotion-display {
            text-align: center;
            padding: 32px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .emotion-icon {
            font-size: 5rem;
            margin-bottom: 16px;
            animation: bounce 0.6s ease;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .emotion-label {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--success-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-transform: capitalize;
        }

        .confidence-score {
            font-size: 1.3rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-detecting {
            background: rgba(56, 239, 125, 0.2);
            color: #38ef7d;
        }

        .status-idle {
            background: rgba(160, 174, 192, 0.2);
            color: var(--text-secondary);
        }

        .probability-bars {
            margin-top: 24px;
        }

        .probability-item {
            margin-bottom: 12px;
        }

        .probability-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .probability-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .probability-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .captured-frames {
            margin-top: 24px;
        }

        .frames-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .frame-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--border-color);
        }

        .frame-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .frame-emotion {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            padding: 4px;
            font-size: 0.7rem;
            text-align: center;
        }

        .info-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 12px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Medical Check - Realtime</h1>
            <p>AI-Powered Continuous Emotion Detection</p>
        </div>

        <div class="grid">
            <!-- Left Column: Video Feed -->
            <div class="card">
                <div class="card-title">
                    <span>📹</span>
                    <span>Camera Feed</span>
                </div>

                <div class="video-container">
                    <video id="videoElement" autoplay playsinline></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                    <div class="video-overlay">
                        <div class="recording-indicator" id="recordingIndicator">
                            <div class="recording-dot"></div>
                            <span>DETECTING</span>
                        </div>
                    </div>
                </div>

                <div class="controls">
                    <button class="btn btn-primary" id="startBtn">
                        🎥 Start Camera
                    </button>
                    <button class="btn btn-danger" id="stopBtn" disabled>
                        ⏹️ Stop Camera
                    </button>
                    <button class="btn btn-secondary" id="captureBtn" disabled>
                        📸 Capture Frame
                    </button>
                </div>

                <p class="info-text">
                    💡 <strong>Tip:</strong> Frame yang di-capture akan disimpan untuk training AI di masa depan.
                </p>
            </div>

            <!-- Right Column: Emotion Results -->
            <div class="card">
                <div class="card-title">
                    <span>😊</span>
                    <span>Emotion Analysis</span>
                </div>

                <div class="emotion-display">
                    <div class="emotion-icon" id="emotionIcon">😐</div>
                    <div class="emotion-label" id="emotionLabel">Neutral</div>
                    <div class="confidence-score">
                        Confidence: <strong id="confidenceValue">0%</strong>
                    </div>
                    <span class="status-badge status-idle" id="statusBadge">Idle</span>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Frames Analyzed</div>
                        <div class="stat-value" id="framesCount">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Frames Captured</div>
                        <div class="stat-value" id="capturedCount">0</div>
                    </div>
                </div>

                <div class="probability-bars">
                    <strong style="font-size: 0.9rem; color: var(--text-secondary);">📊 All Emotions</strong>
                    <div id="probabilityBars" style="margin-top: 12px;"></div>
                </div>
            </div>
        </div>

        <!-- Captured Frames Section -->
        <div class="card">
            <div class="card-title">
                <span>🖼️</span>
                <span>Captured Frames for Dataset</span>
            </div>
            <div class="frames-grid" id="framesGrid"></div>
            <p class="info-text" style="margin-top: 16px;">
                📁 Frame ini akan digunakan untuk melatih AI model di masa depan. Setiap frame disimpan dengan label emosi yang terdeteksi.
            </p>
        </div>
    </div>

    <script>
        const emotionIcons = {
            'angry': '😠',
            'disgust': '🤢',
            'fear': '😨',
            'happy': '😊',
            'neutral': '😐',
            'sad': '😢',
            'surprise': '😲'
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Elements
        const videoElement = document.getElementById('videoElement');
        const canvas = document.getElementById('canvas');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const captureBtn = document.getElementById('captureBtn');
        const recordingIndicator = document.getElementById('recordingIndicator');
        const emotionIcon = document.getElementById('emotionIcon');
        const emotionLabel = document.getElementById('emotionLabel');
        const confidenceValue = document.getElementById('confidenceValue');
        const statusBadge = document.getElementById('statusBadge');
        const framesCount = document.getElementById('framesCount');
        const capturedCount = document.getElementById('capturedCount');
        const probabilityBars = document.getElementById('probabilityBars');
        const framesGrid = document.getElementById('framesGrid');

        let stream = null;
        let detectionInterval = null;
        let analyzedFrames = 0;
        let capturedFrames = 0;
        let capturedData = [];

        // Start camera
        startBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'user'
                    }
                });

                videoElement.srcObject = stream;

                // Enable/disable buttons
                startBtn.disabled = true;
                stopBtn.disabled = false;
                captureBtn.disabled = false;

                // Start realtime detection
                startRealtimeDetection();

                // Update status
                statusBadge.className = 'status-badge status-detecting';
                statusBadge.textContent = 'Detecting';
                recordingIndicator.classList.add('active');

            } catch (err) {
                alert('Failed to access camera: ' + err.message);
            }
        });

        // Stop camera
        stopBtn.addEventListener('click', () => {
            stopCamera();
        });

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                videoElement.srcObject = null;
                stream = null;
            }

            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }

            startBtn.disabled = false;
            stopBtn.disabled = true;
            captureBtn.disabled = true;

            statusBadge.className = 'status-badge status-idle';
            statusBadge.textContent = 'Idle';
            recordingIndicator.classList.remove('active');
        }

        // Realtime detection (every 2 seconds)
        function startRealtimeDetection() {
            detectionInterval = setInterval(async () => {
                await analyzeCurrentFrame();
            }, 2000); // Analyze every 2 seconds
        }

        async function analyzeCurrentFrame() {
            if (!stream) return;

            try {
                // Capture current frame
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;
                canvas.getContext('2d').drawImage(videoElement, 0, 0);

                // Convert to blob
                const blob = await new Promise(resolve => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.8);
                });

                // Send to API
                const formData = new FormData();
                formData.append('image', blob, 'frame.jpg');

                const response = await fetch('{{ route("medical-check.analyze") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    displayEmotion(data.data);
                    analyzedFrames++;
                    framesCount.textContent = analyzedFrames;
                }

            } catch (err) {
                console.error('Detection error:', err);
            }
        }

        // Capture frame for dataset
        captureBtn.addEventListener('click', async () => {
            if (!stream) {
                alert('❌ Camera is not active!');
                return;
            }

            console.log('📸 Capture button clicked!');
            
            // Disable button to prevent multiple clicks
            captureBtn.disabled = true;
            captureBtn.textContent = '⏳ Capturing...';

            try {
                // Get current canvas frame
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;
                canvas.getContext('2d').drawImage(videoElement, 0, 0);

                //Convert to blob and analyze
                const blob = await new Promise(resolve => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.95);
                });

                console.log('🖼️ Blob created, size:', blob.size);

                // Send for analysis
                const formData = new FormData();
                formData.append('image', blob, 'capture.jpg');

                console.log('📤 Sending to API...');

                const response = await fetch('{{ route("medical-check.analyze") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                console.log('📥 Response status:', response.status);

                const data = await response.json();
                console.log('📊 Response data:', data);

                if (data.success) {
                    const emotion = data.data.emotion;
                    const confidence = (data.data.confidence * 100).toFixed(1);

                    console.log('✅ Emotion detected:', emotion, confidence + '%');

                    // Save to captured data
                    const imageUrl = canvas.toDataURL('image/jpeg', 0.95);
                    capturedData.push({
                        image: imageUrl,
                        emotion: emotion,
                        confidence: confidence,
                        timestamp: new Date().toISOString()
                    });

                    capturedFrames++;
                    capturedCount.textContent = capturedFrames;

                    // Display in grid
                    addFrameToGrid(imageUrl, emotion, confidence);

                    // Flash animation
                    canvas.style.display = 'block';
                    canvas.style.opacity = '0.5';
                    setTimeout(() => {
                        canvas.style.display = 'none';
                        canvas.style.opacity = '1';
                    }, 200);

                    // Success alert
                    alert(`✅ Frame captured!\n\nEmotion: ${emotion}\nConfidence: ${confidence}%`);

                } else {
                    console.error('❌ API error:', data.error);
                    alert('❌ Failed to analyze: ' + data.error);
                }

            } catch (err) {
                console.error('❌ Capture error:', err);
                alert('❌ Error: ' + err.message);
            } finally {
                // Re-enable button
                captureBtn.disabled = false;
                captureBtn.textContent = '📸 Capture Frame';
            }
        });

        function displayEmotion(data) {
            const emotion = data.emotion;
            const confidence = (data.confidence * 100).toFixed(1);

            emotionIcon.textContent = emotionIcons[emotion] || '😐';
            emotionLabel.textContent = emotion.charAt(0).toUpperCase() + emotion.slice(1);
            confidenceValue.textContent = confidence + '%';

            // Update probability bars
            if (data.all_probabilities) {
                probabilityBars.innerHTML = '';
                Object.entries(data.all_probabilities).forEach(([emo, prob]) => {
                    const percentage = (prob * 100).toFixed(1);
                    const item = document.createElement('div');
                    item.className = 'probability-item';
                    item.innerHTML = `
                        <div class="probability-label">
                            <span>${emotionIcons[emo] || '😐'} ${emo.charAt(0).toUpperCase() + emo.slice(1)}</span>
                            <span>${percentage}%</span>
                        </div>
                        <div class="probability-bar">
                            <div class="probability-fill" style="width: ${percentage}%"></div>
                        </div>
                    `;
                    probabilityBars.appendChild(item);
                });
            }
        }

        function addFrameToGrid(imageUrl, emotion, confidence) {
            const frameItem = document.createElement('div');
            frameItem.className = 'frame-item';
            frameItem.innerHTML = `
                <img src="${imageUrl}" alt="${emotion}">
                <div class="frame-emotion">${emotionIcons[emotion] || '😐'} ${emotion} (${confidence}%)</div>
            `;
            framesGrid.insertBefore(frameItem, framesGrid.firstChild);
        }
    </script>
</body>
</html>
