<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CampusReserve</title>

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
            padding: 20px;

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
            width: 430px;
            padding: 42px;
            
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
            margin-bottom: 38px;
        }

        .logo span {
            font-weight: 400;
        }

        .title {
            margin-bottom: 28px;
        }

        .title h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .title p {
            color: rgba(255, 255, 255, 0.70);
            font-size: 13px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;

            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.88);
        }

        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 14px;

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

        .form-group input:focus {
            border-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.10);
        }

        .forgot {
            display: flex;
            justify-content: flex-end;
            margin-top: -8px;
            margin-bottom: 20px;
        }

        .forgot a {
            color: rgba(255, 255, 255, 0.72);
            font-size: 11px;
            text-decoration: none;
        }

        .forgot a:hover {
            color: white;
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            height: 46px;

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

        .register {
            margin-top: 22px;
            text-align: center;

            color: rgba(255, 255, 255, 0.65);
            font-size: 11px;
        }

        .register a {
            color: white;
            font-weight: 700;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .container {
                width: 100%;
                padding: 32px 25px;
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
        <h1>Login</h1>

        <p>
            Masuk ke akun CampusReserve untuk
            melakukan reservasi ruangan kampus.
        </p>
    </div>

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
                placeholder="Password"
                required
            >
        </div>

        <div class="forgot">
            <a href="#" onclick="return false;">
                Lupa password?
            </a>
        </div>

        <button
            type="submit"
            class="btn"
        >
            Sign in
        </button>

    </form>

    <div class="register">
        Belum punya akun?
        <a href="register.php">
            Daftar sekarang
        </a>
    </div>

</div>

</body>
</html>