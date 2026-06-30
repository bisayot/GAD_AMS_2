# GAD AMS 2

This project is a GAD (Gender and Development) system with a CodeIgniter 4 backend and a Vue/Vite frontend.

## Prerequisites
- **PHP** >= 8.2 (and Composer)
- **Node.js** (and npm)
- **MySQL** (or any database configured)

## Installation

1. **Clone or Download the Repository**

2. **Backend Setup (CodeIgniter 4)**
   - Navigate to the `backend` directory:
     ```bash
     cd backend
     ```
   - Install PHP dependencies:
     ```bash
     composer install
     ```
   - Setup environment variables:
     Copy `env` to `.env` (if it exists, or configure based on your setup) and adjust your database configurations and `app.baseURL`.
   - Run the backend server:
     ```bash
     php spark serve
     ```

3. **Frontend Setup (Vue & Vite)**
   - Open a new terminal and navigate to the `frontend` directory:
     ```bash
     cd frontend
     ```
   - Install Node.js dependencies:
     ```bash
     npm install
     ```
   - Run the frontend development server:
     ```bash
     npm run dev
     ```

## Database Setup
Import the provided SQL file (`gad_submission_system.sql`) into your MySQL server to set up the necessary tables and initial data. Update your backend's `.env` database connection variables accordingly.
