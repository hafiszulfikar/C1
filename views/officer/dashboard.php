<?php
// views/officer/dashboard.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'petugas'
) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';


// ==================================================
// 1. ANTRIAN RESERVASI
// ==================================================

$sql_menunggu = "
    SELECT
        r.*,
        f.nama_fasilitas,
        f.lokasi,
        u.nama AS nama_pemesan
    FROM reservations r
    JOIN facilities f
        ON r.facility_id = f.id
    JOIN users u
        ON r.user_id = u.id
    WHERE r.status_reservasi = 'menunggu'
    ORDER BY
        r.tanggal ASC,
        r.start_time ASC
";

$antrian_reservasi =
    $pdo->query($sql_menunggu)->fetchAll();


// ==================================================
// 2. RESERVASI DISETUJUI
// ==================================================

$sql_disetujui = "
    SELECT
        r.*,
        f.nama_fasilitas,
        f.lokasi,
        u.nama AS nama_pemesan
    FROM reservations r
    JOIN facilities f
        ON r.facility_id = f.id
    JOIN users u
        ON r.user_id = u.id
    WHERE r.status_reservasi = 'disetujui'
    ORDER BY
        r.tanggal ASC,
        r.start_time ASC
";

$reservasi_disetujui =
    $pdo->query($sql_disetujui)->fetchAll();


// ==================================================
// 3. ANTRIAN LAPORAN
// ==================================================

$sql_laporan = "
    SELECT
        rep.*,
        f.nama_fasilitas,
        f.lokasi,
        u.nama AS nama_pelapor
    FROM reports rep
    JOIN facilities f
        ON rep.facility_id = f.id
    JOIN users u
        ON rep.user_id = u.id
    WHERE rep.status_laporan IN ('baru', 'diproses')
    ORDER BY rep.created_at ASC
";

$antrian_laporan =
    $pdo->query($sql_laporan)->fetchAll();


// ==================================================
// STATISTIK
// ==================================================

$total_menunggu =
    count($antrian_reservasi);

$total_disetujui =
    count($reservasi_disetujui);

$total_laporan =
    count($antrian_laporan);

$total_laporan_baru = 0;

$total_laporan_diproses = 0;


foreach ($antrian_laporan as $laporan) {

    if (
        $laporan['status_laporan']
        === 'baru'
    ) {
        $total_laporan_baru++;
    }

    if (
        $laporan['status_laporan']
        === 'diproses'
    ) {
        $total_laporan_diproses++;
    }
}


// ==================================================
// FORMAT TANGGAL
// ==================================================

function formatTanggal($tanggal)
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

    $timestamp =
        strtotime($tanggal);

    return date('d', $timestamp)
        . ' '
        . $bulan[(int) date('m', $timestamp)]
        . ' '
        . date('Y', $timestamp);
}


