<?php
// views/user/dashboard.php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'];

// ========================================
// RIWAYAT RESERVASI
// ========================================

$sql_res = "
    SELECT
        r.*,
        f.nama_fasilitas,
        f.lokasi
    FROM reservations r
    JOIN facilities f
        ON r.facility_id = f.id
    WHERE r.user_id = :user_id
    ORDER BY r.tanggal DESC, r.start_time DESC
";

$stmt_res = $pdo->prepare($sql_res);

$stmt_res->execute([
    ':user_id' => $user_id
]);

$riwayat_reservasi = $stmt_res->fetchAll();


// ========================================
// RIWAYAT LAPORAN
// ========================================

$sql_rep = "
    SELECT
        rep.*,
        f.nama_fasilitas
    FROM reports rep
    JOIN facilities f
        ON rep.facility_id = f.id
    WHERE rep.user_id = :user_id
    ORDER BY rep.created_at DESC
";

$stmt_rep = $pdo->prepare($sql_rep);

$stmt_rep->execute([
    ':user_id' => $user_id
]);

$riwayat_laporan = $stmt_rep->fetchAll();


// ========================================
// STATISTIK RESERVASI
// ========================================

$total_reservasi = count($riwayat_reservasi);

$total_menunggu = 0;
$total_disetujui = 0;
$total_ditolak = 0;
$total_dibatalkan = 0;

foreach ($riwayat_reservasi as $res) {

    switch ($res['status_reservasi']) {

        case 'menunggu':
            $total_menunggu++;
            break;

        case 'disetujui':
            $total_disetujui++;
            break;

        case 'ditolak':
            $total_ditolak++;
            break;

        case 'dibatalkan':
            $total_dibatalkan++;
            break;
    }
}


// ========================================
// STATISTIK LAPORAN
// ========================================

$total_laporan = count($riwayat_laporan);

$total_laporan_baru = 0;
$total_laporan_proses = 0;
$total_laporan_selesai = 0;

foreach ($riwayat_laporan as $rep) {

    switch ($rep['status_laporan']) {

        case 'baru':
            $total_laporan_baru++;
            break;

        case 'diproses':
            $total_laporan_proses++;
            break;

        case 'selesai':
            $total_laporan_selesai++;
            break;
    }
}


// ========================================
// FORMAT TANGGAL
// ========================================

function formatTanggalIndonesia($tanggal)
{
    $bulan = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des'
    ];

    $time = strtotime($tanggal);

    return date('d', $time)
        . ' '
        . $bulan[(int) date('m', $time)]
        . ' '
        . date('Y', $time);
}


// ========================================
// STATUS CLASS
// ========================================

function reservationStatusClass($status)
{
    switch ($status) {

        case 'menunggu':
            return 'pending';

        case 'disetujui':
            return 'approved';

        case 'ditolak':
            return 'rejected';

        case 'dibatalkan':
            return 'cancelled';

        default:
            return 'default';
    }
}


