<?php

session_start();

require_once __DIR__ . '/../../config/database.php';

$success = false;
$message = '';
$title = 'Login Gagal';
$description = 'Login belum dapat dilakukan.';
$redirect_text = 'Kembali ke Login';
$redirect_link = '../../views/auth/login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // =========================
    // VALIDASI INPUT
    // =========================

    if ($email === '' || $password === '') {

        $message = 'Email dan password wajib diisi.';

    } else {

        try {

            // =========================
            // CARI USER
            // =========================

            $stmt = $pdo->prepare(
                "SELECT *
                 FROM users
                 WHERE email = :email"
            );

            $stmt->execute([
                ':email' => $email
            ]);

            $user = $stmt->fetch();


            // =========================
            // CEK LOGIN
            // =========================

            if (
                $user &&
                password_verify(
                    $password,
                    $user['password']
                )
            ) {

                // =========================
                // CEK STATUS AKUN
                // =========================

                if ($user['role'] === 'pengguna') {

                    if ($user['status_akun'] === 'pending') {

                        $message =
                            'Akun Anda masih berstatus PENDING. ' .
                            'Tunggu verifikasi dari Admin.';

                    } elseif ($user['status_akun'] === 'rejected') {

                        $message =
                            'Akun Anda ditolak oleh Admin. ' .
                            'Silakan hubungi Admin untuk informasi lebih lanjut.';

                    } elseif ($user['status_akun'] !== 'verified') {

                        $message =
                            'Akun Anda belum dapat digunakan. ' .
                            'Silakan hubungi Admin untuk informasi lebih lanjut.';

                    } else {

                        // =========================
                        // LOGIN PENGGUNA BERHASIL
                        // =========================

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['nama'] = $user['nama'];

                        header(
                            "Location: ../../views/user/dashboard.php"
                        );

                        exit();
                    }

                } else {

                    // =========================
                    // LOGIN ADMIN / PETUGAS
                    // =========================

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['nama'] = $user['nama'];


                    // =========================
                    // REDIRECT ROLE
                    // =========================

                    if ($user['role'] === 'admin') {

                        header(
                            "Location: ../../views/admin/dashboard.php"
                        );

                        exit();

                    } elseif ($user['role'] === 'petugas') {

                        header(
                            "Location: ../../views/officer/dashboard.php"
                        );

                        exit();
                    }
                }

            } else {

                $message =
                    'Email atau password yang Anda masukkan salah.';
            }

        } catch (PDOException $e) {

            $message =
                'Terjadi kesalahan saat menghubungkan ke database. ' .
                'Silakan coba lagi.';
        }
    }

} else {

    $message =
        'Akses tidak valid. Silakan login melalui halaman login.';
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
        <?php echo htmlspecialchars($title); ?>
        | ReservasiKampus
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        :root {

            --blue-dark: #063b70;
            --blue: #0879d1;
            --blue-light: #118de4;

            --text: #172033;
            --muted: #6b7280;

            --border: #e3e9f0;

            --bg: #f5f8fc;

            --white: #ffffff;

            --danger: #c23b3b;
            --danger-bg: #fff1f1;
        }


        body {

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: var(--text);

            background:
                var(--bg);
        }


        a {
            text-decoration: none;
        }


        /* =====================================
           NAVBAR
        ===================================== */

        .navbar {

            height: 72px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.96
                );

            border-bottom:
                1px solid
                var(--border);

            display: flex;

            align-items: center;
        }


        .navbar-inner {

            width:
                min(
                    1180px,
                    calc(100% - 40px)
                );

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        .logo {

            color:
                var(--blue-dark);

            font-size: 21px;

            font-weight: 700;

            letter-spacing:
                -0.4px;
        }


        .logo span {

            font-weight: 400;
        }


        .nav-right {

            display: flex;

            align-items: center;

            gap: 8px;
        }


        .nav-link {

            color:
                var(--blue-dark);

            padding:
                9px 12px;

            font-size: 12px;

            font-weight: 700;
        }


        .nav-button {

            background:
                var(--blue-dark);

            color: white;

            padding:
                10px 16px;

            border-radius: 9px;

            font-size: 12px;

            font-weight: 700;

            transition: .2s;
        }


        .nav-button:hover {

            background:
                #052f59;
        }


        /* =====================================
           MAIN
        ===================================== */

        .main {

            min-height:
                calc(
                    100vh - 72px
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding:
                55px 20px;
        }


        /* =====================================
           CARD
        ===================================== */

        .result-card {

            width:
                min(
                    850px,
                    100%
                );

            min-height:
                455px;

            display: grid;

            grid-template-columns:
                0.92fr
                1.08fr;

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius:
                20px;

            overflow: hidden;

            box-shadow:
                0 18px 50px
                rgba(
                    25,
                    56,
                    88,
                    0.10
                );
        }


        /* =====================================
           LEFT
        ===================================== */

        .result-intro {

            background:
                linear-gradient(
                    145deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color: white;

            padding:
                48px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .intro-logo {

            font-size:
                20px;

            font-weight:
                700;

            margin-bottom:
                28px;
        }


        .intro-logo span {

            font-weight:
                400;
        }


        .intro-label {

            display:
                inline-flex;

            align-self:
                flex-start;

            padding:
                6px 10px;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.18
                );

            border-radius:
                999px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.08
                );

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.84
                );

            font-size:
                10px;

            margin-bottom:
                20px;
        }


        .result-intro h1 {

            font-size:
                31px;

            line-height:
                1.2;

            margin-bottom:
                14px;
        }


        .result-intro p {

            max-width:
                310px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.76
                );

            font-size:
                13px;

            line-height:
                1.7;
        }


        /* =====================================
           RIGHT
        ===================================== */

        .result-content {

            padding:
                55px 52px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;
        }


        /* =====================================
           ICON
        ===================================== */

        .result-icon {

            width:
                70px;

            height:
                70px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin-bottom:
                22px;

            background:
                var(--danger-bg);

            color:
                var(--danger);

            border:
                1px solid
                #f0d0d0;

            font-size:
                29px;

            font-weight:
                700;
        }


        /* =====================================
           TITLE
        ===================================== */

        .result-content h2 {

            font-size:
                27px;

            margin-bottom:
                8px;

            color:
                var(--text);
        }


        .result-subtitle {

            max-width:
                380px;

            color:
                var(--muted);

            font-size:
                12px;

            line-height:
                1.7;
        }


        /* =====================================
           MESSAGE
        ===================================== */

        .result-message {

            width:
                100%;

            margin-top:
                22px;

            padding:
                14px 16px;

            border:
                1px solid
                var(--border);

            border-radius:
                10px;

            background:
                #f8fafc;

            color:
                #657181;

            font-size:
                11px;

            line-height:
                1.6;
        }


        /* =====================================
           BUTTON
        ===================================== */

        .result-button {

            width:
                100%;

            height:
                45px;

            margin-top:
                20px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                9px;

            background:
                var(--blue-dark);

            color:
                white;

            font-size:
                12px;

            font-weight:
                700;

            transition:
                .2s;
        }


        .result-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* =====================================
           BACK LINK
        ===================================== */

        .back-link {

            margin-top:
                18px;

            color:
                var(--muted);

            font-size:
                10px;
        }


        .back-link:hover {

            color:
                var(--blue);
        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 760px) {

            .result-card {

                grid-template-columns:
                    1fr;
            }


            .result-intro {

                padding:
                    35px 32px;
            }


            .result-intro h1 {

                font-size:
                    26px;
            }


            .result-content {

                padding:
                    40px 32px;
            }

        }


        @media (max-width: 500px) {

            .navbar-inner {

                width:
                    calc(
                        100% - 26px
                    );
            }


            .nav-link {

                display:
                    none;
            }


            .main {

                padding:
                    25px 13px;
            }


            .result-card {

                border-radius:
                    16px;
            }

        }

    </style>

