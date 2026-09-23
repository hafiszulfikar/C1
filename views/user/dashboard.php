<?php
// views/user/dashboard.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
$user_id = $_SESSION['user_id'];

// 1. Ambil riwayat reservasi
$sql_res = "SELECT r.*, f.nama_fasilitas FROM reservations r JOIN facilities f ON r.facility_id = f.id WHERE r.user_id = :user_id ORDER BY r.tanggal DESC, r.start_time DESC";
$stmt_res = $pdo->prepare($sql_res);
$stmt_res->execute([':user_id' => $user_id]);
$riwayat_reservasi = $stmt_res->fetchAll();

// 2. Ambil riwayat laporan kerusakan
$sql_rep = "SELECT rep.*, f.nama_fasilitas FROM reports rep JOIN facilities f ON rep.facility_id = f.id WHERE rep.user_id = :user_id ORDER BY rep.created_at DESC";
$stmt_rep = $pdo->prepare($sql_rep);
$stmt_rep->execute([':user_id' => $user_id]);
$riwayat_laporan = $stmt_rep->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pengguna</title>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></h2>
    <a href="../../app/controllers/process_logout.php">Logout</a>
    <br><br>
    <a href="buat_reservasi.php"><button>+ Ajukan Reservasi</button></a>
    <a href="buat_laporan.php"><button>+ Laporkan Kerusakan</button></a>
    
    <hr>

    <h3>Riwayat Reservasi Saya</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <!-- (Struktur tabel reservasi sama seperti sebelumnya) -->
        <thead>
            <tr><th>Fasilitas</th><th>Tanggal</th><th>Waktu</th><th>Tujuan</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($riwayat_reservasi as $res): ?>
                <tr>
                    <td><?php echo htmlspecialchars($res['nama_fasilitas']); ?></td>
                    <td><?php echo htmlspecialchars($res['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($res['start_time']) . ' - ' . htmlspecialchars($res['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($res['tujuan_penggunaan']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($res['status_reservasi'])); ?></td>
                    <td>
                        <?php if ($res['status_reservasi'] === 'menunggu'): ?>
                            <form action="../../app/controllers/process_batal_reservasi.php" method="POST" style="display:inline;">
                                <input type="hidden" name="reservasi_id" value="<?php echo $res['id']; ?>">
                                <button type="submit" style="color: red;" onclick="return confirm('Yakin membatalkan?');">Batalkan</button>
                            </form>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><hr><br>

    <h3>Riwayat Laporan Kerusakan</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Fasilitas</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Foto</th>
                <th>Status</th>
                <th>Catatan Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($riwayat_laporan) > 0): ?>
                <?php foreach ($riwayat_laporan as $rep): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rep['nama_fasilitas']); ?></td>
                        <td><?php echo htmlspecialchars($rep['kategori_laporan']); ?></td>
                        <td><?php echo htmlspecialchars($rep['deskripsi']); ?></td>
                        <td>
                            <?php if ($rep['foto']): ?>
                                <a href="../../public/uploads/<?php echo htmlspecialchars($rep['foto']); ?>" target="_blank">Lihat Foto</a>
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars(ucfirst($rep['status_laporan'])); ?></strong></td>
                        <td><?php echo htmlspecialchars($rep['catatan_resolusi'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Belum ada laporan kerusakan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>