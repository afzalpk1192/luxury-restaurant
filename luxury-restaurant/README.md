# Luxury Restaurant - Fullstack Web Application

This project is organized into two primary standalone directories:

```text
luxury-restaurant/
├── app/                      <-- Application Code (Frontend & Backend)
│   ├── frontend/             <-- React + Vite + Tailwind CSS User Interface
│   ├── backend/              <-- Node.js + Express + Mongoose REST API
│   └── start-app.bat         <-- Launch both Frontend & Backend
│
├── mongodb/                  <-- Standalone MongoDB Database
│   ├── bin/                  <-- Portable mongod executable
│   ├── data/db/              <-- Database collections & storage
│   └── start-mongo.bat       <-- Launch standalone MongoDB on port 27017
│
└── start-all.bat             <-- One-click launch for MongoDB + Backend + Frontend
```

---

## Quick Start (Run Everything)

Double-click `start-all.bat` in the root folder, or run:

```cmd
start-all.bat
```

This starts:
1. Standalone MongoDB on `localhost:27017`
2. Backend API server on `http://localhost:5000`
3. Frontend Vite dev server on `http://localhost:5173`

---

## Running Components Individually

### 1. MongoDB Database (`mongodb/`)
To start MongoDB standalone:
- Double-click `mongodb/start-mongo.bat` or run:
  ```cmd
  cd mongodb
  start-mongo.bat
  ```

### 2. Backend Server (`app/backend/`)
```cmd
cd app/backend
npm install    # (if needed)
npm start
```
*Backend runs on port 5000 and automatically connects to MongoDB.*

### 3. Frontend Client (`app/frontend/`)
```cmd
cd app/frontend
npm install    # (if needed)
npm run dev
```
*Frontend runs on port 5173 and interfaces with the backend API on port 5000.*

---

## Building for Production

To create a production build of the frontend:
```cmd
cd app/frontend
npm run build
```
The compiled assets will be output to `app/frontend/dist/`.
