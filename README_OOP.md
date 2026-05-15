# Fishing Log Application - OOP Version

## Struktur Proyek

Aplikasi Fishing Log telah dikonversi ke Object Oriented Programming (OOP) dengan struktur yang lebih rapi dan terorganisir.

```
wongsibuk/
├── api/                    # API Endpoints (OOP)
│   ├── user_api.php        # User authentication (login/register)
│   ├── perjalanan_api.php  # Trip management
│   ├── spot_api.php        # Fishing spot management
│   ├── catatan_api.php     # Notes management
│   ├── tangkapan_api.php   # Catch management
│   ├── foto_api.php        # Photo management
│   └── laporan_api.php     # Reports and statistics
├── classes/                # OOP Model Classes
│   ├── Database.php        # Database connection class
│   ├── User.php            # User model
│   ├── Perjalanan.php      # Trip model
│   ├── Spot.php            # Spot model
│   ├── Catatan.php         # Note model
│   ├── Tangkapan.php       # Catch model
│   └── Foto.php            # Photo model
├── config/                 # Configuration files
│   ├── config.php          # Main configuration
│   └── Database.php        # Database class
├── views/                  # Frontend views
│   ├── login.html          # Login page
│   ├── register.html       # Registration page
│   ├── dashboard.php       # Main dashboard
│   ├── perjalanan.html     # Trip management page
│   ├── spot_memancing.html # Spot management page
│   ├── catatan_memancing.html # Notes & data page
│   ├── tangkapan.html      # Catch management page
│   └── laporan.html        # Reports page
├── uploads/                # Uploaded photos
├── index.php               # Entry point
├── login.php               # Login handler
├── logout.php              # Logout handler
├── check_session.php       # Session checker
└── database_setup.sql      # Database schema
```

## Fitur OOP

### 1. Class Database
- Menangani koneksi database
- Singleton pattern untuk koneksi
- Error handling otomatis

### 2. Model Classes
Setiap tabel database memiliki class model tersendiri:
- **User**: register(), login(), getById()
- **Perjalanan**: getAll(), getById(), create(), update(), delete(), verifyOwnership()
- **Spot**: getAll(), getById(), create(), update(), delete(), exists()
- **Catatan**: getAll(), getById(), create(), update(), delete()
- **Tangkapan**: getAll(), getById(), create(), update(), delete()
- **Foto**: getAll(), getById(), getByTangkapan(), upload(), delete()

### 3. API Endpoints
Semua API menggunakan class model untuk operasi database:
- Validasi otomatis
- Error handling konsisten
- Response format JSON yang terstandar
- Security checks (ownership verification)

## Keuntungan Struktur OOP

1. **Maintainability**: Kode lebih mudah dipahami dan dimaintain
2. **Reusability**: Class dapat digunakan kembali di berbagai bagian aplikasi
3. **Scalability**: Mudah menambah fitur baru tanpa mengubah kode yang sudah ada
4. **Security**: Validasi dan ownership check terpusat di class model
5. **Organization**: Struktur folder yang jelas memisahkan concerns

## Cara Menggunakan

### Entry Point
Buka `http://localhost/wongsibuk/` atau `http://localhost/wongsibuk/index.php`

### API Usage
Semua API endpoint terletak di folder `api/`:
- POST `api/user_api.php` dengan parameter `action=login` atau `action=register`
- GET/POST/PUT/DELETE `api/perjalanan_api.php` untuk manajemen perjalanan
- GET/POST/PUT/DELETE `api/spot_api.php` untuk manajemen spot
- GET/POST/PUT/DELETE `api/catatan_api.php` untuk manajemen catatan
- GET/POST/PUT/DELETE `api/tangkapan_api.php` untuk manajemen tangkapan
- GET/POST/DELETE `api/foto_api.php` untuk manajemen foto
- GET `api/laporan_api.php` untuk laporan dan statistik

### Database Setup
Jalankan SQL di `database_setup.sql` untuk setup tabel database.

## Konfigurasi

Edit `config/config.php` untuk mengubah:
- Database credentials (DB_HOST, DB_USER, DB_PASS, DB_NAME)
- Timezone
- CORS settings

## Security Features

1. **Session Management**: Session check di setiap endpoint
2. **Ownership Verification**: User hanya bisa akses data miliknya sendiri
3. **Input Validation**: Validasi di client-side dan server-side
4. **Password Hashing**: Menggunakan password_hash() dengan BCRYPT
5. **CORS Protection**: Hanya mengizinkan localhost untuk development

## Migration dari Procedural ke OOP

Jika Anda memiliki kode procedural lama:
1. Pindahkan file API lama ke folder `api/`
2. Buat class model di folder `classes/`
3. Refactor API untuk menggunakan class model
4. Update frontend untuk memanggil API baru
5. Hapus file API lama yang sudah tidak terpakai

## Troubleshooting

### Error: Class not found
Pastikan autoloader di `config/config.php` sudah menginclude path ke folder `classes/`.

### Error: Database connection failed
Cek credentials di `config/config.php` dan pastikan database sudah ada.

### Error: Unauthorized
Pastikan user sudah login dan session valid.
