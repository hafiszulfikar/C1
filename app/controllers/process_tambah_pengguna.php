
<?php
// app/controllers/process_tambah_pengguna.php

session_start();

// ========================================
// CEK LOGIN DAN ROLE
// ========================================
if (!isset($_SESSION['role'])) {
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] =
        "Anda tidak memiliki izin untuk tindakan ini.";

    header("Location: ../../public/index.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$halaman_form = "../../views/admin/tambah_pengguna.php";

// ========================================
// CEK METODE REQUEST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Permintaan tidak valid.";
    header("Location: " . $halaman_form);
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

$tipe_pengguna = is_string($_POST['tipe_pengguna'] ?? null)
    ? strtolower(trim($_POST['tipe_pengguna']))
    : '';

$password = is_string($_POST['password'] ?? null)
    ? $_POST['password']
    : '';

// ========================================
// VALIDASI INPUT
// ========================================
if (
    $nama === '' ||
    $email === '' ||
    $tipe_pengguna === '' ||
    trim($password) === ''
) {
    $_SESSION['flash_error'] =
        "Semua field wajib diisi.";

    header("Location: " . $halaman_form);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] =
        "Format email tidak valid.";

    header("Location: " . $halaman_form);
    exit();
}

$tipe_valid = [
    'mahasiswa',
    'dosen',
    'staf'
];

if (!in_array($tipe_pengguna, $tipe_valid, true)) {
    $_SESSION['flash_error'] =
        "Tipe pengguna tidak valid.";

    header("Location: " . $halaman_form);
    exit();
}

// ========================================
// HASH PASSWORD
// ========================================
$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);

if ($hashed_password === false) {
    $_SESSION['flash_error'] =
        "Akun gagal dibuat. Silakan coba kembali.";

    header("Location: " . $halaman_form);
    exit();
}

// ========================================
// SIMPAN AKUN PENGGUNA
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
        ':role' => 'pengguna',
        ':tipe_pengguna' => $tipe_pengguna,
        ':status_akun' => 'verified'
    ]);

    $_SESSION['flash_success'] =
        "Akun pengguna berhasil ditambahkan.";

    header("Location: " . $halaman_form);
    exit();

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $_SESSION['flash_error'] =
            "Email tersebut sudah terdaftar.";
    } else {
        error_log(
            'process_tambah_pengguna.php: ' .
            $e->getMessage()
        );

        $_SESSION['flash_error'] =
            "Akun gagal dibuat karena terjadi kesalahan database.";
    }

    header("Location: " . $halaman_form);
    exit();

} catch (Throwable $e) {
    error_log(
        'process_tambah_pengguna.php: ' .
        $e->getMessage()
    );

    $_SESSION['flash_error'] =
        "Terjadi kesalahan saat membuat akun pengguna.";

    header("Location: " . $halaman_form);
    exit();
}
