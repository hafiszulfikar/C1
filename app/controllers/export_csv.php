<?php
// app/controllers/export_csv.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

// Gunakan query yang sama dengan halaman rekap
$sql = "SELECT f.nama_fasilitas, f.lokasi,
               (SELECT COUNT(*) FROM reservations r WHERE r.facility_id = f.id AND r.status_reservasi = 'disetujui') AS total_reservasi,
               (SELECT COUNT(*) FROM reports rep WHERE rep.facility_id = f.id) AS total_laporan
        FROM facilities f
        ORDER BY f.nama_fasilitas ASC";
        
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Atur header agar browser mengenali ini sebagai file unduhan CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Rekap_Fasilitas_Kampus.csv"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis baris pertama (Header Kolom Excel/CSV)
fputcsv($output, ['Nama Fasilitas', 'Lokasi', 'Total Reservasi (Disetujui)', 'Total Laporan Kerusakan']);

// Tulis isi datanya baris demi baris
foreach ($data as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>