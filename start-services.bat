@echo off
echo ========================================
echo   AI Medical Chatbot - Auto Starter
echo ========================================
echo.

REM Check if Python is installed
python --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Python not found!
    echo Please install Python first.
    pause
    exit /b 1
)

REM Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP not found!
    echo Please install PHP first.
    pause
    exit /b 1
)

echo Starting services...
echo.

REM Start Python API in new window
echo [1/2] Starting Python AI Service (Port 8001)...
start "Python AI Service" cmd /k "cd python_service && python api.py"
timeout /t 5 /nobreak >nul

REM Start Laravel in new window
echo [2/2] Starting Laravel Server (Port 8000)...
start "Laravel Server" cmd /k "php artisan serve"

echo.
echo ========================================
echo   Both services are starting!
echo ========================================
echo.
echo Python API:  http://localhost:8001
echo Laravel:     http://127.0.0.1:8000
echo.
echo Test Page:   http://127.0.0.1:8000/test-simple
echo.
echo Press Ctrl+C in each window to stop services
echo ========================================
pause
