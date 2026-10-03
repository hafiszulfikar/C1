<?php
// views/admin/tambah_fasilitas.php

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';
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
        Tambah Fasilitas | ReservasiKampus
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

            color: var(--text);

            background:
                var(--bg);
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
           HEADER
        ======================================== */

        .page-header {

            margin-bottom:
                22px;
        }

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

        .facility-layout {

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
           INFO PANEL
        ======================================== */

        .info-panel {

            min-height:
                565px;

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

        .info-panel .intro-text {

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
           STEPS
        ======================================== */

        .steps {

            display:
                flex;

            flex-direction:
                column;

            gap:
                14px;

            margin-top:
                28px;
        }

        .step {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                11px;
        }

        .step-number {

            width:
                29px;

            height:
                29px;

            flex-shrink:
                0;

            border-radius:
                8px;

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
                    0.13
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                9px;

            font-weight:
                700;
        }

        .step strong {

            display:
                block;

            font-size:
                11px;

            margin-bottom:
                2px;
        }

        .step span {

            display:
                block;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.64
                );

            font-size:
                9px;

            line-height:
                1.5;
        }


        /* ========================================
           INFO NOTE
        ======================================== */

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
           FORM GROUP
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
                130px;

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
           ACTIONS
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
                6px;

            padding-top:
                18px;

            border-top:
                1px solid
                var(--border);
        }

        .cancel-button {

            height:
                44px;

            padding:
                0 17px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

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
           RESPONSIVE
        ======================================== */

        @media (max-width: 850px) {

            .facility-layout {

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


    <!-- PAGE HEADER -->

    <section class="page-header">


        <a
            href="dashboard.php"
            class="breadcrumb"
        >
            ← Kembali ke Dashboard
        </a>


        <h1>
            Tambah Fasilitas
        </h1>


        <p>
            Tambahkan ruangan atau fasilitas baru
            yang dapat digunakan dalam sistem reservasi.
        </p>


    </section>



    <!-- ======================================
         LAYOUT
    ======================================= -->

    <div class="facility-layout">


        <!-- ==================================
             LEFT PANEL
        =================================== -->

        <aside class="info-panel">


            <div>


                <div class="info-label">
                    KELOLA FASILITAS
                </div>


                <h2>
                    Tambahkan fasilitas baru.
                </h2>


                <p class="intro-text">

                    Masukkan informasi fasilitas
                    dengan lengkap agar pengguna
                    dapat menemukan dan melakukan
                    reservasi dengan mudah.

                </p>


                <div class="steps">


                    <div class="step">

                        <div class="step-number">
                            01
                        </div>

                        <div>

                            <strong>
                                Beri nama fasilitas
                            </strong>

                            <span>
                                Gunakan nama yang mudah
                                dikenali pengguna.
                            </span>

                        </div>

                    </div>


                    <div class="step">

                        <div class="step-number">
                            02
                        </div>

                        <div>

                            <strong>
                                Tentukan tipe dan lokasi
                            </strong>

                            <span>
                                Pilih kategori dan masukkan
                                lokasi fasilitas.
                            </span>

                        </div>

                    </div>


                    <div class="step">

                        <div class="step-number">
                            03
                        </div>

                        <div>

                            <strong>
                                Lengkapi kapasitas
                            </strong>

                            <span>
                                Masukkan kapasitas orang
                                atau jumlah unit.
                            </span>

                        </div>

                    </div>


                    <div class="step">

                        <div class="step-number">
                            04
                        </div>

                        <div>

                            <strong>
                                Berikan deskripsi
                            </strong>

                            <span>
                                Jelaskan fasilitas dan
                                perlengkapan yang tersedia.
                            </span>

                        </div>

                    </div>


                </div>


            </div>


            <div class="info-note">

                Setelah disimpan, fasilitas baru akan
                masuk ke daftar fasilitas dan dapat
                digunakan dalam sistem reservasi.

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
                    Isi semua informasi fasilitas
                    sebelum menyimpannya.
                </p>

            </div>



            <form
                action="../../app/controllers/process_tambah_fasilitas.php"
                method="POST"
                id="facilityForm"
                novalidate
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
                        placeholder="Contoh: Ruang Kuliah E102"
                        required
                    >


                    <div class="form-hint">

                        Gunakan nama yang singkat
                        dan mudah dikenali.

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
                            selected
                            disabled
                        >
                            Pilih tipe fasilitas
                        </option>


                        <option value="ruang kelas">
                            Ruang Kelas
                        </option>


                        <option value="aula">
                            Aula
                        </option>


                        <option value="laboratorium">
                            Laboratorium
                        </option>


                        <option value="alat">
                            Alat
                        </option>


                        <option value="lapangan">
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
                        placeholder="Contoh: Ruang kelas dengan AC, proyektor, papan tulis, dan 40 kursi."
                        required
                    ></textarea>


                    <div class="form-hint">

                        Jelaskan fasilitas,
                        perlengkapan, atau informasi
                        penting lainnya.

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
                        Simpan Fasilitas
                    </button>


                </div>


            </form>


        </section>


    </div>


</main>


<script>

    const form =
        document.getElementById(
            'facilityForm'
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

            nama.setCustomValidity('');
            tipe.setCustomValidity('');
            lokasi.setCustomValidity('');
            kapasitas.setCustomValidity('');
            deskripsi.setCustomValidity('');


            // ============================
            // NAMA
            // ============================

            if (!nama.value.trim()) {

                event.preventDefault();

                nama.setCustomValidity(
                    'Nama fasilitas wajib diisi.'
                );

                nama.reportValidity();

                return;
            }


            // ============================
            // TIPE
            // ============================

            if (!tipe.value) {

                event.preventDefault();

                tipe.setCustomValidity(
                    'Silakan pilih tipe fasilitas.'
                );

                tipe.reportValidity();

                return;
            }


            // ============================
            // LOKASI
            // ============================

            if (!lokasi.value.trim()) {

                event.preventDefault();

                lokasi.setCustomValidity(
                    'Lokasi fasilitas wajib diisi.'
                );

                lokasi.reportValidity();

                return;
            }


            // ============================
            // KAPASITAS
            // ============================

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


            // ============================
            // DESKRIPSI
            // ============================

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