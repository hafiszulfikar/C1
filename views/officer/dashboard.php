<?php
// views/officer/dashboard.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// 1. Ambil antrian reservasi (Menunggu)
$sql_menunggu = "SELECT r.*, f.nama_fasilitas, u.nama AS nama_pemesan FROM reservations r JOIN facilities f ON r.facility_id = f.id JOIN users u ON r.user_id = u.id WHERE r.status_reservasi = 'menunggu' ORDER BY r.tanggal ASC, r.start_time ASC";
$antrian_reservasi = $pdo->query($sql_menunggu)->fetchAll();

// 2. Ambil reservasi disetujui (Fitur Batal Mendesak)
$sql_disetujui = "SELECT r.*, f.nama_fasilitas, u.nama AS nama_pemesan FROM reservations r JOIN facilities f ON r.facility_id = f.id JOIN users u ON r.user_id = u.id WHERE r.status_reservasi = 'disetujui' ORDER BY r.tanggal ASC, r.start_time ASC";
$reservasi_disetujui = $pdo->query($sql_disetujui)->fetchAll();

// 3. Ambil antrian laporan kerusakan (Baru & Diproses)
$sql_laporan = "SELECT rep.*, f.nama_fasilitas, u.nama AS nama_pelapor FROM reports rep JOIN facilities f ON rep.facility_id = f.id JOIN users u ON rep.user_id = u.id WHERE rep.status_laporan IN ('baru', 'diproses') ORDER BY rep.created_at ASC";
$antrian_laporan = $pdo->query($sql_laporan)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Petugas</title>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?> (Petugas)</h2>
    <a href="../../app/controllers/process_logout.php">Logout</a>
    <hr>

    <!-- (Tabel Antrian Reservasi dan Tabel Reservasi Disetujui tetap sama seperti sebelumnya, biarkan di sini) -->
    <h3>Antrian Reservasi (Menunggu Persetujuan)</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr><th>Pemesan</th><th>Fasilitas</th><th>Tanggal & Waktu</th><th>Tujuan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php if (count($antrian_reservasi) > 0): ?>
                <?php foreach ($antrian_reservasi as $res): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['nama_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($res['nama_fasilitas']); ?></td>
                        <td><?php echo htmlspecialchars($res['tanggal']) . '<br>' . htmlspecialchars($res['start_time']) . ' - ' . htmlspecialchars($res['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($res['tujuan_penggunaan']); ?></td>
                        <td>
                            <form action="../../app/controllers/process_status_reservasi.php" method="POST">
                                <input type="hidden" name="reservasi_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="facility_id" value="<?php echo $res['facility_id']; ?>">
                                <input type="hidden" name="tanggal" value="<?php echo $res['tanggal']; ?>">
                                <input type="hidden" name="start_time" value="<?php echo $res['start_time']; ?>">
                                <input type="hidden" name="end_time" value="<?php echo $res['end_time']; ?>">
                                <button type="submit" name="action" value="approve" style="color: green;">Setujui</button>
                                <button type="submit" name="action" value="reject" style="color: red;">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Tidak ada antrian reservasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><hr><br>

    <h3>Jadwal Reservasi Disetujui</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr><th>Pemesan</th><th>Fasilitas</th><th>Tanggal & Waktu</th><th>Aksi Pembatalan Mendesak</th></tr>
        </thead>
        <tbody>
            <?php if (count($reservasi_disetujui) > 0): ?>
                <?php foreach ($reservasi_disetujui as $res): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['nama_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($res['nama_fasilitas']); ?></td>
                        <td><?php echo htmlspecialchars($res['tanggal']) . '<br>' . htmlspecialchars($res['start_time']) . ' - ' . htmlspecialchars($res['end_time']); ?></td>
                        <td>
                            <form action="../../app/controllers/process_batal_mendesak.php" method="POST">
                                <input type="hidden" name="reservasi_id" value="<?php echo $res['id']; ?>">
                                <input type="text" name="alasan" placeholder="Alasan pembatalan..." required>
                                <button type="submit" onclick="return confirm('Yakin ingin membatalkan jadwal ini secara mendesak?');" style="color: red;">Batalkan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Belum ada reservasi yang disetujui.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><hr><br>

    <!-- BAGIAN BARU: Tabel Laporan Kerusakan -->
    <h3>Antrian Laporan Kerusakan</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Pelapor</th>
                <th>Fasilitas</th>
                <th>Masalah</th>
                <th>Foto</th>
                <th>Status Saat Ini</th>
                <th>Proses Laporan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($antrian_laporan) > 0): ?>
                <?php foreach ($antrian_laporan as $rep): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rep['nama_pelapor']); ?></td>
                        <td><?php echo htmlspecialchars($rep['nama_fasilitas']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($rep['kategori_laporan']); ?></strong><br>
                            <?php echo htmlspecialchars($rep['deskripsi']); ?>
                        </td>
                        <td>
                            <?php if ($rep['foto']): ?>
                                <a href="../../public/uploads/<?php echo htmlspecialchars($rep['foto']); ?>" target="_blank">Lihat Foto</a>
                            <?php else: ?>
                                Tidak ada
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars(ucfirst($rep['status_laporan'])); ?></td>
                        <td>
                            <form action="../../app/controllers/process_status_laporan.php" method="POST">
                                <input type="hidden" name="laporan_id" value="<?php echo $rep['id']; ?>">
                                <input type="hidden" name="facility_id" value="<?php echo $rep['facility_id']; ?>">
                                
                                <select name="status_baru" required>
                                    <option value="diproses" <?php echo ($rep['status_laporan'] == 'diproses') ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="ditolak">Ditolak</option>
                                </select><br><br>
                                
                                <textarea name="catatan_resolusi" placeholder="Catatan resolusi..." rows="2" required></textarea><br>
                                
                                <label style="font-size: 12px;">
                                    <input type="checkbox" name="tandai_perbaikan" value="ya"> Tandai fasilitas "Dalam Perbaikan"
                                </label><br><br>
                                
                                <button type="submit">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Tidak ada laporan kerusakan baru.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>