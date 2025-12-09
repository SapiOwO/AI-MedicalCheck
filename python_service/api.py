from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import Optional
from model import EmotionDetector
from chatbot import HealthcareModel
from pathlib import Path
import os

# Initialize FastAPI app
app = FastAPI(
    title="MediSight AI - Detection & Chatbot API",
    description="AI-powered emotion detection and healthcare chatbot",
    version="2.0.0"
)

# CORS configuration - allow Laravel to access this API
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:8000",
        "http://127.0.0.1:8000",
        "http://localhost",
        "*"
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize model - path ke folder AImodel
MODEL_DIR = Path(__file__).parent.parent / "AImodel"
MODEL_PATH = MODEL_DIR / "emotion_best.pth"

# Global variable untuk model
emotion_detector = None
healthcare_model = None


# Pydantic models for chatbot
class ChatRequest(BaseModel):
    message: str
    profile: dict
    context: Optional[str] = ""

class GreetingRequest(BaseModel):
    profile: Optional[dict] = None


@app.on_event("startup")
async def startup_event():
    """Load model saat aplikasi start"""
    global emotion_detector, healthcare_model
    
    try:
        if not MODEL_PATH.exists():
            print(f"WARNING: Model file not found: {MODEL_PATH}")
            print(f"Please place your .pth file in: {MODEL_DIR}")
        else:
            emotion_detector = EmotionDetector(str(MODEL_PATH))
            print(f"Emotion detection model loaded successfully!")
        
        # Initialize Healthcare Expert System
        healthcare_model = HealthcareModel()
        print(f"Healthcare Expert System loaded successfully!")
            
    except Exception as e:
        print(f"ERROR during startup: {str(e)}")


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "MediSight AI - Detection & Chatbot API",
        "status": "running",
        "endpoints": {
            "health": "/health",
            "predict": "/predict (POST with image file)",
            "chatbot_greeting": "/chatbot/greeting (POST)",
            "chatbot_chat": "/chatbot/chat (POST)"
        }
    }


@app.get("/health")
async def health_check():
    """Health check endpoint"""
    model_status = "loaded" if emotion_detector is not None else "not_loaded"
    chatbot_status = "loaded" if healthcare_model is not None else "not_loaded"
    model_path_exists = MODEL_PATH.exists()
    
    return {
        "status": "healthy",
        "model_status": model_status,
        "chatbot_status": chatbot_status,
        "model_path": str(MODEL_PATH),
        "model_path_exists": model_path_exists,
        "expected_location": str(MODEL_DIR)
    }


@app.post("/predict")
async def predict_emotion(file: UploadFile = File(...)):
    """Predict emotion from uploaded image"""
    if emotion_detector is None:
        raise HTTPException(
            status_code=503,
            detail={
                "error": "Model not loaded",
                "message": f"Please place emotion_best.pth in {MODEL_DIR}"
            }
        )
    
    if not file.content_type.startswith('image/'):
        raise HTTPException(
            status_code=400,
            detail="File must be an image (jpg, png, etc)"
        )
    
    try:
        image_bytes = await file.read()
        
        print("=" * 60)
        print(f"Received image: {len(image_bytes)} bytes")
        
        result = emotion_detector.predict_emotion(image_bytes)
        
        if not result['success']:
            raise HTTPException(status_code=400, detail=result.get('error', 'Unknown error'))
        
        print(f"Emotion: {result['emotion']}, Confidence: {result['confidence']:.3f}")
        print("=" * 60)
        
        return JSONResponse(content=result)
        
    except HTTPException:
        raise
    except Exception as e:
        print(f"ERROR: {str(e)}")
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Internal error: {str(e)}")


# ==========================================
# CHATBOT ENDPOINTS
# ==========================================

@app.post("/chatbot/greeting")
async def chatbot_greeting(request: GreetingRequest):
    """Get initial greeting from chatbot with emotion context"""
    if healthcare_model is None:
        raise HTTPException(status_code=503, detail="Chatbot not loaded")
    
    try:
        profile = request.profile or healthcare_model.generate_random_profile()
        greeting = healthcare_model.get_initial_greeting(profile)
        
        return {
            "success": True,
            "greeting": greeting,
            "profile": profile
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/chatbot/chat")
async def chatbot_chat(request: ChatRequest):
    """Process chat message and get response using Expert System"""
    if healthcare_model is None:
        raise HTTPException(status_code=503, detail="Chatbot not loaded")
    
    try:
        print("=" * 60)
        print(f"Message: {request.message}")
        print(f"Profile: {request.profile}")
        print(f"Context: {request.context[:50] if request.context else 'None'}...")
        
        response_text, updated_profile = healthcare_model.recommend(
            request.profile, 
            request.message, 
            request.context
        )
        
        print(f"Response: {response_text[:100]}...")
        print(f"Updated Profile: {updated_profile}")
        print("=" * 60)
        
        return {
            "success": True,
            "response": response_text,
            "profile": updated_profile
        }
    except Exception as e:
        print(f"Chatbot error: {str(e)}")
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/test")
async def test_endpoint():
    """Simple test endpoint"""
    return {
        "status": "ok",
        "message": "API is working!"
    }


if __name__ == "__main__":
    import uvicorn
    
    print("Starting MediSight AI API...")
    print(f"Model directory: {MODEL_DIR}")
    
    uvicorn.run(
        "api:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info"
    )
