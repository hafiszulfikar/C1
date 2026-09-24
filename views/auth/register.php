<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CampusReserve</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px 20px;

            font-family: Arial, Helvetica, sans-serif;
            color: white;

            background:
                linear-gradient(
                    135deg,
                    #06386b 0%,
                    #075ca8 50%,
                    #1188e8 100%
                );
        }

        .container {
            width: 470px;
            padding: 38px 42px 34px;

            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 24px;

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .logo {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 28px;
        }

        .logo span {
            font-weight: 400;
        }

        .title {
            margin-bottom: 25px;
        }

        .title h1 {
            font-size: 29px;
            margin-bottom: 8px;
        }

        .title p {
            color: rgba(255, 255, 255, 0.70);
            font-size: 13px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;

            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.88);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            height: 44px;
            padding: 0 13px;

            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 10px;

            background: rgba(255, 255, 255, 0.95);
            color: #1f2937;

            font-size: 13px;
            outline: none;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.10);
        }

        .btn {
            width: 100%;
            height: 46px;

            margin-top: 5px;

            border: none;
            border-radius: 10px;

            background: #063b70;
            color: white;

            font-size: 13px;
            font-weight: 700;

            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #052f59;
            transform: translateY(-1px);
        }

        .login {
            margin-top: 19px;
            text-align: center;

            color: rgba(255, 255, 255, 0.65);
            font-size: 11px;
        }

        .login a {
            color: white;
            font-weight: 700;
            text-decoration: none;
        }

        .login a:hover {
            text-decoration: underline;
        }

        .note {
            margin-top: 15px;
            padding: 10px 12px;

            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 9px;

            background: rgba(255, 255, 255, 0.06);

            color: rgba(255, 255, 255, 0.68);
            font-size: 10px;
            line-height: 1.5;
        }

        @media (max-width: 500px) {
            .container {
                width: 100%;
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="logo">
        Campus<span>Reserve</span>
    </div>

    <div class="title">
        <h1>Register</h1>

        <p>
            Buat akun untuk mulai melakukan reservasi
            fasilitas kampus.
        </p>
    </div>

    <form
        action="../../app/controllers/process_register.php"
        method="POST"
    >

        <div class="form-group">
            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Masukkan nama Anda"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="nama@email.com"
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
                placeholder="Buat password"
                required
            >
        </div>

        <div class="form-group">
            <label for="tipe_pengguna">
                Status Sivitas
            </label>

            <select
                id="tipe_pengguna"
                name="tipe_pengguna"
                required
            >
                <option value="" disabled selected>
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

        <button
            type="submit"
            class="btn"
        >
            Create account
        </button>

    </form>

    <div class="login">
        Sudah punya akun?
        <a href="login.php">
            Login
        </a>
    </div>

    <div class="note">
        Akun baru akan berstatus <strong>Pending</strong>
        dan perlu diverifikasi oleh admin sebelum dapat login.
    </div>

</div>

</body>
</html>