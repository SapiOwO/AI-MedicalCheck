# Medical Check - Quick Start Guide

## ✅ Status

Semua file sudah siap! Model `.pth` sudah ditemukan di folder `AImodel/`.

## 📋 Quick Setup

### 1. Install Python Dependencies

```bash
cd python_service
pip install -r requirements.txt
```

⏳ **Sedang berjalan...** (downloading PyTorch ~203 MB)

### 2. Start Python API Service

```bash
# Test manual dulu
python api.py
```

Tunggu sampai muncul:
```
✅ Emotion detection model loaded successfully!
   Device: cpu (or cuda)
   Emotions: ['angry', 'disgust', 'fear', 'happy', 'neutral', 'sad', 'surprise']
🚀 Starting Medical Check API...
INFO:     Uvicorn running on http://0.0.0.0:8001
```

### 3. Test API Health Check

Buka browser baru → http://localhost:8001/health

Expected response:
```json
{
  "status": "healthy",
  "model_status": "loaded",
  "model_path_exists": true
}
```

### 4. Start Laravel

Terminal baru:
```bash
php artisan serve
```

### 5. Open Medical Check Page

http://localhost:8000/medical-check

---

## 🎯 Model Files Location

```
Laravel/
├── AImodel/                    👈 Your models here
│   ├── emotion_best.pth        ✅ 11.6 MB
│   ├── fatigue_best.pth        ✅ 9.8 MB  
│   ├── pain_best.pth           ✅ 2.4 MB
│   └── inference_example.py    ✅ Reference
├── python_service/
│   ├── api.py                  ✅ FastAPI service
│   ├── model.py                ✅ EmotionNet + preprocessing
│   └── requirements.txt        ⏳ Installing...
```

---

## 🔧 Auto-start dengan PM2 (Nanti setelah test berhasil)

```bash
# Install PM2
npm install -g pm2 pm2-windows-startup

# Start service
pm2 start ecosystem.config.json

# Save & auto-startup
pm2 save
pm2-startup install
```

---

## 📝 Technical Details

### Model Architecture
- **EmotionNet**: Custom CNN (4 conv layers + BatchNorm + Dropout)
- **Input**: Grayscale 48x48
- **Output**: 7 emotions (angry, disgust, fear, happy, neutral, sad, surprise)

### Preprocessing Pipeline (EXACT match with training)
1. ✅ Convert to grayscale
2. ✅ Resize to 48x48 with `cv2.INTER_AREA`
3. ✅ Normalize: `mean=[0.5], std=[0.5]`
4. ✅ ToTensor + Unsqueeze

### Emotion Classes (MUST match training order)
```python
['angry', 'disgust', 'fear', 'happy', 'neutral', 'sad', 'surprise']
```

---

## ⚡ Next Steps

1. ⏳ Wait for pip install to complete
2. 🧪 Test Python API manually
3. 🚀 Start Laravel and test medical check page
4. ✅ Setup PM2 auto-start
5. 🎉 Done!
