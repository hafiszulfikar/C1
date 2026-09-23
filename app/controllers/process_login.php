<?php
// app/controllers/process_login.php
session_start();

// 1. Tentukan path file koneksi
$db_path = __DIR__ . '/../../config/database.php';

// 2. Cek apakah file fisik benar-benar ada
if (!file_exists($db_path)) {
    die("DEBUG ERROR: File tidak ditemukan di jalur: " . $db_path);
}

// 3. Muat file koneksi (menggunakan require agar menampilkan error jika gagal)
require $db_path;

// 4. Cek apakah variabel $pdo berhasil diciptakan oleh database.php
if (!isset($pdo)) {
    die("DEBUG ERROR: File database.php ditemukan, tetapi variabel \$pdo tidak ada! Pastikan file database.php sudah di-save (Ctrl+S) dan isinya benar.");
}

// === Jika lolos pengecekan di atas, kode login utama berjalan ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Email dan password wajib diisi!");
    }

    try {
        // Cari user berdasarkan email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Jika user ditemukan dan password cocok
        if ($user && password_verify($password, $user['password'])) {
            
            // Cek apakah akun hasil registrasi mandiri sudah diverifikasi admin
            if ($user['role'] == 'pengguna' && $user['status_akun'] != 'verified') {
                die("Login gagal: Akun Anda masih berstatus " . strtoupper($user['status_akun']) . ". Tunggu verifikasi dari Admin.");
            }

            // Simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama'];

            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: ../../views/admin/dashboard.php");
            } elseif ($user['role'] == 'petugas') {
                header("Location: ../../views/officer/dashboard.php");
            } else {
                header("Location: ../../views/user/dashboard.php");
            }
            exit();

        } else {
            echo "Login gagal: Email atau password salah!";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Akses ditolak.";
}
?>