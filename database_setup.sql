-- Create tables for fishing log application
-- Database 'iifi_4131257_fishing' already exists
USE iifi_4131257_fishing;

-- Tabel pengguna (users) with added password column
CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabel perjalanan (trips)
CREATE TABLE IF NOT EXISTS perjalanan (
    id_perjalanan INT PRIMARY KEY AUTO_INCREMENT,
    id_pengguna INT NOT NULL,
    waktu_mulai DATETIME NOT NULL,
    waktu_selesai DATETIME NOT NULL,
    jarak_lokasi FLOAT NOT NULL,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
);

-- Tabel spot_memancing (spots) - updated with jarak_lokasi column
CREATE TABLE IF NOT EXISTS spot_memancing (
    id_spot INT PRIMARY KEY AUTO_INCREMENT,
    alamat VARCHAR(100) NOT NULL,
    deskripsi_spot TEXT NOT NULL,
    jenis_spot VARCHAR(100) NOT NULL,
    jarak_lokasi FLOAT DEFAULT 0
);

-- Tabel catatan_memancing (notes)
CREATE TABLE IF NOT EXISTS catatan_memancing (
    id_catatan INT PRIMARY KEY AUTO_INCREMENT,
    id_perjalanan INT NOT NULL,
    id_spot INT NOT NULL,
    catatan TEXT,
    FOREIGN KEY (id_perjalanan) REFERENCES perjalanan(id_perjalanan) ON DELETE CASCADE,
    FOREIGN KEY (id_spot) REFERENCES spot_memancing(id_spot) ON DELETE CASCADE
);

-- Tabel tangkapan (catches) with added tanggal_jawa column and relasi ke catatan_memancing
CREATE TABLE IF NOT EXISTS tangkapan (
    id_tangkapan INT PRIMARY KEY AUTO_INCREMENT,
    id_catatan INT NOT NULL,
    jenis_ikan VARCHAR(100) NOT NULL,
    nama_ikan VARCHAR(100) NOT NULL,
    jumlah_ikan INT NOT NULL,
    tanggal_jawa VARCHAR(100),
    FOREIGN KEY (id_catatan) REFERENCES catatan_memancing(id_catatan) ON DELETE CASCADE
);

-- Tabel foto (photos) - updated with nama_file column
CREATE TABLE IF NOT EXISTS foto (
    id_foto INT PRIMARY KEY AUTO_INCREMENT,
    id_tangkapan INT NOT NULL,
    deskripsi TEXT,
    nama_file VARCHAR(255) NOT NULL,
    tanggal_ambil DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tangkapan) REFERENCES tangkapan(id_tangkapan) ON DELETE CASCADE
);

-- No default accounts are created. Register the initial user through the application.
