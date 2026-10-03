<?php
// app/controllers/process_status_fasilitas.php

session_start();


// ==========================================
// CEK AKSES
// ==========================================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../views/auth/login.php");
    exit();
}


require_once __DIR__ . '/../../config/database.php';


// ==========================================
// CEK METHOD
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        "Location: ../../views/admin/dashboard.php"
    );

    exit();
}


// ==========================================
// AMBIL DATA
// ==========================================

$id =
    $_POST['id'] ?? null;

$current_status =
    $_POST['current_status'] ?? '';


// ==========================================
// VALIDASI ID
// ==========================================

if (!$id) {

    header(
        "Location: ../../views/admin/dashboard.php"
    );

    exit();
}


// ==========================================
// TENTUKAN STATUS BARU
// ==========================================

if ($current_status === 'nonaktif') {

    // Nonaktif → Aktif
    $new_status = 'aktif';

} else {

    // Aktif / Dalam Perbaikan → Nonaktif
    $new_status = 'nonaktif';
}


// ==========================================
// UPDATE DATABASE
// ==========================================

try {

    $stmt = $pdo->prepare("
        UPDATE facilities
        SET status_fasilitas = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $new_status,
        ':id' => $id
    ]);


    // ======================================
    // KEMBALI KE DASHBOARD
    // ======================================

    header(
        "Location: ../../views/admin/dashboard.php"
    );

    exit();


} catch (PDOException $e) {

    // Untuk sementara kembali ke dashboard
    // agar tidak whitescreen.

    header(
        "Location: ../../views/admin/dashboard.php"
    );

    exit();
}
?>