# 🚀 AI Medical Chatbot - API Documentation

## Quick Start Guide

### 1. Auto-Start Both Services

**Simply double-click this file:**
```
start-services.bat
```

This will automatically start:
- ✅ Python AI Service (Port 8001)
- ✅ Laravel Server (Port 8000)

Access at: **http://127.0.0.1:8000**

---

## 📡 API Endpoints

### Base URL
```
http://127.0.0.1:8000/api
```

---

## 🔐 Authentication Endpoints

###  1. Register

**POST** `/api/register`

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|bearer_token_here"
  }
}
```

### 2. Login

**POST** `/api/login`

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "2|new_bearer_token"
  }
}
```

### 3. Logout

**POST** `/api/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### 4. Get User Profile

**GET** `/api/user`

**Headers:** `Authorization: Bearer {token}`

---

## 🤖 Detection Endpoints

### 1. Multi-Model Detection

**POST** `/api/detect/multi`

**Request (Form-Data):**
```
image: File (jpg/png, max 5MB)
session_token: "guest_abc123" (optional, for guest)
```

**Response:**
```json
{
  "success": true,
  "data": {
    "emotion": {
      "success": true,
      "emotion": "happy",
      "confidence": 0.85,
      "all_probabilities": { ... },
      "face_bbox": { ... }
    },
    "fatigue": {
      "result": "low",
      "confidence": 0.72
    },
    "pain": {
      "result": "none",
      "confidence": 0.91
    }
  }
}
```

### 2. Individual Detection

**POST** `/api/detect/emotion`
**POST** `/api/detect/fatigue` (placeholder)
**POST** `/api/detect/pain` (placeholder)

Same request format as multi-detect.

### 3. Health Check

**GET** `/api/health`

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "model_status": "loaded"
  }
}
```

---

## 💬 Chat Session Endpoints

### 1. Start Chat Session

**POST** `/api/chat/session/start`

**Request:**
```json
{
  "session_token": "guest_xyz" (for guest),
  "emotion": "happy",
  "fatigue": "low",
  "pain": "none",
  "emotion_confidence": 0.85,
  "fatigue_confidence": 0.72,
  "pain_confidence": 0.91,
  "emotion_probabilities": { ... },
  "fatigue_probabilities": { ... },
  "pain_probabilities": { ... }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Chat session started",
  "data": {
    "session": {
      "id": 1,
      "session_token": "abc123",
      "initial_emotion": "happy",
      "status": "active",
      "started_at": "2025-12-03T10:00:00Z"
    }
  }
}
```

### 2. Get Chat History

**GET** `/api/chat/sessions`

**Headers:** `Authorization: Bearer {token}` (authenticated only)

**Response:**
```json
{
  "success": true,
  "data": {
    "sessions": [
      {
        "id": 1,
        "initial_emotion": "happy",
        "message_count": 15,
        "status": "completed",
        "started_at": "..."
      }
    ]
  }
}
```

### 3. Get Specific Session

**GET** `/api/chat/session/{id}?session_token=abc123`

**Response:**
```json
{
  "success": true,
  "data": {
    "session": {
      "id": 1,
      "initial_emotion": "happy",
      "messages": [
        {
          "id": 1,
          "sender": "bot",
          "message": "Hello! I detected you're feeling happy...",
          "created_at": "..."
        }
      ]
    }
  }
}
```

### 4. End Session

**POST** `/api/chat/session/{id}/end?session_token=abc123`

---

## 📨 Chat Message Endpoints

### 1. Send Message

**POST** `/api/chat/message`

**Request:**
```json
{
  "session_id": 1,
  "session_token": "abc123" (for guest),
  "message": "I have a headache"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user_message": {
      "id": 10,
      "sender": "user",
      "message": "I have a headache",
      "created_at": "..."
    },
    "bot_response": {
      "id": 11,
      "sender": "bot",
      "message": "I understand you're experiencing a headache...",
      "created_at": "..."
    }
  }
}
```

### 2. Get Messages

