<?php
// app/controllers/process_tambah_petugas.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nama) || empty($email) || empty($password)) {
        die("Semua field wajib diisi!");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'petugas';
    $status_akun = 'verified'; // Otomatis verified karena dibuat oleh Admin
    $tipe_pengguna = NULL; // Petugas tidak butuh tipe sivitas

    try {
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

        header("Location: ../../views/admin/dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Error: Email tersebut sudah terdaftar!";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>