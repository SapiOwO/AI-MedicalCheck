from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from model import EmotionDetector
from pathlib import Path
import os

# Initialize FastAPI app
app = FastAPI(
    title="Medical Check - Emotion Detection API",
    description="AI-powered emotion detection from facial images",
    version="1.0.0"
)

# CORS configuration - allow Laravel to access this API
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:8000",  # Laravel default
        "http://127.0.0.1:8000",
        "http://localhost",
        "*"  # Allow all for development (sesuaikan di production)
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

@app.on_event("startup")
async def startup_event():
    """Load model saat aplikasi start"""
    global emotion_detector
    
    try:
        if not MODEL_PATH.exists():
            print(f"⚠️  Model file not found: {MODEL_PATH}")
            print(f"📁 Please place your .pth file in: {MODEL_DIR}")
            print(f"   Expected filename: emotion_best.pth")
            # Don't raise error, allow API to start
        else:
            emotion_detector = EmotionDetector(str(MODEL_PATH))
            print(f"✅ Emotion detection model loaded successfully!")
            
    except Exception as e:
        print(f"❌ Error during startup: {str(e)}")
        # Don't crash the app, just log the error


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Medical Check - Emotion Detection API",
        "status": "running",
        "endpoints": {
            "health": "/health",
            "predict": "/predict (POST with image file)"
        }
    }


@app.get("/health")
async def health_check():
    """Health check endpoint"""
    model_status = "loaded" if emotion_detector is not None else "not_loaded"
    model_path_exists = MODEL_PATH.exists()
    
    return {
        "status": "healthy",
        "model_status": model_status,
        "model_path": str(MODEL_PATH),
        "model_path_exists": model_path_exists,
        "expected_location": str(MODEL_DIR)
    }


@app.post("/predict")
async def predict_emotion(file: UploadFile = File(...)):
    """
    Predict emotion from uploaded image
    
    Args:
        file: Image file (jpg, png, etc)
    
    Returns:
        JSON with emotion prediction and confidence
    """
    # Check if model is loaded
    if emotion_detector is None:
        raise HTTPException(
            status_code=503,
            detail={
                "error": "Model not loaded",
                "message": f"Please place emotion_best.pth in {MODEL_DIR}"
            }
        )
    
    # Validate file type
    if not file.content_type.startswith('image/'):
        raise HTTPException(
            status_code=400,
            detail="File must be an image (jpg, png, etc)"
        )
    
    try:
        # Read image bytes
        image_bytes = await file.read()
        
        print("=" * 60)
        print(f"📥 Received image: {len(image_bytes)} bytes")
        print(f"📄 Content type: {file.content_type}")
        
        # Predict emotion
        result = emotion_detector.predict_emotion(image_bytes)
        
        print(f"🔍 Prediction result: {result}")
        
        if not result['success']:
            print(f"❌ Prediction failed: {result.get('error')}")
            raise HTTPException(
                status_code=400,
                detail=result.get('error', 'Unknown error')
            )
        
        print(f"✅ Success! Emotion: {result['emotion']}, Confidence: {result['confidence']:.3f}")
        print("=" * 60)
        
        return JSONResponse(content=result)
        
    except HTTPException:
        raise
    except Exception as e:
        print(f"❌ Unexpected error: {str(e)}")
        import traceback
        traceback.print_exc()
        raise HTTPException(
            status_code=500,
            detail=f"Internal server error: {str(e)}"
        )


@app.post("/test")
async def test_endpoint():
    """Simple test endpoint"""
    return {
        "status": "ok",
        "message": "API is working!"
    }


if __name__ == "__main__":
    import uvicorn
    
    # Run server
    print("🚀 Starting Medical Check API...")
    print(f"📁 Model directory: {MODEL_DIR}")
    print(f"📄 Expected model: {MODEL_PATH}")
    
    uvicorn.run(
        "api:app",
        host="0.0.0.0",
        port=8001,  # Port 8001 to avoid conflict with Laravel (8000)
        reload=True,  # Auto-reload during development
        log_level="info"
    )
