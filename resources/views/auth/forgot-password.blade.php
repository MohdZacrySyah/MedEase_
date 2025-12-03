<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Lupa Password - Praktek Bersama by Fathurrahman</title>
    
    <!-- Font Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
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
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }
        
        /* Back Button */
        .back-btn {
            position: fixed;
            top: 25px;
            left: 25px;
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: var(--p1);
            font-size: 1.3rem;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .back-btn:hover {
            background: var(--p1);
            color: white;
            transform: translateX(-5px);
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        }
        
        .forgot-password-wrapper {
            width: 100%;
            max-width: 900px;
            background: var(--bg-primary);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            display: flex;
            overflow: hidden;
            animation: fadeInScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        
        /* Left Side Illustration */
        .illustration-side {
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
        
        .illustration-side::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }
        
        .illustration-side::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -80px;
            left: -80px;
        }
        
        .illustration-logo {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .illustration-logo img {
            width: 55px;
            height: auto;
        }
        
        .illustration-icon {
            font-size: 4rem;
            margin-bottom: 30px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.95; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        
        .illustration-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
            position: relative;
            z-index: 1;
        }
        
        .illustration-subtitle {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.95;
            line-height: 1.7;
            max-width: 320px;
            position: relative;
            z-index: 1;
        }
        
        /* Right Side Form */
        .form-side {
            flex-basis: 55%;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .form-header {
            margin-bottom: 35px;
        }
        
        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--p1);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-title i {
            font-size: 1.8rem;
        }
        
        .form-subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        /* Alert Styles */
        .alert {
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert i {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        /* Form Group */
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            display: block;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            background-color: #fafafa;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }
        
        .form-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--p1);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
        }
        
        .form-input:focus + .form-input-icon {
            color: var(--p1);
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 17px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1.05rem;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-submit:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(57, 166, 22, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        /* Footer Link */
        .form-footer {
            margin-top: 28px;
            text-align: center;
        }
        
        .form-footer-text {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }
        
        .form-footer-link {
            color: var(--p1);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .form-footer-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--p1);
            transition: width 0.3s ease;
        }
        
        .form-footer-link:hover::after {
            width: 100%;
        }
        
        .form-footer-link:hover {
            color: var(--p3);
        }
        
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-content {
            text-align: center;
            color: white;
        }
        
        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid var(--p1);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .loading-subtext {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .back-btn {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
            
            .forgot-password-wrapper {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
            }
            
            .illustration-side {
                flex-basis: auto;
                padding: 40px 30px;
            }
            
            .illustration-title {
                font-size: 1.6rem;
            }
            
            .illustration-subtitle {
                font-size: 0.9rem;
            }
            
            .form-side {
                flex-basis: auto;
                padding: 40px 30px;
            }
            
            .form-title {
                font-size: 1.6rem;
                justify-content: center;
            }
            
            .form-subtitle {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    
    <!-- Back Button -->
    <a href="{{ route('login') }}" class="back-btn" title="Kembali ke Login">
        <i class="fas fa-arrow-left"></i>
    </a>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Mengirim Link Reset...</div>
            <div class="loading-subtext">Mohon tunggu sebentar</div>
        </div>
    </div>
    
    <div class="forgot-password-wrapper">
        <!-- Left Side Illustration -->
        <div class="illustration-side">
            <div class="illustration-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Praktek Bersama">
            </div>
            <div class="illustration-icon">
                <i class="fas fa-lock-open"></i>
            </div>
            <h1 class="illustration-title">Lupa Password?</h1>
            <p class="illustration-subtitle">
                Tidak perlu khawatir! Masukkan email Anda dan kami akan mengirimkan kode OTP untuk mereset password Anda.
            </p>
        </div>

        <!-- Right Side Form -->
        <div class="form-side">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-key"></i>
                    Reset Password
                </h2>
                <p class="form-subtitle">
                    Masukkan alamat email yang terdaftar dan kami akan mengirimkan instruksi reset password.
                </p>
            </div>
            
            <!-- Success Message -->
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            
            <!-- Error Message -->
            @error('email')
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <!-- Form -->
            <form method="POST" action="{{ route('password.send_otp') }}" id="forgotPasswordForm">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-wrapper">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            value="{{ old('email') }}" 
                            placeholder="contoh@email.com"
                            required 
                            autofocus
                        >
                        <i class="form-input-icon fas fa-envelope"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span>Kirim Link Reset</span>
                </button>
            </form>
            
            <div class="form-footer">
                <p class="form-footer-text">
                    Sudah ingat password Anda? 
                    <a href="{{ route('login') }}" class="form-footer-link">Kembali ke Login</a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Loading Overlay saat submit form
        const form = document.getElementById('forgotPasswordForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        form.addEventListener('submit', function(e) {
            // Show loading overlay
            loadingOverlay.classList.add('active');
        });
        
        // Smooth scroll untuk back button
        document.querySelector('.back-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            // Fade out animation sebelum redirect
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                window.location.href = href;
            }, 300);
        });
    </script>
    
</body>
</html>
