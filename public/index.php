<?php
// public/index.php

// Panggil file koneksi
require_once __DIR__ . '/../config/database.php';

// Menyiapkan query dasar untuk mengambil fasilitas aktif
$sql = "SELECT * FROM facilities WHERE status_fasilitas = 'aktif'";
$params = [];

// Logika Filter/Pencarian (User Story 2)
if (isset($_GET['tipe']) && !empty($_GET['tipe'])) {
    $sql .= " AND tipe = :tipe";
    $params[':tipe'] = $_GET['tipe'];
}
if (isset($_GET['lokasi']) && !empty($_GET['lokasi'])) {
    $sql .= " AND lokasi LIKE :lokasi";
    $params[':lokasi'] = '%' . $_GET['lokasi'] . '%';
}
if (isset($_GET['kapasitas']) && !empty($_GET['kapasitas'])) {
    $sql .= " AND kapasitas >= :kapasitas";
    $params[':kapasitas'] = (int)$_GET['kapasitas'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftar_fasilitas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Reservasi Fasilitas Kampus</title>
</head>
<body>
    <h2>Selamat Datang di Sistem Reservasi Fasilitas Kampus</h2>
    <p>Silakan <a href="../views/auth/login.php">Login</a> untuk mengajukan reservasi atau melaporkan kerusakan.</p>
    <hr>

    <h3>Cari Fasilitas</h3>
    <form action="index.php" method="GET">
        <label>Tipe:</label>
        <select name="tipe">
            <option value="">-- Semua Tipe --</option>
            <option value="ruang kelas" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'ruang kelas') ? 'selected' : ''; ?>>Ruang Kelas</option>
            <option value="aula" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'aula') ? 'selected' : ''; ?>>Aula</option>
            <option value="laboratorium" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'laboratorium') ? 'selected' : ''; ?>>Laboratorium</option>
            <option value="alat" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'alat') ? 'selected' : ''; ?>>Alat</option>
            <option value="lapangan" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'lapangan') ? 'selected' : ''; ?>>Lapangan</option>
        </select>

        <label>Lokasi (Kata Kunci):</label>
        <input type="text" name="lokasi" value="<?php echo isset($_GET['lokasi']) ? htmlspecialchars($_GET['lokasi']) : ''; ?>">

        <label>Minimal Kapasitas:</label>
        <input type="number" name="kapasitas" min="1" value="<?php echo isset($_GET['kapasitas']) ? htmlspecialchars($_GET['kapasitas']) : ''; ?>">

        <button type="submit">Cari</button>
        <a href="index.php"><button type="button">Reset</button></a>
    </form>

    <br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
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
                        <td>
                            <!-- Tombol ini disiapkan untuk mengecek slot waktu (User Story 1) -->
                            <a href="jadwal.php?id=<?php echo $fasilitas['id']; ?>"><button type="button">Lihat Jadwal</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Tidak ada fasilitas yang ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>