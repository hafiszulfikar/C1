<?php
// app/controllers/process_batal_reservasi.php

session_start();
date_default_timezone_set('Asia/Jakarta');


// =============================================
// FUNGSI REDIRECT DAN PESAN
// =============================================

function kembaliKeDashboard($jenis, $pesan)
{
    $_SESSION[$jenis] = $pesan;

    header(
        "Location: ../../views/user/dashboard.php"
    );

    exit();
}


// =============================================
// 1. CEK LOGIN DAN ROLE
// =============================================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'pengguna' ||
    !isset($_SESSION['user_id'])
) {
    $_SESSION['flash_error'] =
        'Silakan login sebagai pengguna terlebih dahulu.';

    header(
        "Location: ../../views/auth/login.php"
    );

    exit();
}


// =============================================
// 2. CEK METHOD
// =============================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    kembaliKeDashboard(
        'flash_error',
        'Permintaan pembatalan tidak valid.'
    );
}


// =============================================
// 3. VALIDASI ID RESERVASI
// =============================================

$reservasi_id = filter_var(
    $_POST['reservasi_id'] ?? '',
    FILTER_VALIDATE_INT
);

if (!$reservasi_id || $reservasi_id <= 0) {
    kembaliKeDashboard(
        'flash_error',
        'ID reservasi tidak valid.'
    );
}

$user_id = (int) $_SESSION['user_id'];


// =============================================
// 4. KONEKSI DATABASE
// =============================================

require_once __DIR__ . '/../../config/database.php';


try {

    $pdo->beginTransaction();


    // =========================================
    // 5. AMBIL RESERVASI MILIK PENGGUNA
    // =========================================

    $stmt = $pdo->prepare("
        SELECT
            id,
            user_id,
            tanggal,
            start_time,
            status_reservasi
        FROM reservations
        WHERE id = :id
          AND user_id = :user_id
        FOR UPDATE
    ");

    $stmt->execute([
        ':id' => $reservasi_id,
        ':user_id' => $user_id
    ]);

    $reservasi = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$reservasi) {

        $pdo->rollBack();

        kembaliKeDashboard(
            'flash_error',
            'Reservasi tidak ditemukan atau bukan milik akun Anda.'
        );
    }


    // =========================================
    // 6. CEK STATUS RESERVASI
    // =========================================

    $status_diizinkan = [
        'menunggu',
        'disetujui'
    ];

    if (
        !in_array(
            $reservasi['status_reservasi'],
            $status_diizinkan,
            true
        )
    ) {

        $pdo->rollBack();

        kembaliKeDashboard(
            'flash_error',
            'Reservasi ini sudah tidak dapat dibatalkan.'
        );
    }


    // =========================================
    // 7. HITUNG BATAS WAKTU PEMBATALAN
    // Minimal 1 jam sebelum waktu mulai
    // =========================================

    $waktu_mulai = strtotime(
        $reservasi['tanggal'] . ' ' .
        $reservasi['start_time']
    );


    if ($waktu_mulai === false) {

        $pdo->rollBack();

        kembaliKeDashboard(
            'flash_error',
            'Data waktu reservasi tidak valid.'
        );
    }


    $batas_pembatalan = $waktu_mulai - 3600;


    if (time() > $batas_pembatalan) {

        $pdo->rollBack();

        kembaliKeDashboard(
            'flash_error',
            'Reservasi tidak dapat dibatalkan karena batas pembatalan adalah 1 jam sebelum waktu mulai.'
        );
    }


    // =========================================
    // 8. UPDATE STATUS
    // =========================================

    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status_reservasi = 'dibatalkan'
        WHERE id = :id
          AND user_id = :user_id
          AND status_reservasi IN ('menunggu', 'disetujui')
    ");

    $stmt->execute([
        ':id' => $reservasi_id,
        ':user_id' => $user_id
    ]);


    if ($stmt->rowCount() !== 1) {

        $pdo->rollBack();

        kembaliKeDashboard(
            'flash_error',
            'Reservasi gagal dibatalkan. Silakan muat ulang dashboard.'
        );
    }


    // =========================================
    // 9. SIMPAN PERUBAHAN
    // =========================================

    $pdo->commit();


    kembaliKeDashboard(
        'flash_success',
        'Reservasi berhasil dibatalkan.'
    );


} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Detail teknis dicatat di log, bukan
    // ditampilkan kepada pengguna.
    error_log(
        'Gagal membatalkan reservasi: ' .
        $e->getMessage()
    );

    kembaliKeDashboard(
        'flash_error',
        'Terjadi kesalahan saat membatalkan reservasi. Silakan coba lagi.'
    );
}