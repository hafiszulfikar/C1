<?php
// views/admin/edit_fasilitas.php

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
// PROSES UPDATE
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id =
        $_POST['id'] ?? null;

    $nama_fasilitas =
        trim($_POST['nama_fasilitas'] ?? '');

    $tipe =
        $_POST['tipe'] ?? '';

    $lokasi =
        trim($_POST['lokasi'] ?? '');

    $kapasitas =
        (int) ($_POST['kapasitas'] ?? 0);

    $deskripsi =
        trim($_POST['deskripsi'] ?? '');


    if (
        $id &&
        $nama_fasilitas !== '' &&
        $tipe !== '' &&
        $lokasi !== '' &&
        $kapasitas > 0 &&
        $deskripsi !== ''
    ) {

        $stmt = $pdo->prepare("
            UPDATE facilities
            SET
                nama_fasilitas = :nama,
                tipe = :tipe,
                lokasi = :lokasi,
                kapasitas = :kapasitas,
                deskripsi = :deskripsi
            WHERE id = :id
        ");

        $stmt->execute([
            ':nama' =>
                $nama_fasilitas,

            ':tipe' =>
                $tipe,

            ':lokasi' =>
                $lokasi,

            ':kapasitas' =>
                $kapasitas,

            ':deskripsi' =>
                $deskripsi,

            ':id' =>
                $id
        ]);

        header(
            "Location: dashboard.php"
        );

        exit();
    }
}


// ========================================
// AMBIL ID FASILITAS
// ========================================

$id_fasilitas =
    $_GET['id'] ?? $_POST['id'] ?? null;


if (!$id_fasilitas) {
    die("ID Fasilitas tidak ditemukan.");
}


// ========================================
// AMBIL DATA FASILITAS
// ========================================

