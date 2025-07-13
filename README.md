# Laravel + Vue Project

This is a simple full-stack project using **Laravel (backend)** and **Vue.js (frontend)**.

---

## 🚀 Features

- Laravel API with authentication
- Vue.js frontend (Vite)
- Tailwind CSS or Bootstrap (based on setup)
- Dynamic component rendering
- RESTful API integration

---

## 🖼️ Image Gallery

Below are some screenshots of the application:

### 📷 Main Interface

<img src="image.png" alt="Main Interface" width="600"/>

### 📊 Additional View

<img src="0adc6fe6-688b-459a-a6b9-db2858498a2e.png" alt="Additional Screenshot" width="600"/>

---

## 🛠️ How to Run Locally

```bash
# Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

# Frontend
cd frontend
npm install
npm run dev
