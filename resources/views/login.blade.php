<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Login - Praktek Bersama by Fathurrahman</title>

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

        /* --- PERBAIKAN SCROLLING DIMULAI DI SINI --- */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            display: flex;
            justify-content: center;
            align-items: center;
            /* Hapus min-height: 100vh agar body bisa lebih tinggi dari viewport */
            /* Tambahkan padding vertikal yang cukup sebagai ganti min-height */
            padding: 20px;
            padding-top: 100px; /* Ruang agar tidak terpotong tombol fixed atas */
            padding-bottom: 100px; 
            position: relative;
            overflow-x: hidden; /* Mencegah scroll horizontal yang tidak diinginkan */
            overflow-y: auto; /* Memastikan vertical scrolling diaktifkan */
            transition: background 0.3s ease;
        }

        [data-theme="dark"] body {
            background: linear-gradient(135deg, #1f2937, #111827);
        }
        /* --- PERBAIKAN SCROLLING SELESAI DI SINI --- */

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
            /* Mengubah translateY(-100px) menjadi translateY(-50px) agar partikel hilang lebih cepat di atas */
            100% { transform: translateY(-50px) scale(1); opacity: 0; }
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

        /* Kolom Kanan (Form Login) */
        .login-form-container {
            flex-basis: 55%;
            padding: 65px 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--bg-primary);
            transition: background 0.3s ease;
            position: relative;
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
            margin-bottom: 28px;
            animation: slideInLeft 0.6s ease-out backwards;
        }

        .floating-label-group:nth-child(1) { animation-delay: 0.2s; }
        .floating-label-group:nth-child(2) { animation-delay: 0.3s; }

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

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 0.92rem;
            animation: slideInLeft 0.6s ease-out 0.4s backwards;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.3s ease;
            user-select: none;
        }

        .form-check:hover {
            color: var(--p2);
        }

        .form-check input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--p1);
            transition: transform 0.2s ease;
        }

        .form-check input[type="checkbox"]:checked {
            transform: scale(1.1);
        }

        .form-footer-link {
            color: var(--p1);
            text-decoration: none;
            font-weight: 600;
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
            animation: slideInLeft 0.6s ease-out 0.5s backwards;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
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

        /* Social Login */
        .social-login {
            margin: 25px 0;
            animation: slideInLeft 0.6s ease-out 0.6s backwards;
        }

        .social-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(57, 166, 22, 0.2);
        }

        .social-divider span {
            padding: 0 15px;
        }

        .social-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .social-btn {
            padding: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        [data-theme="dark"] .social-btn {
            border-color: #374151;
        }

        .social-btn i {
            font-size: 1.2rem;
        }

        .social-btn.google {
            border-color: #ea4335;
            color: #ea4335;
        }

        .social-btn.google:hover {
            background: #ea4335;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(234, 67, 53, 0.3);
        }

        .social-btn.facebook {
            border-color: #1877f2;
            color: #1877f2;
        }

        .social-btn.facebook:hover {
            background: #1877f2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(24, 119, 242, 0.3);
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

        /* Other Login */
        .other-login {
            margin-top: 28px;
            text-align: center;
            animation: slideInLeft 0.6s ease-out 0.7s backwards;
            padding-top: 25px;
            border-top: 1px solid rgba(57, 166, 22, 0.1);
        }

        .other-login a {
            color: var(--p1);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.02rem;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 2px;
        }

        .other-login a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--grad);
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .other-login a:hover::after {
            width: 100%;
        }

        .other-login a:hover {
            color: var(--p2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            /* Perubahan untuk Scrolling di Mobile */
            body {
                padding-top: 90px;
                padding-bottom: 90px;
            }

            .login-wrapper {
                flex-direction: column;
                max-width: 500px;
                margin: 0 20px;
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

            .form-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .social-buttons {
                grid-template-columns: 1fr;
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
            <h1 class="illustration-title">Panel Pengguna</h1>
            <p class="illustration-subtitle">
                Masuk untuk mengakses layanan, melihat jadwal, dan membuat janji temu dengan mudah.
            </p>
        </div>

        <div class="login-form-container">
            <h2 class="form-title">
                <i class="fas fa-user-circle"></i>
                <span>Login</span>
            </h2>
            <p class="form-subtitle">Masukkan kredensial Anda untuk melanjutkan</p>

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
                        <strong>Login Gagal</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST" id="loginForm">
                @csrf

                <div class="floating-label-group">
                    <div class="input-wrapper">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
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
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="form-check">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat Saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="form-footer-link">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login Sekarang</span>
                </button>

                <div class="social-login">
                    <div class="social-divider">
                        <span>Atau login dengan</span>
                    </div>
                    <div class="social-buttons">
                        <a href="{{ route('auth.google') }}" class="social-btn google">
                            <i class="fab fa-google"></i>
                            <span>Google</span>
                        </a>

                        <a href="#" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                </div>
            </form>

            <div class="other-login">
                <span style="color: var(--text-secondary);">Belum punya akun? </span>
                <a href="{{ route('register.form') }}">Daftar Sekarang</a>
            </div>
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

    // Back Button
    function goBack() {
        window.history.back();
    }

    // Password Toggle
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }

    // Form Submit with Ripple
    const loginForm = document.getElementById('loginForm');
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
    loginForm.addEventListener('submit', function(e) {
        if (this.checkValidity()) {
            // Tambahkan class loading ke tombol
            submitBtn.classList.add('loading');
        }
    });

    // Enhanced Form Validation with Visual Feedback
    const emailInput = document.getElementById('email');

    emailInput.addEventListener('blur', function() {
        // Hanya tambahkan/hapus kelas validasi saat pengguna meninggalkan input
        if (this.value) {
            if (this.checkValidity()) {
                this.classList.add('success');
                this.classList.remove('invalid');
            } else {
                this.classList.add('invalid');
                this.classList.remove('success');
            }
        } else {
             this.classList.remove('success', 'invalid');
        }
    });

    const passwordInput = document.getElementById('password');
    
    passwordInput.addEventListener('blur', function() {
         if (this.value) {
            // Contoh validasi sederhana: minimal 6 karakter
            if (this.value.length >= 6) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else {
                this.classList.add('invalid');
                this.classList.remove('valid');
            }
        } else {
             this.classList.remove('valid', 'invalid');
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

    // Shake animation on error (menggunakan sintaks Blade untuk mengecek error)
    const loginWrapper = document.querySelector('.login-wrapper');
    @if ($errors->any())
        loginWrapper.classList.add('shake');
        setTimeout(() => loginWrapper.classList.remove('shake'), 500);
    @endif

    // Social Login Handlers (Peringatan untuk Facebook/Link Kosong)
    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Check if button has valid href (not # or empty)
            const hasValidHref = this.href && this.href !== window.location.href + '#' && !this.href.endsWith('#');
            
            if (this.classList.contains('facebook')) {
                // Facebook - prevent and show message
                e.preventDefault();
                alert('Facebook login belum diimplementasikan');
            } else if (!hasValidHref) {
                // No valid href - prevent and show message
                e.preventDefault();
                alert('Social login belum dikonfigurasi');
            }
            // Untuk Google, biarkan redirect natural
        });
    });


    // Input Animation on Focus
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.01)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Disable form resubmission on page refresh (praktik baik di Laravel/PHP)
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

</body>
</html>