**GET** `/api/chat/session/{sessionId}/messages?session_token=abc123`

---

## 🔄 Complete User Flow

### Registered User Flow

```
1. POST /api/register
   → Get token

2. POST /api/detect/multi (with token)
   → Get emotion/fatigue/pain data

3. POST /api/chat/session/start (with token + detection data)
   → Get session_id

4. POST /api/chat/message (with token + session_id)
   → Chat with bot

5. GET /api/chat/sessions (with token)
   → View chat history

6. POST /api/logout
```

### Guest Flow

```
1. POST /api/detect/multi
   → Get emotion/fatigue/pain data

2. POST /api/chat/session/start (with detection data)
   → Get session_id + session_token

3. POST /api/chat/message (with session_token + session_id)
   → Chat with bot (no history saved after 24h)
```

---

## 🎨 Frontend Integration

### Headers for Authenticated Requests

```javascript
headers: {
  'Authorization': `Bearer ${token}`,
  'Accept': 'application/json'
}
```

### Headers for Guest Requests

```javascript
// No special headers, just send session_token in body
```

### Example: Complete Detection + Chat Flow

```javascript
// 1. Capture image from webcam
const blob = await canvas.toBlob();

// 2. Send to detection API
const formData = new FormData();
formData.append('image', blob, 'frame.jpg');

const detectResponse = await fetch('/api/detect/multi', {
  method: 'POST',
  body: formData
});

const { data } = await detectResponse.json();

// 3. Start chat session with detection data
const sessionResponse = await fetch('/api/chat/session/start', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    emotion: data.emotion.emotion,
    emotion_confidence: data.emotion.confidence,
    emotion_probabilities: data.emotion.all_probabilities,
    // ... fatigue and pain data
  })
});

const { data: sessionData } = await sessionResponse.json();
const sessionId = sessionData.session.id;
const sessionToken = sessionData.session.session_token;

// 4. Send message
const messageResponse = await fetch('/api/chat/message', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    session_id: sessionId,
    session_token: sessionToken, // for guest
    message: 'I need help'
  })
});

const { data: chatData } = await messageResponse.json();
console.log(chatData.bot_response.message);
```

---

## 🚨 Error Responses

### 400 - Bad Request
```json
{
  "success": false,
  "error": "Validation error message"
}
```

### 401 - Unauthorized
```json
{
  "success": false,
  "error": "Unauthenticated"
}
```

### 404 - Not Found
```json
{
  "success": false,
  "error": "Resource not found"
}
```

### 500 - Server Error
```json
{
  "success": false,
  "error": "Internal server error"
}
```

### 503 - Service Unavailable
```json
{
  "success": false,
  "error": "AI Service is not running"
}
```

---

## 🧪 Testing with Postman

Import this collection: (Create separate Postman collection file if needed)

Or test manually:

1. **Test Health:** `GET http://127.0.0.1:8000/api/health`
2. **Test Register:** `POST http://127.0.0.1:8000/api/register`
3. **Test Detection:** `POST http://127.0.0.1:8000/api/detect/emotion` (with image)
4. **Test Chat:** Follow the complete flow above

---

## ✅ What's Implemented

- ✅ Authentication (Register, Login, Logout)
- ✅ Emotion Detection (Working)
- ✅ Chat Sessions (Start, List, Get, End)
- ✅ Chat Messages (Send, Receive with bot response)
- ✅ Guest Mode Support
- ✅ Context-Aware Bot Responses

## ⏸️ Coming Soon

- 🔄 Fatigue Detection Model
- 🔄 Pain Detection Model
- 🔄 Advanced Chatbot (Gemini/OpenAI integration)
- 🔄 File Upload for Detection Results
- 🔄 Analytics Dashboard

---

## 📝 Notes

- Guest sessions expire after 24 hours
- Authenticated user sessions are permanent
- Bot responses are currently rule-based (will be replaced with AI chatbot)
- Detection logs are saved for analytics

---

**Ready to use! 🎉**
