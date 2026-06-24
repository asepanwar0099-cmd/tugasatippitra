-- Struktur Database untuk Sistem Absensi Dosen Berbasis QR Code
-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_absensi_qr;
USE db_absensi_qr;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (username: admin, password: password123)
INSERT INTO admin (username, password, nama_lengkap) 
VALUES ('admin', '$2y$10$HJ/j0O7yxPVR1IjQK.1E6O7B.I9iBtNEFMsMtPgCyA3S5FQGXrkCa', 'Administrator Sistem')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- Tabel Dosen
CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    kontak VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Absensi
CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dosen_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME NOT NULL,
    status ENUM('Hadir', 'Terlambat') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dosen_id) REFERENCES dosen(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_absensi_harian (dosen_id, tanggal)
);

-- Indeks tambahan untuk pencarian cepat
CREATE INDEX idx_tanggal ON absensi(tanggal);

-- Data contoh dosen
INSERT IGNORE INTO dosen (nidn, nama, kontak, email) VALUES
('0012047801', 'Dr. Andi Pratama, M.Kom.', '081234567890', 'andi.pratama@kampus.ac.id'),
('0025058202', 'Siti Rahmawati, S.T., M.T.', '081298765432', 'siti.rahmawati@kampus.ac.id');