$stmt = $pdo->prepare("
    SELECT *
    FROM facilities
    WHERE id = :id
");

$stmt->execute([
    ':id' =>
        $id_fasilitas
]);

$fasilitas =
    $stmt->fetch();


if (!$fasilitas) {
    die("Data fasilitas tidak ditemukan.");
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
        Edit Fasilitas | ReservasiKampus
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

            --red: #c23b3b;
            --red-bg: #fff3f3;
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


        /* ========================================
           NAVBAR
        ======================================== */

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

            display:
                flex;

            align-items:
                center;

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

            padding:
                9px 12px;

            color:
                var(--blue-dark);

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
                    1040px,
                    calc(100% - 40px)
                );

            margin:
                auto;

            padding:
                38px 0
                70px;
        }


        /* ========================================
           PAGE HEADER
        ======================================== */

        .breadcrumb {

            display:
                inline-flex;

            align-items:
                center;

            color:
                var(--blue);

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                10px;
        }


        .page-header {

            margin-bottom:
                22px;
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
           LAYOUT
        ======================================== */

        .edit-layout {

            display:
                grid;

            grid-template-columns:
                .8fr
                1.2fr;

            gap:
                18px;

            align-items:
                stretch;
        }


        /* ========================================
           LEFT INFORMATION PANEL
        ======================================== */

        .info-panel {

            min-height:
                560px;

            padding:
                30px;

            border-radius:
                17px;

            background:
                linear-gradient(
                    145deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color:
                white;

            display:
                flex;

            flex-direction:
                column;

            justify-content:
                space-between;
        }


        .info-label {

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
                    0.85
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
                28px;

            line-height:
                1.25;

            margin-bottom:
                13px;
        }


        .info-panel > div:first-child p {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.74
                );

            font-size:
                12px;

            line-height:
                1.7;
        }


        /* ========================================
           CURRENT DATA
        ======================================== */

        .current-data {

            margin-top:
                27px;

            padding-top:
                20px;

            border-top:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.15
                );
        }


        .current-title {

            font-size:
                10px;

            font-weight:
                700;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.68
                );

            margin-bottom:
                14px;

            text-transform:
                uppercase;
        }


        .data-row {

            padding:
                10px 0;

            border-bottom:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.10
                );
        }


        .data-row:last-child {
            border-bottom:
                none;
        }


        .data-label {

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.58
                );

            font-size:
                9px;

            margin-bottom:
                3px;
        }


        .data-value {

            color:
                white;

            font-size:
                12px;

            font-weight:
                700;

            line-height:
                1.4;
        }


        .info-note {

            padding:
                13px;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.13
                );

            border-radius:
                10px;

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
                    0.70
                );

            font-size:
                9px;

            line-height:
                1.6;
        }


        /* ========================================
           FORM CARD
        ======================================== */

        .form-card {

            padding:
                30px;

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius:
                17px;

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

            padding-bottom:
                18px;

            margin-bottom:
                24px;

            border-bottom:
                1px solid
                var(--border);
        }


        .form-header h2 {

            font-size:
                19px;

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


        /* ========================================
           FORM
        ======================================== */

        .form-group {

            margin-bottom:
                18px;
        }


        .form-group label {

            display:
                block;

            color:
                #364152;

            font-size:
                11px;

            font-weight:
                700;

            margin-bottom:
                7px;
        }


        .form-control {

            width:
                100%;

            height:
                44px;

            padding:
                0 13px;

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


        textarea.form-control {

            height:
                125px;

            padding:
                12px 13px;

            resize:
                vertical;

            line-height:
                1.6;
        }


        .form-control:focus {

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


        .form-control::placeholder {

            color:
                #9aa5b1;
        }


        .form-hint {

            margin-top:
                6px;

            color:
                #98a2b3;

            font-size:
                9px;

            line-height:
                1.5;
        }


        /* ========================================
           TWO COLUMNS
        ======================================== */

        .two-columns {

            display:
                grid;

            grid-template-columns:
                1fr
                1fr;

            gap:
                12px;
        }


        /* ========================================
           FORM ACTION
        ======================================== */

        .form-actions {

            display:
                flex;

            justify-content:
                flex-end;

            align-items:
                center;

            gap:
                10px;

            margin-top:
                7px;

            padding-top:
                18px;

            border-top:
                1px solid
                var(--border);
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
                #d6dee8;

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
        }


        .save-button {

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


        .save-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* ========================================
           ERROR
        ======================================== */

        .error-message {

            display:
                none;

            margin-bottom:
                18px;

            padding:
                12px 14px;

            border:
                1px solid
                #f0caca;

            border-radius:
                9px;

            background:
                var(--red-bg);

            color:
                var(--red);

            font-size:
                10px;

            line-height:
                1.6;
        }


        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 850px) {

            .edit-layout {

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


            .two-columns {

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
            .save-button {

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

    <div class="page-header">


        <a
            href="dashboard.php"
            class="breadcrumb"
        >
            ← Kembali ke Dashboard
        </a>


        <h1>
            Edit Fasilitas
        </h1>


        <p>
            Perbarui informasi fasilitas kampus
            agar data yang ditampilkan tetap akurat.
        </p>


    </div>



    <!-- ======================================
         LAYOUT
    ======================================= -->

    <div class="edit-layout">


        <!-- ==================================
             LEFT PANEL
        =================================== -->

        <aside class="info-panel">


            <div>


                <div class="info-label">
                    KELOLA FASILITAS
                </div>


                <h2>
                    Perbarui informasi fasilitas.
                </h2>


                <p>
                    Ubah nama, tipe, lokasi, kapasitas,
                    atau deskripsi fasilitas sesuai
                    kondisi terbaru.
                </p>


                <!-- DATA SAAT INI -->

                <div class="current-data">


                    <div class="current-title">
                        Data Saat Ini
                    </div>


                    <div class="data-row">

                        <div class="data-label">
                            Nama Fasilitas
                        </div>

                        <div class="data-value">
                            <?php
                            echo htmlspecialchars(
                                $fasilitas[
                                    'nama_fasilitas'
                                ]
                            );
                            ?>
                        </div>

                    </div>


                    <div class="data-row">

                        <div class="data-label">
                            Tipe
                        </div>

                        <div class="data-value">
                            <?php
                            echo htmlspecialchars(
                                ucfirst(
                                    $fasilitas['tipe']
                                )
                            );
                            ?>
                        </div>

                    </div>


                    <div class="data-row">

                        <div class="data-label">
                            Lokasi
                        </div>

                        <div class="data-value">
                            <?php
                            echo htmlspecialchars(
                                $fasilitas['lokasi']
                            );
                            ?>
                        </div>

                    </div>


                    <div class="data-row">

                        <div class="data-label">
                            Kapasitas
                        </div>

                        <div class="data-value">
                            <?php
                            echo htmlspecialchars(
                                $fasilitas['kapasitas']
                            );
                            ?>
                            orang / unit
                        </div>

                    </div>


                </div>


            </div>


            <div class="info-note">

                Perubahan akan langsung tersimpan
                ke data fasilitas setelah tombol
                <strong>Simpan Perubahan</strong>
                ditekan.

            </div>


        </aside>



        <!-- ==================================
             FORM
        =================================== -->

        <section class="form-card">


            <div class="form-header">

                <h2>
                    Informasi Fasilitas
                </h2>


                <p>
                    Pastikan semua informasi sudah
                    sesuai sebelum menyimpan perubahan.
                </p>

            </div>



            <!-- ERROR -->

            <div
                id="errorMessage"
                class="error-message"
            ></div>



            <form
                action=""
                method="POST"
                id="editFacilityForm"
                novalidate
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



                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama_fasilitas">
                        Nama Fasilitas
                    </label>


                    <input
                        type="text"
                        id="nama_fasilitas"
                        name="nama_fasilitas"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $fasilitas[
                                    'nama_fasilitas'
                                ]
                            );
                        ?>"
                        placeholder="Contoh: Ruang Kuliah E101"
                        required
                    >


                    <div class="form-hint">

                        Masukkan nama fasilitas
                        yang mudah dikenali.

                    </div>

                </div>



                <!-- TIPE -->

                <div class="form-group">

                    <label for="tipe">
                        Tipe Fasilitas
                    </label>


                    <select
                        id="tipe"
                        name="tipe"
                        class="form-control"
                        required
                    >

                        <option
                            value=""
                            disabled
                        >
                            Pilih tipe fasilitas
                        </option>


                        <option
                            value="ruang kelas"
                            <?php
                            echo
                            $fasilitas['tipe']
                                === 'ruang kelas'
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
                            $fasilitas['tipe']
                                === 'aula'
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
                            $fasilitas['tipe']
                                === 'laboratorium'
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
                            $fasilitas['tipe']
                                === 'alat'
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
                            $fasilitas['tipe']
                                === 'lapangan'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Lapangan
                        </option>


                    </select>

                </div>



                <!-- LOKASI + KAPASITAS -->

                <div class="two-columns">


                    <div class="form-group">

                        <label for="lokasi">
                            Lokasi
                        </label>


                        <input
                            type="text"
                            id="lokasi"
                            name="lokasi"
                            class="form-control"
                            value="<?php
                                echo htmlspecialchars(
                                    $fasilitas[
                                        'lokasi'
                                    ]
                                );
                            ?>"
                            placeholder="Contoh: Gedung E Lantai 1"
                            required
                        >

                    </div>



                    <div class="form-group">

                        <label for="kapasitas">
                            Kapasitas
                        </label>


                        <input
                            type="number"
                            id="kapasitas"
                            name="kapasitas"
                            class="form-control"
                            min="1"
                            value="<?php
                                echo htmlspecialchars(
                                    $fasilitas[
                                        'kapasitas'
                                    ]
                                );
                            ?>"
                            placeholder="40"
                            required
                        >


                        <div class="form-hint">
                            Orang atau unit.
                        </div>

                    </div>


                </div>



                <!-- DESKRIPSI -->

                <div class="form-group">

                    <label for="deskripsi">
                        Deskripsi
                    </label>


                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        class="form-control"
                        placeholder="Jelaskan fasilitas dan perlengkapan yang tersedia..."
                        required
                    ><?php
                    echo htmlspecialchars(
                        $fasilitas[
                            'deskripsi'
                        ]
                    );
                    ?></textarea>


                    <div class="form-hint">

                        Berikan informasi singkat
                        tentang fasilitas tersebut.

                    </div>

                </div>



                <!-- ACTION -->

                <div class="form-actions">


                    <a
                        href="dashboard.php"
                        class="cancel-button"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="save-button"
                    >
                        Simpan Perubahan
                    </button>


                </div>


            </form>


        </section>


    </div>


</main>



<script>

    const form =
        document.getElementById(
            'editFacilityForm'
        );

    const nama =
        document.getElementById(
            'nama_fasilitas'
        );

    const tipe =
        document.getElementById(
            'tipe'
        );

    const lokasi =
        document.getElementById(
            'lokasi'
        );

    const kapasitas =
        document.getElementById(
            'kapasitas'
        );

    const deskripsi =
        document.getElementById(
            'deskripsi'
        );


    form.addEventListener(
        'submit',
        function (event) {

            // Reset error
            nama.setCustomValidity('');
            tipe.setCustomValidity('');
            lokasi.setCustomValidity('');
            kapasitas.setCustomValidity('');
            deskripsi.setCustomValidity('');


            // =============================
            // NAMA
            // =============================

            if (!nama.value.trim()) {

                event.preventDefault();

                nama.setCustomValidity(
                    'Nama fasilitas wajib diisi.'
                );

                nama.reportValidity();

                return;
            }


            // =============================
            // TIPE
            // =============================

            if (!tipe.value) {

                event.preventDefault();

                tipe.setCustomValidity(
                    'Silakan pilih tipe fasilitas.'
                );

                tipe.reportValidity();

                return;
            }


            // =============================
            // LOKASI
            // =============================

            if (!lokasi.value.trim()) {

                event.preventDefault();

                lokasi.setCustomValidity(
                    'Lokasi fasilitas wajib diisi.'
                );

                lokasi.reportValidity();

                return;
            }


            // =============================
            // KAPASITAS
            // =============================

            if (
                !kapasitas.value ||
                Number(kapasitas.value) <= 0
            ) {

                event.preventDefault();

                kapasitas.setCustomValidity(
                    'Kapasitas harus lebih dari 0.'
                );

                kapasitas.reportValidity();

                return;
            }


            // =============================
            // DESKRIPSI
            // =============================

            if (!deskripsi.value.trim()) {

                event.preventDefault();

                deskripsi.setCustomValidity(
                    'Deskripsi fasilitas wajib diisi.'
                );

                deskripsi.reportValidity();

                return;
            }


            // Semua valid
            nama.setCustomValidity('');
            tipe.setCustomValidity('');
            lokasi.setCustomValidity('');
            kapasitas.setCustomValidity('');
            deskripsi.setCustomValidity('');

        }
    );

</script>


</body>

</html>