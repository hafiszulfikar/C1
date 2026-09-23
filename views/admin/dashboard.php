<?php
// views/admin/dashboard.php
session_start();

// Validasi akses: Hanya Admin yang boleh mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// 1. Mengambil daftar pengguna yang belum diverifikasi
$stmt = $pdo->query("SELECT id, nama, email, tipe_pengguna FROM users WHERE role = 'pengguna' AND status_akun = 'pending'");
$pending_users = $stmt->fetchAll();

// 2. Mengambil daftar fasilitas dari database
$stmt_fasilitas = $pdo->query("SELECT * FROM facilities ORDER BY id DESC");
$daftar_fasilitas = $stmt_fasilitas->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Reservasi</title>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?> (Admin)</h2>
    <a href="../../app/controllers/process_logout.php">Logout</a>
    <br><br>
    <a href="tambah_fasilitas.php"><button>+ Tambah Fasilitas Baru</button></a>
    <a href="tambah_petugas.php"><button>+ Daftarkan Akun Petugas</button></a>
    <a href="rekap.php"><button>Lihat Rekapitulasi & Ekspor</button></a>
    <hr>

    <h3>Daftar Pengguna Menunggu Verifikasi</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Tipe</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($pending_users) > 0): ?>
                <?php foreach ($pending_users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nama']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($user['tipe_pengguna'])); ?></td>
                        <td>
                            <form action="../../app/controllers/process_verify_user.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="action" value="verify" style="color: green;">Verifikasi</button>
                                <button type="submit" name="action" value="reject" style="color: red;">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada pengguna baru yang menunggu verifikasi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><hr><br>

    <!-- Menampilkan Tabel Daftar Fasilitas -->
    <h3>Daftar Fasilitas</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($daftar_fasilitas) > 0): ?>
                <?php foreach ($daftar_fasilitas as $fasilitas): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($fasilitas['tipe'])); ?></td>
                        <td><?php echo htmlspecialchars($fasilitas['lokasi']); ?></td>
                        <td><?php echo htmlspecialchars($fasilitas['kapasitas']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($fasilitas['status_fasilitas'])); ?></td>
                            <!-- edit fasilitas -->
                        <td>
                            <a href="edit_fasilitas.php?id=<?php echo $fasilitas['id']; ?>"><button>Edit</button></a>
                            
                            <form action="../../app/controllers/process_status_fasilitas.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $fasilitas['id']; ?>">
                                <input type="hidden" name="current_status" value="<?php echo $fasilitas['status_fasilitas']; ?>">
                                
                                <?php if ($fasilitas['status_fasilitas'] === 'nonaktif'): ?>
                                    <button type="submit" style="color: green;">Aktifkan</button>
                                <?php else: ?>
                                    <!-- Admin bisa menonaktifkan fasilitas, termasuk yang sedang dalam perbaikan -->
                                    <button type="submit" style="color: red;">Nonaktifkan</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Belum ada data fasilitas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>