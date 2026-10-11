
<?php
// views/admin/tambah_pengguna.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';

unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Pengguna | ReservasiKampus</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue-dark: #063b70;
            --blue: #0879d1;
            --bg: #f5f8fc;
            --text: #172033;
            --muted: #6b7280;
            --border: #e3e9f0;
        }

        body {
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a {
            text-decoration: none;
        }

        input, select, button {
            font-family: inherit;
        }

        .navbar {
            height: 72px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
        }

        .navbar-inner {
            width: min(1180px, calc(100% - 40px));
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            color: var(--blue-dark);
            font-size: 21px;
            font-weight: 700;
        }

        .logo span {
            font-weight: 400;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link {
            color: var(--blue-dark);
            font-size: 12px;
            font-weight: 700;
        }

        .logout {
            padding: 10px 15px;
            border-radius: 9px;
            color: white;
            background: var(--blue-dark);
            font-size: 12px;
            font-weight: 700;
        }

        .main {
            width: min(1040px, calc(100% - 40px));
            margin: auto;
            padding: 38px 0 60px;
        }

        .page-header {
            margin-bottom: 22px;
        }

        .breadcrumb {
            display: inline-block;
            color: var(--blue);
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-header h1 {
            font-size: 29px;
            margin-bottom: 7px;
        }

        .page-header p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .staff-layout {
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            align-items: stretch;
            gap: 18px;
        }

        .info-panel {
            min-height: 500px;
            padding: 30px;
            border-radius: 17px;
            background: linear-gradient(145deg, #073765, #118de4);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .info-label {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 19px;
        }

        .info-panel h2 {
            font-size: 26px;
            line-height: 1.3;
            margin-bottom: 13px;
        }

        .info-panel p {
            color: rgba(255,255,255,.78);
            font-size: 12px;
            line-height: 1.7;
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 28px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 11px;
        }

        .step-number {
            width: 29px;
            height: 29px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.13);
            font-size: 9px;
            font-weight: 700;
        }

        .step strong {
            display: block;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .step span {
            display: block;
            color: rgba(255,255,255,.7);
            font-size: 9px;
            line-height: 1.6;
        }

        .info-note {
            padding: 13px;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            background: rgba(255,255,255,.08);
            color: rgba(255,255,255,.85);
            font-size: 10px;
            line-height: 1.6;
            margin-top: 25px;
        }

        .form-card {
            padding: 30px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 17px;
            box-shadow: 0 14px 38px rgba(25,56,88,.07);
        }

        .form-header {
            padding-bottom: 18px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--border);
        }

        .form-header h2 {
            font-size: 19px;
            margin-bottom: 5px;
        }

        .form-header p {
            color: var(--muted);
            font-size: 10px;
            line-height: 1.6;
        }

        .account-info {
            padding: 13px;
            margin-bottom: 20px;
            border: 1px solid #dceaf5;
            border-radius: 10px;
            background: #f5f9fd;
            color: #667085;
            font-size: 10px;
            line-height: 1.7;
        }

        .account-info strong {
            color: var(--blue-dark);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: #364152;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .form-control {
            width: 100%;
            height: 45px;
            padding: 0 13px;
            border: 1px solid #d6dee8;
            border-radius: 9px;
            background: white;
            color: var(--text);
            outline: none;
            font-size: 12px;
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(8,121,209,.08);
        }

        .form-hint {
            margin-top: 6px;
            color: #98a2b3;
            font-size: 9px;
            line-height: 1.5;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 80px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            padding: 5px;
            border: none;
            background: transparent;
            color: #7b8794;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
        }

        .cancel-button, .submit-button {
            height: 44px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .cancel-button {
            border: 1px solid #d6dee8;
            color: #667085;
            background: white;
        }

        .submit-button {
            border: none;
            color: white;
            background: var(--blue-dark);
        }

        .flash-message {
            padding: 13px 16px;
            margin-bottom: 18px;
            border-radius: 9px;
            font-size: 11px;
            line-height: 1.6;
        }

        .flash-message.success {
            color: #067647;
            background: #ecfdf3;
            border: 1px solid #abefc6;
        }

        .flash-message.error {
            color: #b42318;
            background: #fff1f0;
            border: 1px solid #f5c2c0;
        }

        @media (max-width: 850px) {
            .staff-layout {
                grid-template-columns: 1fr;
            }

            .info-panel {
                min-height: auto;
            }
        }

        @media (max-width: 600px) {
            .navbar-inner, .main {
                width: calc(100% - 26px);
            }

            .form-card, .info-panel {
                padding: 23px;
            }

            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .cancel-button, .submit-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<header class="navbar">
    <div class="navbar-inner">
        <a href="dashboard.php" class="logo">
            Reservasi<span>Kampus</span>
        </a>

        <div class="nav-right">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="../../app/controllers/process_logout.php" class="logout">Logout</a>
        </div>
    </div>
</header>

<main class="main">

    <?php if ($flash_success !== ''): ?>
        <div class="flash-message success" role="status">
            <?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($flash_error !== ''): ?>
        <div class="flash-message error" role="alert">
            <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="page-header">
        <a href="dashboard.php" class="breadcrumb">← Kembali ke Dashboard</a>
        <h1>Daftarkan Pengguna</h1>
        <p>Buat akun mahasiswa, dosen, atau staf kampus secara langsung.</p>
    </section>

    <div class="staff-layout">

        <aside class="info-panel">
            <div>
                <div class="info-label">MANAJEMEN PENGGUNA</div>

                <h2>Tambahkan pengguna baru.</h2>

                <p>
                    Admin dapat membuat akun pengguna tanpa perlu
                    menunggu proses registrasi mandiri.
                </p>

                <div class="steps">
                    <div class="step">
                        <div class="step-number">01</div>
                        <div>
                            <strong>Masukkan nama</strong>
                            <span>Gunakan nama lengkap pengguna.</span>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">02</div>
                        <div>
                            <strong>Pilih tipe pengguna</strong>
                            <span>Mahasiswa, dosen, atau staf kampus.</span>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">03</div>
                        <div>
                            <strong>Atur email dan password</strong>
                            <span>Email digunakan untuk login ke aplikasi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-note">
                Akun yang dibuat oleh Admin langsung mendapatkan
                status <strong>Verified</strong> dan role
                <strong>Pengguna</strong>.
            </div>
        </aside>

        <section class="form-card">
            <div class="form-header">
                <h2>Informasi Akun Pengguna</h2>
                <p>Lengkapi data berikut untuk membuat akun.</p>
            </div>

            <div class="account-info">
                <strong>Akses Pengguna</strong><br>
                Akun ini dapat digunakan untuk mengajukan reservasi
                dan melaporkan kerusakan fasilitas kampus.
            </div>

            <form
                action="../../app/controllers/process_tambah_pengguna.php"
                method="POST"
                id="userForm"
                novalidate
            >
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        placeholder="Contoh: Ahmad Fauzan"
                        autocomplete="name"
                    >
                    <div class="form-hint">
                        Masukkan nama lengkap pengguna.
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipe_pengguna">Tipe Pengguna</label>
                    <select
                        id="tipe_pengguna"
                        name="tipe_pengguna"
                        class="form-control"
                    >
                        <option value="">Pilih tipe pengguna</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="staf">Staf</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email Pengguna</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="contoh@email.com"
                        autocomplete="email"
                        inputmode="email"
                    >
                    <div class="form-hint">
                        Gunakan email yang belum terdaftar.
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password Akun</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Buat password"
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                        >Lihat</button>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="dashboard.php" class="cancel-button">Batal</a>
                    <button type="submit" class="submit-button">
                        Daftarkan Pengguna
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<script>
const form = document.getElementById('userForm');
const nama = document.getElementById('nama');
const tipe = document.getElementById('tipe_pengguna');
const email = document.getElementById('email');
const password = document.getElementById('password');
const passwordToggle = document.getElementById('passwordToggle');

passwordToggle.addEventListener('click', function () {
    const tampil = password.type === 'password';
    password.type = tampil ? 'text' : 'password';
    passwordToggle.textContent = tampil ? 'Sembunyikan' : 'Lihat';
});

[nama, tipe, email, password].forEach(function (input) {
    input.addEventListener('input', function () {
        input.setCustomValidity('');
    });

    input.addEventListener('change', function () {
        input.setCustomValidity('');
    });
});

form.addEventListener('submit', function (event) {
    nama.setCustomValidity('');
    tipe.setCustomValidity('');
    email.setCustomValidity('');
    password.setCustomValidity('');

    if (!nama.value.trim()) {
        event.preventDefault();
        nama.setCustomValidity('Nama lengkap wajib diisi.');
        nama.reportValidity();
        return;
    }

    if (!tipe.value) {
        event.preventDefault();
        tipe.setCustomValidity('Silakan pilih tipe pengguna.');
        tipe.reportValidity();
        return;
    }

    const polaEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    if (!email.value.trim()) {
        event.preventDefault();
        email.setCustomValidity('Email wajib diisi.');
        email.reportValidity();
        return;
    }

    if (!polaEmail.test(email.value.trim())) {
        event.preventDefault();
        email.setCustomValidity(
            'Format email tidak valid. Contoh: nama@email.com.'
        );
        email.reportValidity();
        return;
    }

    if (!password.value.trim()) {
        event.preventDefault();
        password.setCustomValidity('Password wajib diisi.');
        password.reportValidity();
        return;
    }
});
</script>

</body>
</html>
