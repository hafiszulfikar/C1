<?php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'pengguna'
) {
    header("Location: ../../views/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';


// ==========================================
// FUNGSI KEMBALI KE FORM DENGAN ERROR
// ==========================================

function kembaliDenganError($pesan)
{
    $url =
        '../../views/user/buat_reservasi.php?error=' .
        urlencode($pesan);

    header("Location: " . $url);
    exit();
}


// ==========================================
// CEK METHOD
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        "Location: ../../views/user/buat_reservasi.php"
    );

    exit();
}


// ==========================================
// AMBIL DATA
// ==========================================

$user_id =
    $_SESSION['user_id'] ?? null;

$facility_id =
    $_POST['facility_id'] ?? '';

$tanggal =
    $_POST['tanggal'] ?? '';

$start_time =
    $_POST['start_time'] ?? '';

$end_time =
    $_POST['end_time'] ?? '';

$tujuan =
    trim(
        $_POST['tujuan_penggunaan'] ?? ''
    );


// ==========================================
// VALIDASI FIELD
// ==========================================

if (
    empty($facility_id) ||
    empty($tanggal) ||
    empty($start_time) ||
    empty($end_time) ||
    empty($tujuan)
) {

    kembaliDenganError(
        'Semua field reservasi wajib diisi.'
    );
}


// ==========================================
// VALIDASI FASILITAS
// ==========================================

$stmt = $pdo->prepare("
    SELECT id
    FROM facilities
    WHERE id = :id
      AND status_fasilitas = 'aktif'
");

$stmt->execute([
    ':id' => $facility_id
]);

$fasilitas = $stmt->fetch();

if (!$fasilitas) {

    kembaliDenganError(
        'Fasilitas yang dipilih tidak tersedia.'
    );
}


// ==========================================
// VALIDASI TANGGAL
// ==========================================

$hariIni =
    date('Y-m-d');

if ($tanggal < $hariIni) {

    kembaliDenganError(
        'Tanggal penggunaan tidak boleh lebih awal dari hari ini.'
    );
}


// ==========================================
// KONVERSI WAKTU
// ==========================================

$start_ts =
    strtotime($start_time);

$end_ts =
    strtotime($end_time);

$jam_buka =
    strtotime('07:00');

$jam_tutup =
    strtotime('20:00');


// ==========================================
// VALIDASI WAKTU
// ==========================================

if (
    $start_ts === false ||
    $end_ts === false
) {

    kembaliDenganError(
        'Format waktu tidak valid.'
    );
}


// ==========================================
// MULAI HARUS LEBIH AWAL
// ==========================================

if ($start_ts >= $end_ts) {

    kembaliDenganError(
        'Waktu mulai harus lebih awal daripada waktu selesai.'
    );
}


// ==========================================
// JAM OPERASIONAL
// ==========================================

if (
    $start_ts < $jam_buka ||
    $end_ts > $jam_tutup
) {

    kembaliDenganError(
        'Reservasi hanya diperbolehkan pada jam operasional 07:00–20:00.'
    );
}


// ==========================================
// INTERVAL 30 MENIT
// ==========================================

$start_min =
    (int) date('i', $start_ts);

$end_min =
    (int) date('i', $end_ts);


if (
    ($start_min % 30 !== 0) ||
    ($end_min % 30 !== 0)
) {

    kembaliDenganError(
        'Interval waktu harus 30 menit. Contoh: 07:00, 07:30, 08:00, atau 08:30.'
    );
}


// ==========================================
// CEK BENTROK RESERVASI
// ==========================================

$stmt = $pdo->prepare("
    SELECT id
    FROM reservations
    WHERE facility_id = :facility_id
      AND tanggal = :tanggal
      AND status_reservasi = 'disetujui'
      AND start_time < :end_time
      AND end_time > :start_time
    LIMIT 1
");

$stmt->execute([
    ':facility_id' => $facility_id,
    ':tanggal' => $tanggal,
    ':start_time' => $start_time,
    ':end_time' => $end_time
]);

$bentrok = $stmt->fetch();

if ($bentrok) {

    kembaliDenganError(
        'Waktu yang dipilih bertabrakan dengan reservasi yang sudah disetujui.'
    );
}


// ==========================================
// INSERT RESERVASI
// ==========================================

try {

    $sql = "
        INSERT INTO reservations (
            user_id,
            facility_id,
            tujuan_penggunaan,
            tanggal,
            start_time,
            end_time,
            status_reservasi
        )
        VALUES (
            :user_id,
            :facility_id,
            :tujuan,
            :tanggal,
            :start_time,
            :end_time,
            'menunggu'
        )
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' =>
            $user_id,

        ':facility_id' =>
            $facility_id,

        ':tujuan' =>
            $tujuan,

        ':tanggal' =>
            $tanggal,

        ':start_time' =>
            $start_time,

        ':end_time' =>
            $end_time
    ]);


// ==========================================
// BERHASIL
// ==========================================

    header(
        "Location: ../../views/user/dashboard.php"
    );

    exit();


} catch (PDOException $e) {

    kembaliDenganError(
        'Reservasi gagal disimpan. Silakan coba lagi.'
    );
}