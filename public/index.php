<?php
// public/index.php

session_start();

require_once __DIR__ . '/../config/database.php';

// =========================
// AMBIL FASILITAS AKTIF
// =========================

$sql = "SELECT * FROM facilities WHERE status_fasilitas = 'aktif'";
$params = [];

// Filter tipe
if (isset($_GET['tipe']) && !empty($_GET['tipe'])) {
    $sql .= " AND tipe = :tipe";
    $params[':tipe'] = $_GET['tipe'];
}

// Filter lokasi
if (isset($_GET['lokasi']) && !empty($_GET['lokasi'])) {
    $sql .= " AND lokasi LIKE :lokasi";
    $params[':lokasi'] = '%' . $_GET['lokasi'] . '%';
}

// Filter kapasitas
if (isset($_GET['kapasitas']) && !empty($_GET['kapasitas'])) {
    $sql .= " AND kapasitas >= :kapasitas";
    $params[':kapasitas'] = (int) $_GET['kapasitas'];
}

$sql .= " ORDER BY nama_fasilitas ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$daftar_fasilitas = $stmt->fetchAll();

// =========================
// NILAI FILTER
// =========================

$tipe_saat_ini = $_GET['tipe'] ?? '';
$lokasi_saat_ini = $_GET['lokasi'] ?? '';
$kapasitas_saat_ini = $_GET['kapasitas'] ?? '';

// ========================================
// NAVBAR BERDASARKAN STATUS LOGIN
// ========================================

$sudah_login =
    isset($_SESSION['role']);

$dashboard_link =
    '../views/user/dashboard.php';

