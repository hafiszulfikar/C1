<?php
// views/admin/tambah_petugas.php

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
        Daftarkan Petugas | ReservasiKampus
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
                38px 0 70px;
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

        .staff-layout {

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
                520px;

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


        .intro-text {

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
                    0.13
                );

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
           NOTE
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
                19px;
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
                45px;

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
           PASSWORD WRAPPER
        ======================================== */

        .password-wrapper {

            position:
                relative;
        }


        .password-wrapper input {

            padding-right:
                45px;
        }


        .password-toggle {

            position:
                absolute;

            right:
                10px;

            top:
                50%;

            transform:
                translateY(-50%);

            border:
                none;

            background:
                transparent;

            color:
                #7b8794;

            font-size:
                10px;

            font-weight:
                700;

            cursor:
                pointer;

            padding:
                5px;

        }


        .password-toggle:hover {

            color:
                var(--blue);
        }


        /* ========================================
           ACCOUNT INFO
        ======================================== */

        .account-info {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                10px;

            padding:
                13px;

            margin-bottom:
                20px;

            border:
                1px solid
                #dceaf5;

            border-radius:
                10px;

            background:
                #f5f9fd;

            color:
                #667085;

            font-size:
                9px;

            line-height:
                1.6;
        }


        .account-info strong {

            color:
                var(--blue-dark);
        }


        /* ========================================
           ACTION
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


        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 850px) {

            .staff-layout {

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
            Daftarkan Petugas
        </h1>


        <p>
            Buat akun petugas baru untuk membantu
            mengelola reservasi dan laporan fasilitas kampus.
        </p>


    </section>



    <!-- ======================================
         CONTENT
    ======================================= -->

    <div class="staff-layout">


        <!-- ==================================
             LEFT PANEL
        =================================== -->

        <aside class="info-panel">


            <div>


                <div class="info-label">
                    MANAJEMEN PETUGAS
                </div>


                <h2>
                    Tambahkan petugas baru.
                </h2>


                <p class="intro-text">

                    Buat akun khusus untuk petugas
                    yang bertanggung jawab menangani
                    reservasi dan laporan kerusakan.

                </p>


                <div class="steps">


                    <div class="step">


                        <div class="step-number">
                            01
                        </div>


                        <div>

                            <strong>
                                Masukkan nama
                            </strong>

                            <span>
                                Gunakan nama lengkap
                                petugas.
                            </span>

                        </div>


                    </div>


                    <div class="step">


                        <div class="step-number">
                            02
                        </div>


                        <div>

                            <strong>
                                Masukkan email
                            </strong>

                            <span>
                                Gunakan email yang
                                belum terdaftar.
                            </span>

                        </div>


                    </div>


                    <div class="step">


                        <div class="step-number">
                            03
                        </div>


                        <div>

                            <strong>
                                Buat password
                            </strong>

                            <span>
                                Password akan digunakan
                                untuk login petugas.
                            </span>

                        </div>


                    </div>


                </div>


            </div>


            <div class="info-note">

                Akun petugas yang dibuat oleh Admin
                langsung berstatus <strong>Verified</strong>
                sehingga dapat digunakan untuk login.

            </div>


        </aside>



        <!-- ==================================
             FORM
        =================================== -->

        <section class="form-card">


            <div class="form-header">


                <h2>
                    Informasi Akun Petugas
                </h2>


                <p>
                    Lengkapi data berikut untuk
                    membuat akun petugas.
                </p>


            </div>



            <div class="account-info">


                <div>


                    <strong>
                        Akses Petugas
                    </strong>


                    <br>


                    Akun ini otomatis mendapatkan
                    role <strong>Petugas</strong> dan
                    langsung dapat digunakan setelah
                    dibuat oleh Admin.

                </div>


            </div>



            <form
                action="../../app/controllers/process_tambah_petugas.php"
                method="POST"
                id="staffForm"
                novalidate
            >


                <!-- NAMA -->

                <div class="form-group">


                    <label for="nama">
                        Nama Lengkap Petugas
                    </label>


                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        placeholder="Contoh: Ahmad Fauzan"
                        autocomplete="name"
                        required
                    >


                    <div class="form-hint">

                        Masukkan nama lengkap petugas.

                    </div>


                </div>



                <!-- EMAIL -->

                <div class="form-group">


                    <label for="email">
                        Email Petugas
                    </label>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="contoh@email.com"
                        autocomplete="email"
                        required
                    >


                    <div class="form-hint">

                        Email harus belum digunakan
                        oleh akun lain.

                    </div>


                </div>



                <!-- PASSWORD -->

                <div class="form-group">


                    <label for="password">
                        Password Akun
                    </label>


                    <div class="password-wrapper">


                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Buat password"
                            autocomplete="new-password"
                            required
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                        >
                            Lihat
                        </button>


                    </div>


                    <div class="form-hint">

                        Gunakan password yang mudah
                        diingat oleh petugas namun tetap aman.

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
                        class="submit-button"
                    >
                        Daftarkan Petugas
                    </button>


                </div>


            </form>


        </section>


    </div>


</main>



<script>

    const form =
        document.getElementById(
            'staffForm'
        );

    const nama =
        document.getElementById(
            'nama'
        );

    const email =
        document.getElementById(
            'email'
        );

    const password =
        document.getElementById(
            'password'
        );

    const passwordToggle =
        document.getElementById(
            'passwordToggle'
        );


    // =====================================
    // SHOW / HIDE PASSWORD
    // =====================================

    passwordToggle.addEventListener(
        'click',
        function () {

            if (
                password.type ===
                'password'
            ) {

                password.type =
                    'text';

                passwordToggle.textContent =
                    'Sembunyikan';

            } else {

                password.type =
                    'password';

                passwordToggle.textContent =
                    'Lihat';

            }

        }
    );


    // =====================================
    // RESET ERROR
    // =====================================

    nama.addEventListener(
        'input',
        function () {

            nama.setCustomValidity('');

        }
    );


    email.addEventListener(
        'input',
        function () {

            email.setCustomValidity('');

        }
    );


    password.addEventListener(
        'input',
        function () {

            password.setCustomValidity('');

        }
    );


    // =====================================
    // VALIDASI
    // =====================================

    form.addEventListener(
        'submit',
        function (event) {

            nama.setCustomValidity('');
            email.setCustomValidity('');
            password.setCustomValidity('');


            // ===============================
            // NAMA
            // ===============================

            if (!nama.value.trim()) {

                event.preventDefault();

                nama.setCustomValidity(
                    'Nama lengkap petugas wajib diisi.'
                );

                nama.reportValidity();

                return;
            }


            // ===============================
            // EMAIL
            // ===============================

            if (!email.value.trim()) {

                event.preventDefault();

                email.setCustomValidity(
                    'Email petugas wajib diisi.'
                );

                email.reportValidity();

                return;
            }


            if (
                !email.checkValidity()
            ) {

                event.preventDefault();

                email.setCustomValidity(
                    'Silakan masukkan alamat email yang valid.'
                );

                email.reportValidity();

                return;
            }


            // ===============================
            // PASSWORD
            // ===============================

            if (!password.value.trim()) {

                event.preventDefault();

                password.setCustomValidity(
                    'Password akun wajib diisi.'
                );

                password.reportValidity();

                return;
            }


            if (
                password.value.length < 6
            ) {

                event.preventDefault();

                password.setCustomValidity(
                    'Password minimal 6 karakter.'
                );

                password.reportValidity();

                return;
            }


            // ===============================
            // SEMUA VALID
            // ===============================

            nama.setCustomValidity('');
            email.setCustomValidity('');
            password.setCustomValidity('');

        }
    );

</script>


</body>

</html>