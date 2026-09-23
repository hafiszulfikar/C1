CREATE DATABASE IF NOT EXISTS db_reservasi_kampus;
USE db_reservasi_kampus;

-- 1. Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('pengguna', 'petugas', 'admin') NOT NULL,
    tipe_pengguna ENUM('mahasiswa', 'dosen', 'staf') NULL,
    status_akun ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Facilities
CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_fasilitas VARCHAR(100) NOT NULL,
    tipe ENUM('ruang kelas', 'aula', 'laboratorium', 'alat', 'lapangan') NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    deskripsi TEXT,
    status_fasilitas ENUM('aktif', 'dalam perbaikan', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel Reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    tujuan_penggunaan TEXT NOT NULL,
    tanggal DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status_reservasi ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan') DEFAULT 'menunggu',
    alasan_pembatalan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

-- 4. Tabel Reports
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    kategori_laporan VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255) NULL,
    status_laporan ENUM('baru', 'diproses', 'selesai', 'ditolak') DEFAULT 'baru',
    catatan_resolusi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);db