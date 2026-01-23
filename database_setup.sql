-- Database setup for Fishing Log App
-- Run this script in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS fishinglog;
USE fishinglog;

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

-- Tabel spot_memancing (spots)
CREATE TABLE IF NOT EXISTS spot_memancing (
    id_spot INT PRIMARY KEY AUTO_INCREMENT,
    alamat VARCHAR(100) NOT NULL,
    deskripsi_spot TEXT NOT NULL,
    jenis_spot VARCHAR(100) NOT NULL
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

-- Tabel tangkapan (catches) with added tanggal_jawa column
CREATE TABLE IF NOT EXISTS tangkapan (
    id_tangkapan INT PRIMARY KEY AUTO_INCREMENT,
    id_perjalanan INT NOT NULL,
    jenis_ikan VARCHAR(100) NOT NULL,
    nama_ikan VARCHAR(100) NOT NULL,
    jumlah_ikan INT NOT NULL,
    tanggal_jawa VARCHAR(100),
    FOREIGN KEY (id_perjalanan) REFERENCES perjalanan(id_perjalanan) ON DELETE CASCADE
);

-- Tabel foto (photos)
CREATE TABLE IF NOT EXISTS foto (
    id_foto INT PRIMARY KEY AUTO_INCREMENT,
    id_tangkapan INT NOT NULL,
    deskripsi TEXT,
    tanggal_ambil DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tangkapan) REFERENCES tangkapan(id_tangkapan) ON DELETE CASCADE
);

-- Insert sample user (password: 'password' hashed)
INSERT INTO pengguna (nama, email, password) VALUES
('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
