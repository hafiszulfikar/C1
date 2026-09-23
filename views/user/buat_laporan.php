<?php
// views/user/buat_laporan.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$stmt = $pdo->query("SELECT id, nama_fasilitas FROM facilities");
$fasilitas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporkan Kerusakan</title>
</head>
<body>
    <h2>Form Laporan Kerusakan Fasilitas</h2>
    <a href="dashboard.php">Batal & Kembali</a>
    <hr>

    <form action="../../app/controllers/process_laporan.php" method="POST" enctype="multipart/form-data">
        <label>Pilih Fasilitas yang Rusak:</label><br>
        <select name="facility_id" required>
            <option value="">-- Pilih Fasilitas --</option>
            <?php foreach ($fasilitas as $f): ?>
                <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['nama_fasilitas']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Kategori Masalah:</label><br>
        <select name="kategori" required>
            <option value="Listrik/Elektronik">Listrik / Elektronik</option>
            <option value="Infrastruktur Bangunan">Infrastruktur Bangunan</option>
            <option value="Air/Sanitasi">Air / Sanitasi</option>
            <option value="Perabotan/Furnitur">Perabotan / Furnitur</option>
            <option value="Lainnya">Lainnya</option>
        </select><br><br>

        <label>Deskripsi Kerusakan:</label><br>
        <textarea name="deskripsi" rows="4" cols="40" required></textarea><br><br>

        <label>Unggah Foto (Opsional, Maks 2MB):</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <button type="submit">Kirim Laporan</button>
    </form>
</body>
</html>