function reportStatusClass($status)
{
    switch ($status) {

        case 'baru':
            return 'pending';

        case 'diproses':
            return 'processing';

        case 'selesai':
            return 'approved';

        case 'ditolak':
            return 'rejected';

        default:
            return 'default';
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
        Dashboard | ReservasiKampus
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

            --bg: #f5f8fc;

            --white: #ffffff;

            --text: #172033;

            --muted: #6b7280;

            --border: #e3e9f0;

            --green: #0d9f6e;
            --green-bg: #ecfdf5;

            --orange: #c56a0a;
            --orange-bg: #fff7ed;

            --red: #c23b3b;
            --red-bg: #fff1f1;

            --gray-bg: #f3f4f6;
        }


        body {

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                var(--bg);

            color:
                var(--text);
        }


        a {
            text-decoration: none;
        }


        button {
            font-family: inherit;
        }


        /* ==================================================
           NAVBAR
        ================================================== */

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

            position: sticky;

            top: 0;

            z-index: 100;
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

            font-size:
                21px;

            font-weight:
                700;

            letter-spacing:
                -0.4px;
        }


        .logo span {
            font-weight: 400;
        }


        .nav-right {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .nav-link {

            color:
                var(--blue-dark);

            font-size:
                12px;

            font-weight:
                700;

            padding:
                9px 12px;
        }


        .logout-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                10px 15px;

            border-radius:
                9px;

            color:
                white;

            background:
                var(--blue-dark);

            font-size:
                12px;

            font-weight:
                700;

            transition: .2s;
        }


        .logout-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* ==================================================
           MAIN
        ================================================== */

        .main {

            width:
                min(
                    1180px,
                    calc(100% - 40px)
                );

            margin:
                0 auto;

            padding:
                36px 0 70px;
        }


        /* ==================================================
           TOP SECTION
        ================================================== */

        .welcome {

            display: flex;

            justify-content: space-between;

            align-items: flex-end;

            gap: 20px;

            margin-bottom:
                24px;
        }


        .welcome-label {

            display:
                inline-block;

            color:
                var(--blue);

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                7px;
        }


        .welcome h1 {

            font-size:
                29px;

            line-height:
                1.2;

            margin-bottom:
                6px;
        }


        .welcome p {

            color:
                var(--muted);

            font-size:
                12px;

            line-height:
                1.6;
        }


        .welcome-user {

            display:
                flex;

            align-items:
                center;

            gap:
                11px;

            padding:
                10px 13px;

            border:
                1px solid
                var(--border);

            background:
                white;

            border-radius:
                11px;
        }


        .avatar {

            width:
                38px;

            height:
                38px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eaf5ff;

            color:
                var(--blue);

            font-size:
                14px;

            font-weight:
                800;
        }


        .user-name {

            font-size:
                12px;

            font-weight:
                700;
        }


        .user-role {

            color:
                var(--muted);

            font-size:
                10px;

            margin-top:
                2px;
        }


        /* ==================================================
           HERO ACTION
        ================================================== */

        .hero-card {

            background:
                linear-gradient(
                    135deg,
                    #073765,
                    #118de4
                );

            border-radius:
                17px;

            padding:
                28px 30px;

            color:
                white;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                20px;

            margin-bottom:
                22px;

            box-shadow:
                0 14px 35px
                rgba(
                    15,
                    65,
                    110,
                    0.12
                );
        }


        .hero-card h2 {

            font-size:
                21px;

            margin-bottom:
                6px;
        }


        .hero-card p {

            max-width:
                560px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.74
                );

            font-size:
                11px;

            line-height:
                1.6;
        }


        .hero-actions {

            display:
                flex;

            gap:
                9px;

            flex-shrink:
                0;
        }


        .hero-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-width:
                125px;

            padding:
                11px 14px;

            border-radius:
                9px;

            background:
                white;

            color:
                var(--blue-dark);

            font-size:
                11px;

            font-weight:
                700;
        }


        .hero-button.secondary {

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.12
                );

            color:
                white;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.22
                );
        }


        /* ==================================================
           STATISTICS
        ================================================== */

        .stats {

            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                14px;

            margin-bottom:
                24px;
        }


        .stat-card {

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                13px;

            padding:
                18px;
        }


        .stat-top {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            margin-bottom:
                12px;
        }


        .stat-label {

            font-size:
                11px;

            color:
                var(--muted);

            font-weight:
                600;
        }




        .stat-number {

            font-size:
                25px;

            font-weight:
                800;

            color:
                var(--text);
        }


        .stat-detail {

            margin-top:
                5px;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ==================================================
           CONTENT GRID
        ================================================== */

        .content-grid {

            display:
                grid;

            grid-template-columns:
                1.6fr
                1fr;

            gap:
                18px;

            align-items:
                start;
        }


        .card {

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                15px;

            overflow:
                hidden;
        }


        .card-header {

            padding:
                18px 20px;

            border-bottom:
                1px solid
                var(--border);

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;
        }


        .card-title {

            font-size:
                15px;

            font-weight:
                700;
        }


        .card-subtitle {

            color:
                var(--muted);

            font-size:
                10px;

            margin-top:
                3px;
        }


        .card-link {

            color:
                var(--blue);

            font-size:
                10px;

            font-weight:
                700;
        }


        /* ==================================================
           RESERVATION LIST
        ================================================== */

        .reservation-item {

            padding:
                16px 20px;

            border-bottom:
                1px solid
                #eef1f4;

            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                space-between;

            gap:
                15px;
        }


        .reservation-item:last-child {
            border-bottom: none;
        }


        .reservation-left {

            display:
                flex;

            gap:
                13px;

            min-width:
                0;
        }


        .room-icon {

            flex-shrink:
                0;

            width:
                42px;

            height:
                42px;

            border-radius:
                10px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eaf5ff;

            color:
                var(--blue);

            font-size:
                14px;

            font-weight:
                800;
        }


        .reservation-name {

            font-size:
                12px;

            font-weight:
                700;

            margin-bottom:
                4px;
        }


        .reservation-meta {

            color:
                var(--muted);

            font-size:
                10px;

            line-height:
                1.7;
        }


        .reservation-purpose {

            margin-top:
                4px;

            color:
                #7b8794;

            font-size:
                10px;
        }


        .reservation-right {

            display:
                flex;

            flex-direction:
                column;

            align-items:
                flex-end;

            gap:
                8px;

            flex-shrink:
                0;
        }


        /* ==================================================
           STATUS
        ================================================== */

        .status {

            display:
                inline-flex;

            align-items:
                center;

            padding:
                5px 9px;

            border-radius:
                999px;

            font-size:
                9px;

            font-weight:
                700;

            text-transform:
                capitalize;

            white-space:
                nowrap;
        }


        .status.pending {

            color:
                var(--orange);

            background:
                var(--orange-bg);
        }


        .status.approved {

            color:
                var(--green);

            background:
                var(--green-bg);
        }


        .status.rejected {

            color:
                var(--red);

            background:
                var(--red-bg);
        }


        .status.cancelled {

            color:
                #6b7280;

            background:
                var(--gray-bg);
        }


        .status.processing {

            color:
                var(--blue);

            background:
                #eef7ff;
        }


        .status.default {

            color:
                #667085;

            background:
                #f3f4f6;
        }


        .cancel-button {

            background:
                transparent;

            border:
                none;

            color:
                var(--red);

            font-size:
                9px;

            font-weight:
                700;

            cursor:
                pointer;

            padding:
                0;
        }


        .cancel-button:hover {

            text-decoration:
                underline;
        }


        /* ==================================================
           EMPTY
        ================================================== */

        .empty {

            padding:
                42px 20px;

            text-align:
                center;

            color:
                var(--muted);

            font-size:
                11px;
        }



        /* ==================================================
           REPORT
        ================================================== */

        .report-item {

            padding:
                15px 18px;

            border-bottom:
                1px solid
                #eef1f4;
        }


        .report-item:last-child {
            border-bottom:
                none;
        }


        .report-top {

            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                space-between;

            gap:
                10px;

            margin-bottom:
                7px;
        }


        .report-name {

            font-size:
                11px;

            font-weight:
                700;
        }


        .report-category {

            color:
                var(--blue);

            font-size:
                9px;

            margin-top:
                2px;
        }


        .report-description {

            color:
                var(--muted);

            font-size:
                10px;

            line-height:
                1.6;

            margin-bottom:
                8px;
        }


        .report-bottom {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                10px;
        }


        .report-photo {

            color:
                var(--blue);

            font-size:
                9px;

            font-weight:
                700;
        }


        .report-photo:hover {

            text-decoration:
                underline;
        }


        /* ==================================================
           QUICK ACTIONS
        ================================================== */

        .quick-actions {

            margin-top:
                18px;

            display:
                grid;

            grid-template-columns:
                1fr 1fr;

            gap:
                12px;
        }


        .quick-action {

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                13px;

            padding:
                17px;

            transition:
                .2s;
        }


        .quick-action:hover {

            border-color:
                #cfdce9;

            transform:
                translateY(
                    -2px
                );

            box-shadow:
                0 10px 25px
                rgba(
                    20,
                    48,
                    83,
                    0.06
                );
        }


        .quick-action-icon {

            width:
                34px;

            height:
                34px;

            border-radius:
                9px;

            background:
                #eef7ff;

            color:
                var(--blue);

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                13px;

            font-weight:
                800;

            margin-bottom:
                12px;
        }


        .quick-action h3 {

            font-size:
                12px;

            margin-bottom:
                4px;
        }


        .quick-action p {

            color:
                var(--muted);

            font-size:
                9px;

            line-height:
                1.5;
        }


        /* ==================================================
           FOOTER
        ================================================== */

        .footer {

            margin-top:
                26px;

            padding-top:
                20px;

            border-top:
                1px solid
                var(--border);

            color:
                var(--muted);

            font-size:
                9px;

            text-align:
                center;
        }


        /* ==================================================
           RESPONSIVE
        ================================================== */

        @media (max-width: 950px) {

            .stats {

                grid-template-columns:
                    repeat(
                        2,
                        1fr
                    );
            }


            .content-grid {

                grid-template-columns:
                    1fr;
            }

        }


        @media (max-width: 700px) {

            .navbar-inner,
            .main {

                width:
                    calc(
                        100% - 26px
                    );
            }


            .nav-link {

                display:
                    none;
            }


            .welcome {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }


            .hero-card {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }


            .hero-actions {

                width:
                    100%;
            }


            .hero-button {

                flex:
                    1;
            }


            .reservation-item {

                flex-direction:
                    column;
            }


            .reservation-right {

                align-items:
                    flex-start;
            }

        }


        @media (max-width: 500px) {

            .stats {

                grid-template-columns:
                    1fr;
            }


            .quick-actions {

                grid-template-columns:
                    1fr;
            }


            .welcome h1 {

                font-size:
                    25px;
            }


            .hero-card {

                padding:
                    23px;
            }


            .hero-actions {

                flex-direction:
                    column;
            }

        }

    </style>

</head>


<body>


<!-- ====================================================
     NAVBAR
===================================================== -->

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
                href="../../app/controllers/process_logout.php"
                class="logout-button"
            >
                Logout
            </a>

        </div>

    </div>

</header>



<!-- ====================================================
     MAIN
===================================================== -->

<main class="main">


    <!-- =================================================
         WELCOME
    ================================================== -->

    <section class="welcome">


        <div>

            <div class="welcome-label">

                DASHBOARD PENGGUNA

            </div>


            <h1>

                Selamat datang,
                <?php
                    echo htmlspecialchars(
                        $nama_user
                    );
                ?>!

            </h1>


            <p>

                Kelola reservasi ruangan
                dan laporan fasilitas kampus
                dari satu tempat.

            </p>

        </div>


        <div class="welcome-user">


            <div class="avatar">

                <?php
                    echo strtoupper(
                        substr(
                            $nama_user,
                            0,
                            1
                        )
                    );
                ?>

            </div>


            <div>

                <div class="user-name">

                    <?php
                        echo htmlspecialchars(
                            $nama_user
                        );
                    ?>

                </div>


                <div class="user-role">

                    Pengguna

                </div>

            </div>


        </div>


    </section>



    <!-- =================================================
         HERO
    ================================================== -->

    <section class="hero-card">


        <div>

            <h2>

                Butuh ruangan untuk kegiatan?

            </h2>


            <p>

                Cari fasilitas yang tersedia,
                lihat jadwal penggunaannya,
                lalu ajukan reservasi sesuai
                kebutuhanmu.

            </p>

        </div>


        <div class="hero-actions">


            <a
                href="../../public/index.php"
                class="hero-button"
            >

                Cari Ruangan

            </a>


            <a
                href="buat_reservasi.php"
                class="hero-button secondary"
            >

                + Reservasi

            </a>

        </div>


    </section>



    <!-- =================================================
         STATISTICS
    ================================================== -->

    <section class="stats">


        <!-- TOTAL RESERVASI -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-label">
                    Total Reservasi
                </div>


            </div>


            <div class="stat-number">

                <?php
                    echo $total_reservasi;
                ?>

            </div>


            <div class="stat-detail">

                Semua reservasi kamu

            </div>

        </div>



        <!-- MENUNGGU -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-label">
                    Menunggu
                </div>

            </div>


            <div class="stat-number">

                <?php
                    echo $total_menunggu;
                ?>

            </div>


            <div class="stat-detail">

                Menunggu persetujuan

            </div>

        </div>



        <!-- DISETUJUI -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-label">
                    Disetujui
                </div>

            </div>


            <div class="stat-number">

                <?php
                    echo $total_disetujui;
                ?>

            </div>


            <div class="stat-detail">

                Reservasi yang disetujui

            </div>

        </div>



        <!-- LAPORAN -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-label">
                    Laporan
                </div>

            </div>


            <div class="stat-number">

                <?php
                    echo $total_laporan;
                ?>

            </div>


            <div class="stat-detail">

                Total laporan kerusakan

            </div>

        </div>


    </section>



    <!-- =================================================
         CONTENT
    ================================================== -->

    <section class="content-grid">


        <!-- =================================================
             RESERVASI
        ================================================== -->

        <div class="card">


            <div class="card-header">

                <div>

                    <div class="card-title">

                        Reservasi Saya

                    </div>


                    <div class="card-subtitle">

                        Riwayat reservasi terbaru

                    </div>

                </div>


                <a
                    href="buat_reservasi.php"
                    class="card-link"
                >

                    + Buat Reservasi

                </a>

            </div>



            <?php if ($total_reservasi > 0): ?>


                <?php
                    $reservasi_tampil =
                        array_slice(
                            $riwayat_reservasi,
                            0,
                            6
                        );
                ?>


                <?php foreach (
                    $reservasi_tampil
                    as $res
                ): ?>


                    <div class="reservation-item">


                        <div class="reservation-left">


                            <div class="room-icon">

                                <?php

                                echo strtoupper(
                                    substr(
                                        $res[
                                            'nama_fasilitas'
                                        ],
                                        0,
                                        1
                                    )
                                );

                                ?>

                            </div>


                            <div>


                                <div
                                    class="reservation-name"
                                >

                                    <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'nama_fasilitas'
                                            ]
                                        );
                                    ?>

                                </div>


                                <div
                                    class="reservation-meta"
                                >

                                    <?php
                                        echo formatTanggalIndonesia(
                                            $res['tanggal']
                                        );
                                    ?>

                                    <br>

                                    <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'start_time'
                                            ]
                                        );
                                    ?>

                                    –

                                    <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'end_time'
                                            ]
                                        );
                                    ?>

                                    <br>

                                    <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'lokasi'
                                            ]
                                        );
                                    ?>

                                </div>


                                <div
                                    class="reservation-purpose"
                                >

                                    Tujuan:

                                    <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'tujuan_penggunaan'
                                            ]
                                        );
                                    ?>

                                </div>


                            </div>

                        </div>



                        <div class="reservation-right">


                            <span
                                class="
                                    status
                                    <?php
                                    echo reservationStatusClass(
                                        $res[
                                            'status_reservasi'
                                        ]
                                    );
                                    ?>
                                "
                            >

                                <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $res[
                                                'status_reservasi'
                                            ]
                                        )
                                    );
                                ?>

                            </span>


                            <?php if (
                                $res[
                                    'status_reservasi'
                                ]
                                ===
                                'menunggu'
                            ): ?>


                                <form
                                    action="../../app/controllers/process_batal_reservasi.php"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?');"
                                >

                                    <input
                                        type="hidden"
                                        name="reservasi_id"
                                        value="<?php
                                            echo $res['id'];
                                        ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="cancel-button"
                                    >

                                        Batalkan

                                    </button>

                                </form>


                            <?php endif; ?>


                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="empty">




                    <div>

                        Belum ada reservasi.

                    </div>


                </div>


            <?php endif; ?>


        </div>



        <!-- =================================================
             LAPORAN
        ================================================== -->

        <div class="card">


            <div class="card-header">

                <div>

                    <div class="card-title">

                        Laporan Kerusakan

                    </div>


                    <div class="card-subtitle">

                        Status laporan fasilitas kamu

                    </div>

                </div>


                <a
                    href="buat_laporan.php"
                    class="card-link"
                >

                    + Lapor

                </a>

            </div>



            <?php if ($total_laporan > 0): ?>


                <?php
                    $laporan_tampil =
                        array_slice(
                            $riwayat_laporan,
                            0,
                            5
                        );
                ?>


                <?php foreach (
                    $laporan_tampil
                    as $rep
                ): ?>


                    <div class="report-item">


                        <div class="report-top">


                            <div>

                                <div
                                    class="report-name"
                                >

                                    <?php
                                        echo htmlspecialchars(
                                            $rep[
                                                'nama_fasilitas'
                                            ]
                                        );
                                    ?>

                                </div>


                                <div
                                    class="report-category"
                                >

                                    <?php
                                        echo htmlspecialchars(
                                            $rep[
                                                'kategori_laporan'
                                            ]
                                        );
                                    ?>

                                </div>

                            </div>


                            <span
                                class="
                                    status
                                    <?php
                                    echo reportStatusClass(
                                        $rep[
                                            'status_laporan'
                                        ]
                                    );
                                    ?>
                                "
                            >

                                <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $rep[
                                                'status_laporan'
                                            ]
                                        )
                                    );
                                ?>

                            </span>


                        </div>


                        <div
                            class="report-description"
                        >

                            <?php
                                echo htmlspecialchars(
                                    $rep[
                                        'deskripsi'
                                    ]
                                );
                            ?>

                        </div>


                        <div
                            class="report-bottom"
                        >


                            <?php if (
                                !empty(
                                    $rep['foto']
                                )
                            ): ?>

                                <a
                                    href="../../public/uploads/<?php
                                        echo htmlspecialchars(
                                            $rep['foto']
                                        );
                                    ?>"
                                    target="_blank"
                                    class="report-photo"
                                >

                                    Lihat foto

                                </a>

                            <?php else: ?>

                                <span
                                    style="
                                        color:#9ca3af;
                                        font-size:9px;
                                    "
                                >

                                    Tanpa foto

                                </span>

                            <?php endif; ?>


                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="empty">



                    <div>

                        Belum ada laporan kerusakan.

                    </div>


                </div>


            <?php endif; ?>


        </div>


    </section>



    <!-- =================================================
         QUICK ACTIONS
    ================================================== -->

    <section class="quick-actions">


        <a
            href="buat_reservasi.php"
            class="quick-action"
        >

            <div class="quick-action-icon">
                R
            </div>


            <h3>
                Ajukan Reservasi
            </h3>


            <p>
                Booking ruangan atau fasilitas
                untuk kegiatan kampus.
            </p>

        </a>



        <a
            href="buat_laporan.php"
            class="quick-action"
        >

            <div class="quick-action-icon">
                !
            </div>


            <h3>
                Laporkan Kerusakan
            </h3>


            <p>
                Laporkan fasilitas kampus
                yang mengalami masalah.
            </p>

        </a>


    </section>



    <!-- =================================================
         FOOTER
    ================================================== -->

    <div class="footer">

        ReservasiKampus · Sistem Reservasi
        Fasilitas Kampus

    </div>


</main>


</body>

</html>