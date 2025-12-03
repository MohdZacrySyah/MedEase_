<!DOCTYPE html>
<html lang="id">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Praktek Bersama Fathurrahman</title>
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
        }

        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
        }
        
        html { scroll-behavior: smooth; }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            color: var(--text-primary); 
            overflow-x: hidden;
            background: var(--bg-primary);
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ===== LOADING SCREEN SUPER PREMIUM ===== */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #39A616 0%, #1D8208 50%, #0C5B00 100%);
            background-size: 400% 400%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease;
            animation: gradientMove 15s ease infinite;
            overflow: hidden;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .loader-wrapper.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        /* Animated Background Elements */
        .loader-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            animation: rotateBackground 20s linear infinite;
        }

        @keyframes rotateBackground {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .loader-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.2), transparent);
            animation: waveAnimation 4s ease-in-out infinite;
        }

        @keyframes waveAnimation {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .loader-content {
            text-align: center;
            animation: fadeInLoaderContent 1s ease-out;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeInLoaderContent {
            from { 
                opacity: 0; 
                transform: translateY(30px) scale(0.8);
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1);
            }
        }

        /* Logo Container dengan Efek Glow */
        .logo-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-logo {
            width: 120px;
            height: 120px;
            border-radius: 25px;
            position: relative;
            z-index: 5;
            animation: logoAnimateIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55), logoFloat 3.5s ease-in-out infinite 0.8s;
            box-shadow: 
                0 0 30px rgba(255, 255, 255, 0.4),
                0 0 60px rgba(255, 255, 255, 0.2),
                inset 0 0 20px rgba(255, 255, 255, 0.1);
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.3));
        }

        @keyframes logoAnimateIn {
            0% { 
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            60% {
                transform: scale(1.1) rotate(10deg);
            }
            100% { 
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes logoFloat {
            0%, 100% { 
                transform: translateY(0) scale(1);
            }
            50% { 
                transform: translateY(-25px) scale(1.05);
            }
        }

        /* Glow effect behind logo */
        .logo-container::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 2.5s ease-in-out infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes glowPulse {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0.5;
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 1;
            }
        }

        /* Loader Rings - Lebih Banyak Ring */
        .loader-ring {
            position: absolute;
            border-radius: 50%;
            animation: spinRing 2s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spinRing {
            0% { 
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% { 
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Ring 1 - Outer */
        .loader-ring:nth-child(1) {
            width: 160px;
            height: 160px;
            border: 3px solid transparent;
            border-top: 3px solid rgba(255, 255, 255, 0.9);
            border-right: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 0 20px rgba(255, 255, 255, 0.3),
                inset 0 0 20px rgba(255, 255, 255, 0.1);
            animation: spinRing 2.5s linear infinite, ringGlow 2.5s ease-in-out infinite;
        }

        /* Ring 2 - Middle */
        .loader-ring:nth-child(2) {
            width: 110px;
            height: 110px;
            border: 2.5px solid transparent;
            border-bottom: 2.5px solid rgba(255, 255, 255, 0.7);
            border-left: 2.5px solid rgba(255, 255, 255, 0.3);
            animation: spinRingReverse 2s linear infinite, ringGlow2 2s ease-in-out infinite;
        }

        @keyframes spinRingReverse {
            0% { 
                transform: translate(-50%, -50%) rotate(360deg);
            }
            100% { 
                transform: translate(-50%, -50%) rotate(0deg);
            }
        }

        /* Ring 3 - Inner */
        .loader-ring:nth-child(3) {
            width: 70px;
            height: 70px;
            border: 2px solid transparent;
            border-top: 2px solid rgba(255, 255, 255, 0.5);
            border-right: 2px solid rgba(255, 255, 255, 0.2);
            animation: spinRing 1.5s linear infinite, ringGlow3 1.5s ease-in-out infinite;
        }

        @keyframes ringGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3));
                box-shadow: 
                    0 0 20px rgba(255, 255, 255, 0.3),
                    inset 0 0 20px rgba(255, 255, 255, 0.1);
            }
            50% { 
                filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.7));
                box-shadow: 
                    0 0 40px rgba(255, 255, 255, 0.6),
                    inset 0 0 20px rgba(255, 255, 255, 0.2);
            }
        }

        @keyframes ringGlow2 {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.2));
            }
            50% { 
                filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
            }
        }

        @keyframes ringGlow3 {
            0%, 100% { 
                filter: drop-shadow(0 0 3px rgba(255, 255, 255, 0.1));
            }
            50% { 
                filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.4));
            }
        }

        /* Title dengan Glow Effect */
        .loader-title {
            color: white;
            font-size: 2.2rem;
            font-weight: 800;
            margin-top: 40px;
            margin-bottom: 15px;
            animation: titleSlideIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.3s backwards, titleGlow 2.5s ease-in-out infinite 1.1s;
            letter-spacing: 3px;
            text-shadow: 
                0 0 20px rgba(255, 255, 255, 0.3),
                0 2px 10px rgba(0, 0, 0, 0.3);
        }

        @keyframes titleSlideIn {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes titleGlow {
            0%, 100% { 
                text-shadow: 
                    0 0 10px rgba(255, 255, 255, 0.2),
                    0 2px 10px rgba(0, 0, 0, 0.3);
            }
            50% { 
                text-shadow: 
                    0 0 20px rgba(255, 255, 255, 0.5),
                    0 2px 10px rgba(0, 0, 0, 0.3);
            }
        }

        /* Loading Text dengan Dots Animation */
        .loader-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 30px;
            letter-spacing: 2px;
            font-weight: 500;
            animation: textSlideIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s backwards;
            position: relative;
        }

        @keyframes textSlideIn {
            from { 
                opacity: 0;
                transform: translateY(15px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loader-text::after {
            content: '.';
            animation: dotsAnimation 1.5s steps(3, end) infinite;
        }

        @keyframes dotsAnimation {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }

        /* Progress Bar dengan Shimmer Effect */
        .progress-bar {
            width: 240px;
            height: 5px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            overflow: hidden;
            margin: 0 auto;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2), inset 0 0 5px rgba(0, 0, 0, 0.3);
            animation: progressBarSlideIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.7s backwards;
        }

        @keyframes progressBarSlideIn {
            from { 
                opacity: 0;
                transform: scaleX(0);
            }
            to { 
                opacity: 1;
                transform: scaleX(1);
            }
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, 
                rgba(255, 255, 255, 0.2),
                rgba(255, 255, 255, 0.9),
                rgba(255, 255, 255, 0.2)
            );
            background-size: 200% 100%;
            animation: progressMove 2s ease-in-out infinite, shimmerEffect 1.5s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
        }

        @keyframes progressMove {
            0% { 
                width: 0%;
                background-position: 0% center;
            }
            50% { 
                width: 100%;
                background-position: 100% center;
            }
            100% { 
                width: 0%;
                background-position: 0% center;
            }
        }

        @keyframes shimmerEffect {
            0%, 100% { 
                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
            }
            50% { 
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
            }
        }

        /* Floating Particles */
        .loader-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: floatParticle 6s infinite ease-in-out;
        }

        .loader-particle:nth-child(4) {
            left: 20%;
            top: 30%;
            animation-delay: 0s;
            animation-duration: 8s;
        }

        .loader-particle:nth-child(5) {
            left: 80%;
            top: 40%;
            animation-delay: 1s;
            animation-duration: 10s;
        }

        .loader-particle:nth-child(6) {
            left: 15%;
            top: 70%;
            animation-delay: 2s;
            animation-duration: 9s;
        }

        .loader-particle:nth-child(7) {
            left: 85%;
            top: 65%;
            animation-delay: 0.5s;
            animation-duration: 11s;
        }
        /* ===== LOADING OVERLAY (untuk Action/Request) ===== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.loading-overlay.active {
    display: flex;
    opacity: 1;
}

.loading-spinner-container {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    animation: fadeInScale 0.3s ease-out;
}

@keyframes fadeInScale {
    from {
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.loading-spinner {
    position: relative;
    width: 80px;
    height: 80px;
}

.loading-spinner-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    animation: logoRotate 2s ease-in-out infinite;
    box-shadow: 0 0 20px rgba(57, 166, 22, 0.4);
}

@keyframes logoRotate {
    0%, 100% {
        transform: translate(-50%, -50%) rotate(0deg) scale(1);
    }
    50% {
        transform: translate(-50%, -50%) rotate(180deg) scale(1.1);
    }
}

.loading-ring-outer {
    position: absolute;
    width: 80px;
    height: 80px;
    border: 3px solid transparent;
    border-top: 3px solid var(--p1);
    border-right: 3px solid var(--p1);
    border-radius: 50%;
    animation: spinClockwise 1.5s linear infinite;
    box-shadow: 0 0 15px rgba(57, 166, 22, 0.3);
}

@keyframes spinClockwise {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.loading-ring-inner {
    position: absolute;
    width: 60px;
    height: 60px;
    top: 10px;
    left: 10px;
    border: 2px solid transparent;
    border-bottom: 2px solid var(--p2);
    border-left: 2px solid var(--p2);
    border-radius: 50%;
    animation: spinCounterClockwise 1s linear infinite;
}

@keyframes spinCounterClockwise {
    from {
        transform: rotate(360deg);
    }
    to {
        transform: rotate(0deg);
    }
}

.loading-text {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    animation: textPulse 1.5s ease-in-out infinite;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

@keyframes textPulse {
    0%, 100% {
        opacity: 0.8;
    }
    50% {
        opacity: 1;
    }
}

.loading-text::after {
    content: '.';
    animation: loadingDots 1.5s steps(3, end) infinite;
}

@keyframes loadingDots {
    0%, 20% { content: '.'; }
    40% { content: '..'; }
    60%, 100% { content: '...'; }
}

.loading-glow {
    position: absolute;
    width: 100px;
    height: 100px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(57, 166, 22, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: glowPulseOverlay 2s ease-in-out infinite;
    z-index: 1;
}

@keyframes glowPulseOverlay {
    0%, 100% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.5;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 1;
    }
}


        @keyframes floatParticle {
            0%, 100% { 
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% { 
                opacity: 1;
            }
            90% { 
                opacity: 1;
            }
            100% { 
                transform: translateY(-100px) translateX(50px);
                opacity: 0;
            }
        }

        /* Decorative Circle Pulse */
        .loader-circle-pulse {
            position: absolute;
            width: 200px;
            height: 200px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: circlePulse 2s ease-out infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes circlePulse {
            0% { 
                width: 200px;
                height: 200px;
                opacity: 1;
                border-width: 2px;
            }
            100% { 
                width: 400px;
                height: 400px;
                opacity: 0;
                border-width: 1px;
            }
        }

        .loader-circle-pulse:nth-child(1) {
            animation-delay: 0s;
        }

        .loader-circle-pulse:nth-child(2) {
            animation-delay: 0.6s;
        }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: fixed;
            top: 100px;
            right: 30px;
            z-index: 998;
            width: 55px;
            height: 55px;
            background: var(--grad);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(29, 130, 8, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dark-mode-toggle:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 6px 20px rgba(29, 130, 8, 0.5);
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
            opacity: 0.05;
        }
        
        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--p1);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
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

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Header */
        .header { 
            background: rgba(255,255,255,0.98); 
            backdrop-filter: blur(20px); 
            padding: 1rem 0; 
            box-shadow: 0 2px 20px rgba(0,0,0,0.05); 
            position: sticky; 
            top: 0; 
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
            transition: background 0.3s ease;
        }

        [data-theme="dark"] .header {
            background: rgba(31, 41, 55, 0.98);
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .nav-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 2rem; 
        }
        
        .logo { 
            font-size: 1.3rem; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            color: var(--p3);
            animation: fadeInLeft 0.8s ease-out;
        }
        
        @keyframes fadeInLeft {
            from { transform: translateX(-50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .logo img { 
            height: 45px; 
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        
        .logo:hover img {
            transform: rotate(360deg) scale(1.1);
        }
        
        .nav-menu { 
            display: flex; 
            list-style: none; 
            gap: 2.5rem; 
            align-items: center; 
        }
        
        .nav-menu li {
            animation: fadeInDown 0.8s ease-out backwards;
        }
        
        .nav-menu li:nth-child(1) { animation-delay: 0.1s; }
        .nav-menu li:nth-child(2) { animation-delay: 0.2s; }
        .nav-menu li:nth-child(3) { animation-delay: 0.3s; }
        .nav-menu li:nth-child(4) { animation-delay: 0.4s; }
        .nav-menu li:nth-child(5) { animation-delay: 0.5s; }
        .nav-menu li:nth-child(6) { animation-delay: 0.6s; }
        
        @keyframes fadeInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .nav-menu a { 
            color: var(--text-primary); 
            text-decoration: none; 
            transition: all 0.3s ease; 
            font-weight: 500; 
            position: relative; 
        }
        
        .nav-menu a:hover { 
            color: var(--p2);
            transform: translateY(-2px);
        }
        
        .nav-menu a::after { 
            content: ''; 
            position: absolute; 
            bottom: -5px; 
            left: 50%; 
            width: 0; 
            height: 2px; 
            background: var(--grad); 
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-menu a:hover::after { 
            width: 100%; 
        }
        
        .btn-login { 
            background: var(--grad); 
            color: white !important; 
            padding: 10px 24px; 
            border-radius: 25px; 
            box-shadow: 0 4px 15px rgba(29,130,8,0.2);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29,130,8,0.4);
        }
        
        .btn-login::after { display: none; }
        
        .hamburger { 
            display: none; 
            flex-direction: column; 
            cursor: pointer; 
            gap: 5px; 
        }
        
        .hamburger div { 
            width: 28px; 
            height: 3px; 
            background: var(--p3); 
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .hamburger:hover div:nth-child(1) {
            transform: translateX(5px);
        }
        
        .hamburger:hover div:nth-child(3) {
            transform: translateX(-5px);
        }

        /* Hero Section */
        .hero { 
            padding: 8rem 0 6rem; 
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            position: relative;
            overflow: hidden;
            transition: background 0.3s ease;
        }

        [data-theme="dark"] .hero {
            background: linear-gradient(135deg, #1f2937, #111827);
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(57,166,22,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .hero-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 2rem; 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 4rem; 
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .hero-content {
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .hero-content h1 { 
            color: var(--p3); 
            font-size: 3.5rem; 
            margin-bottom: 1.5rem; 
            font-weight: 800; 
            line-height: 1.1;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }

        [data-theme="dark"] .hero-content h1 {
            color: var(--p1);
        }
        
        .hero-content h1 .highlight { 
            background: var(--grad); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
        }

        /* Typed Text Effect */
        .typed-text {
            border-right: 3px solid var(--p1);
            animation: blink 0.7s infinite;
        }

        @keyframes blink {
            0%, 100% { border-color: transparent; }
            50% { border-color: var(--p1); }
        }
        
        .hero-content p { 
            font-size: 1.2rem; 
            color: var(--text-secondary); 
            margin-bottom: 2rem; 
            line-height: 1.8;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }
        
        .hero-buttons { 
            display: flex; 
            gap: 1rem; 
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.6s backwards;
        }
        
        .btn-primary { 
            background: var(--grad); 
            color: white; 
            padding: 16px 40px; 
            border-radius: 30px; 
            text-decoration: none; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            box-shadow: 0 8px 25px rgba(29,130,8,0.3); 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover { 
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 35px rgba(29,130,8,0.4);
        }
        
        .btn-primary i {
            transition: transform 0.3s ease;
        }
        
        .btn-primary:hover i {
            transform: rotate(360deg);
        }
        
        .btn-secondary { 
            background: white; 
            color: var(--p3); 
            padding: 16px 40px; 
            border-radius: 30px; 
            text-decoration: none; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            border: 2px solid var(--p2); 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        [data-theme="dark"] .btn-secondary {
            background: var(--bg-secondary);
            color: var(--p1);
        }
        
        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--grad);
            transition: width 0.3s ease;
            z-index: -1;
        }
        
        .btn-secondary:hover::before {
            width: 100%;
        }
        
        .btn-secondary:hover { 
            color: white; 
            transform: translateY(-3px);
            border-color: var(--p3);
        }
        
        .hero-image {
            animation: fadeInRight 1s ease-out 0.4s backwards;
            position: relative;
        }
        
        @keyframes fadeInRight {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .hero-image img { 
            width: 100%; 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            animation: floatImage 6s ease-in-out infinite;
        }
        
        @keyframes floatImage {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-image:hover img {
            transform: scale(1.05);
        }
        
        .hero-stats { 
            grid-column: 1 / -1; 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 2rem; 
            margin-top: 3rem; 
            padding-top: 3rem; 
            border-top: 1px solid rgba(29,130,8,0.2);
        }
        
        .stat-item { 
            text-align: center;
            animation: fadeInUp 1s ease-out backwards;
            transition: transform 0.3s ease;
        }
        
        .stat-item:nth-child(1) { animation-delay: 0.8s; }
        .stat-item:nth-child(2) { animation-delay: 1s; }
        .stat-item:nth-child(3) { animation-delay: 1.2s; }
        
        .stat-item:hover {
            transform: translateY(-10px);
        }
        
       .stat-number { 
    font-size: 2.5rem; 
    font-weight: 800; 
    background: var(--grad); 
    -webkit-background-clip: text; 
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

/* Tambahkan glow effect saat counting */
.stat-number.counting::after {
    content: '';
    position: absolute;
    inset: -10px;
    background: radial-gradient(circle, rgba(57, 166, 22, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    z-index: -1;
    animation: statGlow 1.5s ease-out;
}

@keyframes statGlow {
    0% { 
        transform: scale(0);
        opacity: 0;
    }
    50% { 
        transform: scale(1.2);
        opacity: 1;
    }
    100% { 
        transform: scale(1);
        opacity: 0;
    }
}

        
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .stat-label { 
            color: var(--text-secondary); 
            font-size: 0.95rem; 
        }

        /* Features Section */
        .features { 
            padding: 6rem 0; 
            background: var(--bg-primary);
            position: relative;
        }
        
        .features-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 2rem; 
        }
        
        .section-header { 
            text-align: center; 
            margin-bottom: 4rem;
        }
        
        .section-badge { 
            display: inline-block; 
            background: linear-gradient(135deg, #dcfce7, #f0fdf4); 
            color: var(--p3); 
            padding: 8px 20px; 
            border-radius: 20px; 
            font-size: 0.9rem; 
            font-weight: 600; 
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .section-header h2 { 
            color: var(--p3); 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin-bottom: 1rem;
        }

        [data-theme="dark"] .section-header h2 {
            color: var(--p1);
        }
        
        .section-header p { 
            color: var(--text-secondary); 
            font-size: 1.1rem; 
            max-width: 600px; 
            margin: 0 auto; 
        }
        
        .features-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 2rem; 
        }
        
        .feature-card { 
            background: var(--bg-primary); 
            padding: 2.5rem; 
            border-radius: 20px; 
            border: 1px solid rgba(57, 166, 22, 0.2); 
            transition: all 0.4s ease; 
            position: relative;
            opacity: 0;
            transform: translateY(50px);
        }

        [data-theme="dark"] .feature-card {
            background: var(--bg-secondary);
        }
        
        .feature-card.animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .feature-card:nth-child(2).animate-in {
            animation-delay: 0.2s;
        }
        
        .feature-card:nth-child(3).animate-in {
            animation-delay: 0.4s;
        }
        
        .feature-card::before { 
            content: ''; 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 4px; 
            background: var(--grad); 
            transform: scaleX(0); 
            transition: transform 0.4s ease;
        }
        
        .feature-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            border-color: var(--p1); 
        }
        
        .feature-card:hover::before { 
            transform: scaleX(1); 
        }
        
        .feature-icon { 
            width: 70px; 
            height: 70px; 
            background: linear-gradient(135deg, #dcfce7, #f0fdf4); 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 1.5rem; 
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon { 
            background: var(--grad);
            transform: rotateY(360deg);
        }
        
        .feature-icon i { 
            font-size: 32px; 
            color: var(--p2); 
            transition: all 0.3s ease; 
        }
        
        .feature-card:hover .feature-icon i { 
            color: white; 
            transform: scale(1.1); 
        }
        
        .feature-card h3 { 
            color: var(--p3); 
            font-size: 1.4rem; 
            margin-bottom: 1rem; 
            font-weight: 700; 
        }

        [data-theme="dark"] .feature-card h3 {
            color: var(--p1);
        }
        
        .feature-card p { 
            color: var(--text-secondary); 
            line-height: 1.8; 
        }

        /* Services Section */
        .services { 
            padding: 6rem 0; 
            background: var(--bg-secondary);
        }
        
        .services-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 2rem; 
        }
        
        .services-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 2rem; 
        }
        
        .service-card { 
            background: var(--bg-primary); 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            transition: all 0.4s ease; 
            cursor: pointer;
            opacity: 0;
            transform: translateY(50px);
        }
        
        .service-card.animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .service-card:nth-child(2).animate-in { animation-delay: 0.1s; }
        .service-card:nth-child(3).animate-in { animation-delay: 0.2s; }
        .service-card:nth-child(4).animate-in { animation-delay: 0.3s; }
        .service-card:nth-child(5).animate-in { animation-delay: 0.4s; }
        .service-card:nth-child(6).animate-in { animation-delay: 0.5s; }
        .service-card:nth-child(7).animate-in { animation-delay: 0.6s; }
        .service-card:nth-child(8).animate-in { animation-delay: 0.7s; }
        
        .service-card:hover { 
            transform: translateY(-10px) scale(1.03); 
            box-shadow: 0 20px 40px rgba(29,130,8,0.15); 
        }
        
        .service-image { 
            width: 100%; 
            height: 200px; 
            overflow: hidden; 
            background: linear-gradient(135deg, #dcfce7, #f0fdf4); 
            display: flex; 
            align-items: center; 
            justify-content: center;
            position: relative;
        }
        
        .service-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--grad);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .service-card:hover .service-image::before {
            opacity: 0.2;
        }
        
        .service-image img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .service-card:hover .service-image img {
            transform: scale(1.1) rotate(2deg);
        }
        
        .service-content { 
            padding: 1.5rem; 
            text-align: center;
            position: relative;
        }
        
        .service-content::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--grad);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .service-card:hover .service-content::before {
            width: 80%;
        }
        
        .service-content h3 { 
            color: var(--p3); 
            font-size: 1rem; 
            font-weight: 600; 
            line-height: 1.4;
            transition: color 0.3s ease;
        }

        [data-theme="dark"] .service-content h3 {
            color: var(--p1);
        }
        
        .service-card:hover .service-content h3 {
            color: var(--p2);
        }

        /* Testimonial Section */
        .testimonials {
            padding: 6rem 0;
            background: var(--bg-primary);
            position: relative;
            overflow: hidden;
        }

        .testimonials::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
            background: radial-gradient(circle, rgba(57, 166, 22, 0.05) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        .testimonials-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .testimonial-card {
            background: var(--bg-primary);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(57, 166, 22, 0.2);
            transition: all 0.4s ease;
            position: relative;
            opacity: 0;
            transform: translateY(50px);
        }

        [data-theme="dark"] .testimonial-card {
            background: var(--bg-secondary);
        }

        .testimonial-card.animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .testimonial-card:nth-child(2).animate-in { animation-delay: 0.2s; }
        .testimonial-card:nth-child(3).animate-in { animation-delay: 0.4s; }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 5rem;
            color: var(--p1);
            opacity: 0.1;
            font-family: serif;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(29, 130, 8, 0.15);
        }

        .testimonial-rating {
            color: #FFC107;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--grad);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .testimonial-info h4 {
            color: var(--p3);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        [data-theme="dark"] .testimonial-info h4 {
            color: var(--p1);
        }

        .testimonial-info p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        /* Contact Form Section */
        .contact-section {
            padding: 6rem 0;
            background: var(--bg-secondary);
        }

        .contact-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 3rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            gap: 1.5rem;
            padding: 2rem;
            background: var(--bg-primary);
            border-radius: 15px;
            border-left: 4px solid var(--p1);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-50px);
        }

        [data-theme="dark"] .contact-item {
            background: var(--bg-secondary);
        }

        .contact-item.animate-in {
            animation: slideInRight 0.6s ease-out forwards;
        }

        .contact-item:nth-child(2).animate-in { animation-delay: 0.2s; }
        .contact-item:nth-child(3).animate-in { animation-delay: 0.4s; }

        @keyframes slideInRight {
            to { opacity: 1; transform: translateX(0); }
        }

        .contact-item:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(29, 130, 8, 0.1);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #dcfce7, #f0fdf4);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--p2);
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .contact-details h4 {
            color: var(--p3);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        [data-theme="dark"] .contact-details h4 {
            color: var(--p1);
        }

        .contact-details p {
            color: var(--text-secondary);
        }

        .contact-form {
            background: var(--bg-primary);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        [data-theme="dark"] .contact-form {
            background: var(--bg-secondary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(57, 166, 22, 0.2);
            border-radius: 10px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .form-group input,
        [data-theme="dark"] .form-group textarea {
            background: var(--bg-secondary);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--p1);
            box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-error {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        .form-group.error input,
        .form-group.error textarea {
            border-color: #ef4444;
        }

        .form-group.error .form-error {
            display: block;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .submit-btn:hover::before {
            width: 400px;
            height: 400px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(29, 130, 8, 0.4);
        }

        /* Why Choose Section */
        .why-choose { 
            padding: 6rem 0; 
            background: var(--bg-primary);
            position: relative;
        }
        
        .why-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 2rem; 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 4rem; 
            align-items: center; 
        }
        
        .why-image { 
            width: 100%; 
            height: 400px; 
            border-radius: 20px; 
            background: linear-gradient(135deg, #dcfce7, #f0fdf4); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.15); 
            overflow: hidden;
            position: relative;
        }
        
        .why-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(57,166,22,0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite;
        }
        
        .why-image img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover;
            transition: transform 0.5s ease;
            position: relative;
            z-index: 1;
        }
        
        .why-image:hover img {
            transform: scale(1.1);
        }
        
        .why-content h2 { 
            color: var(--p3); 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin-bottom: 1.5rem; 
        }

        [data-theme="dark"] .why-content h2 {
            color: var(--p1);
        }
        
        .why-content p { 
            color: var(--text-secondary); 
            font-size: 1.1rem; 
            line-height: 1.8; 
            margin-bottom: 2rem; 
        }
        
        .why-list { 
            display: flex; 
            flex-direction: column; 
            gap: 1rem; 
        }
        
        .why-item { 
            display: flex; 
            gap: 1rem; 
            padding: 1.5rem; 
            background: linear-gradient(135deg, rgba(240, 253, 244, 0.5), rgba(220, 252, 231, 0.5)); 
            border-radius: 12px; 
            border-left: 4px solid var(--p1); 
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-50px);
        }

        [data-theme="dark"] .why-item {
            background: rgba(57, 166, 22, 0.1);
        }
        
        .why-item.animate-in {
            animation: slideInRight 0.6s ease-out forwards;
        }
        
        .why-item:nth-child(2).animate-in { animation-delay: 0.2s; }
        .why-item:nth-child(3).animate-in { animation-delay: 0.4s; }
        .why-item:nth-child(4).animate-in { animation-delay: 0.6s; }
        
        .why-item:hover { 
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 5px 15px rgba(29,130,8,0.1);
        }
        
        .why-item i { 
            color: var(--p2); 
            font-size: 24px; 
            margin-top: 4px;
            transition: transform 0.3s ease;
        }
        
        .why-item:hover i {
            transform: scale(1.2) rotate(360deg);
        }
        
        .why-item-content h4 { 
            color: var(--p3); 
            font-size: 1.1rem; 
            font-weight: 600; 
            margin-bottom: 0.3rem; 
        }

        [data-theme="dark"] .why-item-content h4 {
            color: var(--p1);
        }
        
        .why-item-content p { 
            color: var(--text-secondary); 
            font-size: 0.95rem; 
            margin: 0; 
        }

        /* CTA Section */
        .cta-section { 
            padding: 6rem 0; 
            background: var(--grad); 
            color: white; 
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.05) 10px,
                rgba(255,255,255,0.05) 20px
            );
            animation: moveStripes 20s linear infinite;
        }
        
        @keyframes moveStripes {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .cta-container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }
        
        .cta-container h2 { 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }
        
        .cta-container p { 
            font-size: 1.2rem; 
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }
        
        .cta-button { 
            background: white; 
            color: var(--p3); 
            padding: 18px 50px; 
            border-radius: 30px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 1.2rem; 
            display: inline-flex; 
            align-items: center; 
            gap: 12px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.2); 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--grad-reverse);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }
        
        .cta-button:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .cta-button:hover { 
            transform: translateY(-4px) scale(1.05);
            color: white;
        }
        
        .cta-button i,
        .cta-button span {
            position: relative;
            z-index: 1;
        }

        /* Footer */
        .footer { 
            background: #1f2937; 
            color: white; 
            padding: 4rem 0 2rem; 
        }
        
        .footer-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 2rem; 
        }
        
        .footer-content { 
            display: grid; 
            grid-template-columns: 2fr 1fr 1fr 1fr; 
            gap: 3rem; 
            margin-bottom: 3rem; 
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            padding: 12px 20px;
            background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(29, 130, 8, 0.05));
            border-radius: 15px;
            border-left: 4px solid var(--p1);
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .footer-logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .footer-logo:hover::before {
            left: 100%;
        }

        .footer-logo:hover {
            transform: translateX(5px);
            border-left-width: 6px;
            box-shadow: 0 5px 20px rgba(57, 166, 22, 0.2);
        }

        .footer-logo img {
            height: 50px;
            border-radius: 10px;
            transition: all 0.4s ease;
            filter: drop-shadow(0 4px 8px rgba(57, 166, 22, 0.3));
        }

        .footer-logo:hover img {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 6px 12px rgba(57, 166, 22, 0.5));
        }

        .footer-about h3 { 
            font-size: 1.5rem; 
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .footer-logo:hover h3 {
            color: var(--p1);
            text-shadow: 0 0 10px rgba(57, 166, 22, 0.5);
        }
        
        .footer-about p { 
            color: #d1d5db; 
            line-height: 1.8; 
            margin-bottom: 1.5rem;
            padding-left: 5px;
        }
        
        .social-links { 
            display: flex; 
            gap: 1rem;
            padding-left: 5px;
        }
        
        .social-link { 
            width: 40px; 
            height: 40px; 
            background: rgba(255,255,255,0.1); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            text-decoration: none; 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .social-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--grad);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        
        .social-link:hover::before {
            width: 100%;
            height: 100%;
        }
        
        .social-link:hover { 
            transform: translateY(-3px) rotate(360deg); 
        }
        
        .social-link i {
            position: relative;
            z-index: 1;
        }
        
        .footer-section h4 { 
            font-size: 1.1rem; 
            margin-bottom: 1.5rem; 
            color: var(--p1); 
        }
        
        .footer-links { 
            list-style: none; 
        }
        
        .footer-links li { 
            margin-bottom: 0.8rem;
            transition: transform 0.3s ease;
        }
        
        .footer-links li:hover {
            transform: translateX(5px);
        }
        
        .footer-links a { 
            color: #d1d5db; 
            text-decoration: none; 
            transition: color 0.3s ease;
            position: relative;
        }
        
        .footer-links a::before {
            content: '→ ';
            opacity: 0;
            margin-right: 0;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover::before {
            opacity: 1;
            margin-right: 5px;
        }
        
        .footer-links a:hover { 
            color: var(--p1); 
        }
        
        .footer-contact p { 
            color: #d1d5db; 
            margin-bottom: 0.8rem; 
            display: flex; 
            gap: 0.8rem;
            transition: transform 0.3s ease;
        }
        
        .footer-contact p:hover {
            transform: translateX(5px);
        }
        
        .footer-contact i { 
            color: var(--p1);
            transition: transform 0.3s ease;
        }
        
        .footer-contact p:hover i {
            transform: scale(1.2) rotate(360deg);
        }
        
        .footer-bottom { 
            text-align: center; 
            padding-top: 2rem; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            color: #9ca3af; 
        }

        /* Scroll to Top Button */
        #toTopBtn { 
            display: none; 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            z-index: 999; 
            border: none; 
            background: var(--grad); 
            color: white; 
            cursor: pointer; 
            width: 55px; 
            height: 55px; 
            border-radius: 50%; 
            font-size: 1.3rem; 
            box-shadow: 0 8px 25px rgba(29,130,8,0.3); 
            transition: all 0.3s ease;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        #toTopBtn:hover { 
            transform: translateY(-5px) scale(1.1) rotate(360deg);
            box-shadow: 0 12px 35px rgba(29,130,8,0.5);
            animation: none;
        }
        
        #toTopBtn::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--p1), var(--p2), var(--p3));
            opacity: 0;
            z-index: -1;
            filter: blur(15px);
            transition: opacity 0.4s ease;
        }
        
        #toTopBtn:hover::before {
            opacity: 0.3;
            animation: glowPulse 2s ease-in-out infinite;
        }
        
        @keyframes glowPulse {
            0%, 100% { filter: blur(10px); opacity: 0.7; }
            50% { filter: blur(15px); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 1024px) { 
            .hero-container, .why-container, .contact-grid { grid-template-columns: 1fr; } 
            .features-grid, .testimonials-grid { grid-template-columns: repeat(2, 1fr); } 
            .services-grid { grid-template-columns: repeat(3, 1fr); } 
            .footer-content { grid-template-columns: repeat(2, 1fr); } 
            .dark-mode-toggle { top: 90px; }
        }
        
        @media (max-width: 768px) { 
            .hamburger { display: flex; } 
            .nav-menu { 
                display: none; 
                flex-direction: column; 
                position: absolute; 
                top: 100%; 
                left: 0; 
                width: 100%; 
                background: var(--bg-primary); 
                padding: 1.5rem; 
                gap: 1rem; 
                box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
            } 
            .nav-menu.show { display: flex; } 
            .hero-content h1 { font-size: 2.5rem; } 
            .hero-stats { grid-template-columns: 1fr; } 
            .features-grid, .services-grid, .testimonials-grid { grid-template-columns: 1fr; } 
            .footer-content { grid-template-columns: 1fr; }
            .particle { display: none; }
            .dark-mode-toggle { top: 80px; right: 20px; width: 50px; height: 50px; }
        }
    
/* ========== PROFILE DROPDOWN DI NAVBAR ========== */
.nav-profile-dropdown {
    position: relative;
}

.btn-profile {
    display: flex !important;
    align-items: center;
    gap: 10px;
    background: var(--grad) !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 25px !important;
    box-shadow: 0 4px 15px rgba(29,130,8,0.2);
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
}

.btn-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29,130,8,0.4);
}

.nav-profile-img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
}

.btn-profile i.fa-user-circle {
    font-size: 28px;
}

.btn-profile i.fa-chevron-down {
    font-size: 12px;
    transition: transform 0.3s ease;
}

.btn-profile:hover i.fa-chevron-down {
    transform: rotate(180deg);
}

.profile-dropdown-menu {
    position: absolute;
    top: 120%;
    right: 0;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    min-width: 220px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    overflow: hidden;
}

[data-theme="dark"] .profile-dropdown-menu {
    background: #1a1a1a;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

.profile-dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex !important;
    align-items: center;
    gap: 12px;
    padding: 14px 20px !important;
    color: var(--text-primary) !important;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.dropdown-item:hover {
    background: var(--bg-secondary);
    color: var(--p2) !important;
    padding-left: 26px !important;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.dropdown-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 8px 0;
}

.logout-item {
    color: #e74c3c !important;
}

.logout-item:hover {
    background: #fee;
    color: #c0392b !important;
}

/* Responsive untuk dropdown */
@media (max-width: 768px) {
    .profile-dropdown-menu {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        background: transparent;
        margin-top: 10px;
    }

    [data-theme="dark"] .profile-dropdown-menu {
        background: transparent;
    }

    .dropdown-item {
        border-radius: 8px;
        margin-bottom: 5px;
        background: rgba(57, 166, 22, 0.1);
    }

    .dropdown-item:hover {
        background: rgba(57, 166, 22, 0.2);
    }

    .dropdown-divider {
        display: none;
    }
}

    </style>
</head>
<body>
   
        
        <!-- Floating Particles -->
        <div class="loader-particle"></div>
        <div class="loader-particle"></div>
        <div class="loader-particle"></div>
        <div class="loader-particle"></div>
    </div>


    <!-- Dark Mode Toggle -->
    <button class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Animated Background Particles -->
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

    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <img src="{{ asset('images/logo.hijau.png') }}" alt="Logo MedEase">
                <span>Praktek Bersama by Fathurrahman</span>
            </div>
            <div class="hamburger" onclick="document.getElementById('navMenu').classList.toggle('show')">
                <div></div><div></div><div></div>
            </div>
            <nav>
                <ul class="nav-menu" id="navMenu"> 
            <li><a href="#layanan">Layanan</a></li> 
            <li><a href="#testimoni">Testimoni</a></li> 
            <li><a href="#mengapa">Mengapa Kami</a></li> 
            <li><a href="#kontak">Kontak</a></li> 

             
            @auth
                <!-- Jika sudah login, tampilkan dropdown profil -->
                <li class="nav-profile-dropdown">
                    <a href="#" class="btn-profile" id="profileDropdownBtn">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                 alt="Profil" 
                                 class="nav-profile-img">
                        @else
                            <i class="fas fa-user-circle"></i>
                        @endif
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="profile-dropdown-menu" id="profileDropdownMenu">
                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                            <i class="fas fa-home"></i> Masuk ke Panel
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" onclick="confirmLogoutWelcome(event)" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            @else
                <!-- Jika belum login, tampilkan tombol login -->
                <li><a href="{{ route('login') }}" class="btn-login">Login</a></li>
            @endauth
        </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Layanan Kesehatan <span class="highlight typed-text"></span> untuk Keluarga Indonesia</h1>
                <p>Praktek Bersama Fathurrahman hadir dengan layanan kesehatan berkualitas, tenaga medis profesional, dan fasilitas modern.</p>
                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn-primary"><i class="fas fa-calendar-check"></i> Daftar Sekarang</a>
                    <a href="#layanan" class="btn-secondary"><i class="fas fa-info-circle"></i> Lihat Layanan</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('images/gedung.jpg') }}" alt="Klinik Praktek Bersama">
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Pasien Puas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Layanan Medis</div>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="features">
        <div class="features-container">
            <div class="section-header">
                <span class="section-badge">KEUNGGULAN KAMI</span>
                <h2>Mengapa Memilih Kami?</h2>
                <p>Layanan terpadu untuk semua pihak dalam ekosistem kesehatan</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Untuk Pasien</h3>
                    <p>Akses mudah ke layanan kesehatan berkualitas dengan dokter berpengalaman dan konsultasi online.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-user-md"></i></div>
                    <h3>Tenaga Medis</h3>
                    <p>Dukungan teknologi dan akses rekam medis digital untuk pelayanan yang lebih efektif.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-clipboard-list"></i></div>
                    <h3>Administrasi</h3>
                    <p>Sistem terintegrasi untuk mengelola fasilitas, data pasien, dan operasional dengan mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="services">
        <div class="services-container">
            <div class="section-header">
                <span class="section-badge">LAYANAN KAMI</span>
                <h2>Beragam Layanan Kesehatan Profesional</h2>
                <p>Untuk kebutuhan kesehatan keluarga Anda</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/bidan.jpeg') }}" alt="Spesialis Kebidanan & Kandungan"></div>
                    <div class="service-content"><h3>Spesialis Kebidanan & Kandungan</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/dktrumum.jpeg') }}" alt="Dokter Umum"></div>
                    <div class="service-content"><h3>Dokter Umum</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/kecantika.jpeg') }}" alt="Aesthetic Anti Aging"></div>
                    <div class="service-content"><h3>Aesthetic Anti Aging</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/bidanmandiri.jpeg') }}" alt="Bidan Mandiri"></div>
                    <div class="service-content"><h3>Bidan Mandiri</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/babykids.jpeg') }}" alt="Baby, Kids & Treatment"></div>
                    <div class="service-content"><h3>Baby, Kids & Treatment</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/senamhamil.jpeg') }}" alt="Senam Hamil / Prenatal Yoga"></div>
                    <div class="service-content"><h3>Senam Hamil / Prenatal Yoga</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/apoteker.jpeg') }}" alt="Apoteker"></div>
                    <div class="service-content"><h3>Apoteker</h3></div>
                </div>
                <div class="service-card">
                    <div class="service-image"><img src="{{ asset('images/fisiotrapis.jpeg') }}" alt="Fisioterapis"></div>
                    <div class="service-content"><h3>Fisioterapis</h3></div>
                </div>
            </div>
        </div>
    </section>

    <section id="testimoni" class="testimonials">
        <div class="testimonials-container">
            <div class="section-header">
                <span class="section-badge">TESTIMONI</span>
                <h2>Apa Kata Pasien Kami?</h2>
                <p>Pengalaman nyata dari pasien yang telah merasakan layanan kami</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Pelayanan sangat memuaskan! Dokter dan perawat sangat ramah dan profesional. Fasilitas yang modern membuat saya merasa nyaman."
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">SA</div>
                        <div class="testimonial-info">
                            <h4>Siti Aminah</h4>
                            <p>Pasien Rawat Jalan</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Proses pendaftaran mudah dan cepat. Sistem antrian online sangat membantu. Dokter memberikan penjelasan yang detail dan mudah dipahami."
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">BP</div>
                        <div class="testimonial-info">
                            <h4>Budi Prasetyo</h4>
                            <p>Pasien Konsultasi Online</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Saya sangat puas dengan layanan bidan di sini. Sangat perhatian dan sabar dalam menangani kehamilan saya. Highly recommended!"
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">DW</div>
                        <div class="testimonial-info">
                            <h4>Dewi Wulandari</h4>
                            <p>Pasien Kebidanan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="mengapa" class="why-choose">
        <div class="why-container">
            <div class="why-image">
                <img src="{{ asset('images/gbr.jpeg') }}" alt="Pasien dan Dokter">
            </div>
            <div class="why-content">
                <h2>Mengapa Praktek Bersama Fathurrahman?</h2>
                <p>Kami berkomitmen memberikan layanan kesehatan terbaik dengan standar profesional tinggi.</p>
                <div class="why-list">
                    <div class="why-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="why-item-content">
                            <h4>Tenaga Medis Berpengalaman</h4>
                            <p>Tim dokter dan perawat profesional dengan sertifikasi lengkap</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="why-item-content">
                            <h4>Fasilitas Modern</h4>
                            <p>Peralatan medis canggih dan ruangan yang nyaman</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="why-item-content">
                            <h4>Pelayanan 24/7</h4>
                            <p>Siap melayani Anda kapan saja untuk situasi darurat</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="why-item-content">
                            <h4>Harga Terjangkau</h4>
                            <p>Layanan berkualitas dengan biaya yang kompetitif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kontak" class="contact-section">
        <div class="contact-container">
            <div class="section-header">
                <span class="section-badge">HUBUNGI KAMI</span>
                <h2>Butuh Bantuan? Hubungi Kami</h2>
                <p>Kami siap membantu Anda 24/7</p>
            </div>
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Alamat Kami</h4>
                            <p>Jl. I. Mohammad Ali, Bengkalis, Riau, Indonesia</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-details">
                            <h4>WhatsApp</h4>
                            <p>0822 1117 8167</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>fathurrahmanbengkalis@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" required>
                            <span class="form-error">Nama wajib diisi</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                            <span class="form-error">Email tidak valid</span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor Telepon *</label>
                            <input type="tel" id="phone" name="phone" required>
                            <span class="form-error">Nomor telepon wajib diisi</span>
                        </div>
                        <div class="form-group">
                            <label for="message">Pesan *</label>
                            <textarea id="message" name="message" required></textarea>
                            <span class="form-error">Pesan wajib diisi</span>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-container">
            <h2>Siap Memulai Perjalanan Kesehatan Anda?</h2>
            <p>Daftar sekarang dan dapatkan konsultasi gratis untuk kunjungan pertama Anda</p>
            <a href="{{ route('login') }}" class="cta-button">
                <i class="fas fa-calendar-alt"></i>
                <span>Buat Janji Temu Sekarang</span>
            </a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo.hijau.png') }}" alt="Logo MedEase">
                        <h3>MedEase</h3>
                    </div>
                    <p>Layanan kesehatan terpercaya untuk keluarga Indonesia dengan tenaga medis profesional dan fasilitas modern.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Tautan Cepat</h4>
                    <ul class="footer-links">
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#testimoni">Testimoni</a></li>
                        <li><a href="#mengapa">Mengapa Kami</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="#">Konsultasi Online</a></li>
                        <li><a href="#">Pemeriksaan Umum</a></li>
                        <li><a href="#">Layanan Darurat</a></li>
                        <li><a href="#">Rawat Inap</a></li>
                    </ul>
                </div>
                <div class="footer-section footer-contact">
                    <h4>Kontak Kami</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. I. Mohammad Ali, Bengkalis, Riau</p>
                    <p><i class="fab fa-whatsapp"></i> 0822 1117 8167</p>
                    <p><i class="fas fa-envelope"></i> fathurrahmanbengkalis@gmail.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 MEDEASE | Praktek Bersama by Fathurrahman | RPL 5B</p>
            </div>
        </div>
    </footer>

    <button onclick="window.scrollTo({top:0,behavior:'smooth'})" id="toTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Loading Screen
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loader').classList.add('fade-out');
            }, 2000);
        });

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

        // Typed Text Effect
        const words = ['Terpercaya', 'Profesional', 'Modern', 'Berkualitas'];
        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typedElement = document.querySelector('.typed-text');

        function type() {
            const currentWord = words[wordIndex];
            
            if (isDeleting) {
                typedElement.textContent = currentWord.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typedElement.textContent = currentWord.substring(0, charIndex + 1);
                charIndex++;
            }

            if (!isDeleting && charIndex === currentWord.length) {
                isDeleting = true;
                setTimeout(type, 2000);
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                wordIndex = (wordIndex + 1) % words.length;
                setTimeout(type, 500);
            } else {
                setTimeout(type, isDeleting ? 50 : 100);
            }
        }

        type();

        // Scroll to Top Button
        window.onscroll = () => {
            document.getElementById("toTopBtn").style.display = 
                (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) ? "block" : "none";
        };

        // Scroll Animation Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .service-card, .testimonial-card, .why-item, .contact-item').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    document.getElementById('navMenu').classList.remove('show');
                }
            });
        });

        // Contact Form Validation
        const contactForm = document.getElementById('contactForm');
        
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const formGroups = this.querySelectorAll('.form-group');
            
            formGroups.forEach(group => {
                const input = group.querySelector('input, textarea');
                group.classList.remove('error');
                
                if (!input.value.trim()) {
                    group.classList.add('error');
                    isValid = false;
                } else if (input.type === 'email' && !isValidEmail(input.value)) {
                    group.classList.add('error');
                    isValid = false;
                }
            });
            
            if (isValid) {
                alert('Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
                contactForm.reset();
            }
        });

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Ripple effect
        document.querySelectorAll('.btn-primary, .btn-secondary, .btn-login, .cta-button, .submit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Parallax
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroImage = document.querySelector('.hero-image img');
            if (heroImage && scrolled < 800) {
                heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });

        // Counter animation
        // Counter animation yang lebih keren dengan easing
