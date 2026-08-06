# ⚓ Wongsibuk: Modern Fishing Log Application

A premium, high-end web application for tracking and managing your fishing expeditions. Built with a focus on modern aesthetics, glassmorphism, and seamless user experience.

---

## ✨ Features

*   **🌊 Modern Nautical UI**: A beautiful, custom-designed interface inspired by oceanic elements.
*   **🔮 Glassmorphism Design**: Elegant translucent cards and containers with backdrop-blur effects.
*   **🌙 Premium Dark Mode**: Fully integrated dark theme with persistent preference (localStorage).
*   **📱 Fully Responsive**: Optimized for all devices with a compact, modern mobile side-drawer menu.
*   **📊 Advanced Analytics**: Detailed reporting with fish distribution charts, distance tracking, and trip summaries.
*   **📸 Photo Gallery**: Integrated catch media management with full-screen preview modals.
*   **🛠️ Full CRUD Logic**: Seamless management of Trips, Spots, Daily Notes, and Catches via jQuery AJAX.

---

## 📋 Project Structure

```text
wongsibuk/
├── api/                    # Backend API Endpoints (PHP)
├── assets/
│   ├── css/
│   │   └── style.css       # Core Design System & Theme
│   └── img/                # Project Assets
├── classes/                # OOP Logic (Database, User, etc.)
├── config/                 # Configuration files
├── uploads/                # User-uploaded media
├── views/                  # Modern UI Templates (HTML/PHP)
│   ├── dashboard.php       # Main Stats & Navigation
│   ├── perjalanan.html     # Trip Management
│   ├── catatan_memancing.html # Spots, Notes, & Catches
│   └── laporan.html        # Analytics Hub
└── database_setup.sql      # Database Schema
```

---

## 🛠️ Setup & Installation

### 1. Database Configuration

1.  Run the `database_setup.sql` script in your MySQL environment (phpMyAdmin or CLI).
2.  Set the values shown in `.env.example` as web-server environment variables (or in your hosting control panel). Do not commit a file containing real credentials:

```dotenv
DB_HOST=localhost
DB_USER=app_user
DB_PASS=use_a_strong_password
DB_NAME=wongsibuk
```

### 2. Initial Account
Register an account through the application after importing the schema. The application intentionally has no default credentials.

### 3. Local Deployment
1.  Place the project folder in your server's root directory (`htdocs` for XAMPP, `www` for Laragon/Wamp).
2.  Access via: `http://localhost/wongsibuk/`

---

## 🔐 Security & Tech Stack

*   **Frontend**: Custom CSS (Vanilla), HTML5, jQuery (AJAX), FontAwesome 6, Google Fonts (Outfit).
*   **Backend**: PHP 7.4+ (OOP Architecture).
*   **Security**: Bcrypt Hashing, Prepared Statements (SQL Injection Prevention), Session Security.
*   **Database**: MySQL.

---

## ✍️ Author & Version

*   **Version**: 2.0.0 (Modernization Update)
*   **Updated**: May 2026
*   **Design System**: Modern Nautical & Glassmorphism

---
*Developed for Wongsibuk Fishing Log Project.*
