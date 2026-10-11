<?php
// app/controllers/export_excel.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    http_response_code(403);
    exit('Akses ditolak.');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ========================================
// BUAT FILE EXCEL ASLI
// ========================================

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Rekap Fasilitas');

$sheet->fromArray(
    [
        'Nama Fasilitas',
        'Lokasi',
        'Total Reservasi (Disetujui)',
        'Total Laporan Kerusakan'
    ],
    null,
    'A1'
);


// ========================================
// MASUKKAN DATA
// ========================================

$nomor_baris = 2;

foreach ($data as $row) {

    $sheet->setCellValue(
        'A' . $nomor_baris,
        $row['nama_fasilitas']
    );

    $sheet->setCellValue(
        'B' . $nomor_baris,
        $row['lokasi']
    );

    $sheet->setCellValue(
        'C' . $nomor_baris,
        (int) $row['total_reservasi']
    );

    $sheet->setCellValue(
        'D' . $nomor_baris,
        (int) $row['total_laporan']
    );

    $nomor_baris++;
}


// ========================================
// FORMAT TABEL
// ========================================

$sheet->getStyle('A1:D1')
    ->getFont()
    ->setBold(true);

foreach (['A', 'B', 'C', 'D'] as $kolom) {
    $sheet->getColumnDimension($kolom)
        ->setAutoSize(true);
}

$sheet->freezePane('A2');
$sheet->setAutoFilter(
    'A1:D' . max(1, $nomor_baris - 1)
);


// ========================================
// DOWNLOAD XLSX
// ========================================

$filename = 'Rekap_Fasilitas_Kampus.xlsx';

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="' . $filename . '"'
);

header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$spreadsheet->disconnectWorksheets();

exit;