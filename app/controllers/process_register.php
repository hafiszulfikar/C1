<?php

require_once __DIR__ . '/../../config/database.php';

$success = false;
$message = '';
$redirect_text = '';
$redirect_link = '../../views/auth/login.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ambil data dari form
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipe_pengguna = $_POST['tipe_pengguna'] ?? '';

    // =========================
    // VALIDASI
    // =========================

    if (empty($nama) || empty($email) || empty($password) || empty($tipe_pengguna)) {

        $message = "Semua field wajib diisi.";
        $redirect_text = "Kembali ke Register";
        $redirect_link = '../../views/auth/register.php';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Format email tidak valid.";
        $redirect_text = "Kembali ke Register";
        $redirect_link = '../../views/auth/register.php';

    } elseif (!in_array($tipe_pengguna, ['mahasiswa', 'dosen', 'staf'])) {

        $message = "Status sivitas tidak valid.";
        $redirect_text = "Kembali ke Register";
        $redirect_link = '../../views/auth/register.php';

    } else {

        // =========================
        // PASSWORD
        // =========================

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Semua registrasi umum menjadi pengguna
        $role = 'pengguna';

        // Menunggu verifikasi admin
        $status_akun = 'pending';

        try {

            // =========================
            // INSERT DATABASE
            // =========================

            $sql = "
                INSERT INTO users
                (
                    nama,
                    email,
                    password,
                    role,
                    tipe_pengguna,
                    status_akun
                )
                VALUES
                (
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

            $success = true;

            $message = "
                Akun berhasil dibuat.
                <br>
                Silakan tunggu verifikasi admin
                sebelum melakukan login.
            ";

            $redirect_text = "Kembali ke Login";
            $redirect_link = '../../views/auth/login.php';

        } catch (PDOException $e) {

            // Email sudah terdaftar
            if ($e->getCode() == 23000) {

                $message = "
                    Email tersebut sudah terdaftar.
                    <br>
                    Silakan gunakan email lain.
                ";

            } else {

                $message = "
                    Terjadi kesalahan saat membuat akun.
                    <br>
                    Silakan coba lagi.
                ";
            }

            $redirect_text = "Kembali ke Register";
            $redirect_link = '../../views/auth/register.php';
        }
    }

} else {

    $message = "Akses tidak valid.";
    $redirect_text = "Kembali ke Register";
    $redirect_link = '../../views/auth/register.php';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo $success ? 'Registrasi Berhasil' : 'Registrasi'; ?>
        | CampusReserve
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    #06386b 0%,
                    #075ca8 50%,
                    #1188e8 100%
                );
        }

        .container {

            width: 430px;

            padding: 42px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.10
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.22
                );

            border-radius: 24px;

            backdrop-filter:
                blur(18px);

            -webkit-backdrop-filter:
                blur(18px);

            box-shadow:

                0 25px 60px
                rgba(
                    0,
                    0,
                    0,
                    0.25
                ),

                inset 0 1px 0
                rgba(
                    255,
                    255,
                    255,
                    0.12
                );

            text-align: center;
        }

        /* LOGO */

        .logo {

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 34px;
        }

        .logo span {

            font-weight: 400;
        }

        /* ICON */

        .icon {

            width: 76px;
            height: 76px;

            margin:
                0 auto 24px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.14
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.20
                );

            font-size: 34px;

            box-shadow:
                inset 0 1px 0
                rgba(
                    255,
                    255,
                    255,
                    0.10
                );
        }

        /* TITLE */

        .title {

            margin-bottom: 12px;
        }

        .title h1 {

            font-size: 28px;

            line-height: 1.2;

            margin-bottom: 10px;
        }

        .title p {

            font-size: 13px;

            line-height: 1.7;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.70
                );
        }

        /* MESSAGE */

        .message {

            margin-top: 24px;

            padding: 15px 17px;

            border-radius: 12px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.07
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.12
                );

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.82
                );

            font-size: 12px;

            line-height: 1.7;
        }

        /* BUTTON */

        .button {

            display: flex;

            align-items: center;
            justify-content: center;

            width: 100%;

            height: 46px;

            margin-top: 24px;

            border-radius: 10px;

            background: #063b70;

            color: white;

            font-size: 13px;

            font-weight: 700;

            text-decoration: none;

            transition: 0.2s;
        }

        .button:hover {

            background: #052f59;

            transform:
                translateY(-1px);
        }

        /* LOGIN TEXT */

        .bottom-text {

            margin-top: 20px;

            font-size: 11px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.60
                );
        }

        .bottom-text a {

            color: white;

            font-weight: 700;

            text-decoration: none;
        }

        .bottom-text a:hover {

            text-decoration: underline;
        }

        /* MOBILE */

        @media (max-width: 500px) {

            .container {

                width: 100%;

                padding:
                    32px 24px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <!-- LOGO -->

    <div class="logo">
        Campus<span>Reserve</span>
    </div>


    <!-- ICON -->

    <div class="icon">

        <?php if ($success): ?>

            ✓

        <?php else: ?>

            !

        <?php endif; ?>

    </div>


    <!-- TITLE -->

    <div class="title">

        <?php if ($success): ?>

            <h1>
                Registrasi Berhasil
            </h1>

            <p>
                Selamat, akun CampusReserve
                berhasil dibuat.
            </p>

        <?php else: ?>

            <h1>
                Registrasi Gagal
            </h1>

            <p>
                Akun belum dapat dibuat.
                Silakan periksa informasi berikut.
            </p>

        <?php endif; ?>

    </div>


    <!-- MESSAGE -->

    <div class="message">

        <?php
        echo $message;
        ?>

    </div>


    <!-- BUTTON -->

    <a
        href="<?php echo htmlspecialchars($redirect_link); ?>"
        class="button"
    >

        <?php
        echo htmlspecialchars($redirect_text);
        ?>

    </a>


    <!-- BOTTOM -->

    <?php if ($success): ?>

        <div class="bottom-text">

            Akun akan berstatus
            <strong>Pending</strong>
            sampai diverifikasi admin.

        </div>

    <?php endif; ?>

</div>

</body>
</html>