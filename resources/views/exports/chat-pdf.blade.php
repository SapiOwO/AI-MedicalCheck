<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MediSight AI Chat Export</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
        }
        
        .header h1 {
            font-size: 24px;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 11px;
        }
        
        .session-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .session-info h2 {
            font-size: 14px;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            padding: 4px 10px 4px 0;
            font-weight: bold;
            width: 120px;
            color: #64748b;
        }
        
        .info-value {
            display: table-cell;
            padding: 4px 0;
        }
        
        .messages {
            margin-top: 20px;
        }
        
        .messages h2 {
            font-size: 14px;
            color: #4f46e5;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .message {
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        
        .message-bot {
            background: #f1f5f9;
            border-left: 3px solid #4f46e5;
        }
        
        .message-user {
            background: #eef2ff;
            border-left: 3px solid #818cf8;
        }
        
        .message-header {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .message-sender {
            font-weight: bold;
            color: #4f46e5;
        }
        
        .message-content {
            font-size: 12px;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        
        .emotion-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 MediSight AI</h1>
        <p>Chat Session Export</p>
    </div>

    <div class="session-info">
        <h2>Session Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Session ID:</span>
                <span class="info-value">#{{ $session->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $session->created_at->format('l, F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Time:</span>
                <span class="info-value">{{ $session->created_at->format('H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Emotion Detected:</span>
                <span class="info-value">
                    <span class="emotion-badge">{{ $session->initial_emotion ?? 'N/A' }}</span>
                    @if($session->emotion_confidence)
                        ({{ number_format($session->emotion_confidence * 100, 1) }}% confidence)
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($session->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Messages:</span>
                <span class="info-value">{{ $messages->count() }}</span>
            </div>
        </div>
    </div>

    <div class="messages">
        <h2>Conversation</h2>
        
        @foreach($messages as $message)
        <div class="message message-{{ $message->sender }}">
            <div class="message-header">
                <span class="message-sender">{{ $message->sender === 'bot' ? 'MediSight AI' : 'You' }}</span>
                &middot; {{ $message->created_at->format('H:i:s') }}
            </div>
            <div class="message-content">
                {{ $message->message }}
            </div>
        </div>
        @endforeach
        
        @if($messages->isEmpty())
        <p style="text-align: center; color: #94a3b8; padding: 20px;">No messages in this session.</p>
        @endif
    </div>

    <div class="footer">
        <p>Exported from MediSight AI on {{ $exportDate }}</p>
        <p>This document is for personal health reference only. Consult a medical professional for any health concerns.</p>
    </div>
</body>
</html>
