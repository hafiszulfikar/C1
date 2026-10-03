<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register | ReservasiKampus</title>

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

            --text: #172033;
            --muted: #6b7280;

            --border: #e3e9f0;

            --bg: #f5f8fc;

            --white: #ffffff;
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

            gap: 8px;
        }


        .nav-link {

            color:
                var(--blue-dark);

            padding:
                9px 12px;

            font-size: 12px;

            font-weight: 700;
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

            transition: 0.2s;
        }


        .nav-button:hover {

            background:
                #052f59;
        }


        /* =====================================
           MAIN
        ===================================== */

        .main {

            min-height:
                calc(
                    100vh - 72px
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding:
                45px 20px;
        }


        /* =====================================
           REGISTER CARD
        ===================================== */

        .register-card {

            width:
                min(
                    880px,
                    100%
                );

            min-height: 560px;

            display: grid;

            grid-template-columns:
                0.92fr
                1.08fr;

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius:
                20px;

            overflow: hidden;

            box-shadow:
                0 18px 50px
                rgba(
                    25,
                    56,
                    88,
                    0.10
                );
        }


        /* =====================================
           LEFT PANEL
        ===================================== */

        .register-intro {

            background:
                linear-gradient(
                    145deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color: white;

            padding:
                48px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .intro-logo {

            font-size: 20px;

            font-weight: 700;

            margin-bottom: 28px;
        }


        .intro-logo span {

            font-weight: 400;
        }


        .intro-label {

            display: inline-flex;

            align-self: flex-start;

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

            border-radius: 999px;

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
                    0.84
                );

            font-size: 10px;

            margin-bottom: 20px;
        }


        .register-intro h1 {

            font-size: 31px;

            line-height: 1.2;

            margin-bottom: 14px;
        }


        .register-intro p {

            max-width: 310px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.76
                );

            font-size: 13px;

            line-height: 1.7;
        }


        .intro-line {

            width: 52px;

            height: 3px;

            margin-top: 26px;

            border-radius: 999px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.80
                );
        }


        /* =====================================
           FORM AREA
        ===================================== */

        .register-form-area {

            padding:
                42px 52px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .register-form-area h2 {

            font-size: 26px;

            margin-bottom: 7px;

            color:
                var(--text);
        }


        .form-subtitle {

            color:
                var(--muted);

            font-size: 12px;

            margin-bottom: 24px;

            line-height: 1.6;
        }


        /* =====================================
           FORM
        ===================================== */

        .form-group {

            margin-bottom: 15px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color:
                #364152;

            font-size: 11px;

            font-weight: 700;
        }


        .form-group input,
        .form-group select {

            width: 100%;

            height: 44px;

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

            font-size: 12px;

            outline: none;

            transition:
                .2s;
        }


        .form-group input::placeholder {

            color:
                #9aa5b1;
        }


        .form-group input:focus,
        .form-group select:focus {

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


        /* =====================================
           BUTTON
        ===================================== */

        .submit-button {

            width: 100%;

            height: 45px;

            margin-top: 5px;

            border: none;

            border-radius:
                9px;

            background:
                var(--blue-dark);

            color: white;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s;
        }


        .submit-button:hover {

            background:
                #052f59;

            transform:
                translateY(-1px);
        }


        /* =====================================
           LOGIN LINK
        ===================================== */

        .login-text {

            margin-top:
                18px;

            text-align:
                center;

            color:
                var(--muted);

            font-size:
                11px;
        }


        .login-text a {

            color:
                var(--blue);

            font-weight:
                700;
        }


        .login-text a:hover {

            text-decoration:
                underline;
        }


        /* =====================================
           NOTE
        ===================================== */

        .note {

            margin-top:
                14px;

            padding:
                10px 12px;

            background:
                #f5f9fd;

            border:
                1px solid
                #dceaf5;

            border-radius:
                9px;

            color:
                #687789;

            font-size:
                10px;

            line-height:
                1.6;
        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 760px) {

            .register-card {

                grid-template-columns:
                    1fr;

            }


            .register-intro {

                padding:
                    35px 32px;
            }


            .register-intro h1 {

                font-size:
                    26px;
            }


            .register-form-area {

                padding:
                    38px 32px;
            }

        }


        @media (max-width: 500px) {

            .navbar-inner {

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

                padding:
                    25px 13px;
            }


            .register-card {

                border-radius:
                    16px;
            }

        }

    </style>

</head>


<body>


<!-- =====================================
     NAVBAR
===================================== -->

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
                href="login.php"
                class="nav-button"
            >
                Login
            </a>

        </div>

    </div>

</header>



<!-- =====================================
     MAIN
===================================== -->

<main class="main">


    <div class="register-card">


        <!-- =================================
             LEFT INTRO
        ================================== -->

        <section class="register-intro">


            <div class="intro-logo">

                Reservasi<span>Kampus</span>

            </div>


            <div class="intro-label">

                SISTEM RESERVASI FASILITAS KAMPUS

            </div>


            <h1>

                Mulai reservasi
                fasilitas kampus.

            </h1>


            <p>

                Buat akun ReservasiKampus untuk
                menemukan ruangan, melihat jadwal,
                dan mengajukan reservasi untuk
                kegiatan kampusmu.

            </p>





        </section>



        <!-- =================================
             REGISTER FORM
        ================================== -->

        <section class="register-form-area">


            <h2>
                Buat akun
            </h2>


            <p class="form-subtitle">

                Lengkapi data berikut untuk
                membuat akun ReservasiKampus.

            </p>


            <form
                action="../../app/controllers/process_register.php"
                method="POST"
            >


                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama">
                        Nama Lengkap
                    </label>


                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        required
                    >

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Buat password"
                        autocomplete="new-password"
                        required
                    >

                </div>



                <!-- TIPE PENGGUNA -->

                <div class="form-group">

                    <label for="tipe_pengguna">
                        Status Sivitas
                    </label>


                    <select
                        id="tipe_pengguna"
                        name="tipe_pengguna"
                        required
                    >

                        <option
                            value=""
                            disabled
                            selected
                        >
                            Pilih status
                        </option>


                        <option value="mahasiswa">
                            Mahasiswa
                        </option>


                        <option value="dosen">
                            Dosen
                        </option>


                        <option value="staf">
                            Staf
                        </option>

                    </select>

                </div>



                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="submit-button"
                >
                    Create account
                </button>


            </form>



            <!-- LOGIN -->

            <div class="login-text">

                Sudah punya akun?

                <a href="login.php">
                    Login sekarang
                </a>

            </div>



            <!-- NOTE -->

            <div class="note">

                Akun baru akan berstatus
                <strong>Pending</strong>
                dan perlu diverifikasi admin
                sebelum dapat digunakan untuk login.

            </div>


        </section>


    </div>


</main>


</body>

</html>