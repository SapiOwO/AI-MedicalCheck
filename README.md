# MediSight-AI: Real-Time Medical Emotion Analysis

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python)
![TailwindCSS](https://img.shields.io/badge/Tailwind-4.0-38B2AC?style=for-the-badge&logo=tailwind-css)
![PyTorch](https://img.shields.io/badge/PyTorch-EE4C2C?style=for-the-badge&logo=pytorch)

**MediSight-AI** is a cutting-edge web application designed to assist medical professionals by providing real-time analysis of patient emotional states, fatigue levels, and pain indicators during consultations. Built with a robust **Laravel 12** backend and a high-performance **Python FastAPI** AI service.

---

## 🚀 Features

### ❤️ Medical Intelligence
- **Real-time Emotion Detection**: Instantly identifies 7 core emotions (Angry, Disgust, Fear, Happy, Neutral, Sad, Surprise).
- **Fatigue Monitoring**: Detects signs of drowsiness and fatigue.
- **Pain Level Assessment**: AI-driven analysis of facial cues indicating pain.
- **High Accuracy**: Powered by custom CNN models optimized for medical contexts.

### 💻 Modern Tech Stack
- **Seamless Integration**: Laravel communicates with Python AI microservice via HTTP.
- **Beautiful UI/UX**: Built with Blade and TailwindCSS v4 for a responsive, clean interface.
- **Webcam Support**: Direct browser integration for live video analysis.
- **Image Upload**: Supports high-res medical imagery analysis.

---

## 🛠️ Prerequisites

Before you begin, ensure you have the following installed:

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: v18+ & NPM
- **Python**: 3.10+ (with pip)
- **Database**: SQLite (built-in) or MySQL/MariaDB
- **Git**

---

## 📦 Installation Guide

### 1. Setup Laravel Application
```bash
# Clone the repository
git clone https://github.com/your-username/medisight-ai.git
cd medisight-ai

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Install frontend dependencies (Tailwind 4 + Vite)
npm install
npm run build
```

### 2. Setup Database
```bash
# Run migrations (SQLite is default, requires no extra config)
php artisan migrate
```

### 3. Setup AI Service (Python)
```bash
cd python_service

# Create virtual environment (Recommended)
python -m venv venv
# Windows:
.\venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# Install dependencies (Torch, FastAPI, OpenCV, etc.)
pip install -r requirements.txt
```

### 4. Place AI Models
Ensure your trained `.pth` models are placed in the `AImodel` directory in the project root:
```
Laravel/
├── AImodel/
│   ├── emotion_best.pth    ✅ Required
│   ├── fatigue_best.pth    ✅ Optional
│   └── pain_best.pth       ✅ Optional
```

---

## 🖥️ Running Locally

You need to run both the Laravel server and the Python AI service.

**Option 1: Two Terminals (Easiest)**

**Terminal 1 (Laravel):**
```bash
php artisan serve
# Accessible at http://localhost:8000
```

**Terminal 2 (Python API):**
```bash
cd python_service
python api.py
# API runs at http://localhost:8001
```

**Option 2: Concurrently (Dev Mode)**
If configured in `composer.json`, you can run:
```bash
npm run dev
```

---

## 🚀 Deployment (Production)

For a production environment (Windows Server or Linux), we use **PM2** to keep the Python service alive and auto-restart it.

### 1. Install PM2
```bash
npm install -g pm2
# If on Windows, also install:
npm install -g pm2-windows-startup
```

### 2. Configure & Start Services
The project includes an `ecosystem.config.json` for easy management.

```bash
# Start the Python AI Service using PM2
pm2 start ecosystem.config.json

# Check status
pm2 list
pm2 logs medical-check-api
```

### 3. Setup Auto-start on Boot
**Windows:**
```bash
pm2-startup install
pm2 save
```

**Linux:**
```bash
pm2 startup
pm2 save
```

### 4. Optimize Laravel
```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🧪 Testing

1. **API Health Check**: Python service status.
   - URL: `http://localhost:8001/health`
   - Response: `{"status": "healthy", "model_status": "loaded"}`

2. **Web Interface**:
   - Go to `http://localhost:8000/medical-check` (or your domain).
   - Allow camera access.
   - Click **"Start Camera"** -> **"Analyze"**.

---

## ⚠️ Troubleshooting

- **Error: "Model not found"**: Check `AImodel/` folder and ensure `emotion_best.pth` exists.
- **Port Conflicts**:
    - Laravel default: 8000.
    - Python API default: 8001.
    - Change ports in `.env` (Laravel) and `api.py` (Python) if needed.
- **CUDA/GPU Issues**: If you have an NVIDIA GPU but it's not detected, reinstall Torch with CUDA support:
  ```bash
  pip install torch torchvision --index-url https://download.pytorch.org/whl/cu118
  ```

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
