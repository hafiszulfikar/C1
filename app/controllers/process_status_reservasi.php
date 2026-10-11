<?php
// app/controllers/process_status_reservasi.php

session_start();
date_default_timezone_set('Asia/Jakarta');


// =============================================
// FUNGSI REDIRECT DAN FLASH MESSAGE
// =============================================

function redirectPetugas($jenis, $pesan)
{
    $_SESSION[$jenis] = $pesan;

    header(
        "Location: ../../views/officer/dashboard.php"
    );

    exit();
}


// =============================================
// 1. CEK ROLE
// =============================================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'petugas'
) {
    $_SESSION['flash_error'] =
        'Akses ditolak. Silakan login sebagai petugas.';

    header(
        "Location: ../../views/auth/login.php"
    );

    exit();
}


// =============================================
// 2. CEK METHOD REQUEST
// =============================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectPetugas(
        'flash_error',
        'Permintaan tidak valid.'
    );
}


// =============================================
// 3. VALIDASI INPUT
// =============================================

$reservasi_id = filter_var(
    $_POST['reservasi_id'] ?? '',
    FILTER_VALIDATE_INT
);

$action = $_POST['action'] ?? '';

if (
    !$reservasi_id ||
    $reservasi_id <= 0
) {
    redirectPetugas(
        'flash_error',
        'ID reservasi tidak valid.'
    );
}

if (!in_array($action, ['approve', 'reject'], true)) {
    redirectPetugas(
        'flash_error',
        'Aksi reservasi tidak valid.'
    );
}


// =============================================
// 4. KONEKSI DATABASE
// =============================================

require_once __DIR__ . '/../../config/database.php';


try {

    // Mulai transaksi untuk menjaga konsistensi data.
    $pdo->beginTransaction();


    // =========================================
    // 5. AMBIL RESERVASI DARI DATABASE
    // =========================================

    $stmt = $pdo->prepare("
        SELECT
            id,
            facility_id,
            tanggal,
            start_time,
            end_time,
            status_reservasi
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

        redirectPetugas(
            'flash_error',
            'Reservasi tidak ditemukan.'
        );
    }


    // =========================================
    // 6. CEK STATUS RESERVASI
    // =========================================

    if (
        $reservasi['status_reservasi'] !== 'menunggu'
    ) {
        $pdo->rollBack();

        redirectPetugas(
            'flash_error',
            'Reservasi ini sudah diproses dan tidak dapat diproses ulang.'
        );
    }


    // Gunakan data database, bukan hidden input.
    $facility_id = (int) $reservasi['facility_id'];
    $tanggal = $reservasi['tanggal'];
    $start_time = $reservasi['start_time'];
    $end_time = $reservasi['end_time'];


    // =========================================
    // 7. AKSI TOLAK
    // =========================================

    if ($action === 'reject') {

        $stmt = $pdo->prepare("
            UPDATE reservations
            SET status_reservasi = 'ditolak'
            WHERE id = :id
              AND status_reservasi = 'menunggu'
        ");

        $stmt->execute([
            ':id' => $reservasi_id
        ]);


        if ($stmt->rowCount() !== 1) {

            $pdo->rollBack();

            redirectPetugas(
                'flash_error',
                'Reservasi gagal ditolak. Silakan muat ulang dashboard.'
            );
        }


        $pdo->commit();

        redirectPetugas(
            'flash_success',
            'Reservasi berhasil ditolak.'
        );
    }


    // =========================================
    // 8. KUNCI FASILITAS
    // =========================================
    // Persetujuan untuk fasilitas yang sama akan
    // berjalan bergantian melalui controller ini.

    $stmt = $pdo->prepare("
        SELECT id, status_fasilitas
        FROM facilities
        WHERE id = :id
        FOR UPDATE
    ");

    $stmt->execute([
        ':id' => $facility_id
    ]);

    $fasilitas = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$fasilitas) {

        $pdo->rollBack();

        redirectPetugas(
            'flash_error',
            'Fasilitas untuk reservasi ini tidak ditemukan.'
        );
    }


    if ($fasilitas['status_fasilitas'] !== 'aktif') {

        $pdo->rollBack();

        redirectPetugas(
            'flash_error',
            'Reservasi tidak dapat disetujui karena fasilitas sedang tidak aktif atau dalam perbaikan.'
        );
    }


    // =========================================
    // 9. CEK BENTROK JADWAL
    // =========================================
    // Interval tumpang tindih jika:
    // waktu mulai baru < waktu selesai lama
    // DAN
    // waktu selesai baru > waktu mulai lama.
    //
    // Reservasi yang sedang diproses dikecualikan.

    $sql_cek = "
        SELECT id
        FROM reservations
        WHERE facility_id = :facility_id
          AND tanggal = :tanggal
          AND status_reservasi = 'disetujui'
          AND id <> :reservasi_id
          AND start_time < :end_time
          AND end_time > :start_time
        LIMIT 1
        FOR UPDATE
    ";

    $stmt_cek = $pdo->prepare($sql_cek);

    $stmt_cek->execute([
        ':facility_id' => $facility_id,
        ':tanggal' => $tanggal,
        ':reservasi_id' => $reservasi_id,
        ':end_time' => $end_time,
        ':start_time' => $start_time
    ]);

    $reservasi_bentrok = $stmt_cek->fetchColumn();


    if ($reservasi_bentrok !== false) {

        $pdo->rollBack();

        redirectPetugas(
            'flash_error',
            'Gagal menyetujui reservasi. Jadwal berbenturan dengan reservasi lain yang sudah disetujui. Silakan periksa jadwal kembali.'
        );
    }


    // =========================================
    // 10. SETUJUI RESERVASI
    // =========================================

    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status_reservasi = 'disetujui'
        WHERE id = :id
          AND status_reservasi = 'menunggu'
    ");

    $stmt->execute([
        ':id' => $reservasi_id
    ]);


    if ($stmt->rowCount() !== 1) {

        $pdo->rollBack();

        redirectPetugas(
            'flash_error',
            'Reservasi gagal disetujui. Silakan periksa status reservasi.'
        );
    }


    // Simpan perubahan.
    $pdo->commit();


    // =========================================
    // 11. BERHASIL
    // =========================================

    redirectPetugas(
        'flash_success',
        'Reservasi berhasil disetujui.'
    );


} catch (Throwable $e) {

    // Batalkan perubahan jika transaksi belum selesai.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    // Detail teknis hanya dicatat di log server.
    error_log(
        'process_status_reservasi.php: ' .
        $e->getMessage()
    );


    redirectPetugas(
        'flash_error',
        'Terjadi kesalahan saat memproses reservasi. Silakan coba lagi.'
    );
}