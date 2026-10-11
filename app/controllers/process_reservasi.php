<?php
// app/controllers/process_reservasi.php

session_start();
date_default_timezone_set('Asia/Jakarta');


// =============================================
// FUNGSI ERROR: KEMBALI KE FORM
// =============================================

function kembaliDenganError($pesan)
{
    $_SESSION['flash_error'] = $pesan;

    header(
        "Location: ../../views/user/buat_reservasi.php"
    );

    exit();
}


// =============================================
// 1. CEK METHOD DAN LOGIN
// =============================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/index.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] =
        'Silakan login terlebih dahulu untuk mengajukan reservasi.';

    header("Location: ../../views/auth/login.php");
    exit();
}

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'pengguna'
) {
    $_SESSION['flash_error'] =
        'Hanya pengguna yang dapat mengajukan reservasi.';

    header("Location: ../../public/index.php");
    exit();
}


require_once __DIR__ . '/../../config/database.php';


// =============================================
// 2. AMBIL DATA FORM
// =============================================

$user_id = (int) $_SESSION['user_id'];

$facility_id = filter_var(
    $_POST['facility_id'] ?? '',
    FILTER_VALIDATE_INT
);

$tanggal = trim($_POST['tanggal'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$end_time = trim($_POST['end_time'] ?? '');
$tujuan = trim($_POST['tujuan_penggunaan'] ?? '');


// =============================================
// 3. VALIDASI FIELD WAJIB
// =============================================

if (
    !$facility_id ||
    $facility_id <= 0 ||
    $tanggal === '' ||
    $start_time === '' ||
    $end_time === '' ||
    $tujuan === ''
) {
    kembaliDenganError(
        'Semua field wajib diisi.'
    );
}


// =============================================
// 4. VALIDASI TANGGAL
// =============================================

$tanggal_obj = DateTimeImmutable::createFromFormat(
    '!Y-m-d',
    $tanggal
);

$date_errors = DateTimeImmutable::getLastErrors();

if (
    !$tanggal_obj ||
    (
        $date_errors !== false &&
        (
            $date_errors['warning_count'] > 0 ||
            $date_errors['error_count'] > 0
        )
    ) ||
    $tanggal_obj->format('Y-m-d') !== $tanggal
) {
    kembaliDenganError(
        'Format tanggal tidak valid.'
    );
}

$hari_ini = date('Y-m-d');

if ($tanggal < $hari_ini) {
    kembaliDenganError(
        'Tanggal reservasi tidak boleh sebelum hari ini.'
    );
}


// =============================================
// 5. FUNGSI VALIDASI FORMAT WAKTU
// Menerima HH:MM atau HH:MM:00
// =============================================

function waktuKeMenit($waktu)
{
    if (
        !preg_match(
            '/^([01]\d|2[0-3]):([0-5]\d)(?::([0-5]\d))?$/',
            $waktu,
            $bagian
        )
    ) {
        return false;
    }

    // Reservasi menggunakan ketelitian sampai menit.
    if (
        isset($bagian[3]) &&
        (int) $bagian[3] !== 0
    ) {
        return false;
    }

    $jam = (int) $bagian[1];
    $menit = (int) $bagian[2];

    return ($jam * 60) + $menit;
}


$start_minutes = waktuKeMenit($start_time);
$end_minutes = waktuKeMenit($end_time);

if (
    $start_minutes === false ||
    $end_minutes === false
) {
    kembaliDenganError(
        'Format waktu tidak valid.'
    );
}


// =============================================
// 6. VALIDASI URUTAN WAKTU
// =============================================

if ($start_minutes >= $end_minutes) {
    kembaliDenganError(
        'Waktu mulai harus lebih awal daripada waktu selesai.'
    );
}


// =============================================
// 7. VALIDASI JAM OPERASIONAL
// 07:00 = 420 menit
// 20:00 = 1200 menit
// =============================================

if (
    $start_minutes < 420 ||
    $end_minutes > 1200
) {
    kembaliDenganError(
        'Jam operasional reservasi adalah 07.00–20.00.'
    );
}


// =============================================
// 8. VALIDASI SLOT 30 MENIT
// =============================================

if (
    $start_minutes % 30 !== 0 ||
    $end_minutes % 30 !== 0
) {
    kembaliDenganError(
        'Waktu reservasi harus menggunakan interval 30 menit. Contoh: 07.00, 07.30, 08.00.'
    );
}


// =============================================
// 9. VALIDASI WAKTU YANG SUDAH LEWAT HARI INI
// =============================================

$menit_sekarang =
    ((int) date('H') * 60) +
    (int) date('i');

if (
    $tanggal === $hari_ini &&
    $start_minutes <= $menit_sekarang
) {
    kembaliDenganError(
        'Waktu mulai reservasi sudah lewat. Silakan pilih waktu berikutnya.'
    );
}


// =============================================
// 10. NORMALISASI FORMAT WAKTU UNTUK DATABASE
// =============================================

$start_time_db = substr($start_time, 0, 5) . ':00';
$end_time_db = substr($end_time, 0, 5) . ':00';


// =============================================
// 11. VALIDASI DATABASE
// =============================================

try {

    // Cek akun pengguna yang sedang login.
    $stmt = $pdo->prepare("
        SELECT status_akun
        FROM users
        WHERE id = :user_id
          AND role = 'pengguna'
        LIMIT 1
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        !$user ||
        $user['status_akun'] !== 'verified'
    ) {
        kembaliDenganError(
            'Akun Anda belum terverifikasi dan belum dapat mengajukan reservasi.'
        );
    }


    // Cek fasilitas masih aktif.
    $stmt = $pdo->prepare("
        SELECT id, status_fasilitas
        FROM facilities
        WHERE id = :facility_id
        LIMIT 1
    ");

    $stmt->execute([
        ':facility_id' => $facility_id
    ]);

    $fasilitas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fasilitas) {
        kembaliDenganError(
            'Fasilitas yang dipilih tidak ditemukan.'
        );
    }

    if ($fasilitas['status_fasilitas'] !== 'aktif') {
        kembaliDenganError(
            'Fasilitas sedang tidak tersedia untuk reservasi.'
        );
    }


    // =========================================
    // 12. CEK BENTROK DENGAN RESERVASI DISETUJUI
    // =========================================

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM reservations
        WHERE facility_id = :facility_id
          AND tanggal = :tanggal
          AND status_reservasi = 'disetujui'
          AND start_time < :end_time
          AND end_time > :start_time
    ");

    $stmt->execute([
        ':facility_id' => $facility_id,
        ':tanggal' => $tanggal,
        ':start_time' => $start_time_db,
        ':end_time' => $end_time_db
    ]);

    $jumlah_bentrok = (int) $stmt->fetchColumn();

    if ($jumlah_bentrok > 0) {
        kembaliDenganError(
            'Jadwal yang dipilih berbenturan dengan reservasi yang sudah disetujui. Silakan pilih waktu lain.'
        );
    }


    // =========================================
    // 13. SIMPAN RESERVASI
    // =========================================

    $sql = "
        INSERT INTO reservations (
            user_id,
            facility_id,
            tanggal,
            start_time,
            end_time,
            tujuan_penggunaan,
            status_reservasi
        ) VALUES (
            :user_id,
            :facility_id,
            :tanggal,
            :start_time,
            :end_time,
            :tujuan,
            'menunggu'
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $user_id,
        ':facility_id' => $facility_id,
        ':tanggal' => $tanggal,
        ':start_time' => $start_time_db,
        ':end_time' => $end_time_db,
        ':tujuan' => $tujuan
    ]);


    // =========================================
    // 14. BERHASIL
    // =========================================

    $_SESSION['flash_success'] =
        'Pengajuan reservasi berhasil dikirim. Silakan tunggu persetujuan petugas.';

    header(
        "Location: ../../views/user/dashboard.php"
    );

    exit();


} catch (PDOException $e) {

    // Simpan detail untuk diperiksa di log server,
    // bukan ditampilkan kepada pengguna.
    error_log(
        'Gagal memproses reservasi: ' .
        $e->getMessage()
    );

    kembaliDenganError(
        'Terjadi kesalahan saat menyimpan reservasi. Silakan coba lagi.'
    );
}