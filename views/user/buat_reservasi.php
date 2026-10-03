<?php
// views/user/buat_reservasi.php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Ambil daftar fasilitas aktif
$stmt = $pdo->query("
    SELECT id, nama_fasilitas
    FROM facilities
    WHERE status_fasilitas = 'aktif'
    ORDER BY nama_fasilitas ASC
");

$fasilitas = $stmt->fetchAll();

$nama_user = $_SESSION['nama'] ?? 'Pengguna';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Ajukan Reservasi | ReservasiKampus</title>


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
        }


        body {

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: var(--text);

            background: var(--bg);
        }


        a {
            text-decoration: none;
        }


        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        .error-message {
            margin-top: 16px;
            margin-bottom: 20px;
            padding: 14px 16px;

            border: 1px solid #f0caca;
            border-radius: 10px;

            background: #fff5f5;

            color: #b42318;
        }

        .error-title {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .error-text {
            font-size: 10px;
            line-height: 1.6;
        }


        /* ======================================
           NAVBAR
        ====================================== */

        .navbar {

            height: 72px;

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

            background:
                var(--blue-dark);

            color: white;

            padding:
                10px 15px;

            border-radius:
                9px;

            font-size:
                12px;

            font-weight:
                700;

            transition: .2s;
        }


        .logout-button:hover {

            background:
                #052f59;
        }


        /* ======================================
           MAIN
        ====================================== */

        .main {

            width:
                min(
                    1040px,
                    calc(100% - 40px)
                );

            margin:
                auto;

            padding:
                38px 0
                70px;
        }


        /* ======================================
           PAGE HEADER
        ====================================== */

        .page-header {

            margin-bottom:
                22px;
        }


        .breadcrumb {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

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
                28px;

            margin-bottom:
                7px;

            line-height:
                1.2;
        }


        .page-header p {

            color:
                var(--muted);

            font-size:
                12px;

            line-height:
                1.6;
        }


        /* ======================================
           LAYOUT
        ====================================== */

        .booking-layout {

            display:
                grid;

            grid-template-columns:
                0.78fr
                1.22fr;

            gap:
                18px;

            align-items:
                stretch;
        }


        /* ======================================
           LEFT INFO
        ====================================== */

        .info-panel {

            border-radius:
                17px;

            overflow:
                hidden;

            background:
                linear-gradient(
                    145deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color:
                white;

            padding:
                30px;

            min-height:
                575px;

            display:
                flex;

            flex-direction:
                column;

            justify-content:
                space-between;
        }


        .info-top {

            margin-bottom:
                35px;
        }


        .info-label {

            display:
                inline-flex;

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

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.08
                );

            border-radius:
                999px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.84
                );

            font-size:
                10px;

            font-weight:
                700;

            margin-bottom:
                19px;
        }


        .info-panel h2 {

            font-size:
                27px;

            line-height:
                1.25;

            margin-bottom:
                13px;
        }


        .info-panel p {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.75
                );

            font-size:
                12px;

            line-height:
                1.7;
        }


        /* ======================================
           STEPS
        ====================================== */

        .steps {

            display:
                flex;

            flex-direction:
                column;

            gap:
                13px;

            margin-top:
                27px;
        }


        .step {

            display:
                flex;

            gap:
                11px;

            align-items:
                flex-start;
        }


        .step-number {

            flex-shrink:
                0;

            width:
                28px;

            height:
                28px;

            border-radius:
                8px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.12
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.12
                );

            font-size:
                9px;

            font-weight:
                700;
        }


        .step-content strong {

            display:
                block;

            font-size:
                11px;

            margin-bottom:
                2px;
        }


        .step-content span {

            display:
                block;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.65
                );

            font-size:
                9px;

            line-height:
                1.5;
        }


        .info-note {

            margin-top:
                25px;

            padding:
                13px;

            border-radius:
                10px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.08
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
                    0.72
                );

            font-size:
                9px;

            line-height:
                1.6;
        }


        /* ======================================
           FORM CARD
        ====================================== */

        .form-card {

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius:
                17px;

            padding:
                30px;

            box-shadow:
                0 14px 38px
                rgba(
                    25,
                    56,
                    88,
                    0.07
                );
        }


        .form-header {

            margin-bottom:
                25px;

            padding-bottom:
                18px;

            border-bottom:
                1px solid
                var(--border);
        }


        .form-header h2 {

            font-size:
                18px;

            margin-bottom:
                5px;
        }


        .form-header p {

            color:
                var(--muted);

            font-size:
                10px;

            line-height:
                1.6;
        }


        /* ======================================
           FORM GROUP
        ====================================== */

        .form-group {

            margin-bottom:
                19px;
        }


        .form-group label {

            display:
                block;

            margin-bottom:
                7px;

            color:
                #364152;

            font-size:
                11px;

            font-weight:
                700;
        }


        .form-hint {

            margin-top:
                5px;

            color:
                #98a2b3;

            font-size:
                9px;
        }


        .form-group input,
        .form-group select,
        .form-group textarea {

            width:
                100%;

            border:
                1px solid
                #d6dee8;

            border-radius:
                9px;

            background:
                white;

            color:
                var(--text);

            outline:
                none;

            font-size:
                12px;

            transition:
                .2s;
        }


        .form-group input,
        .form-group select {

            height:
                44px;

            padding:
                0 13px;
        }


        .form-group textarea {

            min-height:
                110px;

            padding:
                12px 13px;

            resize:
                vertical;

            line-height:
                1.6;
        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

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


        .form-group input::placeholder,
        .form-group textarea::placeholder {

            color:
                #9aa5b1;
        }


        /* ======================================
           TIME
        ====================================== */

        .time-grid {

            display:
                grid;

            grid-template-columns:
                1fr
                1fr;

            gap:
                12px;
        }


        /* ======================================
           BUTTON AREA
        ====================================== */

        .form-actions {

            display:
                flex;

            align-items:
                center;

            justify-content:
                flex-end;

            gap:
                10px;

            padding-top:
                7px;
        }


        .cancel-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            height:
                44px;

            padding:
                0 17px;

            border:
                1px solid
                #d8e0e8;

            border-radius:
                9px;

            background:
                white;

            color:
                #667085;

            font-size:
                11px;

            font-weight:
                700;

            transition:
                .2s;
        }


        .cancel-button:hover {

            background:
                #f8fafc;

            border-color:
                #cbd5df;
        }


        .submit-button {

            height:
                44px;

            padding:
                0 20px;

            border:
                none;

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

            cursor:
                pointer;

            transition:
                .2s;
        }


        .submit-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* ======================================
           FOOTER NOTE
        ====================================== */

        .bottom-note {

            margin-top:
                17px;

            padding:
                12px 14px;

            border:
                1px solid
                #dceaf5;

            border-radius:
                9px;

            background:
                #f5f9fd;

            color:
                #667085;

            font-size:
                9px;

            line-height:
                1.6;
        }


        .bottom-note strong {

            color:
                var(--blue-dark);
        }


        /* ======================================
           EMPTY FACILITY
        ====================================== */

        .empty-facility {

            padding:
                13px;

            border:
                1px solid
                #f0d0d0;

            border-radius:
                9px;

            background:
                #fff7f7;

            color:
                #b54747;

            font-size:
                10px;

            line-height:
                1.5;
        }

        // ==========================================
        // VALIDASI TANGGAL
        // ==========================================

        function cekTanggal() {

            if (!tanggal.value) {
                tanggal.setCustomValidity('');
                return true;
            }

            const hariIni = tanggalHariIni();

            if (tanggal.value < hariIni) {

                tanggal.setCustomValidity(
                    'Tanggal penggunaan tidak boleh lebih awal dari hari ini.'
                );

                return false;
            }

            tanggal.setCustomValidity('');

            return true;
        }


        /* ======================================
           RESPONSIVE
        ====================================== */

        @media (max-width: 850px) {

            .booking-layout {

                grid-template-columns:
                    1fr;
            }


            .info-panel {

                min-height:
                    auto;
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


            .main {

                padding-top:
                    25px;
            }


            .info-panel,
            .form-card {

                padding:
                    23px;
            }


            .time-grid {

                grid-template-columns:
                    1fr;
            }


            .form-actions {

                flex-direction:
                    column-reverse;

                align-items:
                    stretch;
            }


            .cancel-button,
            .submit-button {

                width:
                    100%;
            }

        }

    </style>

</head>


<body>


<!-- =========================================
     NAVBAR
========================================== -->

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
                href="dashboard.php"
                class="nav-link"
            >
                Dashboard
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



<!-- =========================================
     MAIN
========================================== -->

<main class="main">


    <!-- PAGE HEADER -->

    <div class="page-header">


        <a
            href="dashboard.php"
            class="breadcrumb"
        >

            ← Kembali ke Dashboard

        </a>


        <h1>
            Ajukan Reservasi
        </h1>


        <p>
            Lengkapi informasi berikut untuk mengajukan
            penggunaan fasilitas kampus.
        </p>

        <?php if (!empty($_GET['error'])): ?>

            <div class="error-message">

                <div class="error-title">
                    Reservasi tidak dapat diajukan
                </div>

                <div class="error-text">
                    <?php
                        echo htmlspecialchars(
                            $_GET['error']
                        );
                    ?>
                </div>

            </div>

<?php endif; ?>


    </div>



    <!-- =====================================
         BOOKING LAYOUT
    ====================================== -->

    <div class="booking-layout">


        <!-- =================================
             LEFT INFO PANEL
        ================================== -->

        <aside class="info-panel">


            <div class="info-top">


                <div class="info-label">

                    RESERVASI FASILITAS

                </div>


                <h2>

                    Pesan ruangan
                    untuk kegiatanmu.

                </h2>


                <p>

                    Pilih fasilitas, tentukan tanggal
                    dan waktu penggunaan, kemudian
                    jelaskan tujuan reservasimu.

                </p>


                <!-- STEPS -->

                <div class="steps">


                    <div class="step">


                        <div class="step-number">
                            01
                        </div>


                        <div class="step-content">

                            <strong>
                                Pilih fasilitas
                            </strong>

                            <span>
                                Tentukan ruangan yang
                                ingin digunakan.
                            </span>

                        </div>


                    </div>


                    <div class="step">


                        <div class="step-number">
                            02
                        </div>


                        <div class="step-content">

                            <strong>
                                Tentukan jadwal
                            </strong>

                            <span>
                                Pilih tanggal dan
                                waktu penggunaan.
                            </span>

                        </div>


                    </div>


                    <div class="step">


                        <div class="step-number">
                            03
                        </div>


                        <div class="step-content">

                            <strong>
                                Ajukan reservasi
                            </strong>

                            <span>
                                Permintaan akan diproses
                                oleh petugas.
                            </span>

                        </div>


                    </div>


                </div>


                <div class="info-note">

                    Pastikan waktu yang kamu pilih
                    tidak bertabrakan dengan jadwal
                    reservasi yang sudah disetujui.

                </div>


            </div>


        </aside>



        <!-- =================================
             FORM
        ================================== -->

        <section class="form-card">


            <div class="form-header">

                <h2>
                    Detail Reservasi
                </h2>


                <p>
                    Isi data reservasi dengan lengkap
                    sebelum mengirim pengajuan.
                </p>

            </div>



            <form
                action="../../app/controllers/process_reservasi.php"
                method="POST"
                novalidate
            >


                <!-- FACILITY -->

                <div class="form-group">

                    <label for="facility_id">
                        Fasilitas
                    </label>


                    <?php if (count($fasilitas) > 0): ?>


                        <select
                            id="facility_id"
                            name="facility_id"
                            required
                        >

                            <option
                                value=""
                                disabled
                                selected
                            >

                                Pilih fasilitas

                            </option>


                            <?php foreach (
                                $fasilitas
                                as $f
                            ): ?>


                                <option
                                    value="<?php
                                        echo htmlspecialchars(
                                            $f['id']
                                        );
                                    ?>"
                                >

                                    <?php
                                        echo htmlspecialchars(
                                            $f[
                                                'nama_fasilitas'
                                            ]
                                        );
                                    ?>

                                </option>


                            <?php endforeach; ?>


                        </select>


                        <div class="form-hint">

                            Hanya fasilitas yang sedang aktif
                            yang dapat dipilih.

                        </div>


                    <?php else: ?>


                        <div class="empty-facility">

                            Belum ada fasilitas aktif yang
                            tersedia untuk reservasi.

                        </div>


                    <?php endif; ?>

                </div>



                <!-- DATE -->

                <div class="form-group">

                    <label for="tanggal">
                        Tanggal Penggunaan
                    </label>


                    <input
                        type="date"
                        id="tanggal"
                        name="tanggal"
                        required
                    >


                    <div class="form-hint">

                        Pilih tanggal kegiatan yang
                        ingin dilaksanakan.

                    </div>

                </div>



                <!-- TIME -->

                <div class="form-group">

                    <label>
                        Waktu Penggunaan
                    </label>

                    <div class="time-grid">

                        <div>

                            <select
                                id="start_time"
                                name="start_time"
                                required
                            >

                                <option value="">
                                    Pilih waktu mulai
                                </option>

                                <?php
                                $start = strtotime('07:00');
                                $end = strtotime('19:30');

                                while ($start <= $end) {
                                    $time = date('H:i', $start);

                                    echo '<option value="' . $time . '">';
                                    echo $time;
                                    echo '</option>';

                                    $start = strtotime('+30 minutes', $start);
                                }
                                ?>

                            </select>

                            <div class="form-hint">
                                Waktu mulai
                            </div>

                        </div>


                        <div>

                            <select
                                id="end_time"
                                name="end_time"
                                required
                            >

                                <option value="">
                                    Pilih waktu selesai
                                </option>

                                <?php
                                $start = strtotime('07:30');
                                $end = strtotime('20:00');

                                while ($start <= $end) {
                                    $time = date('H:i', $start);

                                    echo '<option value="' . $time . '">';
                                    echo $time;
                                    echo '</option>';

                                    $start = strtotime('+30 minutes', $start);
                                }
                                ?>

                            </select>

                            <div class="form-hint">
                                Waktu selesai
                            </div>

                        </div>

                    </div>

                    <div class="form-hint">
                        Jam operasional 07:00–20:00. Interval waktu 30 menit.
                    </div>

                </div>



                <!-- PURPOSE -->

                <div class="form-group">

                    <label for="tujuan_penggunaan">

                        Tujuan Penggunaan

                    </label>


                    <textarea
                        id="tujuan_penggunaan"
                        name="tujuan_penggunaan"
                        placeholder="Contoh: Perkuliahan, rapat organisasi, seminar, atau kegiatan akademik lainnya."
                        required
                    ></textarea>


                    <div class="form-hint">

                        Jelaskan kegiatan yang akan dilakukan
                        di fasilitas tersebut.

                    </div>

                </div>



                <!-- ACTIONS -->

                <div class="form-actions">


                    <a
                        href="dashboard.php"
                        class="cancel-button"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="submit-button"
                        <?php
                            echo count($fasilitas) === 0
                                ? 'disabled'
                                : '';
                        ?>
                    >
                        Ajukan Reservasi
                    </button>


                </div>


            </form>


            <!-- NOTE -->

            <div class="bottom-note">

                <strong>Catatan:</strong>

                Pengajuan reservasi tidak langsung disetujui.
                Status awal reservasi adalah
                <strong>Menunggu</strong>
                dan akan diproses oleh petugas.

            </div>


        </section>


    </div>


</main>

<script>

    const form = document.querySelector(
        'form[action*="process_reservasi.php"]'
    );

    const facility =
        document.getElementById('facility_id');

    const tanggal =
        document.getElementById('tanggal');

    const startTime =
        document.getElementById('start_time');

    const endTime =
        document.getElementById('end_time');

    const tujuan =
        document.getElementById('tujuan_penggunaan');


    function waktuKeMenit(waktu) {

        if (!waktu) {
            return null;
        }

        const [jam, menit] =
            waktu.split(':').map(Number);

        return (jam * 60) + menit;
    }


    function tanggalHariIni() {

        const hariIni = new Date();

        const tahun =
            hariIni.getFullYear();

        const bulan =
            String(
                hariIni.getMonth() + 1
            ).padStart(2, '0');

        const hari =
            String(
                hariIni.getDate()
            ).padStart(2, '0');

        return `${tahun}-${bulan}-${hari}`;
    }


    // Batasi tanggal minimal hari ini
    tanggal.min = tanggalHariIni();


    function cekTanggal() {

        if (!tanggal.value) {
            return true;
        }

        if (tanggal.value < tanggalHariIni()) {

            tanggal.setCustomValidity(
                'Tanggal penggunaan tidak boleh lebih awal dari hari ini.'
            );

            return false;
        }

        tanggal.setCustomValidity('');

        return true;
    }


    // Hapus pesan error ketika input diperbaiki
    facility.addEventListener(
        'change',
        function () {
            facility.setCustomValidity('');
        }
    );


    tanggal.addEventListener(
        'change',
        function () {
            cekTanggal();
        }
    );


    startTime.addEventListener(
        'change',
        function () {
            startTime.setCustomValidity('');
            endTime.setCustomValidity('');
        }
    );


    endTime.addEventListener(
        'change',
        function () {
            endTime.setCustomValidity('');
        }
    );


    tujuan.addEventListener(
        'input',
        function () {
            tujuan.setCustomValidity('');
        }
    );


    form.addEventListener(
        'submit',
        function (event) {

            // ==============================
            // FASILITAS
            // ==============================

            if (!facility.value) {

                event.preventDefault();

                facility.setCustomValidity(
                    'Silakan pilih fasilitas yang ingin digunakan.'
                );

                facility.reportValidity();

                return;
            }


            // ==============================
            // TANGGAL
            // ==============================

            if (!tanggal.value) {

                event.preventDefault();

                tanggal.setCustomValidity(
                    'Silakan pilih tanggal penggunaan.'
                );

                tanggal.reportValidity();

                return;
            }


            if (!cekTanggal()) {

                event.preventDefault();

                tanggal.reportValidity();

                return;
            }


            // ==============================
            // WAKTU MULAI
            // ==============================

            if (!startTime.value) {

                event.preventDefault();

                startTime.setCustomValidity(
                    'Silakan pilih waktu mulai.'
                );

                startTime.reportValidity();

                return;
            }


            // ==============================
            // WAKTU SELESAI
            // ==============================

            if (!endTime.value) {

                event.preventDefault();

                endTime.setCustomValidity(
                    'Silakan pilih waktu selesai.'
                );

                endTime.reportValidity();

                return;
            }


            // ==============================
            // URUTAN WAKTU
            // ==============================

            const mulai =
                waktuKeMenit(
                    startTime.value
                );

            const selesai =
                waktuKeMenit(
                    endTime.value
                );


            if (mulai >= selesai) {

                event.preventDefault();

                endTime.setCustomValidity(
                    'Waktu mulai harus lebih awal daripada waktu selesai.'
                );

                endTime.reportValidity();

                return;
            }


            endTime.setCustomValidity('');


            // ==============================
            // TUJUAN
            // ==============================

            if (!tujuan.value.trim()) {

                event.preventDefault();

                tujuan.setCustomValidity(
                    'Silakan isi tujuan penggunaan fasilitas.'
                );

                tujuan.reportValidity();

                return;
            }


            tujuan.setCustomValidity('');

        }
    );

</script>

</body>

</html>