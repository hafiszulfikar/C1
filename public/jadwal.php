<?php

session_start();

require_once __DIR__ . '/../config/database.php';

$id_fasilitas = $_GET['id'] ?? null;
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

if (!$id_fasilitas) {
    die('ID Fasilitas tidak ditemukan.');
}

// ========================================
// NAVBAR BERDASARKAN STATUS LOGIN
// ========================================

$sudah_login = isset($_SESSION['role']);

$dashboard_link = '../views/user/dashboard.php';

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

// Ambil data fasilitas
$stmt_fac = $pdo->prepare("
    SELECT *
    FROM facilities
    WHERE id = :id
");

$stmt_fac->execute([
    ':id' => $id_fasilitas
]);

$fasilitas = $stmt_fac->fetch();

if (!$fasilitas) {
    die('Data fasilitas tidak ditemukan.');
}

// Ambil reservasi yang sudah disetujui
$stmt_res = $pdo->prepare("
    SELECT start_time, end_time
    FROM reservations
    WHERE facility_id = :id
      AND tanggal = :tanggal
      AND status_reservasi = 'disetujui'
");

$stmt_res->execute([
    ':id' => $id_fasilitas,
    ':tanggal' => $tanggal
]);

$reservasi = $stmt_res->fetchAll();

// Generate slot 30 menit
$slots = [];

$cursor = strtotime('07:00');
$end = strtotime('20:00');

while ($cursor < $end) {

    $slot_start = date('H:i', $cursor);

    $next = strtotime('+30 minutes', $cursor);

    $slot_end = date('H:i', $next);

    $available = true;

    foreach ($reservasi as $res) {

        $res_start = strtotime($res['start_time']);
        $res_end = strtotime($res['end_time']);

        if (
            strtotime($slot_start) < $res_end &&
            strtotime($slot_end) > $res_start
        ) {
            $available = false;
            break;
        }
    }

    $slots[] = [
        'start' => $slot_start,
        'end' => $slot_end,
        'available' => $available
    ];

    $cursor = $next;
}

// Hitung jumlah slot
$total_slot = count($slots);

$slot_tersedia = count(
    array_filter(
        $slots,
        fn($slot) => $slot['available']
    )
);

$slot_terisi = $total_slot - $slot_tersedia;

// Format tanggal Indonesia
function formatTanggal($tanggal)
{
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $timestamp = strtotime($tanggal);

    return $hari[date('l', $timestamp)] .
        ', ' .
        date('d', $timestamp) .
        ' ' .
        $bulan[(int) date('m', $timestamp)] .
        ' ' .
        date('Y', $timestamp);
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
        Jadwal |
        <?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?>
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

            --bg: #f5f8fc;

            --text: #172033;

            --muted: #6b7280;

            --border: #e3e9f0;

            --success: #0d9f6e;
            --success-bg: #eaf9f2;

            --danger: #c23b3b;
            --danger-bg: #fff0f0;

            --white: #ffffff;
        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: var(--bg);

            color: var(--text);

            min-height: 100vh;
        }

        a {
            text-decoration: none;
        }

        button,
        input {
            font-family: inherit;
        }


        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-login {
            color: #063b70;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 14px;
            text-decoration: none;
        }

        .nav-login:hover {
            color: #0879d1;
        }

        .nav-register {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            background: #063b70;
            color: white;

            padding: 10px 16px;
            border-radius: 9px;

            font-size: 13px;
            font-weight: 600;

            text-decoration: none;

            transition: 0.2s;
        }

        .nav-register:hover {
            background: #052f59;
        }

        /* ======================================
           NAVBAR
        ====================================== */

        .navbar {

            height: 72px;

            background: rgba(
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

            gap: 14px;
        }

        .nav-link {

            color:
                var(--blue-dark);

            font-size: 12px;

            font-weight: 700;

            padding:
                9px 12px;
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


        /* ======================================
           PAGE
        ====================================== */

        .page {

            width:
                min(
                    1050px,
                    calc(100% - 40px)
                );

            margin:
                0 auto;

            padding:
                34px 0 70px;
        }


        /* ======================================
           BACK
        ====================================== */

        .back {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            color:
                var(--blue);

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 18px;
        }


        /* ======================================
           FACILITY HEADER
        ====================================== */

        .facility-card {

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            padding: 24px;

            display: flex;

            justify-content: space-between;

            gap: 25px;

            align-items: center;

            margin-bottom: 18px;

            box-shadow:
                0 12px 35px
                rgba(
                    25,
                    56,
                    88,
                    0.07
                );
        }

        .facility-info {

            min-width: 0;
        }

        .facility-label {

            display: inline-block;

            padding:
                5px 9px;

            border-radius: 99px;

            background:
                #eef7ff;

            color:
                var(--blue);

            font-size: 10px;

            font-weight: 700;

            margin-bottom: 10px;
        }

        .facility-info h1 {

            font-size: 27px;

            line-height: 1.2;

            margin-bottom: 8px;
        }

        .facility-description {

            color:
                var(--muted);

            font-size: 12px;

            line-height: 1.6;
        }

        .facility-meta {

            display: flex;

            gap: 9px;

            margin-top: 15px;

            flex-wrap: wrap;
        }

        .meta-item {

            padding:
                8px 11px;

            border:
                1px solid
                var(--border);

            border-radius: 8px;

            background:
                #fafcfe;

            color:
                #5d6875;

            font-size: 11px;
        }


        /* ======================================
           DATE CARD
        ====================================== */

        .date-card {

            min-width: 250px;

            padding:
                17px;

            border:
                1px solid
                var(--border);

            border-radius: 13px;

            background:
                #fbfdff;
        }

        .date-card label {

            display: block;

            font-size: 11px;

            font-weight: 700;

            color:
                #4a5563;

            margin-bottom: 7px;
        }

        .date-row {

            display: flex;

            gap: 8px;
        }

        .date-input {

            flex: 1;

            height: 42px;

            border:
                1px solid
                #d7e0e8;

            border-radius: 9px;

            padding:
                0 10px;

            background: white;

            outline: none;

            font-size: 12px;
        }

        .date-input:focus {

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

        .check-button {

            height: 42px;

            padding:
                0 14px;

            border: none;

            border-radius: 9px;

            background:
                var(--blue-dark);

            color: white;

            font-size: 11px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s;
        }

        .check-button:hover {

            background:
                #052f59;
        }


        /* ======================================
           SUMMARY
        ====================================== */

        .summary-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    1fr
                );

            gap: 14px;

            margin-bottom: 18px;
        }

        .summary-card {

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 14px;

            padding: 18px;
        }

        .summary-label {

            font-size: 11px;

            color:
                var(--muted);

            margin-bottom: 7px;
        }

        .summary-number {

            font-size: 25px;

            font-weight: 800;
        }

        .summary-number.green {

            color:
                var(--success);
        }

        .summary-number.red {

            color:
                var(--danger);
        }


        /* ======================================
           SCHEDULE PANEL
        ====================================== */

        .schedule-card {

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            overflow: hidden;

            box-shadow:
                0 12px 35px
                rgba(
                    25,
                    56,
                    88,
                    0.06
                );
        }

        .schedule-header {

            padding:
                20px 22px;

            border-bottom:
                1px solid
                var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }

        .schedule-title h2 {

            font-size: 18px;

            margin-bottom: 4px;
        }

        .schedule-title p {

            color:
                var(--muted);

            font-size: 11px;
        }

        .legend {

            display: flex;

            align-items: center;

            gap: 14px;

            font-size: 10px;

            color:
                var(--muted);
        }

        .legend-item {

            display: flex;

            align-items: center;

            gap: 6px;
        }

        .legend-dot {

            width: 8px;

            height: 8px;

            border-radius: 50%;
        }

        .legend-available {

            background:
                var(--success);
        }

        .legend-busy {

            background:
                var(--danger);
        }


        /* ======================================
           SLOT GRID
        ====================================== */

        .slot-area {

            padding:
                22px;
        }

        .slot-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 11px;
        }

        .slot {

            min-height: 82px;

            border:
                1px solid
                var(--border);

            border-radius: 11px;

            padding:
                13px;

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            transition: .2s;
        }

        .slot-available {

            background:
                var(--success-bg);

            border-color:
                #caebdc;

            color:
                #087e58;
        }

        .slot-busy {

            background:
                var(--danger-bg);

            border-color:
                #efd1d1;

            color:
                #b22f2f;
        }

        .slot:hover {

            transform:
                translateY(-2px);
        }

        .slot-time {

            font-size: 12px;

            font-weight: 800;
        }

        .slot-status {

            font-size: 10px;

            font-weight: 700;
        }


        /* ======================================
           FOOTNOTE
        ====================================== */

        .schedule-note {

            margin-top:
                17px;

            padding:
                13px 15px;

            border:
                1px solid
                #dceaf5;

            border-radius:
                10px;

            background:
                #f5f9fd;

            color:
                #637181;

            font-size: 10px;

            line-height: 1.6;
        }

        .schedule-note strong {

            color:
                var(--blue-dark);
        }


        /* ======================================
           MOBILE
        ====================================== */

        @media (max-width: 850px) {

            .facility-card {

                flex-direction:
                    column;

                align-items:
                    stretch;
            }

            .date-card {

                min-width: 0;

            }

            .slot-grid {

                grid-template-columns:
                    repeat(
                        3,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }

        }


        @media (max-width: 650px) {

            .page {

                width:
                    calc(
                        100% - 26px
                    );

                padding-top:
                    22px;
            }

            .navbar-inner {

                width:
                    calc(
                        100% - 26px
                    );
            }

            .nav-link {

                display: none;
            }

            .facility-card {

                padding:
                    19px;
            }

            .facility-info h1 {

                font-size:
                    23px;
            }

            .summary-grid {

                grid-template-columns:
                    1fr 1fr;
            }

            .schedule-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;
            }

            .legend {

                flex-wrap:
                    wrap;
            }

            .slot-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }

        }


        @media (max-width: 430px) {

            .summary-grid {

                grid-template-columns:
                    1fr;
            }

            .date-row {

                flex-direction:
                    column;
            }

            .check-button {

                width:
                    100%;
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



<!-- ==========================================
     PAGE
=========================================== -->

<main class="page">


    <!-- BACK -->

    <a
        href="index.php"
        class="back"
    >
        ← Kembali ke daftar fasilitas
    </a>


    <!-- ======================================
         FACILITY HEADER
    ======================================= -->

    <section class="facility-card">


        <div class="facility-info">

            <div class="facility-label">
                <?php
                echo htmlspecialchars(
                    $fasilitas['tipe']
                );
                ?>
            </div>


            <h1>
                <?php
                echo htmlspecialchars(
                    $fasilitas['nama_fasilitas']
                );
                ?>
            </h1>


            <p class="facility-description">

                Cek ketersediaan waktu penggunaan
                fasilitas sebelum melakukan reservasi.

            </p>


            <div class="facility-meta">

                <div class="meta-item">

                    Lokasi:
                    <?php
                    echo htmlspecialchars(
                        $fasilitas['lokasi']
                    );
                    ?>

                </div>


                <div class="meta-item">

                    Kapasitas:
                    <?php
                    echo htmlspecialchars(
                        $fasilitas['kapasitas']
                    );
                    ?>
                    orang

                </div>

            </div>

        </div>


        <!-- DATE -->

        <div class="date-card">

            <form
                action="jadwal.php"
                method="GET"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php
                        echo htmlspecialchars(
                            $id_fasilitas
                        );
                    ?>"
                >


                <label for="tanggal">
                    Pilih Tanggal
                </label>


                <div class="date-row">

                    <input
                        type="date"
                        id="tanggal"
                        name="tanggal"
                        value="<?php
                            echo htmlspecialchars(
                                $tanggal
                            );
                        ?>"
                        class="date-input"
                        required
                    >


                    <button
                        type="submit"
                        class="check-button"
                    >
                        Cek Jadwal
                    </button>

                </div>

            </form>

        </div>

    </section>



    <!-- ======================================
         SUMMARY
    ======================================= -->

    <section class="summary-grid">


        <div class="summary-card">

            <div class="summary-label">
                Total Slot
            </div>

            <div class="summary-number">
                <?php echo $total_slot; ?>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Slot Tersedia
            </div>

            <div class="summary-number green">
                <?php echo $slot_tersedia; ?>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Slot Terisi
            </div>

            <div class="summary-number red">
                <?php echo $slot_terisi; ?>
            </div>

        </div>


    </section>



    <!-- ======================================
         SCHEDULE
    ======================================= -->

    <section class="schedule-card">


        <div class="schedule-header">


            <div class="schedule-title">

                <h2>

                    Jadwal
                    <?php
                    echo htmlspecialchars(
                        formatTanggal(
                            $tanggal
                        )
                    );
                    ?>

                </h2>


                <p>
                    Jam operasional 07:00–20:00
                    · Interval slot 30 menit
                </p>

            </div>


            <div class="legend">


                <div class="legend-item">

                    <span
                        class="legend-dot legend-available"
                    ></span>

                    Tersedia

                </div>


                <div class="legend-item">

                    <span
                        class="legend-dot legend-busy"
                    ></span>

                    Tidak tersedia

                </div>


            </div>

        </div>


        <div class="slot-area">


            <div class="slot-grid">


                <?php foreach ($slots as $slot): ?>


                    <div
                        class="
                            slot

                            <?php
                            echo $slot['available']
                                ? 'slot-available'
                                : 'slot-busy';
                            ?>
                        "
                    >


                        <div class="slot-time">

                            <?php
                            echo
                                htmlspecialchars(
                                    $slot['start']
                                )
                                .
                                ' - '
                                .
                                htmlspecialchars(
                                    $slot['end']
                                );
                            ?>

                        </div>


                        <div class="slot-status">

                            <?php
                            echo $slot['available']
                                ? 'Tersedia'
                                : 'Tidak tersedia';
                            ?>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


            <div class="schedule-note">

                <strong>Informasi:</strong>

                Jadwal yang ditampilkan berdasarkan
                reservasi yang sudah disetujui.
                Untuk melakukan reservasi, silakan
                login terlebih dahulu.

            </div>


        </div>

    </section>


</main>


</body>

</html>