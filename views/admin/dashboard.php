<?php
// views/admin/dashboard.php

session_start();

// ========================================
// VALIDASI AKSES ADMIN
// ========================================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';


// ========================================
// DATA PENGGUNA MENUNGGU VERIFIKASI
// ========================================

$stmt = $pdo->query("
    SELECT
        id,
        nama,
        email,
        tipe_pengguna
    FROM users
    WHERE role = 'pengguna'
      AND status_akun = 'pending'
    ORDER BY id DESC
");

$pending_users = $stmt->fetchAll();


// ========================================
// DATA FASILITAS
// ========================================

$stmt_fasilitas = $pdo->query("
    SELECT *
    FROM facilities
    ORDER BY id DESC
");

$daftar_fasilitas =
    $stmt_fasilitas->fetchAll();


// ========================================
// STATISTIK
// ========================================

$total_pending =
    count($pending_users);

$total_fasilitas =
    count($daftar_fasilitas);

$total_aktif = 0;
$total_nonaktif = 0;

foreach ($daftar_fasilitas as $fasilitas) {

    if (
        $fasilitas['status_fasilitas']
        === 'aktif'
    ) {
        $total_aktif++;
    } else {
        $total_nonaktif++;
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
        Dashboard Admin | ReservasiKampus
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

            color: var(--text);

            background:
                var(--bg);
        }


        a {
            text-decoration: none;
        }


        button {
            font-family: inherit;
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
                36px 0 70px;
        }


        /* ========================================
           HEADER
        ======================================== */

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


        .admin-user {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

            padding:
                10px 13px;

            background:
                white;

            border:
                1px solid
                var(--border);

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


        .admin-name {

            font-size:
                11px;

            font-weight:
                700;
        }


        .admin-role {

            margin-top:
                2px;

            color:
                var(--muted);

            font-size:
                9px;
        }


        /* ========================================
           HERO
        ======================================== */

        .hero {

            padding:
                28px 30px;

            margin-bottom:
                20px;

            border-radius:
                17px;

            color:
                white;

            background:
                linear-gradient(
                    135deg,
                    #073765,
                    #118de4
                );

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
                650px;

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
                120px;

            height:
                40px;

            padding:
                0 14px;

            border-radius:
                9px;

            background:
                white;

            color:
                var(--blue-dark);

            font-size:
                10px;

            font-weight:
                700;
        }


        .hero-button.secondary {

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

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.10
                );
        }


        /* ========================================
           STATISTICS
        ======================================== */

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

            color:
                var(--muted);

            font-size:
                9px;

            margin-top:
                4px;
        }


        /* ========================================
           SECTION CARD
        ======================================== */

        .card {

            overflow:
                hidden;

            background:
                white;

            border:
                1px solid
                var(--border);

            border-radius:
                15px;

            margin-bottom:
                20px;
        }


        .card-header {

            padding:
                19px 20px;

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

            margin-top:
                3px;

            color:
                var(--muted);

            font-size:
                10px;
        }


        .card-action {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            height:
                36px;

            padding:
                0 13px;

            border-radius:
                8px;

            background:
                var(--blue-dark);

            color:
                white;

            font-size:
                10px;

            font-weight:
                700;

            transition:
                .2s;
        }


        .card-action:hover {

            background:
                #052f59;
        }


        /* ========================================
           PENDING USERS
        ======================================== */

        .user-table-wrapper {

            width:
                100%;

            overflow-x:
                auto;
        }


        .data-table {

            width:
                100%;

            border-collapse:
                collapse;
        }


        .data-table th {

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
        }


        .data-table td {

            padding:
                14px 17px;

            border-bottom:
                1px solid
                #eef1f4;

            font-size:
                10px;

            color:
                #475467;

            vertical-align:
                middle;
        }


        .data-table tr:last-child td {

            border-bottom:
                none;
        }


        .user-name-cell {

            color:
                var(--text);

            font-weight:
                700;
        }


        .email-cell {

            color:
                var(--muted);
        }


        .type-badge {

            display:
                inline-flex;

            padding:
                5px 8px;

            border-radius:
                999px;

            background:
                #eef7ff;

            color:
                var(--blue);

            font-size:
                9px;

            font-weight:
                700;

            text-transform:
                capitalize;
        }


        .action-group {

            display:
                flex;

            align-items:
                center;

            gap:
                7px;
        }


        .action-form {

            margin:
                0;
        }


        .action-button {

            height:
                31px;

            padding:
                0 10px;

            border:
                none;

            border-radius:
                7px;

            font-size:
                9px;

            font-weight:
                700;

            cursor:
                pointer;

            transition:
                .2s;
        }


        .verify-button {

            background:
                var(--green-bg);

            color:
                var(--green);

            border:
                1px solid
                #c9ebdc;
        }


        .verify-button:hover {

            background:
                #dff7ec;
        }


        .reject-button {

            background:
                var(--red-bg);

            color:
                var(--red);

            border:
                1px solid
                #f0d0d0;
        }


        .reject-button:hover {

            background:
                #ffe7e7;
        }


        /* ========================================
           FACILITIES
        ======================================== */

        .facility-table {

            width:
                100%;

            border-collapse:
                collapse;
        }


        .facility-table th {

            padding:
                13px 15px;

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
        }


        .facility-table td {

            padding:
                14px 15px;

            border-bottom:
                1px solid
                #eef1f4;

            font-size:
                10px;

            color:
                #475467;

            vertical-align:
                middle;
        }


        .facility-table tr:last-child td {
            border-bottom:
                none;
        }


        .facility-name {

            color:
                var(--text);

            font-weight:
                700;
        }


        .facility-location {

            color:
                var(--muted);
        }


        .capacity {

            font-weight:
                700;

            color:
                var(--text);
        }


        /* ========================================
           STATUS
        ======================================== */

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
        }


        .status.active {

            background:
                var(--green-bg);

            color:
                var(--green);
        }


        .status.inactive {

            background:
                var(--gray-bg);

            color:
                #667085;
        }


        /* ========================================
           FACILITY ACTIONS
        ======================================== */

        .edit-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            height:
                31px;

            padding:
                0 10px;

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

            transition:
                .2s;
        }


        .edit-button:hover {

            background:
                #deefff;
        }


        .toggle-button {

            height:
                31px;

            padding:
                0 10px;

            border:
                1px solid;

            border-radius:
                7px;

            font-size:
                9px;

            font-weight:
                700;

            cursor:
                pointer;

            transition:
                .2s;
        }


        .toggle-on {

            color:
                var(--green);

            background:
                var(--green-bg);

            border-color:
                #c9ebdc;
        }


        .toggle-off {

            color:
                var(--red);

            background:
                var(--red-bg);

            border-color:
                #f0d0d0;
        }


        /* ========================================
           EMPTY STATE
        ======================================== */

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


        .empty h3 {

            color:
                var(--text);

            font-size:
                13px;

            margin-bottom:
                5px;
        }


        .empty p {

            font-size:
                10px;
        }


        /* ========================================
           FOOTER
        ======================================== */

        .footer {

            margin-top:
                24px;

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


        /* ========================================
           RESPONSIVE
        ======================================== */

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


            .hero-actions {

                width:
                    100%;
            }


            .hero-button {

                flex:
                    1;
            }


            .action-group {

                flex-direction:
                    column;

                align-items:
                    flex-start;
            }

        }


        @media (max-width: 500px) {

            .stats {

                grid-template-columns:
                    1fr;
            }


            .hero-actions {

                flex-direction:
                    column;
            }


            .hero-button {

                width:
                    100%;
            }


            .card-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;
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



<!-- ==========================================
     MAIN
=========================================== -->

<main class="main">


    <!-- ======================================
         WELCOME
    ======================================= -->

    <section class="welcome">


        <div>

            <div class="welcome-label">
                DASHBOARD ADMIN
            </div>


            <h1>

                Selamat datang,
                <?php
                echo htmlspecialchars(
                    $_SESSION['nama']
                );
                ?>

            </h1>


            <p>

                Kelola pengguna, fasilitas,
                dan sistem reservasi kampus
                dari satu tempat.

            </p>

        </div>


        <div class="admin-user">


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

                <div class="admin-name">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION['nama']
                    );
                    ?>

                </div>


                <div class="admin-role">
                    Administrator
                </div>

            </div>


        </div>


    </section>



    <!-- ======================================
         HERO
    ======================================= -->

    <section class="hero">


        <div>

            <h2>
                Kelola ReservasiKampus
            </h2>


            <p>

                Verifikasi pengguna baru,
                kelola fasilitas kampus,
                dan lihat rekapitulasi
                penggunaan fasilitas.

            </p>

        </div>


        <div class="hero-actions">


            <a
                href="tambah_fasilitas.php"
                class="hero-button"
            >
                + Fasilitas
            </a>


            <a
                href="tambah_petugas.php"
                class="hero-button secondary"
            >
                + Petugas
            </a>


            <a
                href="rekap.php"
                class="hero-button secondary"
            >
                Lihat Rekap
            </a>


        </div>


    </section>



    <!-- ======================================
         STATISTICS
    ======================================= -->

    <section class="stats">


        <div class="stat-card">

            <div class="stat-label">
                Menunggu Verifikasi
            </div>


            <div class="stat-number">

                <?php
                echo $total_pending;
                ?>

            </div>


            <div class="stat-description">

                Pengguna yang perlu diverifikasi

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Total Fasilitas
            </div>


            <div class="stat-number">

                <?php
                echo $total_fasilitas;
                ?>

            </div>


            <div class="stat-description">

                Semua fasilitas terdaftar

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Fasilitas Aktif
            </div>


            <div class="stat-number">

                <?php
                echo $total_aktif;
                ?>

            </div>


            <div class="stat-description">

                Siap digunakan pengguna

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Fasilitas Nonaktif
            </div>


            <div class="stat-number">

                <?php
                echo $total_nonaktif;
                ?>

            </div>


            <div class="stat-description">

                Tidak tersedia saat ini

            </div>

        </div>


    </section>



    <!-- ======================================
         PENDING USERS
    ======================================= -->

    <section class="card">


        <div class="card-header">


            <div>

                <div class="card-title">
                    Pengguna Menunggu Verifikasi
                </div>


                <div class="card-subtitle">
                    Verifikasi akun sebelum pengguna
                    dapat melakukan login.
                </div>

            </div>


        </div>


        <?php if (
            count($pending_users) > 0
        ): ?>


            <div class="user-table-wrapper">


                <table class="data-table">


                    <thead>

                        <tr>

                            <th>
                                Nama
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Tipe
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $pending_users
                            as $user
                        ): ?>


                            <tr>


                                <td>

                                    <div
                                        class="user-name-cell"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $user['nama']
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <div
                                        class="email-cell"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $user['email']
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="type-badge"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst(
                                                $user[
                                                    'tipe_pengguna'
                                                ]
                                            )
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>


                                    <div
                                        class="action-group"
                                    >


                                        <form
                                            action="../../app/controllers/process_verify_user.php"
                                            method="POST"
                                            class="action-form"
                                            onsubmit="return confirm('Verifikasi pengguna ini?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $user['id']
                                                );
                                                ?>"
                                            >


                                            <button
                                                type="submit"
                                                name="action"
                                                value="verify"
                                                class="
                                                    action-button
                                                    verify-button
                                                "
                                            >

                                                Verifikasi

                                            </button>


                                        </form>



                                        <form
                                            action="../../app/controllers/process_verify_user.php"
                                            method="POST"
                                            class="action-form"
                                            onsubmit="return confirm('Tolak pengguna ini?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $user['id']
                                                );
                                                ?>"
                                            >


                                            <button
                                                type="submit"
                                                name="action"
                                                value="reject"
                                                class="
                                                    action-button
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

                <h3>
                    Tidak ada pengguna baru
                </h3>

                <p>
                    Saat ini tidak ada akun yang
                    menunggu verifikasi.
                </p>

            </div>


        <?php endif; ?>


    </section>



    <!-- ======================================
         FACILITIES
    ======================================= -->

    <section class="card">


        <div class="card-header">


            <div>

                <div class="card-title">
                    Daftar Fasilitas
                </div>


                <div class="card-subtitle">
                    Kelola fasilitas yang tersedia
                    di sistem reservasi.
                </div>

            </div>


            <a
                href="tambah_fasilitas.php"
                class="card-action"
            >
                + Tambah Fasilitas
            </a>


        </div>


        <?php if (
            count($daftar_fasilitas) > 0
        ): ?>


            <div
                class="user-table-wrapper"
            >


                <table
                    class="facility-table"
                >


                    <thead>

                        <tr>

                            <th>
                                Nama Fasilitas
                            </th>

                            <th>
                                Tipe
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th>
                                Kapasitas
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $daftar_fasilitas
                            as $fasilitas
                        ): ?>


                            <tr>


                                <td>

                                    <div
                                        class="facility-name"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $fasilitas[
                                                'nama_fasilitas'
                                            ]
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="type-badge"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst(
                                                $fasilitas[
                                                    'tipe'
                                                ]
                                            )
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="facility-location"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $fasilitas[
                                                'lokasi'
                                            ]
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="capacity"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $fasilitas[
                                                'kapasitas'
                                            ]
                                        );
                                        ?>

                                        orang

                                    </span>

                                </td>


                                <td>


                                    <?php if (
                                        $fasilitas[
                                            'status_fasilitas'
                                        ]
                                        ===
                                        'aktif'
                                    ): ?>


                                        <span
                                            class="
                                                status
                                                active
                                            "
                                        >

                                            Aktif

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="
                                                status
                                                inactive
                                            "
                                        >

                                            Nonaktif

                                        </span>


                                    <?php endif; ?>


                                </td>


                                <td>


                                    <div
                                        class="action-group"
                                    >


                                        <a
                                            href="edit_fasilitas.php?id=<?php
                                            echo htmlspecialchars(
                                                $fasilitas['id']
                                            );
                                            ?>"
                                            class="edit-button"
                                        >

                                            Edit

                                        </a>



                                        <form
                                            action="../../app/controllers/process_status_fasilitas.php"
                                            method="POST"
                                            class="action-form"
                                            onsubmit="return confirm('Yakin ingin mengubah status fasilitas ini?');"
                                        >


                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $fasilitas['id']
                                                );
                                                ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="current_status"
                                                value="<?php
                                                echo htmlspecialchars(
                                                    $fasilitas[
                                                        'status_fasilitas'
                                                    ]
                                                );
                                                ?>"
                                            >


                                            <?php if (
                                                $fasilitas[
                                                    'status_fasilitas'
                                                ]
                                                ===
                                                'nonaktif'
                                            ): ?>


                                                <button
                                                    type="submit"
                                                    class="
                                                        toggle-button
                                                        toggle-on
                                                    "
                                                >

                                                    Aktifkan

                                                </button>


                                            <?php else: ?>


                                                <button
                                                    type="submit"
                                                    class="
                                                        toggle-button
                                                        toggle-off
                                                    "
                                                >

                                                    Nonaktifkan

                                                </button>


                                            <?php endif; ?>


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

                <h3>
                    Belum ada fasilitas
                </h3>

                <p>
                    Tambahkan fasilitas baru
                    untuk ditampilkan di sistem.
                </p>

            </div>


        <?php endif; ?>


    </section>



    <!-- ======================================
         FOOTER
    ======================================= -->

    <div class="footer">

        ReservasiKampus · Dashboard Administrator

    </div>


</main>


</body>

</html>