const animateCounter = (element, target) => {
    let current = 0;
    const duration = 8000; // 2 detik
    const startTime = performance.now();
    
    // Tambah class counting untuk efek glow
    element.classList.add('counting');
    
    const easeOutQuart = (t) => 1 - Math.pow(1 - t, 4);
    
    const updateCounter = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easedProgress = easeOutQuart(progress);
        
        current = Math.floor(easedProgress * target);
        
        // Format number dengan separator
        const formattedNumber = current.toLocaleString('id-ID');
        element.textContent = formattedNumber + '+';
        
        // Scale effect saat counting
        const scale = 1 + (Math.sin(progress * Math.PI) * 0.1);
        element.style.transform = `scale(${scale})`;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString('id-ID') + '+';
            element.style.transform = 'scale(1)';
            
            // Remove counting class setelah selesai
            setTimeout(() => {
                element.classList.remove('counting');
            }, 500);
        }
    };
    
    requestAnimationFrame(updateCounter);
};

// Observer untuk trigger animation saat scroll
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumber = entry.target.querySelector('.stat-number');
            const targetText = statNumber.textContent;
            const target = parseInt(targetText.replace(/[^0-9]/g, ''));
            
            // Tambahkan delay berbeda untuk setiap item
            const index = Array.from(document.querySelectorAll('.stat-item')).indexOf(entry.target);
            
            setTimeout(() => {
                animateCounter(statNumber, target);
            }, index * 200); // Delay 200ms antar item
            
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

