<?php
// views/user/buat_laporan.php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Ambil semua fasilitas
$stmt = $pdo->query("
    SELECT id, nama_fasilitas
    FROM facilities
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

    <title>
        Laporkan Kerusakan | ReservasiKampus
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


        button,
        input,
        select,
        textarea {
            font-family: inherit;
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
            font-weight: 400;
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

            font-size:
                12px;

            font-weight:
                700;

            padding:
                9px 12px;
        }


        .logout-button {

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


        .logout-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* =====================================
           MAIN
        ===================================== */

        .main {

            width:
                min(
                    1040px,
                    calc(100% - 40px)
                );

            margin:
                auto;

            padding:
                38px 0 70px;
        }


        /* =====================================
           HEADER
        ===================================== */

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
                28px;

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


        /* =====================================
           LAYOUT
        ===================================== */

        .report-layout {

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


        /* =====================================
           LEFT PANEL
        ===================================== */

        .info-panel {

            min-height:
                570px;

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

            overflow:
                hidden;
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
                27px;

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
                    0.75
                );

            font-size:
                12px;

            line-height:
                1.7;
        }


        /* =====================================
           GUIDELINES
        ===================================== */

        .guidelines {

            display:
                flex;

            flex-direction:
                column;

            gap:
                14px;

            margin-top:
                28px;
        }


        .guideline {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                11px;
        }


        .guideline-number {

            flex-shrink:
                0;

            width:
                28px;

            height:
                28px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

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
                    0.12
                );

            font-size:
                9px;

            font-weight:
                700;
        }


        .guideline strong {

            display:
                block;

            font-size:
                11px;

            margin-bottom:
                2px;
        }


        .guideline span {

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

            padding:
                13px;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.12
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


        /* =====================================
           FORM CARD
        ===================================== */

        .form-card {

            background:
                white;

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


        /* =====================================
           FORM GROUP
        ===================================== */

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


        .form-group select,
        .form-group textarea {

            width:
                100%;

            border:
                1px solid
                #d6dee8;

            border-radius:
                9px;

            outline:
                none;

            background:
                white;

            color:
                var(--text);

            font-size:
                12px;

            transition:
                .2s;
        }


        .form-group select {

            height:
                44px;

            padding:
                0 13px;

            cursor:
                pointer;
        }


        .form-group textarea {

            min-height:
                135px;

            resize:
                vertical;

            padding:
                12px 13px;

            line-height:
                1.6;
        }


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


        .form-group textarea::placeholder {

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


        /* =====================================
           PHOTO UPLOAD
        ===================================== */

        .upload-box {

            border:
                1px dashed
                #cdd8e4;

            border-radius:
                11px;

            padding:
                17px;

            background:
                #fafcff;

            transition:
                .2s;

            cursor:
                pointer;
        }


        .upload-box:hover {

            border-color:
                var(--blue);

            background:
                #f5faff;
        }


        .upload-box input[type="file"] {

            width:
                100%;

            font-size:
                11px;

            color:
                #64748b;

            cursor:
                pointer;
        }


        .upload-title {

            font-size:
                11px;

            font-weight:
                700;

            color:
                #364152;

            margin-bottom:
                4px;
        }


        .upload-description {

            color:
                var(--muted);

            font-size:
                9px;

            margin-bottom:
                10px;

            line-height:
                1.5;
        }


        .file-name {

            display:
                none;

            margin-top:
                9px;

            padding:
                7px 9px;

            border-radius:
                7px;

            background:
                #eef7ff;

            color:
                var(--blue-dark);

            font-size:
                9px;

            line-height:
                1.5;
        }


        /* =====================================
           ACTIONS
        ===================================== */

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
                8px;
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


        /* =====================================
           BOTTOM NOTE
        ===================================== */

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


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 850px) {

            .report-layout {

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
            Laporkan Kerusakan
        </h1>


        <p>
            Bantu kami menjaga fasilitas kampus
            dengan melaporkan kerusakan yang kamu temukan.
        </p>


    </div>



    <!-- ======================================
         CONTENT
    ======================================= -->

    <div class="report-layout">


        <!-- ==================================
             LEFT INFO
        =================================== -->

        <aside class="info-panel">


            <div>


                <div class="info-label">
                    LAPORAN FASILITAS
                </div>


                <h2>
                    Temukan masalah?
                    Laporkan di sini.
                </h2>


                <p>
                    Berikan informasi yang jelas
                    agar petugas dapat segera
                    memeriksa dan menangani
                    fasilitas yang bermasalah.
                </p>


                <!-- GUIDELINES -->

                <div class="guidelines">


                    <div class="guideline">


                        <div class="guideline-number">
                            01
                        </div>


                        <div>

                            <strong>
                                Pilih fasilitas
                            </strong>

                            <span>
                                Tentukan ruangan atau
                                fasilitas yang mengalami
                                kerusakan.
                            </span>

                        </div>


                    </div>



                    <div class="guideline">


                        <div class="guideline-number">
                            02
                        </div>


                        <div>

                            <strong>
                                Jelaskan masalah
                            </strong>

                            <span>
                                Tuliskan kondisi kerusakan
                                dengan detail.
                            </span>

                        </div>


                    </div>



                    <div class="guideline">


                        <div class="guideline-number">
                            03
                        </div>


                        <div>

                            <strong>
                                Tambahkan foto
                            </strong>

                            <span>
                                Foto membantu petugas
                                memahami kondisi fasilitas.
                            </span>

                        </div>


                    </div>


                </div>


            </div>


            <div class="info-note">

                <strong>
                    Tips:
                </strong>

                Gunakan foto yang jelas dan
                deskripsi yang spesifik agar
                laporan lebih mudah ditindaklanjuti.

            </div>


        </aside>



        <!-- ==================================
             FORM
        =================================== -->

        <section class="form-card">


            <div class="form-header">

                <h2>
                    Detail Laporan
                </h2>


                <p>
                    Isi informasi berikut sebelum
                    mengirim laporan kerusakan.
                </p>

            </div>



            <form
                action="../../app/controllers/process_laporan.php"
                method="POST"
                enctype="multipart/form-data"
                novalidate
                id="reportForm"
            >


                <!-- ==================================
                     FACILITY
                =================================== -->

                <div class="form-group">

                    <label for="facility_id">
                        Fasilitas yang Rusak
                    </label>


                    <select
                        id="facility_id"
                        name="facility_id"
                        required
                    >

                        <option
                            value=""
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

                        Pilih fasilitas yang mengalami
                        masalah atau kerusakan.

                    </div>

                </div>



                <!-- ==================================
                     CATEGORY
                =================================== -->

                <div class="form-group">

                    <label for="kategori">
                        Kategori Masalah
                    </label>


                    <select
                        id="kategori"
                        name="kategori"
                        required
                    >

                        <option
                            value="Listrik/Elektronik"
                        >
                            Listrik / Elektronik
                        </option>


                        <option
                            value="Infrastruktur Bangunan"
                        >
                            Infrastruktur Bangunan
                        </option>


                        <option
                            value="Air/Sanitasi"
                        >
                            Air / Sanitasi
                        </option>


                        <option
                            value="Perabotan/Furnitur"
                        >
                            Perabotan / Furnitur
                        </option>


                        <option
                            value="Lainnya"
                        >
                            Lainnya
                        </option>

                    </select>

                </div>



                <!-- ==================================
                     DESCRIPTION
                =================================== -->

                <div class="form-group">

                    <label for="deskripsi">
                        Deskripsi Kerusakan
                    </label>


                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Contoh: AC di Ruang E101 tidak menyala meskipun sudah dicoba beberapa kali."
                        required
                    ></textarea>


                    <div class="form-hint">

                        Jelaskan lokasi, kondisi,
                        dan masalah yang ditemukan
                        secara spesifik.

                    </div>

                </div>



                <!-- ==================================
                     PHOTO
                =================================== -->

                <div class="form-group">

                    <label for="foto">
                        Foto Kerusakan
                        <span
                            style="
                                font-weight:400;
                                color:#98a2b3;
                            "
                        >
                            (Opsional)
                        </span>
                    </label>


                    <div
                        class="upload-box"
                        onclick="document.getElementById('foto').click()"
                    >

                        <div class="upload-title">

                            Tambahkan foto kerusakan

                        </div>


                        <div class="upload-description">

                            Format JPG atau PNG.
                            Ukuran maksimal 2 MB.

                        </div>


                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            onclick="event.stopPropagation();"
                        >


                        <div
                            id="fileName"
                            class="file-name"
                        ></div>

                    </div>


                </div>



                <!-- ==================================
                     ACTION
                =================================== -->

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
                    >
                        Kirim Laporan
                    </button>


                </div>


            </form>



            <!-- NOTE -->

            <div class="bottom-note">

                <strong>Catatan:</strong>

                Laporan akan berstatus
                <strong>Baru</strong>
                setelah dikirim dan kemudian
                dapat diproses oleh petugas.

            </div>


        </section>


    </div>


</main>


<script>

    const form =
        document.getElementById('reportForm');

    const facility =
        document.getElementById('facility_id');

    const kategori =
        document.getElementById('kategori');

    const deskripsi =
        document.getElementById('deskripsi');

    const foto =
        document.getElementById('foto');

    const fileName =
        document.getElementById('fileName');


    // =====================================
    // TAMPILKAN NAMA FILE
    // =====================================

    foto.addEventListener(
        'change',
        function () {

            fileName.style.display = 'none';
            fileName.textContent = '';

            if (!foto.files.length) {
                return;
            }

            const file =
                foto.files[0];

            const maxSize =
                2 * 1024 * 1024;


            if (file.size > maxSize) {

                foto.value = '';

                fileName.textContent =
                    'Ukuran file terlalu besar. Maksimal 2 MB.';

                fileName.style.display =
                    'block';

                fileName.style.background =
                    '#fff3f3';

                fileName.style.color =
                    '#b42318';

                return;
            }


            const allowedTypes = [
                'image/jpeg',
                'image/png'
            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                foto.value = '';

                fileName.textContent =
                    'Format file tidak didukung. Gunakan JPG atau PNG.';

                fileName.style.display =
                    'block';

                fileName.style.background =
                    '#fff3f3';

                fileName.style.color =
                    '#b42318';

                return;
            }


            fileName.textContent =
                'File dipilih: ' +
                file.name;

            fileName.style.display =
                'block';

            fileName.style.background =
                '#eef7ff';

            fileName.style.color =
                '#063b70';

        }
    );


    // =====================================
    // HAPUS ERROR SAAT INPUT DIPERBAIKI
    // =====================================

    facility.addEventListener(
        'change',
        function () {
            facility.setCustomValidity('');
        }
    );


    kategori.addEventListener(
        'change',
        function () {
            kategori.setCustomValidity('');
        }
    );


    deskripsi.addEventListener(
        'input',
        function () {
            deskripsi.setCustomValidity('');
        }
    );


    // =====================================
    // VALIDASI SUBMIT
    // =====================================

    form.addEventListener(
        'submit',
        function (event) {

            facility.setCustomValidity('');
            kategori.setCustomValidity('');
            deskripsi.setCustomValidity('');


            // -------------------------------
            // FASILITAS
            // -------------------------------

            if (!facility.value) {

                event.preventDefault();

                facility.setCustomValidity(
                    'Silakan pilih fasilitas yang mengalami kerusakan.'
                );

                facility.reportValidity();

                return;
            }


            // -------------------------------
            // KATEGORI
            // -------------------------------

            if (!kategori.value) {

                event.preventDefault();

                kategori.setCustomValidity(
                    'Silakan pilih kategori masalah.'
                );

                kategori.reportValidity();

                return;
            }


            // -------------------------------
            // DESKRIPSI
            // -------------------------------

            if (!deskripsi.value.trim()) {

                event.preventDefault();

                deskripsi.setCustomValidity(
                    'Silakan jelaskan kerusakan yang ditemukan.'
                );

                deskripsi.reportValidity();

                return;
            }


            // -------------------------------
            // FOTO
            // -------------------------------

            if (foto.files.length > 0) {

                const file =
                    foto.files[0];

                const maxSize =
                    2 * 1024 * 1024;


                if (file.size > maxSize) {

                    event.preventDefault();

                    foto.setCustomValidity(
                        'Ukuran foto maksimal 2 MB.'
                    );

                    foto.reportValidity();

                    return;
                }


                const allowedTypes = [
                    'image/jpeg',
                    'image/png'
                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    event.preventDefault();

                    foto.setCustomValidity(
                        'Format foto harus JPG atau PNG.'
                    );

                    foto.reportValidity();

                    return;
                }

            }


            foto.setCustomValidity('');

        }
    );

</script>


</body>

</html>