if ($sudah_login) {

    if ($_SESSION['role'] === 'admin') {

        $dashboard_link =
            '../views/admin/dashboard.php';

    } elseif ($_SESSION['role'] === 'petugas') {

        $dashboard_link =
            '../views/officer/dashboard.php';

    } elseif ($_SESSION['role'] === 'pengguna') {

        $dashboard_link =
            '../views/user/dashboard.php';

    }
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
        ReservasiKampus | Sistem Reservasi Fasilitas Kampus
    </title>

    <style>

        /* ========================================
           RESET
        ======================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #172033;

            background: #f5f8fc;

            line-height: 1.5;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select {
            font-family: inherit;
        }


        /* ========================================
           COLORS
        ======================================== */

        :root {

            --blue-dark: #063b70;
            --blue: #0879d1;
            --blue-light: #25a8f0;

            --text: #172033;
            --muted: #6b7280;

            --border: #e4eaf1;

            --white: #ffffff;

            --green: #0d9f6e;
        }


        /* ========================================
           NAVBAR
        ======================================== */

        .navbar {

            position: sticky;

            top: 0;

            z-index: 100;

            background: rgba(255, 255, 255, 0.96);

            backdrop-filter: blur(12px);

            border-bottom:
                1px solid
                rgba(228, 234, 241, 0.9);
        }

        .nav-container {

            width: min(
                1180px,
                calc(100% - 40px)
            );

            margin: auto;

            height: 72px;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .logo {

            font-size: 21px;

            font-weight: 700;

            color: var(--blue-dark);

            letter-spacing: -0.4px;
        }

        .logo span {
            font-weight: 400;
        }

        .nav-right {

            display: flex;

            align-items: center;

            gap: 12px;
        }

        .nav-login {

            color: var(--blue-dark);

            font-size: 13px;

            font-weight: 600;

            padding: 10px 14px;
        }

        .nav-register {

            background: var(--blue-dark);

            color: white;

            padding:
                10px 16px;

            border-radius: 9px;

            font-size: 13px;

            font-weight: 600;

            transition: 0.2s;
        }

        .nav-register:hover {
            background: #052f59;
        }


        /* ========================================
           HEADER
        ======================================== */

        .header {

            background:
                linear-gradient(
                    135deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color: white;

            padding:
                58px 20px
                88px;
        }

        .header-container {

            width: min(
                1050px,
                100%
            );

            margin: auto;

            text-align: center;
        }

        .header-label {

            display: inline-block;

            margin-bottom: 10px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.68
                );

            font-size: 12px;

            font-weight: 500;
        }

        .header-title {

            font-size: 24px;

            font-weight: 700;

            margin-bottom: 8px;
        }

        .header-subtitle {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.76
                );

            font-size: 13px;
        }


        /* ========================================
           SEARCH CARD
        ======================================== */

        .search-wrapper {

            width: min(
                1060px,
                calc(100% - 40px)
            );

            margin:
                -48px auto 0;

            position: relative;

            z-index: 5;
        }

        .search-card {

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            padding: 24px;

            box-shadow:
                0 18px 45px
                rgba(
                    24,
                    48,
                    81,
                    0.12
                );
        }

        .search-title {

            font-size: 16px;

            font-weight: 700;

            margin-bottom: 5px;
        }

        .search-subtitle {

            color: var(--muted);

            font-size: 12px;

            margin-bottom: 18px;
        }

        .search-form {

            display: grid;

            grid-template-columns:
                1fr
                1.3fr
                0.8fr
                auto;

            gap: 12px;

            align-items: end;
        }

        .field label {

            display: block;

            margin-bottom: 7px;

            color: #4b5563;

            font-size: 11px;

            font-weight: 600;
        }

        .field input,
        .field select {

            width: 100%;

            height: 44px;

            padding:
                0 12px;

            border:
                1px solid
                #d8e0e9;

            border-radius: 9px;

            background: #fff;

            color: #1f2937;

            outline: none;

            font-size: 12px;

            transition: 0.2s;
        }

        .field input:focus,
        .field select:focus {

            border-color:
                var(--blue);

            box-shadow:
                0 0 0 3px
                rgba(
                    8,
                    121,
                    209,
                    0.08
                );
        }

        .search-button {

            height: 44px;

            padding:
                0 22px;

            border: none;

            border-radius: 9px;

            background:
                var(--blue-dark);

            color: white;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;
        }

        .search-button:hover {

            background: #052f59;

            transform:
                translateY(-1px);
        }


        /* ========================================
           MAIN
        ======================================== */

        .main {

            width: min(
                1180px,
                calc(100% - 40px)
            );

            margin: auto;

            padding:
                65px 0
                80px;
        }


        /* ========================================
           SECTION HEADING
        ======================================== */

        .section-heading {

            display: flex;

            align-items: end;

            justify-content: space-between;

            margin-bottom: 22px;
        }

        .section-heading h2 {

            font-size: 24px;

            color: var(--text);

            margin-bottom: 5px;
        }

        .section-heading p {

            color: var(--muted);

            font-size: 13px;
        }

        .result-count {

            color:
                var(--blue);

            font-size: 12px;

            font-weight: 600;
        }


        /* ========================================
           FACILITY GRID
        ======================================== */

        .facility-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 18px;
        }

        .facility-card {

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            overflow: hidden;

            transition:
                transform .2s,
                box-shadow .2s,
                border-color .2s;
        }

        .facility-card:hover {

            transform:
                translateY(-4px);

            border-color:
                #d3dfeb;

            box-shadow:
                0 16px 35px
                rgba(
                    20,
                    48,
                    83,
                    0.09
                );
        }

        .facility-banner {

            height: 128px;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #eaf5ff,
                    #d9ecfc
                );

            color:
                var(--blue);

            font-size: 30px;

            font-weight: 700;
        }

        .facility-content {

            padding: 18px;
        }

        .facility-type {

            display: inline-block;

            padding:
                5px 9px;

            border-radius: 999px;

            background:
                #eef7ff;

            color:
                var(--blue);

            font-size: 10px;

            font-weight: 700;

            margin-bottom: 10px;

            text-transform:
                capitalize;
        }

        .facility-name {

            font-size: 17px;

            font-weight: 700;

            color: var(--text);

            margin-bottom: 10px;

            line-height: 1.3;
        }

        .facility-info {

            display: flex;

            flex-direction: column;

            gap: 7px;

            margin-bottom: 17px;
        }

        .info-row {

            display: flex;

            align-items: center;

            gap: 8px;

            color: var(--muted);

            font-size: 11px;
        }

        .info-icon {

            width: 20px;

            text-align: center;

            color: var(--blue);

            font-weight: 700;
        }

        .facility-footer {

            padding-top: 14px;

            border-top:
                1px solid
                #edf1f5;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .status {

            display: flex;

            align-items: center;

            gap: 6px;

            color:
                var(--green);

            font-size: 11px;

            font-weight: 600;
        }

        .status-dot {

            width: 7px;

            height: 7px;

            border-radius: 50%;

            background:
                var(--green);
        }

        .schedule-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                9px 13px;

            border-radius: 8px;

            background:
                var(--blue-dark);

            color: white;

            font-size: 11px;

            font-weight: 700;

            transition: 0.2s;
        }

        .schedule-button:hover {

            background: #052f59;
        }


        /* ========================================
           EMPTY STATE
        ======================================== */

        .empty-state {

            grid-column: 1 / -1;

            padding:
                55px 20px;

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            text-align: center;
        }

        .empty-icon {

            width: 60px;

            height: 60px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 16px;

            border-radius: 50%;

            background:
                #eef6ff;

            color:
                var(--blue);

            font-size: 22px;

            font-weight: 700;
        }

        .empty-state h3 {

            font-size: 16px;

            margin-bottom: 6px;
        }

        .empty-state p {

            color: var(--muted);

            font-size: 12px;
        }


        /* ========================================
           HOW IT WORKS
        ======================================== */

        .how-section {

            margin-top: 80px;

            padding-top: 65px;

            border-top:
                1px solid
                var(--border);
        }

        .steps {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-top: 26px;
        }

        .step {

            padding: 25px;

            border:
                1px solid
                var(--border);

            background: white;

            border-radius: 15px;
        }

        .step-number {

            width: 38px;

            height: 38px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            background:
                #eaf5ff;

            color:
                var(--blue);

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 17px;
        }

        .step h3 {

            font-size: 15px;

            margin-bottom: 8px;
        }

        .step p {

            color: var(--muted);

            font-size: 12px;

            line-height: 1.7;
        }


        /* ========================================
           CTA
        ======================================== */

        .cta {

            margin-top: 70px;

            padding:
                45px 35px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #073765,
                    #0a6fbd
                );

            color: white;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;
        }

        .cta h2 {

            font-size: 25px;

            margin-bottom: 8px;
        }

        .cta p {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.72
                );

            font-size: 12px;
        }

        .cta-button {

            flex-shrink: 0;

            padding:
                12px 18px;

            border-radius: 9px;

            background: white;

            color:
                var(--blue-dark);

            font-size: 12px;

            font-weight: 700;
        }


        /* ========================================
           FOOTER
        ======================================== */

        .footer {

            background:
                #071d34;

            color: white;

            padding:
                28px 20px;
        }

        .footer-container {

            width: min(
                1180px,
                100%
            );

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }

        .footer-logo {

            font-size: 16px;

            font-weight: 700;
        }

        .footer-logo span {
            font-weight: 400;
        }

        .footer-text {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.50
                );

            font-size: 10px;
        }


        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 900px) {

            .search-form {

                grid-template-columns:
                    1fr 1fr;
            }

            .search-button {
                width: 100%;
            }

            .facility-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }

            .steps {

                grid-template-columns: 1fr;
            }
        }


        @media (max-width: 650px) {

            .nav-container {

                width:
                    calc(100% - 28px);
            }

            .header {

                padding:
                    45px 16px
                    70px;
            }

            .search-wrapper {

                width:
                    calc(100% - 24px);

                margin-top: -35px;
            }

            .search-form {

                grid-template-columns: 1fr;
            }

            .facility-grid {

                grid-template-columns: 1fr;
            }

            .section-heading {

                align-items: flex-start;

                flex-direction: column;

                gap: 8px;
            }

            .cta {

                align-items: flex-start;

                flex-direction: column;
            }

            .footer-container {

                align-items: flex-start;

                flex-direction: column;
            }
        }

    </style>

