<!-- views/admin/tambah_petugas.php -->
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
    <title>Daftarkan Petugas - Admin</title>
</head>
<body>
    <h2>Daftarkan Akun Petugas Baru</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>

    <form action="../../app/controllers/process_tambah_petugas.php" method="POST">
        <label>Nama Lengkap Petugas:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Email Petugas:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password Akun:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Daftarkan Petugas</button>
    </form>
</body>
</html>