// ==================================================
// STATUS CLASS
// ==================================================

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
            return 'new';

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
        Dashboard Petugas | ReservasiKampus
    </title>


    <style>

        /* ==================================================
           RESET
        ================================================== */

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


        input,
        select,
        textarea,
        button {
            font-family: inherit;
        }


        button {
            cursor: pointer;
        }


        /* ==================================================
           NAVBAR
        ================================================== */

        .navbar {

            height: 72px;

            display: flex;

            align-items: center;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.97
                );

            border-bottom:
                1px solid
                var(--border);

            position:
                sticky;

            top: 0;

            z-index: 100;
        }


        .navbar-inner {

            width:
                min(
                    1180px,
                    calc(100% - 40px)
                );

            margin:
                auto;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;
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

            font-weight:
                400;
        }


        .nav-right {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;
        }


        .nav-link {

            color:
                var(--blue-dark);

            padding:
                9px 12px;

            font-size:
                12px;

            font-weight:
                700;
        }


        .logout {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                10px 15px;

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


        .logout:hover {

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
                auto;

            padding:
                36px 0 70px;
        }


        /* ==================================================
           WELCOME
        ================================================== */

        .welcome {

            display:
                flex;

            align-items:
                flex-end;

            justify-content:
                space-between;

            gap:
                20px;

            margin-bottom:
                24px;
        }


        .welcome-label {

            color:
                var(--blue);

            font-size:
                10px;

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


        .officer-user {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

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

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                #eaf5ff;

            color:
                var(--blue);

            font-size:
                14px;

            font-weight:
                800;
        }


        .officer-name {

            font-size:
                11px;

            font-weight:
                700;
        }


        .officer-role {

            margin-top:
                2px;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ==================================================
           HERO
        ================================================== */

        .hero {

            margin-bottom:
                20px;

            padding:
                28px 30px;

            border-radius:
                17px;

            background:
                linear-gradient(
                    135deg,
                    #073765,
                    #118de4
                );

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

            box-shadow:
                0 14px 35px
                rgba(
                    15,
                    65,
                    110,
                    0.12
                );
        }


        .hero h2 {

            font-size:
                21px;

            margin-bottom:
                6px;
        }


        .hero p {

            max-width:
                680px;

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


        .hero-badge {

            flex-shrink:
                0;

            padding:
                10px 13px;

            border-radius:
                10px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.11
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.15
                );

            font-size:
                10px;

            font-weight:
                700;
        }


        /* ==================================================
           STATS
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
                22px;
        }


        .stat-card {

            padding:
                18px;

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                13px;
        }


        .stat-label {

            color:
                var(--muted);

            font-size:
                10px;

            font-weight:
                600;

            margin-bottom:
                10px;
        }


        .stat-number {

            color:
                var(--text);

            font-size:
                25px;

            font-weight:
                800;
        }


        .stat-description {

            margin-top:
                4px;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ==================================================
           CARD
        ================================================== */

        .card {

            overflow:
                hidden;

            margin-bottom:
                20px;

            border:
                1px solid
                var(--border);

            border-radius:
                15px;

            background:
                white;
        }


        .card-header {

            padding:
                19px 20px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;

            border-bottom:
                1px solid
                var(--border);
        }


        .card-title {

            font-size:
                15px;

            font-weight:
                700;
        }


        .card-subtitle {

            margin-top:
                3px;

            color:
                var(--muted);

            font-size:
                10px;
        }


        .count-badge {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-width:
                27px;

            height:
                27px;

            padding:
                0 8px;

            border-radius:
                999px;

            background:
                #eef7ff;

            color:
                var(--blue);

            font-size:
                10px;

            font-weight:
                800;
        }


        /* ==================================================
           TABLE
        ================================================== */

        .table-wrapper {

            width:
                100%;

            overflow-x:
                auto;
        }


        table {

            width:
                100%;

            border-collapse:
                collapse;
        }


        th {

            padding:
                13px 17px;

            background:
                #f8fafc;

            border-bottom:
                1px solid
                var(--border);

            color:
                #657181;

            font-size:
                9px;

            font-weight:
                700;

            text-align:
                left;

            white-space:
                nowrap;
        }


        td {

            padding:
                15px 17px;

            border-bottom:
                1px solid
                #eef1f4;

            color:
                #475467;

            font-size:
                10px;

            vertical-align:
                middle;
        }


        tr:last-child td {

            border-bottom:
                none;
        }


        /* ==================================================
           RESERVATION
        ================================================== */

        .booking-person {

            color:
                var(--text);

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                3px;
        }


        .booking-email {

            color:
                var(--muted);

            font-size:
                9px;
        }


        .facility-name {

            color:
                var(--text);

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                3px;
        }


        .facility-location {

            color:
                var(--muted);

            font-size:
                9px;
        }


        .date-main {

            color:
                var(--text);

            font-weight:
                700;

            margin-bottom:
                3px;
        }


        .date-time {

            color:
                var(--muted);

            font-size:
                9px;
        }


        .purpose {

            max-width:
                270px;

            color:
                #596579;

            line-height:
                1.6;
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

            white-space:
                nowrap;
        }


        .status.pending {

            background:
                var(--orange-bg);

            color:
                var(--orange);
        }


        .status.approved {

            background:
                var(--green-bg);

            color:
                var(--green);
        }


        .status.rejected {

            background:
                var(--red-bg);

            color:
                var(--red);
        }


        .status.cancelled {

            background:
                var(--gray-bg);

            color:
                #667085;
        }


        .status.new {

            background:
                var(--orange-bg);

            color:
                var(--orange);
        }


        .status.processing {

            background:
                #eef7ff;

            color:
                var(--blue);
        }


        .status.default {

            background:
                var(--gray-bg);

            color:
                #667085;
        }


        /* ==================================================
           APPROVE / REJECT
        ================================================== */

        .decision-group {

            display:
                flex;

            gap:
                7px;
        }


        .decision-form {

            margin:
                0;
        }


        .decision-button {

            height:
                32px;

            padding:
                0 11px;

            border:
                1px solid;

            border-radius:
                7px;

            font-size:
                9px;

            font-weight:
                700;

            transition:
                .2s;
        }


        .approve-button {

            color:
                var(--green);

            background:
                var(--green-bg);

            border-color:
                #c9ebdc;
        }


        .approve-button:hover {

            background:
                #ddf8eb;
        }


        .reject-button {

            color:
                var(--red);

            background:
                var(--red-bg);

            border-color:
                #f0d0d0;
        }


        .reject-button:hover {

            background:
                #ffe6e6;
        }


        /* ==================================================
           EMERGENCY CANCELLATION
        ================================================== */

        .urgent-form {

            min-width:
                235px;
        }


        .urgent-input {

            width:
                100%;

            height:
                35px;

            padding:
                0 10px;

            margin-bottom:
                7px;

            border:
                1px solid
                #d6dee8;

            border-radius:
                8px;

            background:
                white;

            color:
                var(--text);

            outline:
                none;

            font-size:
                9px;
        }


        .urgent-input:focus {

            border-color:
                var(--red);

            box-shadow:
                0 0 0 3px
                rgba(
                    194,
                    59,
                    59,
                    0.07
                );
        }


        .urgent-actions {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                10px;
        }


        .urgent-note {

            color:
                #98a2b3;

            font-size:
                8px;

            line-height:
                1.4;
        }


        .urgent-button {

            height:
                31px;

            padding:
                0 10px;

            border:
                1px solid
                #f0d0d0;

            border-radius:
                7px;

            background:
                var(--red-bg);

            color:
                var(--red);

            font-size:
                9px;

            font-weight:
                700;

            white-space:
                nowrap;

            transition:
                .2s;
        }


        .urgent-button:hover {

            background:
                #ffe6e6;
        }


        /* ==================================================
           REPORT
        ================================================== */

        .report-info {

            max-width:
                290px;
        }


        .report-category {

            display:
                inline-block;

            margin-bottom:
                5px;

            color:
                var(--blue);

            font-size:
                9px;

            font-weight:
                700;
        }


        .report-description {

            color:
                #596579;

            font-size:
                10px;

            line-height:
                1.6;
        }


        .report-description strong {

            color:
                var(--text);
        }


        .report-photo {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                7px 10px;

            border-radius:
                7px;

            background:
                #eef7ff;

            color:
                var(--blue);

            font-size:
                9px;

            font-weight:
                700;

            white-space:
                nowrap;
        }


        .report-photo:hover {

            background:
                #dff0ff;
        }


        .no-photo {

            color:
                #98a2b3;

            font-size:
                9px;
        }


        /* ==================================================
           REPORT PROCESS
        ================================================== */

        .report-process {

            min-width:
                240px;
        }


        .process-form {

            width:
                100%;
        }


        .process-select {

            width:
                100%;

            height:
                34px;

            padding:
                0 9px;

            margin-bottom:
                7px;

            border:
                1px solid
                #d6dee8;

            border-radius:
                8px;

            background:
                white;

            color:
                var(--text);

            font-size:
                9px;

            outline:
                none;
        }


        .process-select:focus {

            border-color:
                var(--blue);

            box-shadow:
                0 0 0 3px
                rgba(
                    8,
                    121,
                    209,
                    0.07
                );
        }


        .process-textarea {

            width:
                100%;

            min-height:
                65px;

            padding:
                9px;

            margin-bottom:
                8px;

            resize:
                vertical;

            border:
                1px solid
                #d6dee8;

            border-radius:
                8px;

            background:
                white;

            color:
                var(--text);

            outline:
                none;

            font-size:
                9px;

            line-height:
                1.5;
        }


        .process-textarea:focus {

            border-color:
                var(--blue);

            box-shadow:
                0 0 0 3px
                rgba(
                    8,
                    121,
                    209,
                    0.07
                );
        }


        .process-textarea::placeholder {

            color:
                #9aa5b1;
        }


        .repair-check {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                7px;

            margin-bottom:
                9px;

            color:
                #667085;

            font-size:
                9px;

            line-height:
                1.4;
        }


        .repair-check input {

            margin-top:
                1px;

            accent-color:
                var(--blue);
        }


        .process-button {

            height:
                32px;

            width:
                100%;

            border:
                none;

            border-radius:
                7px;

            background:
                var(--blue-dark);

            color:
                white;

            font-size:
                9px;

            font-weight:
                700;

            transition:
                .2s;
        }


        .process-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* ==================================================
           EMPTY STATE
        ================================================== */

        .empty {

            padding:
                45px 20px;

            text-align:
                center;
        }


        .empty-title {

            color:
                var(--text);

            font-size:
                13px;

            font-weight:
                700;

            margin-bottom:
                5px;
        }


        .empty-text {

            color:
                var(--muted);

            font-size:
                10px;

            line-height:
                1.5;
        }


        /* ==================================================
           FOOTER
        ================================================== */

        .footer {

            margin-top:
                25px;

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
                        minmax(
                            0,
                            1fr
                        )
                    );
            }


            .hero {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }


            .hero-badge {

                align-self:
                    flex-start;
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


            .card-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }

        }


        @media (max-width: 500px) {

            .stats {

                grid-template-columns:
                    1fr;
            }


            .hero {

                padding:
                    23px;
            }


            .hero-badge {

                width:
                    100%;

                text-align:
                    center;
            }

        }

    </style>

</head>


<body>


<!-- ==================================================
     NAVBAR
================================================== -->

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
                class="logout"
            >
                Logout
            </a>


        </div>


    </div>

</header>



<!-- ==================================================
     MAIN
================================================== -->

<main class="main">


    <!-- =================================================
         WELCOME
    ================================================== -->

    <section class="welcome">


        <div>


            <div class="welcome-label">
                DASHBOARD PETUGAS
            </div>


            <h1>

                Selamat datang,
                <?php
                echo htmlspecialchars(
                    $_SESSION['nama']
                );
                ?>!

            </h1>


            <p>

                Kelola persetujuan reservasi
                dan tindak lanjuti laporan
                kerusakan fasilitas kampus.

            </p>


        </div>



        <div class="officer-user">


            <div class="avatar">

                <?php

                echo strtoupper(
                    substr(
                        $_SESSION['nama'],
                        0,
                        1
                    )
                );

                ?>

            </div>


            <div>

                <div class="officer-name">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION['nama']
                    );
                    ?>

                </div>


                <div class="officer-role">
                    Petugas
                </div>

            </div>


        </div>


    </section>



    <!-- =================================================
         HERO
    ================================================== -->

    <section class="hero">


        <div>


            <h2>
                Pusat Pengelolaan Fasilitas
            </h2>


            <p>

                Periksa pengajuan reservasi,
                kelola jadwal yang telah disetujui,
                dan tangani laporan kerusakan
                dari pengguna.

            </p>


        </div>


        <div class="hero-badge">

            <?= $total_menunggu ?>
            reservasi menunggu

        </div>


    </section>



    <!-- =================================================
         STATISTICS
    ================================================== -->

    <section class="stats">


        <div class="stat-card">


            <div class="stat-label">
                Reservasi Menunggu
            </div>


            <div class="stat-number">

                <?= $total_menunggu ?>

            </div>


            <div class="stat-description">

                Perlu diproses

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-label">
                Reservasi Disetujui
            </div>


            <div class="stat-number">

                <?= $total_disetujui ?>

            </div>


            <div class="stat-description">

                Jadwal aktif

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-label">
                Laporan Baru
            </div>


            <div class="stat-number">

                <?= $total_laporan_baru ?>

            </div>


            <div class="stat-description">

                Belum mulai diproses

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-label">
                Laporan Diproses
            </div>


            <div class="stat-number">

                <?= $total_laporan_diproses ?>

            </div>


            <div class="stat-description">

                Sedang ditangani

            </div>


        </div>


    </section>



    <!-- =================================================
         ANTRIAN RESERVASI
    ================================================== -->

    <section class="card">


        <div class="card-header">


            <div>


                <div class="card-title">

                    Antrian Reservasi

                </div>


                <div class="card-subtitle">

                    Pengajuan yang menunggu
                    persetujuan petugas.

                </div>


            </div>


            <div class="count-badge">

                <?= $total_menunggu ?>

            </div>


        </div>



        <?php if (
            $total_menunggu > 0
        ): ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Pemesan
                            </th>

                            <th>
                                Fasilitas
                            </th>

                            <th>
                                Tanggal & Waktu
                            </th>

                            <th>
                                Tujuan
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $antrian_reservasi
                            as $res
                        ): ?>


                            <tr>


                                <!-- PEMESAN -->

                                <td>


                                    <div
                                        class="booking-person"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'nama_pemesan'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- FASILITAS -->

                                <td>


                                    <div
                                        class="facility-name"
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
                                        class="facility-location"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'lokasi'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- TANGGAL -->

                                <td>


                                    <div
                                        class="date-main"
                                    >

                                        <?php
                                        echo formatTanggal(
                                            $res[
                                                'tanggal'
                                            ]
                                        );
                                        ?>

                                    </div>


                                    <div
                                        class="date-time"
                                    >

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

                                    </div>


                                </td>



                                <!-- TUJUAN -->

                                <td>


                                    <div
                                        class="purpose"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'tujuan_penggunaan'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <div
                                        class="decision-group"
                                    >


                                        <!-- SETUJUI -->

                                        <form
                                            action="../../app/controllers/process_status_reservasi.php"
                                            method="POST"
                                            class="decision-form"
                                            onsubmit="return confirm('Setujui reservasi ini?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="reservasi_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res['id']
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="facility_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'facility_id'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="tanggal"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'tanggal'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="start_time"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'start_time'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="end_time"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'end_time'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <button
                                                type="submit"
                                                name="action"
                                                value="approve"
                                                class="
                                                    decision-button
                                                    approve-button
                                                "
                                            >

                                                Setujui

                                            </button>


                                        </form>



                                        <!-- TOLAK -->

                                        <form
                                            action="../../app/controllers/process_status_reservasi.php"
                                            method="POST"
                                            class="decision-form"
                                            onsubmit="return confirm('Tolak reservasi ini?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="reservasi_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res['id']
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="facility_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'facility_id'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="tanggal"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'tanggal'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="start_time"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'start_time'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="end_time"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $res[
                                                        'end_time'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <button
                                                type="submit"
                                                name="action"
                                                value="reject"
                                                class="
                                                    decision-button
                                                    reject-button
                                                "
                                            >

                                                Tolak

                                            </button>


                                        </form>


                                    </div>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <div class="empty">


                <div class="empty-title">

                    Tidak ada antrian reservasi

                </div>


                <div class="empty-text">

                    Saat ini semua pengajuan reservasi
                    sudah diproses.

                </div>


            </div>


        <?php endif; ?>


    </section>



    <!-- =================================================
         RESERVASI DISETUJUI
    ================================================== -->

    <section class="card">


        <div class="card-header">


            <div>


                <div class="card-title">

                    Jadwal Reservasi Disetujui

                </div>


                <div class="card-subtitle">

                    Reservasi aktif yang dapat
                    dibatalkan secara mendesak.

                </div>


            </div>


            <div class="count-badge">

                <?= $total_disetujui ?>

            </div>


        </div>



        <?php if (
            $total_disetujui > 0
        ): ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Pemesan
                            </th>

                            <th>
                                Fasilitas
                            </th>

                            <th>
                                Tanggal & Waktu
                            </th>

                            <th>
                                Pembatalan Mendesak
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $reservasi_disetujui
                            as $res
                        ): ?>


                            <tr>


                                <!-- PEMESAN -->

                                <td>


                                    <div
                                        class="booking-person"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'nama_pemesan'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- FASILITAS -->

                                <td>


                                    <div
                                        class="facility-name"
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
                                        class="facility-location"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $res[
                                                'lokasi'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- JADWAL -->

                                <td>


                                    <div
                                        class="date-main"
                                    >

                                        <?php
                                        echo formatTanggal(
                                            $res[
                                                'tanggal'
                                            ]
                                        );
                                        ?>

                                    </div>


                                    <div
                                        class="date-time"
                                    >

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

                                    </div>


                                </td>



                                <!-- BATAL MENDESAK -->

                                <td>


                                    <form
                                        action="../../app/controllers/process_batal_mendesak.php"
                                        method="POST"
                                        class="urgent-form"
                                        onsubmit="return confirm('Yakin ingin membatalkan jadwal ini secara mendesak?');"
                                    >


                                        <input
                                            type="hidden"
                                            name="reservasi_id"
                                            value="<?php
                                            echo htmlspecialchars(
                                                $res['id']
                                            );
                                            ?>"
                                        >


                                        <input
                                            type="text"
                                            name="alasan"
                                            class="urgent-input"
                                            placeholder="Tuliskan alasan pembatalan..."
                                            required
                                        >


                                        <div
                                            class="urgent-actions"
                                        >


                                            <div
                                                class="urgent-note"
                                            >

                                                Gunakan hanya jika
                                                diperlukan.

                                            </div>


                                            <button
                                                type="submit"
                                                class="urgent-button"
                                            >

                                                Batalkan

                                            </button>


                                        </div>


                                    </form>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <div class="empty">


                <div class="empty-title">

                    Belum ada reservasi disetujui

                </div>


                <div class="empty-text">

                    Jadwal reservasi yang telah disetujui
                    akan muncul di sini.

                </div>


            </div>


        <?php endif; ?>


    </section>



    <!-- =================================================
         LAPORAN KERUSAKAN
    ================================================== -->

    <section class="card">


        <div class="card-header">


            <div>


                <div class="card-title">

                    Antrian Laporan Kerusakan

                </div>


                <div class="card-subtitle">

                    Tangani laporan baru dan laporan
                    yang sedang diproses.

                </div>


            </div>


            <div class="count-badge">

                <?= $total_laporan ?>

            </div>


        </div>



        <?php if (
            $total_laporan > 0
        ): ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Pelapor
                            </th>

                            <th>
                                Fasilitas
                            </th>

                            <th>
                                Masalah
                            </th>

                            <th>
                                Foto
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Proses Laporan
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $antrian_laporan
                            as $rep
                        ): ?>


                            <tr>


                                <!-- PELAPOR -->

                                <td>


                                    <div
                                        class="booking-person"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $rep[
                                                'nama_pelapor'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- FASILITAS -->

                                <td>


                                    <div
                                        class="facility-name"
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
                                        class="facility-location"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $rep[
                                                'lokasi'
                                            ]
                                        );
                                        ?>

                                    </div>


                                </td>



                                <!-- MASALAH -->

                                <td>


                                    <div
                                        class="report-info"
                                    >


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


                                    </div>


                                </td>



                                <!-- FOTO -->

                                <td>


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

                                            Lihat Foto

                                        </a>


                                    <?php else: ?>


                                        <span
                                            class="no-photo"
                                        >

                                            Tidak ada foto

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- STATUS -->

                                <td>


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


                                </td>



                                <!-- PROCESS -->

                                <td>


                                    <div
                                        class="report-process"
                                    >


                                        <form
                                            action="../../app/controllers/process_status_laporan.php"
                                            method="POST"
                                            class="process-form"
                                            novalidate
                                        >


                                            <input
                                                type="hidden"
                                                name="laporan_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $rep['id']
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="facility_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $rep[
                                                        'facility_id'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <select
                                                name="status_baru"
                                                class="process-select"
                                                required
                                            >


                                                <option
                                                    value="diproses"
                                                    <?php
                                                    echo
                                                    $rep[
                                                        'status_laporan'
                                                    ]
                                                    ===
                                                    'diproses'
                                                        ? 'selected'
                                                        : '';
                                                    ?>
                                                >
                                                    Diproses
                                                </option>


                                                <option
                                                    value="selesai"
                                                >
                                                    Selesai
                                                </option>


                                                <option
                                                    value="ditolak"
                                                >
                                                    Ditolak
                                                </option>


                                            </select>



                                            <textarea
                                                name="catatan_resolusi"
                                                class="process-textarea"
                                                placeholder="Catatan resolusi..."
                                                required
                                            ></textarea>



                                            <label
                                                class="repair-check"
                                            >


                                                <input
                                                    type="checkbox"
                                                    name="tandai_perbaikan"
                                                    value="ya"
                                                >


                                                <span>

                                                    Tandai fasilitas
                                                    sebagai
                                                    <strong>
                                                        "Dalam Perbaikan"
                                                    </strong>

                                                </span>


                                            </label>



                                            <button
                                                type="submit"
                                                class="process-button"
                                            >

                                                Update Status

                                            </button>


                                        </form>


                                    </div>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <div class="empty">


                <div class="empty-title">

                    Tidak ada laporan kerusakan

                </div>


                <div class="empty-text">

                    Saat ini tidak ada laporan baru
                    atau laporan yang sedang diproses.

                </div>


            </div>


        <?php endif; ?>


    </section>



    <!-- =================================================
         FOOTER
    ================================================== -->

    <div class="footer">

        ReservasiKampus · Dashboard Petugas

    </div>


