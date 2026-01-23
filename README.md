# Fishing Log Application

Aplikasi pencatatan hasil memancing dengan PHP & MySQL.

## 📋 Struktur File

```
fishing log/
├── config.php                  # Konfigurasi database
├── login.html                  # Halaman login (frontend)
├── login.php                   # API endpoint login
├── logout.php                  # API endpoint logout
├── check_session.php           # API endpoint check session
├── dashboard.php               # Halaman dashboard (setelah login)
├── database_setup.sql          # Setup database MySQL
└── README.md                   # File ini
```

## 🛠️ Setup & Instalasi

### 1. Setup Database

1. Buka phpMyAdmin atau MySQL command line
2. Copy dan jalankan isi file `database_setup.sql`
3. Database `fishinglog` akan terbuat beserta semua tabelnya

**User default untuk testing:**

- Email: `admin@example.com`
- Password: `password`

### 2. Update Konfigurasi Database (config.php)

Edit file `config.php` sesuaikan dengan konfigurasi server Anda:

```php
define('DB_HOST', 'localhost');    // Host MySQL
define('DB_USER', 'root');         // Username MySQL
define('DB_PASS', '');             // Password MySQL
define('DB_NAME', 'fishinglog');   // Database name
```

### 3. Deploy ke Server

1. Copy semua file ke folder `htdocs` (XAMPP) atau `www` (Wamp)
2. Pastikan Apache & MySQL berjalan
3. Buka browser: `http://localhost/fishing%20log/`

## 📝 File Penjelasan

### config.php

Mengatur koneksi database MySQL dengan konfigurasi:

- Host, Username, Password database
- Charset UTF-8
- Timezone Asia/Jakarta

### login.html

Frontend halaman login dengan:

- Bootstrap 5 untuk styling
- jQuery untuk AJAX request
- Validasi email & password
- Koneksi ke API `login.php`

### login.php

API endpoint untuk proses login:

- Menerima POST request dengan format JSON
- Validasi email & password
- Hash password menggunakan bcrypt
- Set session untuk user yang berhasil login
- Return response JSON

**Request Format:**

```json
{
  "email": "admin@example.com",
  "password": "password",
  "remember": true
}
```

**Success Response:**

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "id_pengguna": 1,
    "nama": "Admin",
    "email": "admin@example.com"
  },
  "redirect": "dashboard.php"
}
```

**Error Response:**

```json
{
  "success": false,
  "message": "Email atau password salah"
}
```

### logout.php

API endpoint untuk logout:

- Destroy session
- Hapus cookie remember me
- Return response JSON

### check_session.php

API endpoint untuk check apakah user sudah login:

- Cek session user
- Return data user jika login
- Return error jika tidak login

### dashboard.php

Halaman dashboard setelah login:

- Cek session user (redirect ke login jika tidak ada session)
- Tampilkan informasi user
- Menu sidebar untuk navigasi
- Modal untuk profil user

### database_setup.sql

Script untuk setup database dengan tabel:

1. **pengguna** - Data pengguna aplikasi
2. **perjalanan** - Catatan perjalanan memancing
3. **spot_memancing** - Daftar spot memancing
4. **catatan_memancing** - Catatan detail perjalanan
5. **tangkapan** - Data hasil tangkapan
6. **foto** - Foto hasil tangkapan

## 🔐 Security Features

✅ Password hash menggunakan bcrypt
✅ SQL Injection prevention dengan prepared statement
✅ Session management
✅ CORS headers untuk API
✅ Email format validation
✅ Password length validation
✅ Remember me cookie (opsional)

## 🚀 Penggunaan

### Login Flow

1. User buka `login.html`
2. Masukkan email & password
3. Klik tombol "Masuk"
4. Frontend kirim AJAX request ke `login.php`
5. Backend validasi & cek database
6. Jika berhasil, set session & redirect ke `dashboard.php`
7. Jika gagal, tampilkan pesan error

### Logout Flow

1. User klik tombol Logout di dashboard
2. Confirm dialog muncul
3. Frontend kirim request ke `logout.php`
4. Backend destroy session & redirect ke `login.html`

## 📱 API Reference

### POST /login.php

Login user

**Request:**

```
Content-Type: application/json
{
    "email": "admin@example.com",
    "password": "password",
    "remember": true
}
```

**Response:** (200 OK)

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {...},
    "redirect": "dashboard.php"
}
```

### POST /logout.php

Logout user

**Response:**

```json
{
  "success": true,
  "message": "Logout berhasil",
  "redirect": "login.html"
}
```

### GET /check_session.php

Check session user

**Response:**

```json
{
    "success": true,
    "logged_in": true,
    "data": {...}
}
```

## 🐛 Troubleshooting

**Error: Koneksi database gagal**

- Pastikan MySQL running
- Cek username & password di `config.php`
- Cek apakah database `fishinglog` sudah dibuat

**Error: Email atau password salah**

- Pastikan email terdaftar di database
- Password case-sensitive
- Cek database apakah ada data user

**Error: Redirect tidak bekerja**

- Pastikan folder PHP bisa menulis session
- Cek session.save_path di php.ini
- Coba clear browser cache

## 📚 Teknologi

- **Frontend:** HTML5, CSS3, Bootstrap 5, jQuery
- **Backend:** PHP 7.2+
- **Database:** MySQL 5.6+
- **Server:** Apache (XAMPP/WAMP)

## 📄 Lisensi

Open Source - Bebas digunakan

## ✍️ Author

Dibuat untuk aplikasi Fishing Log

---

**Versi:** 1.0.0
**Updated:** Januari 2026
