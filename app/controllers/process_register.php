<?php
// app/controllers/process_register.php

// Panggil file koneksi database
require_once __DIR__ . '/../../config/database.php';

// Validasi request harus berupa POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $tipe_pengguna = $_POST['tipe_pengguna'];
    
    // Validasi server-side sederhana
    if (empty($nama) || empty($email) || empty($password)) {
        die("Semua field wajib diisi!");
    }

    // Enkripsi password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Set default role dan status sesuai aturan proyek
    $role = 'pengguna';
    $status_akun = 'pending'; // Harus diverifikasi admin

    try {
        // Query untuk menyimpan data
        $sql = "INSERT INTO users (nama, email, password, role, tipe_pengguna, status_akun) 
                VALUES (:nama, :email, :password, :role, :tipe_pengguna, :status_akun)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role,
            ':tipe_pengguna' => $tipe_pengguna,
            ':status_akun' => $status_akun
        ]);
        
        echo "<h3>Registrasi Berhasil!</h3>";
        echo "<p>Akun Anda sedang berstatus PENDING. Silakan tunggu admin untuk memverifikasi akun Anda sebelum dapat melakukan login.</p>";
        echo "<a href='../../views/auth/login.php'>Kembali ke Login</a>";
        
    } catch (PDOException $e) {
        // Menangkap error jika email sudah terdaftar (UNIQUE constraint)
        if ($e->getCode() == 23000) {
            echo "Error: Email sudah terdaftar!";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Akses ditolak.";
}
?>