@echo off
echo ====================================================
echo Starting Luxury Restaurant: MongoDB + Backend + Frontend
echo ====================================================
echo.
cd /d "%~dp0"

echo [1/3] Starting Standalone MongoDB on port 27017...
start "MongoDB Standalone" cmd /k "cd mongodb && start-mongo.bat"

timeout /t 2 /nobreak > nul

echo [2/3] Starting Backend Server (port 5000)...
start "Restaurant Backend" cmd /k "cd app\backend && npm start"

timeout /t 1 /nobreak > nul

echo [3/3] Starting Frontend Dev Server (port 5173)...
start "Restaurant Frontend" cmd /k "cd app\frontend && npm run dev"

echo.
echo All services launched!
pause
