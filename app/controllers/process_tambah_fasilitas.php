<?php
// app/controllers/process_tambah_fasilitas.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_fasilitas = trim($_POST['nama_fasilitas']);
    $tipe = $_POST['tipe'];
    $lokasi = trim($_POST['lokasi']);
    $kapasitas = (int)$_POST['kapasitas'];
    $deskripsi = trim($_POST['deskripsi']);

    // Validasi data tidak boleh kosong (Validasi sisi server)
    if (empty($nama_fasilitas) || empty($tipe) || empty($lokasi) || $kapasitas <= 0 || empty($deskripsi)) {
        die("Semua data wajib diisi dengan benar!");
    }

    try {
        $sql = "INSERT INTO facilities (nama_fasilitas, tipe, lokasi, kapasitas, deskripsi) 
                VALUES (:nama_fasilitas, :tipe, :lokasi, :kapasitas, :deskripsi)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_fasilitas' => $nama_fasilitas,
            ':tipe' => $tipe,
            ':lokasi' => $lokasi,
            ':kapasitas' => $kapasitas,
            ':deskripsi' => $deskripsi
        ]);

        header("Location: ../../views/admin/dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>