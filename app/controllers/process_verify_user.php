
<?php
// app/controllers/process_verify_user.php

session_start();

// ========================================
// CEK LOGIN DAN ROLE
// ========================================
if (!isset($_SESSION['role'])) {
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] =
        "Anda tidak memiliki izin untuk tindakan ini.";

    header("Location: ../../public/index.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$halaman_dashboard = "../../views/admin/dashboard.php";

// ========================================
// CEK METODE REQUEST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: " . $halaman_dashboard);
    exit();
}

// ========================================
// VALIDASI INPUT
// ========================================
$user_id = filter_var(
    $_POST['user_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$action = $_POST['action'] ?? '';

if (!$user_id) {
    $_SESSION['flash_error'] = "ID pengguna tidak valid.";
    header("Location: " . $halaman_dashboard);
    exit();
}

if (!in_array($action, ['verify', 'reject'], true)) {
    $_SESSION['flash_error'] = "Tindakan yang diminta tidak valid.";
    header("Location: " . $halaman_dashboard);
    exit();
}

// ========================================
// TENTUKAN STATUS BARU
// ========================================
$new_status = ($action === 'verify')
    ? 'verified'
    : 'rejected';

// ========================================
// PROSES VERIFIKASI / PENOLAKAN
// ========================================
try {
    $stmt = $pdo->prepare("
        UPDATE users
        SET status_akun = :status
        WHERE id = :id
          AND role = 'pengguna'
          AND status_akun = 'pending'
    ");

    $stmt->execute([
        ':status' => $new_status,
        ':id' => $user_id
    ]);

    if ($stmt->rowCount() !== 1) {
        $_SESSION['flash_error'] =
            "Akun tidak ditemukan atau sudah diproses.";

        header("Location: " . $halaman_dashboard);
        exit();
    }

    if ($action === 'verify') {
        $_SESSION['flash_success'] =
            "Akun pengguna berhasil diverifikasi.";
    } else {
        $_SESSION['flash_success'] =
            "Registrasi akun pengguna berhasil ditolak.";
    }

    header("Location: " . $halaman_dashboard);
    exit();

} catch (Throwable $e) {
    // Catat detail teknis di log server, bukan kepada pengguna.
    error_log(
        'process_verify_user.php: ' . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Terjadi kesalahan saat memproses akun pengguna. Silakan coba kembali.";

    header("Location: " . $halaman_dashboard);
    exit();
}
