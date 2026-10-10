<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | ReservasiKampus</title>

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

            color: var(--blue-dark);

            font-size: 21px;

            font-weight: 700;

            letter-spacing: -0.4px;
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

            transition: .2s;
        }


        .nav-button:hover {

            background: #052f59;
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
                55px 20px;
        }


        /* =====================================
           LOGIN CARD
        ===================================== */

        .login-card {

            width:
                min(
                    850px,
                    100%
                );

            min-height: 490px;

            display: grid;

            grid-template-columns:
                0.92fr
                1.08fr;

            background:
                var(--white);

            border:
                1px solid
                var(--border);

            border-radius: 20px;

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
           LEFT
        ===================================== */

        .login-intro {

            background:
                linear-gradient(
                    145deg,
                    #073765 0%,
                    #0869b5 55%,
                    #118de4 100%
                );

            color: white;

            padding: 48px;

            display: flex;

            flex-direction: column;

            justify-content: center;
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

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.84
                );

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.08
                );

            font-size: 10px;

            margin-bottom: 20px;
        }


        .intro-logo {

            font-size: 20px;

            font-weight: 700;

            margin-bottom: 28px;
        }


        .intro-logo span {
            font-weight: 400;
        }


        .login-intro h1 {

            font-size: 31px;

            line-height: 1.2;

            margin-bottom: 14px;
        }


        .login-intro p {

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

            border-radius: 99px;

            background: rgba(
                255,
                255,
                255,
                0.80
            );
        }


        /* =====================================
           RIGHT
        ===================================== */

        .login-form-area {

            padding:
                55px 52px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .login-form-area h2 {

            font-size: 26px;

            margin-bottom: 7px;

            color: var(--text);
        }


        .form-subtitle {

            color: var(--muted);

            font-size: 12px;

            margin-bottom: 27px;

            line-height: 1.6;
        }


        /* =====================================
           FORM
        ===================================== */

        .form-group {

            margin-bottom: 18px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color:
                #364152;

            font-size: 11px;

            font-weight: 700;
        }


        .form-group input {

            width: 100%;

            height: 45px;

            padding:
                0 13px;

            border:
                1px solid
                #d6dee8;

            border-radius: 9px;

            background: white;

            color: var(--text);

            font-size: 12px;

            outline: none;

            transition: .2s;
        }


        .form-group input::placeholder {

            color:
                #9aa5b1;
        }


        .form-group input:focus {

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

            margin-top: 4px;

            border: none;

            border-radius: 9px;

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
           REGISTER
        ===================================== */

        .register-text {

            margin-top: 20px;

            text-align: center;

            color: var(--muted);

            font-size: 11px;
        }


        .register-text a {

            color:
                var(--blue);

            font-weight: 700;
        }


        .register-text a:hover {

            text-decoration:
                underline;
        }


        /* =====================================
           HOME LINK
        ===================================== */

        .home-link {

            display: inline-block;

            margin-top: 18px;

            text-align: center;

            color:
                #7b8794;

            font-size: 10px;
        }


        .home-link:hover {

            color:
                var(--blue);
        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 760px) {

            .login-card {

                grid-template-columns: 1fr;

            }


            .login-intro {

                padding:
                    35px 32px;
            }


            .login-intro h1 {

                font-size:
                    26px;
            }


            .login-form-area {

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


            .login-card {

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
                href="register.php"
                class="nav-button"
            >
                Daftar
            </a>

        </div>

    </div>

</header>



<!-- =====================================
     MAIN
===================================== -->

<main class="main">


    <div class="login-card">


        <!-- =================================
             LEFT INTRO
        ================================== -->

        <section class="login-intro">


            <div class="intro-logo">

                Reservasi<span>Kampus</span>

            </div>


            <div class="intro-label">

                SISTEM RESERVASI FASILITAS KAMPUS

            </div>


            <h1>

                Kelola reservasi
                ruangan dengan mudah.

            </h1>


            <p>

                Temukan fasilitas kampus,
                cek jadwal penggunaannya,
                dan ajukan reservasi dalam
                satu sistem.

            </p>




        </section>



        <!-- =================================
             LOGIN FORM
        ================================== -->

        <section class="login-form-area">


            <h2>
                Selamat datang kembali
            </h2>


            <p class="form-subtitle">

                Masuk ke akun ReservasiKampus
                untuk melanjutkan.

            </p>


            <form
                action="../../app/controllers/process_login.php"
                method="POST"
            >


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



                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                </div>



                <button
                    type="submit"
                    class="submit-button"
                >
                    Login
                </button>


            </form>


            <div class="register-text">

                Belum punya akun?

                <a href="register.php">
                    Daftar sekarang
                </a>

            </div>


            <a
                href="../../public/index.php"
                class="home-link"
            >
                ← Kembali ke Beranda

            </a>


        </section>


    </div>


</main>


</body>

</html>