// Observe semua stat items
document.querySelectorAll('.stat-item').forEach(item => {
    statsObserver.observe(item);
});


        // ===== LOADING OVERLAY FUNCTIONS =====
function showLoading(text = 'Memproses') {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = overlay.querySelector('.loading-text');
    
    if (loadingText) {
        loadingText.textContent = text;
    }
    
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Auto attach to buttons and forms
document.addEventListener('DOMContentLoaded', function() {
    // Attach to login/register buttons
    document.querySelectorAll('.btn-login, .btn-primary').forEach(button => {
        const originalClick = button.onclick;
        button.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && (href.includes('login') || href.includes('register') || href.includes('daftar'))) {
                showLoading('Memuat halaman');
            }
            if (originalClick) originalClick.call(this, e);
        });
    });
    
    // Attach to submit buttons
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                showLoading('Mengirim data');
            }
        });
    });
    
    // Contact form special handling
    const contactForm = document.getElementById('contactForm');
    const originalSubmit = contactForm.onsubmit;
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        const formGroups = this.querySelectorAll('.form-group');
        
        formGroups.forEach(group => {
            const input = group.querySelector('input, textarea');
            group.classList.remove('error');
            
            if (!input.value.trim()) {
                group.classList.add('error');
                isValid = false;
            } else if (input.type === 'email' && !isValidEmail(input.value)) {
                group.classList.add('error');
                isValid = false;
            }
        });
        
        if (isValid) {
            showLoading('Mengirim pesan');
            
            // Simulate sending
            setTimeout(() => {
                hideLoading();
                alert('Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
                contactForm.reset();
            }, 2000);
        }
    });
});

    </script>

    <script>
    // ========== DROPDOWN PROFILE TOGGLE ==========
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profileDropdownBtn');
        const dropdownMenu = document.getElementById('profileDropdownMenu');

        if (profileBtn && dropdownMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });

    // ========== LOGOUT CONFIRMATION FOR WELCOME PAGE ==========
    function confirmLogoutWelcome(event) {
        event.preventDefault();

        if (confirm('Yakin ingin logout?')) {
            // Create and submit logout form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

</body>
</html>
