<!-- views/admin/tambah_fasilitas.php -->
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Fasilitas - Admin</title>
</head>
<body>
    <h2>Tambah Data Fasilitas Baru</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>

    <form action="../../app/controllers/process_tambah_fasilitas.php" method="POST">
        <label>Nama Fasilitas:</label><br>
        <input type="text" name="nama_fasilitas" required><br><br>

        <label>Tipe Fasilitas:</label><br>
        <select name="tipe" required>
            <option value="ruang kelas">Ruang Kelas</option>
            <option value="aula">Aula</option>
            <option value="laboratorium">Laboratorium</option>
            <option value="alat">Alat</option>
            <option value="lapangan">Lapangan</option>
        </select><br><br>

        <label>Lokasi:</label><br>
        <input type="text" name="lokasi" required><br><br>

        <label>Kapasitas (Orang/Unit):</label><br>
        <input type="number" name="kapasitas" min="1" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4" cols="30" required></textarea><br><br>

        <button type="submit">Simpan Fasilitas</button>
    </form>
</body>
</html>