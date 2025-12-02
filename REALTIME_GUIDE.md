# 🎉 Medical Check - Realtime Emotion Detection

## ✅ Update Terbaru

### 1. Health Check Sekarang di Port 8000!
Sekarang kamu bisa akses health check di:
```
http://127.0.0.1:8000/health  ✅ (Laravel route)
```

Tidak perlu lagi akses port 8001 langsung!

### 2. Realtime Video Emotion Detection 🎥

**Fitur Baru:**
- ✅ **Continuous emotion detection** setiap 2 detik dari video stream
- ✅ **On/Off camera** controls
- ✅ **Live emotion display** dengan confidence score
- ✅ **Capture frame** untuk dataset training AI
- ✅ **Statistics tracking** (frames analyzed & captured)
- ✅ **Probability distribution** untuk semua emosi
- ✅ **Gallery captured frames** dengan emotion labels

---

## 🚀 Cara Menggunakan

### 1. Start Services

**Terminal 1 - Python API:**
```bash
cd python_service
python api.py
```

**Terminal 2 - Laravel:**
```bash
php artisan serve
```

### 2. Akses Aplikasi

**Health Check:**
```
http://127.0.0.1:8000/health
```

**Medical Check (Realtime):**
```
http://127.0.0.1:8000/medical-check
```

### 3. Gunakan Features

1. **Click "Start Camera"** → Webcam akan aktif
2. **Realtime Detection** → Emotion otomatis ter-detect setiap 2 detik
3. **Live Display** → Lihat emotion, confidence, dan probability bars
4. **Capture Frame** → Click "Capture Frame" untuk simpan foto ke dataset
5. **Stop Camera** → Click "Stop Camera" untuk matikan

---

## 📊 UI Features

### Video Feed Section
- Live camera preview
- Recording indicator (merah saat detecting)
- Camera controls (Start/Stop/Capture)

### Emotion Analysis Section
- Large emoji icon (berubah realtime)
- Emotion label (angry, happy, sad, dll)
- Confidence percentage
- Detection status badge
- Frame counters (analyzed & captured)
- Probability bars untuk semua 7 emotions

### Captured Frames Gallery
- Grid view semua frame yang di-capture
- Setiap frame punya label emotion + confidence
- Auto-scroll ke frame terbaru

---

## 💾 Dataset Collection

Setiap kali kamu click **"Capture Frame"**:

1. ✅ Frame di-analyze oleh AI
2. ✅ Disimpan dengan label emotion
3. ✅ Ditampilkan di gallery
4. ✅ Ready untuk training AI kedepannya

**Data Format:**
```javascript
{
    image: "data:image/jpeg;base64,...",
    emotion: "happy",
    confidence: "95.3",
    timestamp: "2025-12-01T11:08:09.000Z"
}
```

---

## 🔧 Technical Details

### Realtime Detection Flow

```
Start Camera
    ↓
Video Stream Active
    ↓
Every 2 seconds:
    1. Capture frame from video
    2. Send to Laravel API
    3. Laravel → Python AI Service
    4. Get emotion prediction
    5. Update UI realtime
    ↓
Loop continues until Stop
```

### Manual Capture Flow

```
Click "Capture Frame"
    ↓
1. Get current video frame
2. Convert to JPEG blob
3. Send to API for analysis
4. Get emotion + confidence
5. Save to captured dataset
6. Display in gallery
```

---

## 📝 Next Steps

Untuk kedepannya, captured frames bisa:

1. **Export to JSON/CSV** untuk training
2. **Save to database** untuk tracking
3. **Download as ZIP** untuk offline training
4. **Auto-upload** ke cloud storage
5. **Batch labeling** untuk correction

---

## 🎯 Ports Summary

| Service | Port | URL |
|---------|------|-----|
| Laravel | 8000 | http://127.0.0.1:8000 |
| Python AI | 8001 | http://127.0.0.1:8001 (backend only) |

**User-facing URLs:**
- Health: `http://127.0.0.1:8000/health`
- Medical Check: `http://127.0.0.1:8000/medical-check`

Semua akses melalui port 8000! 🎉

---

## ✨ Benefits

✅ **Realtime monitoring** - Tidak perlu capture lalu analyze  
✅ **Continuous detection** - Emosi ter-update otomatis  
✅ **Dataset collection** - Simpan frame untuk future training  
✅ **User-friendly** - Simple controls, beautiful UI  
✅ **Production-ready** - Error handling & smooth UX  

Ready to use! 🚀
