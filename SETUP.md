# Medical Check - Setup Instructions

## 🎯 Fitur yang Sudah Dibuat

✅ Python FastAPI service untuk emotion detection  
✅ Laravel controller & routes  
✅ Beautiful UI dengan webcam & upload  
✅ PM2 auto-start configuration (NO .bat files!)  
✅ MyModels folder untuk .pth files  

---

## 📋 Setup Steps

### 1. Install Python Dependencies

```bash
cd python_service
pip install -r requirements.txt
```

### 2. Place Your Model

✅ **DONE!** Model files sudah ada di folder `AImodel`:

```
Laravel/
├── AImodel/
│   ├── emotion_best.pth    ✅ 11.6 MB (Active)
│   ├── fatigue_best.pth    ✅ 9.8 MB
│   └── pain_best.pth       ✅ 2.4 MB
└── ...
```

### 3. Install PM2 (kalau belum ada)

```bash
npm install -g pm2
npm install -g pm2-windows-startup
```

### 4. Setup PM2 Auto-start (TANPA .bat!)

```bash
# Dari folder Laravel root
pm2 start ecosystem.config.json
pm2 save
pm2-startup install
```

Sekarang Python service akan **otomatis jalan** setiap kali komputer boot! 🚀

### 5. Start Laravel

```bash
php artisan serve
```

---

## 🧪 Testing

### Test 1: Check Python API
Buka browser: http://localhost:8001/health

Expected response:
```json
{
  "status": "healthy",
  "model_status": "loaded",
  "model_path_exists": true
}
```

### Test 2: Check Laravel Integration
Buka browser: http://localhost:8000/medical-check

Kamu akan lihat:
- 📷 Camera tab untuk capture foto
- 📁 Upload tab untuk upload gambar
- Status indicator di kanan bawah (hijau = AI service online)

### Test 3: Analyze Emotion
1. Click **"Start Camera"** atau upload foto
2. Click **"Capture & Analyze"** atau **"Analyze Emotion"**
3. Tunggu beberapa detik
4. Hasil emosi akan muncul dengan confidence score!

---

## 🔧 Commands Cheat Sheet

### PM2 Commands (manage Python service)
```bash
pm2 list                    # Lihat semua services
pm2 logs medical-check-api  # Lihat logs
pm2 restart medical-check-api  # Restart service
pm2 stop medical-check-api  # Stop service
pm2 start medical-check-api # Start service
```

### Laravel Commands
```bash
php artisan serve           # Start Laravel
php artisan route:list      # Lihat semua routes
```

---

## ⚠️ Troubleshooting

### Python API tidak jalan?
```bash
pm2 logs medical-check-api  # Check error logs
```

### Model not loaded?
Pastikan file `emotion_best.pth` ada di folder `MyModels/`

### Cannot connect to API?
Cek apakah Python service running:
```bash
pm2 list
```

Atau manual start:
```bash
cd python_service
python api.py
```

### Port 8001 sudah dipakai?
Edit file `python_service/api.py`, line terakhir:
```python
uvicorn.run("api:app", host="0.0.0.0", port=8002)  # Ganti port
```

Dan update juga `app/Http/Controllers/MedicalCheckController.php`:
```php
private const API_BASE_URL = 'http://localhost:8002';  // Ganti port
```

---

## 📝 Important Notes

⚠️ **Model Architecture**: File `model.py` menggunakan arsitektur CNN sederhana. Jika model kamu pakai arsitektur berbeda (ResNet, VGG, dll), kamu perlu update fungsi `_create_model_architecture()` di `python_service/model.py`

⚠️ **GPU Support**: Kalau kamu punya NVIDIA GPU, install PyTorch dengan CUDA support untuk inference lebih cepat:
```bash
pip install torch torchvision --index-url https://download.pytorch.org/whl/cu118
```

✅ **Auto-start**: Sekali setup PM2, Python service akan **otomatis running** setiap kali komputer nyala. **TIDAK PERLU .bat file!**

---

## 🎉 Done!

Kalau semua berjalan dengan baik, sekarang kamu punya:
- ✅ Medical check page yang keren
- ✅ Real-time emotion detection dari webcam
- ✅ Upload & analyze dari foto
- ✅ Auto-start Python service (no .bat!)
- ✅ Clean Laravel integration

Enjoy! 🚀
