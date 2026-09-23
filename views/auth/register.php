<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<!-- views/auth/register.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
</head>
<body>
    <h2>Registrasi Akun Pengguna</h2>
    <!-- Data dikirim ke controller untuk diproses -->
    <form action="../../app/controllers/process_register.php" method="POST">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" required><br><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <label>Status Sivitas:</label><br>
        <select name="tipe_pengguna" required>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
            <option value="staf">Staf</option>
        </select><br><br>
        
        <button type="submit">Daftar</button>
    </form>
</body>
</html>