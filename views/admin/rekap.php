<?php
// views/admin/rekap.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Query untuk menghitung total reservasi (yang disetujui) dan total laporan per fasilitas
$sql = "SELECT f.id, f.nama_fasilitas, f.lokasi,
               (SELECT COUNT(*) FROM reservations r WHERE r.facility_id = f.id AND r.status_reservasi = 'disetujui') AS total_reservasi,
               (SELECT COUNT(*) FROM reports rep WHERE rep.facility_id = f.id) AS total_laporan
        FROM facilities f
        ORDER BY f.nama_fasilitas ASC";
        
$stmt = $pdo->query($sql);
$rekap_data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Fasilitas - Admin</title>
</head>
<body>
    <h2>Rekap Okupansi & Kerusakan Fasilitas</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>

    <!-- Tombol Ekspor CSV -->
    <a href="../../app/controllers/export_csv.php"><button style="background-color: #4CAF50; color: white; padding: 10px;">Download Ekspor (CSV)</button></a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Lokasi</th>
                <th>Total Reservasi (Disetujui)</th>
                <th>Total Laporan Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rekap_data) > 0): ?>
                <?php foreach ($rekap_data as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_fasilitas']); ?></td>
                        <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                        <td style="text-align: center;"><strong><?php echo $row['total_reservasi']; ?></strong> kali</td>
                        <td style="text-align: center;"><strong><?php echo $row['total_laporan']; ?></strong> laporan</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Belum ada data fasilitas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>