</head>


<body>


<!-- =====================================
     NAVBAR
===================================== -->

<header class="navbar">

    <div class="navbar-inner">


        <a
            href="../../public/index.php"
            class="logo"
        >

            Reservasi<span>Kampus</span>

        </a>


        <div class="nav-right">

            <a
                href="../../public/index.php"
                class="nav-link"
            >
                Beranda
            </a>


            <a
                href="../../views/auth/register.php"
                class="nav-button"
            >
                Daftar
            </a>

        </div>

    </div>

</header>



<!-- =====================================
     MAIN
===================================== -->

<main class="main">


    <section class="result-card">


        <!-- =================================
             LEFT PANEL
        ================================== -->

        <div class="result-intro">


            <div class="intro-logo">

                Reservasi<span>Kampus</span>

            </div>


            <div class="intro-label">

                SISTEM RESERVASI FASILITAS KAMPUS
            </div>


            <h1>

                Login ke
                akunmu.

            </h1>


            <p>

                Gunakan akun yang telah
                terdaftar untuk mengakses
                layanan ReservasiKampus.

            </p>


        </div>



        <!-- =================================
             RIGHT PANEL
        ================================== -->

        <div class="result-content">


            <!-- ERROR ICON -->

            <div class="result-icon">

                !

            </div>


            <!-- TITLE -->

            <h2>

                <?php
                echo htmlspecialchars(
                    $title
                );
                ?>

            </h2>


            <!-- DESCRIPTION -->

            <p class="result-subtitle">

                <?php
                echo htmlspecialchars(
                    $description
                );
                ?>

            </p>


            <!-- MESSAGE -->

            <div class="result-message">

                <?php
                echo htmlspecialchars(
                    $message
                );
                ?>

            </div>


            <!-- BUTTON -->

            <a
                href="<?php
                    echo htmlspecialchars(
                        $redirect_link
                    );
                ?>"
                class="result-button"
            >

                <?php
                echo htmlspecialchars(
                    $redirect_text
                );
                ?>

            </a>


            <!-- BACK -->

            <a
                href="../../public/index.php"
                class="back-link"
            >

                ← Kembali ke Beranda

            </a>


        </div>


    </section>


</main>


</body>

</html>