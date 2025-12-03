<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Login Admin - Praktek Bersama</title>

    <!-- Font, Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        /* Kolom Kiri */
        .login-illustration {
            flex-basis: 45%;
            background: linear-gradient(160deg, #169400 0%, #0f7300 100%);
            padding: 50px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .illustration-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .illustration-logo img {
            width: 50px;
        }

        .illustration-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .illustration-subtitle {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* Kolom Form */
        .login-form-container {
            flex-basis: 55%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: #169400;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }

        .form-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .input-wrapper:focus-within .form-input {
            border-color: #169400;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1);
        }

        .input-wrapper:focus-within .form-input-icon {
            color: #169400;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(22, 148, 0, 0.35);
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .other-login {
            margin-top: 20px;
            text-align: center;
        }

        .other-login a {
            color: #169400;
            text-decoration: none;
            font-weight: 600;
        }

        .other-login a:hover {
            color: #0f7300;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
            }
            .login-illustration {
                padding: 40px 30px;
            }
            .login-form-container {
                padding: 40px 30px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    
    <!-- Bagian Kiri -->
    <div class="login-illustration">
        <div class="illustration-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <h1 class="illustration-title">Panel Admin</h1>
        <p class="illustration-subtitle">
            Masuk untuk mengelola sistem, data pasien, dan operasional klinik.
        </p>
    </div>

    <!-- Bagian Kanan (Form) -->
    <div class="login-form-container">
        
        <h2 class="form-title"><i class="fas fa-user-shield"></i> Login Admin</h2>
        <p class="form-subtitle">Silakan masukkan email dan password Anda.</p>

        @if ($errors->any())
            <div class="alert-error">
                Email atau password salah.
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="form-input" placeholder="Masukkan email admin" required>
                    <i class="form-input-icon fas fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required>
                    <i class="form-input-icon fas fa-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="other-login">
            <a href="{{ route('login') }}">Login sebagai Pasien</a><br><br>
            {{-- <a href="{{ route('admin.password.request') }}">Lupa Password?</a> --}}
        </div>

    </div>

</div>

</body>
</html>