</head>


<body>


<!-- ========================================
     NAVBAR
========================================= -->

<header class="navbar">

    <div class="nav-container">

        <a
            href="index.php"
            class="logo"
        >
            Reservasi<span>Kampus</span>
        </a>


        <div class="nav-right">

            <?php if ($sudah_login): ?>

                <a
                    href="<?php echo htmlspecialchars($dashboard_link); ?>"
                    class="nav-login"
                >
                    Dashboard
                </a>

                <a
                    href="../app/controllers/process_logout.php"
                    class="nav-register"
                >
                    Logout
                </a>

            <?php else: ?>

                <a
                    href="../views/auth/login.php"
                    class="nav-login"
                >
                    Login
                </a>

                <a
                    href="../views/auth/register.php"
                    class="nav-register"
                >
                    Daftar
                </a>

            <?php endif; ?>

        </div>

    </div>

</header>


<!-- ========================================
     HEADER
========================================= -->

<section class="header">

    <div class="header-container">

        <div class="header-label">
            Sistem Reservasi Fasilitas Kampus
        </div>

        <div class="header-title">
            Cari dan reservasi ruangan kampus
        </div>

        <div class="header-subtitle">
            Temukan fasilitas yang sesuai dengan kebutuhan kegiatanmu.
        </div>

    </div>

