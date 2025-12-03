<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Lupa Password - Tenaga Medis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* =========================================== */
        /* ===== CSS KONSISTEN UNTUK LOGIN ===== */
        /* =========================================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; padding: 20px;
        }
        .login-wrapper {
            width: 100%; max-width: 900px; background: #ffffff;
            border-radius: 24px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            display: flex; overflow: hidden; animation: fadeIn 0.8s ease-out;
        }
        .login-illustration {
            flex-basis: 45%;
            background: linear-gradient(160deg, #169400 0%, #0f7300 100%);
            padding: 50px 40px; color: white; display: flex; flex-direction: column;
            justify-content: center; align-items: center; text-align: center; position: relative;
        }
        .illustration-logo {
            width: 80px; height: 80px; background: rgba(255, 255, 255, 0.9);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .illustration-logo img { width: 50px; height: auto; }
        .illustration-title { font-size: 2rem; font-weight: 700; margin-bottom: 15px; line-height: 1.3; }
        .illustration-subtitle { font-size: 1rem; font-weight: 400; opacity: 0.9; line-height: 1.6; }
        .login-form-container {
            flex-basis: 55%; padding: 50px 40px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .form-title {
            font-size: 2rem; font-weight: 700; color: #169400;
            margin-bottom: 10px; display: flex; align-items: center; gap: 12px;
        }
        .form-subtitle { font-size: 1rem; color: #6b7280; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-label { font-size: 0.9rem; font-weight: 600; color: #374151; margin-bottom: 8px; display: block; }
        .input-wrapper { position: relative; }
        .form-input {
            width: 100%; padding: 14px 20px 14px 45px;
            border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.95rem;
            font-family: 'Poppins', sans-serif; background-color: #fafafa; transition: all 0.3s ease;
        }
        .form-input-icon {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%); color: #9ca3af; transition: color 0.3s ease;
        }
        .form-input:focus {
            outline: none; border-color: #169400; background-color: #fff;
            box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1);
        }
        .form-input:focus + .form-input-icon { color: #169400; }
        .btn-submit {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
            color: white; border: none; border-radius: 12px; font-weight: 600;
            font-size: 1.05rem; font-family: 'Poppins', sans-serif; cursor: pointer;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(22, 148, 0, 0.25);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(22, 148, 0, 0.35);
        }
        .form-footer-link { color: #169400; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
        .form-footer-link:hover { color: #0f7300; }
        .form-footer-text { text-align: center; margin-top: 25px; color: #6b7280; }
        .alert {
            padding: 15px 20px; border-radius: 12px; margin-bottom: 20px;
            font-size: 0.9rem; font-weight: 500; display: flex; gap: 10px;
            align-items: flex-start;
        }
        .alert-success { background-color: #e6f9f0; color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; max-width: 450px; margin: 20px; }
            .login-illustration { flex-basis: auto; padding: 40px 30px; }
            .illustration-title { font-size: 1.5rem; }
            .illustration-subtitle { font-size: 0.9rem; }
            .login-form-container { flex-basis: auto; padding: 40px 30px; }
            .form-title { font-size: 1.5rem; text-align: center; justify-content: center; }
            .form-subtitle { text-align: center; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    
    <div class="login-wrapper">
        <div class="login-illustration">
            <div class="illustration-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <h1 class="illustration-title">Lupa Password?</h1>
            <p class="illustration-subtitle">
                Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.
            </p>
        </div>

        <div class="login-form-container">
            <h2 class="form-title">
                <i class="fas fa-key"></i>
                Reset Password Nakes
            </h2>
            
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            
            @error('email')
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <form method="POST" action="{{ route('tenaga-medis.password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-wrapper">
                        <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
                        <i class="form-input-icon fas fa-envelope"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Kirim Link Reset</button>
            </form>

            <p class="form-footer-text">
                Ingat password Anda? 
                <a href="{{ route('tenaga-medis.login') }}" class="form-footer-link">Kembali ke Login</a>
            </p>
        </div>
    </div>
    
</body>
</html>