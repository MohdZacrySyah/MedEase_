<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Login Apoteker - Praktek Bersama</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* =========================================== */
        /* ===== CSS KONSISTEN UNTUK LOGIN/REGISTER ===== */
        /* =========================================== */
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

        /* Kolom Kiri (Ilustrasi) */
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
            position: relative;
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .illustration-logo img {
            width: 50px;
            height: auto;
        }

        .illustration-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .illustration-subtitle {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Kolom Kanan (Form Login) */
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
            color: #169400; /* Warna hijau primer */
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
            position: relative;
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
            padding: 14px 20px 14px 45px; /* Padding untuk ikon */
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }
        
        .form-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #169400;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1);
        }

        .form-input:focus + .form-input-icon {
            color: #169400;
        }
        
        /* Opsi Form (Remember & Lupa Password) */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
        }
        .form-check input[type="checkbox"] {
            width: auto; /* Balikin ke default */
            height: auto;
        }
        .form-footer-link {
            color: #169400;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .form-footer-link:hover {
            color: #0f7300;
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
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(22, 148, 0, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(22, 148, 0, 0.35);
        }
        
        /* Alert/Error Styling */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Animasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
            }
            .login-illustration {
                flex-basis: auto;
                padding: 40px 30px;
            }
            .illustration-title {
                font-size: 1.5rem;
            }
            .illustration-subtitle {
                font-size: 0.9rem;
            }
            .login-form-container {
                flex-basis: auto;
                padding: 40px 30px;
            }
            .form-title {
                font-size: 1.5rem;
                text-align: center;
                justify-content: center;
            }
            .form-subtitle {
                text-align: center;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-wrapper">
        <div class="login-illustration">
            <div class="illustration-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <h1 class="illustration-title">Panel Apoteker</h1>
            <p class="illustration-subtitle">
                Masuk untuk mengelola antrian resep, melihat riwayat, dan mengelola stok obat.
            </p>
        </div>

        <div class="login-form-container">
            <h2 class="form-title">
                <i class="fas fa-pills"></i>
                Login Apoteker
            </h2>
            <p class="form-subtitle">Silakan masukkan email dan password Anda.</p>

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle" style="padding-top: 5px;"></i>
                    <div>
                        <strong>Login Gagal</strong>
                        <ul style="margin:0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('apoteker.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
                        <i class="form-input-icon fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input id="password" type="password" name="password" class="form-input" required>
                        <i class="form-input-icon fas fa-lock"></i>
                    </div>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat Saya</label>
                    </div>
                    
                    {{-- ===== PERBAIKAN: LINK LUPA PASSWORD DITAMBAHKAN DI SINI ===== --}}
                    <a href="{{ route('apoteker.password.request') }}" class="form-footer-link">Lupa Password?</a>
                    
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>
        </div>
    </div>
    
</body>
</html>