<?php
// app/controllers/export_csv.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';


// ========================================
// AMBIL DATA REKAP
// ========================================

$sql = "
    SELECT
        f.nama_fasilitas,
        f.lokasi,

        (
            SELECT COUNT(*)
            FROM reservations r
            WHERE r.facility_id = f.id
              AND r.status_reservasi = 'disetujui'
        ) AS total_reservasi,

        (
            SELECT COUNT(*)
            FROM reports rep
            WHERE rep.facility_id = f.id
        ) AS total_laporan

    FROM facilities f

    ORDER BY f.nama_fasilitas ASC
";


$stmt = $pdo->query($sql);

$data =
    $stmt->fetchAll(PDO::FETCH_ASSOC);


// ========================================
// HEADER DOWNLOAD
// ========================================

header(
    'Content-Type: text/csv; charset=utf-8'
);

header(
    'Content-Disposition: attachment; filename="Rekap_Fasilitas_Kampus.csv"'
);


// ========================================
// OUTPUT
// ========================================

$output =
    fopen('php://output', 'w');


// ========================================
// BOM UTF-8
// Supaya Excel membaca karakter dengan benar
// ========================================

fwrite(
    $output,
    "\xEF\xBB\xBF"
);


// ========================================
// HEADER KOLOM
// Gunakan ; sebagai pemisah
// agar langsung terbaca per kolom
// di Excel Indonesia
// ========================================

fputcsv(
    $output,
    [
        'Nama Fasilitas',
        'Lokasi',
        'Total Reservasi (Disetujui)',
        'Total Laporan Kerusakan'
    ],
    ';'
);


// ========================================
// DATA
// ========================================

foreach ($data as $row) {

    fputcsv(
        $output,
        [
            $row['nama_fasilitas'],
            $row['lokasi'],
            $row['total_reservasi'],
            $row['total_laporan']
        ],
        ';'
    );
}


// ========================================
// SELESAI
// ========================================

fclose($output);

exit();

?>