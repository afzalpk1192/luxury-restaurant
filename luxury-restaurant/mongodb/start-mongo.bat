@echo off
echo Starting Standalone MongoDB on port 27017...
cd /d "%~dp0"
if not exist "data\db" mkdir "data\db"
bin\mongod.exe --dbpath "data\db" --port 27017
pause