</section>


<!-- ========================================
     SEARCH
========================================= -->

<div class="search-wrapper">

    <div class="search-card">

        <div class="search-title">
            Cari Ruangan
        </div>

        <div class="search-subtitle">
            Gunakan filter untuk menemukan fasilitas yang sesuai.
        </div>


        <form
            action="index.php"
            method="GET"
            class="search-form"
        >

            <!-- TIPE -->

            <div class="field">

                <label for="tipe">
                    Tipe Fasilitas
                </label>

                <select
                    id="tipe"
                    name="tipe"
                >

                    <option value="">
                        Semua tipe
                    </option>

                    <option
                        value="ruang kelas"
                        <?php
                            echo
                            $tipe_saat_ini === 'ruang kelas'
                            ? 'selected'
                            : '';
                        ?>
                    >
                        Ruang Kelas
                    </option>

                    <option
                        value="aula"
                        <?php
                            echo
                            $tipe_saat_ini === 'aula'
                            ? 'selected'
                            : '';
                        ?>
                    >
                        Aula
                    </option>

                    <option
                        value="laboratorium"
                        <?php
                            echo
                            $tipe_saat_ini === 'laboratorium'
                            ? 'selected'
                            : '';
                        ?>
                    >
                        Laboratorium
                    </option>

                    <option
                        value="alat"
                        <?php
                            echo
                            $tipe_saat_ini === 'alat'
                            ? 'selected'
                            : '';
                        ?>
                    >
                        Alat
                    </option>

                    <option
                        value="lapangan"
                        <?php
                            echo
                            $tipe_saat_ini === 'lapangan'
                            ? 'selected'
                            : '';
                        ?>
                    >
                        Lapangan
                    </option>

                </select>

            </div>


            <!-- LOKASI -->

            <div class="field">

                <label for="lokasi">
                    Lokasi
                </label>

                <input
                    type="text"
                    id="lokasi"
                    name="lokasi"
                    placeholder="Contoh: Gedung E"
                    value="<?php
                        echo htmlspecialchars(
                            $lokasi_saat_ini
                        );
                    ?>"
                >

            </div>


            <!-- KAPASITAS -->

            <div class="field">

                <label for="kapasitas">
                    Minimal Kapasitas
                </label>

                <input
                    type="number"
                    id="kapasitas"
                    name="kapasitas"
                    min="1"
                    placeholder="40"
                    value="<?php
                        echo htmlspecialchars(
                            $kapasitas_saat_ini
                        );
                    ?>"
                >

            </div>


            <button
                type="submit"
                class="search-button"
            >
                Cari Ruangan
            </button>

        </form>

    </div>

</div>


<!-- ========================================
     MAIN
========================================= -->

