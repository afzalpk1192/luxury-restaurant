@echo off
echo ====================================================
echo Starting Luxury Restaurant Application (Backend + Frontend)
echo ====================================================
echo.
cd /d "%~dp0"

echo [1/2] Launching Backend Server (port 5000)...
start "Restaurant Backend" cmd /k "cd backend && npm start"

echo [2/2] Launching Frontend Dev Server (port 5173)...
start "Restaurant Frontend" cmd /k "cd frontend && npm run dev"

echo.
echo Both servers have been launched in separate terminal windows!
pause
