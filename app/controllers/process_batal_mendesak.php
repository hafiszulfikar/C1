```php
<?php
// app/controllers/process_batal_mendesak.php

session_start();

date_default_timezone_set('Asia/Jakarta');

// ===============================
// CEK LOGIN DAN ROLE
// ===============================
if (!isset($_SESSION['role'])) {
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'petugas') {
    $_SESSION['flash_error'] = "Anda tidak memiliki izin untuk melakukan tindakan ini.";
    header("Location: ../../public/index.php");
    exit();
}

try {
    require_once __DIR__ . '/../../config/database.php';
} catch (Throwable $e) {
    error_log(
        'process_batal_mendesak.php - Koneksi database: '
        . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Koneksi database bermasalah. Silakan coba kembali nanti.";

    header("Location: ../../views/officer/dashboard.php");
    exit();
}

// ===============================
// CEK METODE REQUEST
// ===============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: ../../views/officer/dashboard.php");
    exit();
}

// ===============================
// VALIDASI INPUT
// ===============================
$reservasi_id = filter_input(
    INPUT_POST,
    'reservasi_id',
    FILTER_VALIDATE_INT
);

$alasan = trim($_POST['alasan'] ?? '');

if (!$reservasi_id || $reservasi_id < 1) {
    $_SESSION['flash_error'] = "Data reservasi tidak valid.";
    header("Location: ../../views/officer/dashboard.php");
    exit();
}

if ($alasan === '') {
    $_SESSION['flash_error'] = "Alasan pembatalan wajib diisi.";
    header("Location: ../../views/officer/dashboard.php");
    exit();
}

if (strlen($alasan) > 1000) {
    $_SESSION['flash_error'] = "Alasan pembatalan terlalu panjang.";
    header("Location: ../../views/officer/dashboard.php");
    exit();
}

// ===============================
// PROSES PEMBATALAN DARURAT
// ===============================
try {
    $pdo->beginTransaction();

    // Ambil dan kunci reservasi selama proses berlangsung
    $stmt = $pdo->prepare("
        SELECT id, status_reservasi
        FROM reservations
        WHERE id = :id
        FOR UPDATE
    ");

    $stmt->execute([
        ':id' => $reservasi_id
    ]);

    $reservasi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservasi) {
        $pdo->rollBack();

        $_SESSION['flash_error'] = "Reservasi tidak ditemukan.";
        header("Location: ../../views/officer/dashboard.php");
        exit();
    }

    // Pembatalan darurat hanya untuk reservasi disetujui
    if ($reservasi['status_reservasi'] !== 'disetujui') {
        $pdo->rollBack();

        $_SESSION['flash_error'] =
            "Reservasi tidak dapat dibatalkan karena statusnya sudah berubah.";

        header("Location: ../../views/officer/dashboard.php");
        exit();
    }

    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status_reservasi = 'dibatalkan',
            alasan_pembatalan = :alasan
        WHERE id = :id
          AND status_reservasi = 'disetujui'
    ");

    $stmt->execute([
        ':alasan' => $alasan,
        ':id' => $reservasi_id
    ]);

    if ($stmt->rowCount() !== 1) {
        $pdo->rollBack();

        $_SESSION['flash_error'] =
            "Pembatalan gagal. Silakan periksa status reservasi.";

        header("Location: ../../views/officer/dashboard.php");
        exit();
    }

    $pdo->commit();

    $_SESSION['flash_success'] =
        "Reservasi berhasil dibatalkan. Alasan pembatalan telah disimpan.";

    header("Location: ../../views/officer/dashboard.php");
    exit();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Detail teknis dicatat di log server, bukan ditampilkan kepada pengguna
    error_log(
        'process_batal_mendesak.php: ' . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Terjadi kesalahan saat membatalkan reservasi. Silakan coba kembali.";

    header("Location: ../../views/officer/dashboard.php");
    exit();
}
?>
```