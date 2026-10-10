<?php
// views/admin/rekap.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';


// ========================================
// QUERY REKAP
// ========================================

$sql = "
    SELECT
        f.id,
        f.nama_fasilitas,
        f.lokasi,

        (
            SELECT COUNT(*)
            FROM reservations r
            WHERE r.facility_id = f.id
              AND r.status_reservasi = 'disetujui'
        ) AS total_reservasi,

        (
            SELECT COUNT(*)
            FROM reports rep
            WHERE rep.facility_id = f.id
        ) AS total_laporan

    FROM facilities f

    ORDER BY f.nama_fasilitas ASC
";

$stmt = $pdo->query($sql);

$rekap_data = $stmt->fetchAll();


// ========================================
// STATISTIK REKAP
// ========================================

$total_fasilitas = count($rekap_data);

$total_reservasi = 0;

$total_laporan = 0;

foreach ($rekap_data as $row) {

    $total_reservasi +=
        (int) $row['total_reservasi'];

    $total_laporan +=
        (int) $row['total_laporan'];
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
        Rekapitulasi | ReservasiKampus
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

            --red: #c23b3b;
            --red-bg: #fff1f1;
        }


        body {

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color:
                var(--text);

            background:
                var(--bg);
        }


        a {
            text-decoration: none;
        }


        /* ========================================
           NAVBAR
        ======================================== */

        .navbar {

            height: 72px;

            display:
                flex;

            align-items:
                center;

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

            top:
                0;

            z-index:
                100;
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


        /* ========================================
           MAIN
        ======================================== */

        .main {

            width:
                min(
                    1180px,
                    calc(100% - 40px)
                );

            margin:
                auto;

            padding:
                38px 0 70px;
        }


        /* ========================================
           PAGE HEADER
        ======================================== */

        .page-header {

            display:
                flex;

            align-items:
                flex-end;

            justify-content:
                space-between;

            gap:
                20px;

            margin-bottom:
                22px;
        }


        .breadcrumb {

            display:
                inline-flex;

            color:
                var(--blue);

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                10px;
        }


        .page-header h1 {

            font-size:
                29px;

            line-height:
                1.2;

            margin-bottom:
                7px;
        }


        .page-header p {

            color:
                var(--muted);

            font-size:
                12px;

            line-height:
                1.6;
        }


        /* ========================================
           EXPORT BUTTON
        ======================================== */

        .export-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            height:
                42px;

            padding:
                0 17px;

            border-radius:
                9px;

            background:
                var(--blue-dark);

            color:
                white;

            font-size:
                11px;

            font-weight:
                700;

            transition:
                .2s;

            white-space:
                nowrap;
        }


        .export-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* ========================================
           SUMMARY
        ======================================== */

        .summary-grid {

            display:
                grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                14px;

            margin-bottom:
                20px;
        }


        .summary-card {

            padding:
                19px;

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                13px;
        }


        .summary-label {

            color:
                var(--muted);

            font-size:
                10px;

            margin-bottom:
                9px;

            font-weight:
                600;
        }


        .summary-number {

            font-size:
                25px;

            font-weight:
                800;

            color:
                var(--text);
        }


        .summary-description {

            margin-top:
                4px;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ========================================
           REKAP CARD
        ======================================== */

        .rekap-card {

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                16px;

            overflow:
                hidden;

            box-shadow:
                0 12px 30px
                rgba(
                    25,
                    56,
                    88,
                    0.05
                );
        }


        .rekap-header {

            padding:
                20px;

            border-bottom:
                1px solid
                var(--border);
        }


        .rekap-title {

            font-size:
                16px;

            font-weight:
                700;

            margin-bottom:
                4px;
        }


        .rekap-subtitle {

            color:
                var(--muted);

            font-size:
                10px;

            line-height:
                1.5;
        }


        /* ========================================
           TABLE
        ======================================== */

        .table-wrapper {

            width:
                100%;

            overflow-x:
                auto;
        }


        .rekap-table {

            width:
                100%;

            border-collapse:
                collapse;

            min-width:
                700px;
        }


        .rekap-table th {

            padding:
                14px 18px;

            background:
                #f8fafc;

            color:
                #657181;

            font-size:
                9px;

            font-weight:
                700;

            text-align:
                left;

            border-bottom:
                1px solid
                var(--border);
        }


        .rekap-table td {

            padding:
                16px 18px;

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


        .rekap-table tr:last-child td {

            border-bottom:
                none;
        }


        .facility-name {

            color:
                var(--text);

            font-weight:
                700;

            margin-bottom:
                3px;
        }


        .facility-id {

            color:
                #98a2b3;

            font-size:
                8px;
        }


        .location {

            color:
                var(--muted);
        }


        .metric {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;
        }


        .metric-number {

            font-size:
                17px;

            font-weight:
                800;

            color:
                var(--text);
        }


        .metric-label {

            color:
                var(--muted);

            font-size:
                9px;
        }


        .metric.reservation
        .metric-number {

            color:
                var(--blue);
        }


        .metric-report
        .metric-number {

            color:
                var(--red);
        }


        /* ========================================
           EMPTY
        ======================================== */

        .empty {

            padding:
                50px 20px;

            text-align:
                center;
        }


        .empty h3 {

            color:
                var(--text);

            font-size:
                14px;

            margin-bottom:
                6px;
        }


        .empty p {

            color:
                var(--muted);

            font-size:
                10px;
        }


        /* ========================================
           NOTE
        ======================================== */

        .note {

            margin-top:
                17px;

            padding:
                13px 15px;

            background:
                #f5f9fd;

            border:
                1px solid
                #dceaf5;

            border-radius:
                10px;

            color:
                #667085;

            font-size:
                9px;

            line-height:
                1.6;
        }


        .note strong {

            color:
                var(--blue-dark);
        }


        /* ========================================
           FOOTER
        ======================================== */

        .footer {

            margin-top:
                25px;

            padding-top:
                20px;

            border-top:
                1px solid
                var(--border);

            text-align:
                center;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 800px) {

            .page-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }


            .export-button {

                width:
                    100%;
            }


            .summary-grid {

                grid-template-columns:
                    1fr;
            }

        }


        @media (max-width: 600px) {

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

        }

    </style>

</head>


<body>


<!-- ==========================================
     NAVBAR
=========================================== -->

<header class="navbar">

    <div class="navbar-inner">


        <a
            href="dashboard.php"
            class="logo"
        >
            Reservasi<span>Kampus</span>
        </a>


        <div class="nav-right">


            <a
                href="dashboard.php"
                class="nav-link"
            >
                Dashboard
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



<!-- ==========================================
     MAIN
=========================================== -->

<main class="main">


    <!-- HEADER -->

    <section class="page-header">


        <div>

            <a
                href="dashboard.php"
                class="breadcrumb"
            >
                ← Kembali ke Dashboard
            </a>


            <h1>
                Rekapitulasi Fasilitas
            </h1>


            <p>

                Pantau penggunaan fasilitas
                dan jumlah laporan kerusakan
                yang tercatat dalam sistem.

            </p>

        </div>


        <a
            href="../../app/controllers/export_csv.php"
            class="export-button"
        >

            Download Ekspor CSV

        </a>


    </section>



    <!-- SUMMARY -->

    <section class="summary-grid">


        <div class="summary-card">

            <div class="summary-label">
                Total Fasilitas
            </div>


            <div class="summary-number">

                <?php
                echo $total_fasilitas;
                ?>

            </div>


            <div class="summary-description">
                Fasilitas yang tercatat
            </div>

        </div>



        <div class="summary-card">

            <div class="summary-label">
                Total Reservasi Disetujui
            </div>


            <div class="summary-number">

                <?php
                echo $total_reservasi;
                ?>

            </div>


            <div class="summary-description">
                Seluruh reservasi yang disetujui
            </div>

        </div>



        <div class="summary-card">

            <div class="summary-label">
                Total Laporan Kerusakan
            </div>


            <div class="summary-number">

                <?php
                echo $total_laporan;
                ?>

            </div>


            <div class="summary-description">
                Semua laporan yang tercatat
            </div>

        </div>


    </section>



    <!-- REKAP TABLE -->

    <section class="rekap-card">


        <div class="rekap-header">

            <div class="rekap-title">
                Rekap Okupansi & Kerusakan
            </div>


            <div class="rekap-subtitle">

                Data dihitung berdasarkan
                reservasi berstatus disetujui
                dan seluruh laporan kerusakan.

            </div>

        </div>



        <?php if (
            count($rekap_data) > 0
        ): ?>


            <div class="table-wrapper">


                <table class="rekap-table">


                    <thead>

                        <tr>

                            <th>
                                Fasilitas
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th>
                                Reservasi Disetujui
                            </th>

                            <th>
                                Laporan Kerusakan
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $rekap_data
                            as $row
                        ): ?>


                            <tr>


                                <td>


                                    <div
                                        class="facility-name"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $row[
                                                'nama_fasilitas'
                                            ]
                                        );
                                        ?>

                                    </div>


                                    <div
                                        class="facility-id"
                                    >

                                        ID fasilitas:
                                        <?php
                                        echo htmlspecialchars(
                                            $row['id']
                                        );
                                        ?>

                                    </div>


                                </td>


                                <td>


                                    <div
                                        class="location"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $row['lokasi']
                                        );
                                        ?>

                                    </div>


                                </td>


                                <td>


                                    <div
                                        class="
                                            metric
                                            reservation
                                        "
                                    >


                                        <span
                                            class="metric-number"
                                        >

                                            <?php
                                            echo (int)
                                                $row[
                                                    'total_reservasi'
                                                ];
                                            ?>

                                        </span>


                                        <span
                                            class="metric-label"
                                        >

                                            kali

                                        </span>


                                    </div>


                                </td>


                                <td>


                                    <div
                                        class="
                                            metric
                                            metric-report
                                        "
                                    >


                                        <span
                                            class="metric-number"
                                        >

                                            <?php
                                            echo (int)
                                                $row[
                                                    'total_laporan'
                                                ];
                                            ?>

                                        </span>


                                        <span
                                            class="metric-label"
                                        >

                                            laporan

                                        </span>


                                    </div>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <div class="empty">


                <h3>
                    Belum ada data fasilitas
                </h3>


                <p>
                    Tambahkan fasilitas terlebih dahulu
                    untuk melihat rekapitulasi.
                </p>


            </div>


        <?php endif; ?>


    </section>



    <!-- NOTE -->

    <div class="note">

        <strong>Informasi:</strong>

        Reservasi yang dihitung pada rekap
        adalah reservasi dengan status
        <strong>Disetujui</strong>.
        Laporan kerusakan dihitung dari seluruh
        laporan yang tersimpan.

    </div>



    <!-- FOOTER -->

    <div class="footer">

        ReservasiKampus · Rekapitulasi Administrator

    </div>


</main>


</body>

</html>