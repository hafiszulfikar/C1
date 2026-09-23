<?php
// views/user/buat_reservasi.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

// Ambil daftar fasilitas aktif untuk pilihan form
$stmt = $pdo->query("SELECT id, nama_fasilitas FROM facilities WHERE status_fasilitas = 'aktif'");
$fasilitas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Reservasi</title>
</head>
<body>
    <h2>Form Pengajuan Reservasi</h2>
    <a href="dashboard.php">Batal & Kembali</a>
    <hr>

    <form action="../../app/controllers/process_reservasi.php" method="POST">
        <label>Pilih Fasilitas:</label><br>
        <select name="facility_id" required>
            <option value="">-- Pilih Fasilitas --</option>
            <?php foreach ($fasilitas as $f): ?>
                <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['nama_fasilitas']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Tanggal Penggunaan:</label><br>
        <input type="date" name="tanggal" required><br><br>

        <label>Waktu Mulai (Jam Operasional: 07.00 - 20.00, Kelipatan 30 Menit):</label><br>
        <input type="time" name="start_time" required><br><br>

        <label>Waktu Selesai:</label><br>
        <input type="time" name="end_time" required><br><br>

        <label>Tujuan Penggunaan:</label><br>
        <textarea name="tujuan_penggunaan" rows="4" cols="30" required></textarea><br><br>

        <button type="submit">Ajukan Reservasi</button>
    </form>
</body>
</html>