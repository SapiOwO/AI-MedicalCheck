<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simple Emotion Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        button {
            padding: 12px 24px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        video { width: 100%; max-width: 640px; border: 2px solid #ddd; }
        #result { padding: 15px; background: #f8f9fa; margin-top: 20px; border-radius: 4px; }
        #logs { background: #000; color: #0f0; padding: 15px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; }
        .emotion-big { font-size: 48px; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🧪 Simple Emotion Detection Test</h1>

    <div class="card">
        <h3>1. Camera</h3>
        <video id="video" autoplay playsinline></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <br><br>
        <button class="btn-primary" onclick="startCamera()">Start Camera</button>
        <button class="btn-danger" onclick="stopCamera()">Stop Camera</button>
    </div>

    <div class="card">
        <h3>2. Test Emotion Detection</h3>
        <button class="btn-success" onclick="testEmotion()">📸 Capture & Analyze</button>
        <button class="btn-success" onclick="testAPI()">🔧 Test API Directly</button>
    </div>

    <div class="card">
        <h3>3. Results</h3>
        <div class="emotion-big" id="emotionDisplay">😐 Waiting...</div>
        <div id="result">No results yet</div>
    </div>

    <div class="card">
        <h3>4. Debug Logs</h3>
        <div id="logs"></div>
    </div>

    <script>
        let stream = null;
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const emotionIcons = {
            'angry': '😠', 'disgust': '🤢', 'fear': '😨',
            'happy': '😊', 'neutral': '😐', 'sad': '😢', 'surprise': '😲'
        };

        function log(msg) {
            const logs = document.getElementById('logs');
            const time = new Date().toLocaleTimeString();
            logs.innerHTML += `[${time}] ${msg}<br>`;
            logs.scrollTop = logs.scrollHeight;
            console.log(msg);
        }

        async function startCamera() {
            try {
                log('🎥 Starting camera...');
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 640, height: 480, facingMode: 'user' }
                });
                video.srcObject = stream;
                log('✅ Camera started successfully');
            } catch (err) {
                log('❌ Camera error: ' + err.message);
                alert('Camera error: ' + err.message);
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
                log('⏹️ Camera stopped');
            }
        }

        async function testEmotion() {
            if (!stream) {
                alert('Please start camera first!');
                return;
            }

            log('📸 Capturing frame...');

            // Capture frame
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            log(`📐 Canvas size: ${canvas.width}x${canvas.height}`);

            // Convert to blob
            const blob = await new Promise(resolve => {
                canvas.toBlob(resolve, 'image/jpeg', 0.9);
            });

            log(`📦 Blob size: ${blob.size} bytes`);

            // Send to API
            const formData = new FormData();
            formData.append('image', blob, 'test.jpg');

            log('📤 Sending to Laravel API...');

            try {
                const response = await fetch('{{ route("medical-check.analyze") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                log(`📥 Response status: ${response.status}`);

                const data = await response.json();
                log(`📊 Response: ${JSON.stringify(data)}`);

                if (data.success) {
                    const emotion = data.data.emotion;
                    const confidence = (data.data.confidence * 100).toFixed(1);

                    document.getElementById('emotionDisplay').textContent = 
                        `${emotionIcons[emotion]} ${emotion.toUpperCase()}`;

                    let resultHTML = `
                        <h4>✅ Success!</h4>
                        <p><strong>Emotion:</strong> ${emotion}</p>
                        <p><strong>Confidence:</strong> ${confidence}%</p>
                        <h5>All Probabilities:</h5>
                        <ul>
                    `;

                    for (const [emo, prob] of Object.entries(data.data.all_probabilities)) {
                        const pct = (prob * 100).toFixed(1);
                        resultHTML += `<li>${emotionIcons[emo]} <strong>${emo}:</strong> ${pct}%</li>`;
                    }

                    resultHTML += '</ul>';
                    document.getElementById('result').innerHTML = resultHTML;

                    log(`✅ DETECTED: ${emotion} (${confidence}%)`);
                } else {
                    log(`❌ API Error: ${data.error}`);
                    document.getElementById('result').innerHTML = 
                        `<h4>❌ Error</h4><p>${data.error}</p>`;
                }

            } catch (err) {
                log(`❌ Request failed: ${err.message}`);
                alert('Request failed: ' + err.message);
            }
        }

        async function testAPI() {
            log('🔧 Testing Python API directly...');

            try {
                // Test health first
                const healthResponse = await fetch('http://localhost:8001/health');
                const healthData = await healthResponse.json();
                log(`🏥 Health check: ${JSON.stringify(healthData)}`);

                // Test with a simple test image
                log('🖼️ Creating test image...');
                const testCanvas = document.createElement('canvas');
                testCanvas.width = 200;
                testCanvas.height = 200;
                const ctx = testCanvas.getContext('2d');
                
                // Draw a simple face
                ctx.fillStyle = '#ddd';
                ctx.fillRect(0, 0, 200, 200);
                ctx.fillStyle = '#000';
                ctx.fillRect(60, 80, 20, 20); // left eye
                ctx.fillRect(120, 80, 20, 20); // right eye
                ctx.fillRect(80, 120, 40, 10); // mouth

                const testBlob = await new Promise(resolve => {
                    testCanvas.toBlob(resolve, 'image/jpeg', 0.9);
                });

                log(`📦 Test blob size: ${testBlob.size} bytes`);

                const formData = new FormData();
                formData.append('file', testBlob, 'test.jpg');

                log('📤 Sending directly to Python API...');

                const response = await fetch('http://localhost:8001/predict', {
                    method: 'POST',
                    body: formData
                });

                log(`📥 Response status: ${response.status}`);

                const data = await response.json();
                log(`📊 Python API response: ${JSON.stringify(data)}`);

                if (data.success) {
                    log(`✅ Python API working! Emotion: ${data.emotion}`);
                    alert(`Python API works!\nEmotion: ${data.emotion}\nConfidence: ${(data.confidence*100).toFixed(1)}%`);
                } else {
                    log(`⚠️ Python API returned: ${data.error}`);
                    alert('Python API error: ' + data.error);
                }

            } catch (err) {
                log(`❌ Direct API test failed: ${err.message}`);
                alert('Direct API error: '+ err.message);
            }
        }

        // Auto-load
        log('🚀 Page loaded. Ready to test!');
        log('📍 Laravel: {{ url("/") }}');
        log('📍 Python API: http://localhost:8001');
    </script>
</body>
</html>