<main class="main">


    <!-- SECTION -->

    <div class="section-heading">

        <div>

            <h2>
                Ruangan Tersedia
            </h2>

            <p>
                Fasilitas kampus yang sedang aktif untuk digunakan.
            </p>

        </div>


        <div class="result-count">

            <?php echo count($daftar_fasilitas); ?>

            fasilitas ditemukan

        </div>

    </div>


    <!-- FACILITIES -->

    <div class="facility-grid">


        <?php if (count($daftar_fasilitas) > 0): ?>


            <?php foreach ($daftar_fasilitas as $fasilitas): ?>

                <article class="facility-card">


                    <div class="facility-banner">

                        <?php

                        $nama =
                            $fasilitas[
                                'nama_fasilitas'
                            ];

                        $initial =
                            strtoupper(
                                substr(
                                    $nama,
                                    0,
                                    1
                                )
                            );

                        echo htmlspecialchars(
                            $initial
                        );

                        ?>

                    </div>


                    <div class="facility-content">


                        <span
                            class="facility-type"
                        >

                            <?php
                                echo htmlspecialchars(
                                    $fasilitas['tipe']
                                );
                            ?>

                        </span>


                        <h3
                            class="facility-name"
                        >

                            <?php
                                echo htmlspecialchars(
                                    $fasilitas[
                                        'nama_fasilitas'
                                    ]
                                );
                            ?>

                        </h3>


                        <div
                            class="facility-info"
                        >

                            <div
                                class="info-row"
                            >

                                <span
                                    class="info-icon"
                                >
                                    L
                                </span>

                                <span>

                                    <?php
                                        echo htmlspecialchars(
                                            $fasilitas[
                                                'lokasi'
                                            ]
                                        );
                                    ?>

                                </span>

                            </div>


                            <div
                                class="info-row"
                            >

                                <span
                                    class="info-icon"
                                >
                                    K
                                </span>

                                <span>

                                    Kapasitas

                                    <?php
                                        echo htmlspecialchars(
                                            $fasilitas[
                                                'kapasitas'
                                            ]
                                        );
                                    ?>

                                    orang

                                </span>

                            </div>

                        </div>


                        <div
                            class="facility-footer"
                        >

                            <div
                                class="status"
                            >

                                <span
                                    class="status-dot"
                                ></span>

                                Tersedia

                            </div>


                            <a
                                href="jadwal.php?id=<?php echo
                                    $fasilitas['id'];
                                ?>"
                                class="schedule-button"
                            >
                                Lihat Jadwal
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>


        <?php else: ?>


            <div
                class="empty-state"
            >

                <div
                    class="empty-icon"
                >
                    ?
                </div>

                <h3>
                    Tidak ada fasilitas ditemukan
                </h3>

                <p>
                    Coba ubah filter pencarian
                    atau gunakan kata kunci lain.
                </p>

            </div>


        <?php endif; ?>


    </div>


    <!-- ========================================
         HOW IT WORKS
    ========================================= -->

    <section
        class="how-section"
    >

        <div class="section-heading">

            <div>

                <h2>
                    Cara Menggunakan
                </h2>

                <p>
                    Reservasi ruangan dalam tiga langkah sederhana.
                </p>

            </div>

        </div>


        <div class="steps">


            <div class="step">

                <div class="step-number">
                    01
                </div>

                <h3>
                    Cari Ruangan
                </h3>

                <p>
                    Gunakan filter tipe, lokasi,
                    dan kapasitas untuk menemukan
                    fasilitas yang kamu butuhkan.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    02
                </div>

                <h3>
                    Cek Jadwal
                </h3>

                <p>
                    Lihat jadwal penggunaan fasilitas
                    sebelum menentukan waktu reservasi.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    03
                </div>

                <h3>
                    Ajukan Reservasi
                </h3>

                <p>
                    Login ke akun kemudian ajukan
                    reservasi sesuai kebutuhan kegiatanmu.
                </p>

            </div>


        </div>

    </section>


</main>


<!-- ========================================
     FOOTER
========================================= -->

<footer class="footer">

    <div class="footer-container">

        <div class="footer-logo">

            Reservasi<span>Kampus</span>

        </div>


        <div class="footer-text">

            Sistem Reservasi Fasilitas Kampus

        </div>

    </div>

</footer>


</body>

</html>