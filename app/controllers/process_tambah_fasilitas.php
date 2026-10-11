<?php
// app/controllers/process_tambah_fasilitas.php

session_start();

// ========================================
// CEK LOGIN DAN ROLE
// ========================================
if (!isset($_SESSION['role'])) {
    $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = "Anda tidak memiliki izin untuk tindakan ini.";
    header("Location: ../../public/index.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// ========================================
// CEK METODE REQUEST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: ../../views/admin/dashboard.php");
    exit();
}

// ========================================
// AMBIL INPUT
// ========================================
$nama_fasilitas = trim($_POST['nama_fasilitas'] ?? '');
$tipe = trim($_POST['tipe'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');
$kapasitas_input = trim((string) ($_POST['kapasitas'] ?? ''));
$deskripsi = trim($_POST['deskripsi'] ?? '');

// ========================================
// VALIDASI INPUT
// ========================================
if (
    $nama_fasilitas === '' ||
    $tipe === '' ||
    $lokasi === '' ||
    $kapasitas_input === '' ||
    $deskripsi === ''
) {
    $_SESSION['flash_error'] = "Semua data fasilitas wajib diisi.";
    header("Location: ../../views/admin/dashboard.php");
    exit();
}

// Kapasitas harus berupa bilangan bulat positif
$kapasitas = filter_var(
    $kapasitas_input,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($kapasitas === false) {
    $_SESSION['flash_error'] =
        "Kapasitas harus berupa bilangan bulat lebih dari nol.";

    header("Location: ../../views/admin/dashboard.php");
    exit();
}

// ========================================
// SIMPAN DATA
// ========================================
try {
    $sql = "
        INSERT INTO facilities (
            nama_fasilitas,
            tipe,
            lokasi,
            kapasitas,
            deskripsi
        )
        VALUES (
            :nama_fasilitas,
            :tipe,
            :lokasi,
            :kapasitas,
            :deskripsi
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nama_fasilitas' => $nama_fasilitas,
        ':tipe' => $tipe,
        ':lokasi' => $lokasi,
        ':kapasitas' => $kapasitas,
        ':deskripsi' => $deskripsi
    ]);

    $_SESSION['flash_success'] =
        "Fasilitas berhasil ditambahkan.";

    header("Location: ../../views/admin/dashboard.php");
    exit();

} catch (Throwable $e) {
    // Detail teknis hanya masuk ke log server
    error_log(
        'process_tambah_fasilitas.php: ' . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Fasilitas gagal ditambahkan. Silakan periksa data dan coba kembali.";

    header("Location: ../../views/admin/dashboard.php");
    exit();
}