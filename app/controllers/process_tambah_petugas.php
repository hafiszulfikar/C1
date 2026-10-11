
<?php
// app/controllers/process_tambah_petugas.php

session_start();

// ========================================
// CEK LOGIN DAN ROLE
// ========================================
if (!isset($_SESSION['role'])) {
    $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = "Anda tidak memiliki izin untuk tindakan ini.";
    header("Location: ../../public/index.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// ========================================
// CEK METODE REQUEST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: ../../views/admin/dashboard.php");
    exit();
}

// ========================================
// AMBIL INPUT
// ========================================
$nama = is_string($_POST['nama'] ?? null)
    ? trim($_POST['nama'])
    : '';

$email = is_string($_POST['email'] ?? null)
    ? strtolower(trim($_POST['email']))
    : '';

$password = is_string($_POST['password'] ?? null)
    ? $_POST['password']
    : '';

// ========================================
// VALIDASI INPUT
// ========================================
if ($nama === '' || $email === '' || $password === '') {
    $_SESSION['flash_error'] = "Semua field wajib diisi.";
    header("Location: ../../views/admin/dashboard.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = "Format email tidak valid.";
    header("Location: ../../views/admin/dashboard.php");
    exit();
}

// ========================================
// SIAPKAN DATA PETUGAS
// ========================================
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if ($hashed_password === false) {
    $_SESSION['flash_error'] =
        "Akun petugas gagal dibuat. Silakan coba kembali.";

    header("Location: ../../views/admin/dashboard.php");
    exit();
}

$role = 'petugas';
$status_akun = 'verified';
$tipe_pengguna = null;

// ========================================
// SIMPAN KE DATABASE
// ========================================
try {
    $sql = "
        INSERT INTO users (
            nama,
            email,
            password,
            role,
            tipe_pengguna,
            status_akun
        )
        VALUES (
            :nama,
            :email,
            :password,
            :role,
            :tipe_pengguna,
            :status_akun
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nama' => $nama,
        ':email' => $email,
        ':password' => $hashed_password,
        ':role' => $role,
        ':tipe_pengguna' => $tipe_pengguna,
        ':status_akun' => $status_akun
    ]);

    $_SESSION['flash_success'] =
        "Akun petugas berhasil ditambahkan.";

    header("Location: ../../views/admin/dashboard.php");
    exit();

} catch (PDOException $e) {
    // Tangani kemungkinan email duplikat tanpa menampilkan
    // pesan error database mentah kepada pengguna.
    if ($e->getCode() === '23000') {
        $_SESSION['flash_error'] =
            "Email tersebut sudah terdaftar.";
    } else {
        error_log(
            'process_tambah_petugas.php: ' . $e->getMessage()
        );

        $_SESSION['flash_error'] =
            "Akun petugas gagal dibuat. Silakan coba kembali.";
    }

    header("Location: ../../views/admin/dashboard.php");
    exit();

} catch (Throwable $e) {
    error_log(
        'process_tambah_petugas.php: ' . $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Terjadi kesalahan saat membuat akun petugas.";

    header("Location: ../../views/admin/dashboard.php");
    exit();
}
?>
