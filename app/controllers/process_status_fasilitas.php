
<?php
// app/controllers/process_status_fasilitas.php

session_start();

$halaman_dashboard = "../../views/admin/dashboard.php";

// Cek login
if (!isset($_SESSION['role'])) {
    header("Location: ../../views/auth/login.php");
    exit();
}

// Cek hak akses
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] =
        "Anda tidak memiliki izin untuk tindakan ini.";

    header("Location: ../../public/index.php");
    exit();
}

// Cek metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: " . $halaman_dashboard);
    exit();
}

// Validasi ID fasilitas
$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    $_SESSION['flash_error'] = "ID fasilitas tidak valid.";
    header("Location: " . $halaman_dashboard);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo->beginTransaction();

    // Ambil status aktual dari database
    $stmt = $pdo->prepare("
        SELECT id, status_fasilitas
        FROM facilities
        WHERE id = :id
        FOR UPDATE
    ");

    $stmt->execute([':id' => $id]);
    $fasilitas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fasilitas) {
        $pdo->rollBack();

        $_SESSION['flash_error'] =
            "Fasilitas tidak ditemukan.";

        header("Location: " . $halaman_dashboard);
        exit();
    }

    $current_status = $fasilitas['status_fasilitas'];

    // Status dalam perbaikan tidak diubah melalui tombol admin ini
    if (!in_array($current_status, ['aktif', 'nonaktif'], true)) {
        $pdo->rollBack();

        $_SESSION['flash_error'] =
            "Status fasilitas tidak dapat diubah melalui tindakan ini.";

        header("Location: " . $halaman_dashboard);
        exit();
    }

    // Tentukan status berikutnya
    $new_status = ($current_status === 'aktif')
        ? 'nonaktif'
        : 'aktif';

    // Update berdasarkan status aktual
    $stmt = $pdo->prepare("
        UPDATE facilities
        SET status_fasilitas = :status_baru
        WHERE id = :id
          AND status_fasilitas = :status_lama
    ");

    $stmt->execute([
        ':status_baru' => $new_status,
        ':id' => $id,
        ':status_lama' => $current_status
    ]);

    if ($stmt->rowCount() !== 1) {
        $pdo->rollBack();

        $_SESSION['flash_error'] =
            "Status fasilitas gagal diubah. Silakan coba kembali.";

        header("Location: " . $halaman_dashboard);
        exit();
    }

    $pdo->commit();

    $_SESSION['flash_success'] = ($new_status === 'aktif')
        ? "Fasilitas berhasil diaktifkan."
        : "Fasilitas berhasil dinonaktifkan.";

    header("Location: " . $halaman_dashboard);
    exit();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'process_status_fasilitas.php: ' . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Terjadi kesalahan saat mengubah status fasilitas. Silakan coba kembali.";

    header("Location: " . $halaman_dashboard);
    exit();
}