</main>

<script>

    const reportForms =
        document.querySelectorAll(
            '.process-form'
        );


    reportForms.forEach(function (form) {

        const status =
            form.querySelector(
                'select[name="status_baru"]'
            );

        const catatan =
            form.querySelector(
                'textarea[name="catatan_resolusi"]'
            );


        // ==================================
        // HAPUS ERROR SAAT DIUBAH
        // ==================================

        status.addEventListener(
            'change',
            function () {

                status.setCustomValidity('');

            }
        );


        catatan.addEventListener(
            'input',
            function () {

                catatan.setCustomValidity('');

            }
        );


        // ==================================
        // SUBMIT
        // ==================================

        form.addEventListener(
            'submit',
            function (event) {

                status.setCustomValidity('');
                catatan.setCustomValidity('');


                // ------------------------------
                // STATUS
                // ------------------------------

                if (!status.value) {

                    event.preventDefault();

                    status.setCustomValidity(
                        'Silakan pilih status laporan.'
                    );

                    status.reportValidity();

                    return;
                }


                // ------------------------------
                // CATATAN
                // ------------------------------

                if (!catatan.value.trim()) {

                    event.preventDefault();

                    catatan.setCustomValidity(
                        'Silakan isi catatan resolusi.'
                    );

                    catatan.reportValidity();

                    return;
                }


                // ------------------------------
                // VALID
                // ------------------------------

                status.setCustomValidity('');
                catatan.setCustomValidity('');

            }
        );

    });

</script>


</body>

</html>