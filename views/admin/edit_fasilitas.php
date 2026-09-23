<?php
// views/admin/edit_fasilitas.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Cek apakah ada data yang dikirim melalui form POST (Proses Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama_fasilitas = trim($_POST['nama_fasilitas']);
    $tipe = $_POST['tipe'];
    $lokasi = trim($_POST['lokasi']);
    $kapasitas = (int)$_POST['kapasitas'];
    $deskripsi = trim($_POST['deskripsi']);

    if (!empty($nama_fasilitas) && !empty($tipe) && !empty($lokasi) && $kapasitas > 0 && !empty($deskripsi)) {
        $stmt = $pdo->prepare("UPDATE facilities SET nama_fasilitas = :nama, tipe = :tipe, lokasi = :lokasi, kapasitas = :kapasitas, deskripsi = :deskripsi WHERE id = :id");
        $stmt->execute([
            ':nama' => $nama_fasilitas, ':tipe' => $tipe, ':lokasi' => $lokasi, 
            ':kapasitas' => $kapasitas, ':deskripsi' => $deskripsi, ':id' => $id
        ]);
        header("Location: dashboard.php");
        exit();
    }
}

// Mengambil data fasilitas saat ini untuk ditampilkan di form (Proses Read)
$id_fasilitas = $_GET['id'] ?? null;
if (!$id_fasilitas) {
    die("ID Fasilitas tidak ditemukan.");
}

$stmt = $pdo->prepare("SELECT * FROM facilities WHERE id = :id");
$stmt->execute([':id' => $id_fasilitas]);
$fasilitas = $stmt->fetch();

if (!$fasilitas) {
    die("Data fasilitas tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Fasilitas - Admin</title>
</head>
<body>
    <h2>Edit Data Fasilitas</h2>
    <a href="dashboard.php">Batal & Kembali</a>
    <hr>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $fasilitas['id']; ?>">

        <label>Nama Fasilitas:</label><br>
        <input type="text" name="nama_fasilitas" value="<?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>" required><br><br>

        <label>Tipe Fasilitas:</label><br>
        <select name="tipe" required>
            <option value="ruang kelas" <?php echo ($fasilitas['tipe'] == 'ruang kelas') ? 'selected' : ''; ?>>Ruang Kelas</option>
            <option value="aula" <?php echo ($fasilitas['tipe'] == 'aula') ? 'selected' : ''; ?>>Aula</option>
            <option value="laboratorium" <?php echo ($fasilitas['tipe'] == 'laboratorium') ? 'selected' : ''; ?>>Laboratorium</option>
            <option value="alat" <?php echo ($fasilitas['tipe'] == 'alat') ? 'selected' : ''; ?>>Alat</option>
            <option value="lapangan" <?php echo ($fasilitas['tipe'] == 'lapangan') ? 'selected' : ''; ?>>Lapangan</option>
        </select><br><br>

        <label>Lokasi:</label><br>
        <input type="text" name="lokasi" value="<?php echo htmlspecialchars($fasilitas['lokasi']); ?>" required><br><br>

        <label>Kapasitas (Orang/Unit):</label><br>
        <input type="number" name="kapasitas" min="1" value="<?php echo htmlspecialchars($fasilitas['kapasitas']); ?>" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4" cols="30" required><?php echo htmlspecialchars($fasilitas['deskripsi']); ?></textarea><br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>