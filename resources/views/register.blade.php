<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Registrasi - Praktek Bersama by Fathurrahman</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --p1: #39A616;
            --p2: #1D8208;
            --p3: #0C5B00;
            --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
            --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --text-primary: #374151;
            --text-secondary: #6b7280;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.4);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.5);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            transition: background 0.3s ease;
        }

        [data-theme="dark"] body {
            background: linear-gradient(135deg, #1f2937, #111827);
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(57, 166, 22, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
            z-index: 0;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Animated Background Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.08;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--p1);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
            box-shadow: 0 0 10px rgba(57, 166, 22, 0.5);
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 15s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 20s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 22s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 17s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 19s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 21s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 4.5s; animation-duration: 16s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 1.5s; animation-duration: 23s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) scale(1); opacity: 0; }
        }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 1001;
            width: 55px;
            height: 55px;
            background: var(--grad);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dark-mode-toggle::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: var(--grad);
            opacity: 0;
            z-index: -1;
            filter: blur(10px);
            transition: opacity 0.3s ease;
        }

        .dark-mode-toggle:hover::before {
            opacity: 0.7;
            animation: rotateBorder 3s linear infinite;
        }

        @keyframes rotateBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .dark-mode-toggle:hover {
            transform: scale(1.15) rotate(180deg);
            box-shadow: var(--shadow-md);
        }

        /* Back Button */
        .back-button {
            position: fixed;
            top: 30px;
            left: 30px;
            z-index: 1001;
            width: 55px;
            height: 55px;
            background: var(--bg-primary);
            border: 2px solid var(--p1);
            border-radius: 50%;
            color: var(--p2);
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-button::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: var(--grad);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .back-button:hover::before {
            opacity: 1;
        }

        .back-button:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-md);
            color: white;
            border-color: transparent;
        }

        .back-button i {
            position: relative;
            z-index: 1;
        }

        /* Login Wrapper */
        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            background: var(--bg-primary);
            border-radius: 28px;
            box-shadow: var(--shadow-lg);
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            border: 1px solid rgba(57, 166, 22, 0.1);
        }

        [data-theme="dark"] .login-wrapper {
            border-color: rgba(57, 166, 22, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Shake Animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }

        .login-wrapper.shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Kolom Kiri (Ilustrasi) */
        .login-illustration {
            flex-basis: 45%;
            background: var(--grad);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-illustration::before,
        .login-illustration::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
        }

        .login-illustration::before {
            width: 400px;
            height: 400px;
            top: -200px;
            right: -200px;
            animation: rotateIllustration 25s linear infinite;
        }

        .login-illustration::after {
            width: 300px;
            height: 300px;
            bottom: -150px;
            left: -150px;
            animation: rotateIllustration 20s linear infinite reverse;
        }

        @keyframes rotateIllustration {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .illustration-logo {
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 35px;
            position: relative;
            z-index: 1;
            animation: logoFloat 3.5s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-18px) scale(1.05); }
        }

        .illustration-logo img {
            width: 150px;
            height: auto;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.3));
        }

        .illustration-title {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 18px;
            line-height: 1.2;
            position: relative;
            z-index: 1;
            text-shadow: 0 3px 15px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .illustration-subtitle {
            font-size: 1.08rem;
            font-weight: 400;
            opacity: 0.96;
            line-height: 1.75;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Kolom Kanan (Form Register) */
        .login-form-container {
            flex-basis: 55%;
            padding: 65px 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--bg-primary);
            transition: background 0.3s ease;
            position: relative;
            max-height: 95vh;
            overflow-y: auto;
        }

        .login-form-container::-webkit-scrollbar {
            width: 8px;
        }

        .login-form-container::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 10px;
        }

        .login-form-container::-webkit-scrollbar-thumb {
            background: var(--p1);
            border-radius: 10px;
        }

        .login-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(57, 166, 22, 0.03) 0%, transparent 70%);
            border-radius: 50%;
        }

        .form-title {
            font-size: 2.4rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .form-title i {
            font-size: 2rem;
        }

        .form-subtitle {
            font-size: 1.02rem;
            color: var(--text-secondary);
            margin-bottom: 40px;
            animation: slideInLeft 0.6s ease-out 0.1s backwards;
        }

        /* Floating Label Group */
        .floating-label-group {
            position: relative;
            margin-bottom: 25px;
            animation: slideInLeft 0.6s ease-out backwards;
        }

        .floating-label-group:nth-child(1) { animation-delay: 0.2s; }
        .floating-label-group:nth-child(2) { animation-delay: 0.3s; }
        .floating-label-group:nth-child(3) { animation-delay: 0.4s; }
        .floating-label-group:nth-child(4) { animation-delay: 0.5s; }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 17px 20px 17px 52px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] .form-input {
            border-color: #374151;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--p1);
            background-color: var(--bg-primary);
            box-shadow: 0 0 0 5px rgba(57, 166, 22, 0.08);
            transform: translateY(-3px);
        }

        .form-input-icon {
            position: absolute;
            left: 19px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.15rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        .form-input:focus ~ .form-input-icon {
            color: var(--p1);
            transform: translateY(-50%) scale(1.15);
        }

        /* Floating Labels */
        .floating-label {
            position: absolute;
            left: 52px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            font-weight: 500;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            padding: 0 5px;
        }

        .form-input:focus ~ .floating-label,
        .form-input:not(:placeholder-shown) ~ .floating-label {
            top: -12px;
            left: 15px;
            font-size: 0.75rem;
            color: var(--p1);
            background: var(--bg-primary);
            font-weight: 600;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--p1);
            background: rgba(57, 166, 22, 0.08);
        }

        /* Password Strength Meter */
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .password-strength.active {
            opacity: 1;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #ef4444;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #f59e0b;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: var(--p1);
        }

        .password-strength-text {
            font-size: 0.75rem;
            margin-top: 4px;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .password-strength-text.active {
            opacity: 1;
        }

        .password-strength-text.weak { color: #ef4444; }
        .password-strength-text.medium { color: #f59e0b; }
        .password-strength-text.strong { color: var(--p1); }

        /* Input Validation States */
        .form-input.valid {
            border-color: var(--p1);
        }

        .form-input.invalid {
            border-color: #ef4444;
        }

        .form-input.success ~ .form-input-icon::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: -30px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--p1);
            font-size: 0.9rem;
            animation: successPop 0.3s ease-out;
        }

        @keyframes successPop {
            0% { transform: translateY(-50%) scale(0); }
            50% { transform: translateY(-50%) scale(1.2); }
            100% { transform: translateY(-50%) scale(1); }
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 19px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.12rem;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(57, 166, 22, 0.35);
            position: relative;
            overflow: hidden;
            animation: slideInLeft 0.6s ease-out 0.6s backwards;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-submit:hover::before {
            width: 500px;
            height: 500px;
        }

        .btn-submit:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(57, 166, 22, 0.45);
        }

        .btn-submit:active {
            transform: translateY(-2px);
        }

        .btn-submit i,
        .btn-submit span {
            position: relative;
            z-index: 1;
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: buttonSpinner 0.6s linear infinite;
        }

        @keyframes buttonSpinner {
            to { transform: rotate(360deg); }
        }

        /* Ripple Effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: rippleEffect 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes rippleEffect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Alert Styling */
        .alert {
            padding: 18px 22px;
            border-radius: 16px;
            margin-bottom: 28px;
            font-size: 0.96rem;
            font-weight: 500;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert i {
            font-size: 1.3rem;
            margin-top: 2px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 2px solid #fca5a5;
        }

        [data-theme="dark"] .alert-error {
            background-color: rgba(220, 38, 38, 0.15);
            color: #fca5a5;
            border-color: #991b1b;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        [data-theme="dark"] .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #a7f3d0;
            border-color: #059669;
        }

        /* Form Footer */
        .form-footer-text {
            margin-top: 28px;
            text-align: center;
            animation: slideInLeft 0.6s ease-out 0.7s backwards;
            padding-top: 25px;
            border-top: 1px solid rgba(57, 166, 22, 0.1);
            color: var(--text-secondary);
        }

        .form-footer-link {
            color: var(--p1);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.02rem;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 2px;
        }

        .form-footer-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--grad);
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-footer-link:hover::after {
            width: 100%;
        }

        .form-footer-link:hover {
            color: var(--p2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 500px;
                margin: 20px;
                border-radius: 24px;
            }

            .login-illustration {
                flex-basis: auto;
                padding: 55px 35px;
            }

            .illustration-logo {
                width: 90px;
                height: 90px;
            }

            .illustration-logo img {
                width: 70px;
            }

            .illustration-title {
                font-size: 2rem;
            }

            .illustration-subtitle {
                font-size: 1rem;
            }

            .login-form-container {
                flex-basis: auto;
                padding: 45px 35px;
                max-height: none;
            }

            .form-title {
                font-size: 2rem;
                justify-content: center;
            }

            .form-subtitle {
                text-align: center;
                font-size: 0.96rem;
            }

            .dark-mode-toggle,
            .back-button {
                width: 50px;
                height: 50px;
                top: 20px;
            }

            .dark-mode-toggle {
                right: 20px;
            }

            .back-button {
                left: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <button class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
    </button>

    <button onclick="goBack()" class="back-button" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="login-wrapper">
        <div class="login-illustration">
            <div class="illustration-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <h1 class="illustration-title">Mulai Perjalanan Sehat</h1>
            <p class="illustration-subtitle">
                Buat akun baru untuk mengakses layanan, mendaftar antrian, dan melihat riwayat kesehatan dengan mudah.
            </p>
        </div>

        <div class="login-form-container">
            <h2 class="form-title">
                <i class="fas fa-user-plus"></i>
                <span>Buat Akun</span>
            </h2>
            <p class="form-subtitle">Silakan lengkapi data diri Anda</p>

            @if (session('success'))
                <div class="alert alert-success" id="successAlert">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Registrasi Gagal</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register.proses') }}" method="POST" id="registerForm">
                @csrf

                <div class="floating-label-group">
                    <div class="input-wrapper">
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            class="form-input" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus 
                            placeholder=" "
                        >
                        <i class="form-input-icon fas fa-user"></i>
                        <label for="name" class="floating-label">Nama Lengkap</label>
                    </div>
                </div>

                <div class="floating-label-group">
                    <div class="input-wrapper">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            value="{{ old('email') }}" 
                            required 
                            placeholder=" "
                        >
                        <i class="form-input-icon fas fa-envelope"></i>
                        <label for="email" class="floating-label">Email Address</label>
                    </div>
                </div>

                <div class="floating-label-group">
                    <div class="input-wrapper">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            class="form-input" 
                            required 
                            placeholder=" "
                        >
                        <i class="form-input-icon fas fa-lock"></i>
                        <label for="password" class="floating-label">Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar"></div>
                    </div>
                    <div class="password-strength-text"></div>
                </div>

                <div class="floating-label-group">
                    <div class="input-wrapper">
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            class="form-input" 
                            required 
                            placeholder=" "
                        >
                        <i class="form-input-icon fas fa-check-circle"></i>
                        <label for="password_confirmation" class="floating-label">Konfirmasi Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </form>

            <p class="form-footer-text">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="form-footer-link">Login di sini</a>
            </p>
        </div>
    </div>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        const icon = darkModeToggle.querySelector('i');

        const currentTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', currentTheme);
        icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';

        darkModeToggle.addEventListener('click', () => {
            const theme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });

        // Back Button (tanpa loading overlay)
        function goBack() {
            window.location.href = '{{ route("login") }}';
        }

        // Password Toggle (dengan parameter untuk multi input)
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Password Strength Checker
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('passwordStrength');
        const strengthBar = strengthMeter.querySelector('.password-strength-bar');
        const strengthText = strengthMeter.nextElementSibling;

        passwordInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.length === 0) {
                strengthMeter.classList.remove('active');
                strengthText.classList.remove('active');
                return;
            }
            
            strengthMeter.classList.add('active');
            strengthText.classList.add('active');
            
            let strength = 0;
            
            // Check length
            if (value.length >= 8) strength++;
            if (value.length >= 12) strength++;
            
            // Check for numbers
            if (/\d/.test(value)) strength++;
            
            // Check for special characters
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) strength++;
            
            // Check for uppercase and lowercase
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) strength++;
            
            // Update UI
            strengthBar.className = 'password-strength-bar';
            strengthText.className = 'password-strength-text active';
            
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthText.classList.add('weak');
                strengthText.textContent = 'Lemah - Gunakan kombinasi huruf, angka & simbol';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                strengthText.classList.add('medium');
                strengthText.textContent = 'Sedang - Tambahkan huruf kapital & simbol';
            } else {
                strengthBar.classList.add('strong');
                strengthText.classList.add('strong');
                strengthText.textContent = 'Kuat - Password aman!';
            }
        });

        // Password Match Validation
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        passwordConfirmation.addEventListener('input', function() {
            if (this.value === passwordInput.value && this.value.length > 0) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (this.value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Form Submit with Ripple (Loading Overlay dihapus)
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.querySelector('.btn-submit');

        // Ripple Effect on Button Click
        submitBtn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });

        // Form Submit with simple Loading class on button
        registerForm.addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                // Check if passwords match
                if (passwordInput.value !== passwordConfirmation.value) {
                    e.preventDefault();
                    alert('Password dan Konfirmasi Password tidak cocok!');
                    return;
                }
                
                // Tambahkan class loading ke tombol, tanpa menampilkan overlay
                submitBtn.classList.add('loading');
            }
        });

        // Enhanced Form Validation
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');

        nameInput.addEventListener('blur', function() {
            const icon = this.nextElementSibling;
            
            if (this.value.length >= 3) {
                this.classList.add('success');
                this.classList.remove('invalid');
                icon.classList.add('animated');
                setTimeout(() => icon.classList.remove('animated'), 500);
            } else if (this.value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('success');
            }
        });

        emailInput.addEventListener('blur', function() {
            const icon = this.nextElementSibling;
            
            if (this.value && this.checkValidity()) {
                this.classList.add('success');
                this.classList.remove('invalid');
                icon.classList.add('animated');
                setTimeout(() => icon.classList.remove('animated'), 500);
            } else if (this.value) {
                this.classList.add('invalid');
                this.classList.remove('success');
            }
        });

        // Auto hide success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => successAlert.remove(), 700);
            }, 4000);
        }

        // Shake animation on error
        const registerWrapper = document.querySelector('.login-wrapper');
        @if ($errors->any())
            registerWrapper.classList.add('shake');
            setTimeout(() => registerWrapper.classList.remove('shake'), 500);
        @endif

        // Login Link (Langsung redirect tanpa loading overlay)
        // Tidak perlu event listener khusus karena sudah menggunakan anchor tag biasa

        // Input Animation on Focus
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Disable form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>