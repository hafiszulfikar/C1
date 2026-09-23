<?php
// config/database.php

$host = 'localhost';
$dbname = 'db_reservasi_kampus';
$username = 'root'; // Default username database pada Laragon
$password = '';     // Default password database pada Laragon biasanya kosong

try {
    // Membuat instance koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Mengatur agar PDO menampilkan pesan error/exception jika ada masalah
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mengatur default pengembalian data sebagai array asosiatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Menghentikan eksekusi dan menampilkan pesan jika koneksi gagal
    die("Koneksi database gagal: " . $e->getMessage());
}
?>