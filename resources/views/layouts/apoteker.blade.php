<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>@yield('title') - Praktek Bersama by Fathurrahman</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            overflow-x: hidden;
            transition: background-color 0.5s ease, color 0.3s ease;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-secondary, #f9fafb);
            color: var(--text-primary, #374151);
            position: relative;
        }

        /* Background animated particles */
        body::before {
            content: '';
            position: fixed;
            top: 0; left:0;
            width: 100%; height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(57,166,22,0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(29,130,8,0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(12,91,0,0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: backgroundPulse 15s ease-in-out infinite;
        }
        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            padding: 0;
            overflow-y: auto;
            transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
            z-index: 1000;
            box-shadow: 4px 0 30px rgba(0,0,0,0.2);
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .sidebar.show {
            transform: translateX(0);
        }

        /* Sidebar Brand */
        .sidebar-brand {
            text-align: center;
            padding: 30px 20px;
            background: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            animation: fadeInDown 0.6s ease-out;
        }
        .sidebar-brand::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .sidebar-brand a {
            color: white;
            text-decoration: none;
            display: block;
            position: relative;
            z-index: 1;
        }
        .brand-logo {
            width: 70px;
            height: auto;
            margin: 0 auto 15px;
            display: flex;
            justify-content: center;
            /* animation: float 3s ease-in-out infinite; */
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .brand-logo img {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }
        .brand-logo:hover img {
            transform: scale(1.1) rotate(5deg);
        }
        .brand-text {
            font-weight: 700;
            font-size: 18px;
            line-height: 1.3;
            margin-bottom: 5px;
            animation: fadeIn 0.8s ease-out 0.2s both;
        }
        .brand-subtitle {
            font-size: 12px;
            opacity: 0.9;
            font-weight: 400;
            animation: fadeIn 0.8s ease-out 0.4s both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* User profile */
        .user-profile {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 20px;
            margin: 20px;
            border-radius: 18px;
            text-decoration: none;
            color: white;
            transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
            border: 1px solid rgba(255,255,255,0.15);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }
        .user-profile:hover {
            background: rgba(255,255,255,0.18);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        }
        .user-profile .avatar-wrapper {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            color: var(--p1);
            border-radius: 50%;
            margin-right: 15px;
            font-size: 26px;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
            animation: pulse 2s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
            50% { box-shadow: 0 8px 25px rgba(57,166,22,0.4); }
        }
        .sidebar-profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-info {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        .user-info h4 {
            font-size: 15px;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .user-info p {
            font-size: 12px;
            margin: 0;
            opacity: 0.95;
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .user-info p i {
            font-size: 10px;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Sidebar nav */
        .sidebar-nav {
            padding: 10px 20px;
            flex-grow: 1;
        }
        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.6);
            font-weight: 600;
            margin: 25px 0 12px 0;
            padding-left: 10px;
            animation: fadeInLeft 0.5s ease-out;
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 6px;
            animation: fadeInLeft 0.5s ease-out both;
        }
        .sidebar-menu li:nth-child(1) { animation-delay: 0.1s; }
        .sidebar-menu li:nth-child(2) { animation-delay: 0.2s; }
        .sidebar-menu li:nth-child(3) { animation-delay: 0.3s; }
        .sidebar-menu li:nth-child(4) { animation-delay: 0.4s; }
        .sidebar-menu li:nth-child(5) { animation-delay: 0.5s; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.88);
            text-decoration: none;
            padding: 15px 18px;
            border-radius: 14px;
            transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
            font-size: 14px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: white;
            transform: scaleY(0);
            transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }
        .sidebar-menu a::after {
            content: '';
            position: absolute;
            right: -50px;
            top: 0;
            width: 50px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-20deg);
            transition: right 0.5s;
        }
        .sidebar-menu a:hover::after {
            right: 110%;
        }
        .sidebar-menu a i {
            margin-right: 14px;
            width: 22px;
            text-align: center;
            font-size: 17px;
            transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.18);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .sidebar-menu a:hover i {
            transform: scale(1.25) rotate(10deg);
        }
        .sidebar-menu a:hover::before {
            transform: scaleY(1);
        }
        .sidebar-menu li.active > a {
            background: rgba(255,255,255,0.22);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .sidebar-menu .active > a::before {
            transform: scaleY(1);
        }
        .nav-badge {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 12px;
            margin-left: auto;
            box-shadow: 0 3px 10px rgba(231,76,60,0.5);
            animation: pulse-badge 2s infinite, glow 2s ease-in-out infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 3px 10px rgba(231,76,60,0.5); }
            50% { box-shadow: 0 4px 20px rgba(231,76,60,0.8); }
        }

        /* Sidebar footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.18);
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }
        .logout-link {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            border-radius: 14px;
            transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
            cursor: pointer;
            background: linear-gradient(135deg, rgba(198,40,40,0.9), rgba(183,28,28,0.9));
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(198,40,40,0.3);
        }
        .logout-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transform: translate(-50%,-50%);
            transition: width 0.6s, height 0.6s;
        }
        .logout-link:hover::before {
            width: 350px;
            height: 350px;
        }
        .logout-link:hover {
            background: linear-gradient(135deg, #c62828, #b71c1c);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(198,40,40,0.6);
        }
        .logout-link i {
            margin-right: 10px;
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        .app-info {
            font-size: 11px;
            color: rgba(255,255,255,0.75);
            margin-top: 18px;
            text-align: center;
            line-height: 1.8;
        }

        /* Main content and topbar */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.4s ease;
            width: calc(100% - 280px);
            position: relative;
            z-index: 1;
        }
        .topbar {
            background: var(--bg-primary, #fff);
            color: var(--text-primary, #374151);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color, #e5e7eb);
            height: 75px;
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            z-index: 900;
            box-shadow: var(--shadow-sm, 0 4px 15px rgba(0,0,0,0.08));
            backdrop-filter: blur(10px);
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
            animation: fadeInLeft 0.6s ease-out;
        }
        .menu-toggle {
            display: none;
            font-size: 24px;
            background: var(--bg-secondary, #f9fafb);
            border: none;
            color: var(--p1,#39A616);
            cursor: pointer;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm, 0 4px 15px rgba(0,0,0,0.08));
        }
        .menu-toggle:hover {
            background: var(--p1, #39A616);
            color: white;
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 15px rgba(57, 166, 22, 0.3);
        }
        #greeting {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 17px;
            color: var(--p1, #39A616);
        }
        #greeting i {
            margin-right: 12px;
            font-size: 22px;
            animation: pulse 2s ease-in-out infinite;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        #datetime {
            font-size: 13px;
            color: var(--text-secondary, #6b7280);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: var(--bg-secondary, #f9fafb);
            border-radius: 14px;
            box-shadow: var(--shadow-sm, 0 4px 15px rgba(0,0,0,0.08));
            transition: all 0.3s ease;
        }
        #datetime:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md, 0 10px 30px rgba(0,0,0,0.12));
        }
        #datetime i {
            color: var(--p1, #39A616);
            font-size: 16px;
        }
        .page-content {
            padding-top: 105px;
            padding-left: 30px;
            padding-right: 30px;
            padding-bottom: 40px;
            flex-grow: 1;
            overflow-y: auto;
            animation: fadeIn 0.8s ease-out;
        }
        .page-content::-webkit-scrollbar { width: 10px; }
        .page-content::-webkit-scrollbar-track {
            background: var(--bg-secondary, #f9fafb);
            border-radius: 10px;
        }
        .page-content::-webkit-scrollbar-thumb {
            background: var(--grad, linear-gradient(135deg, #39A616, #1D8208, #0C5B00));
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .page-content::-webkit-scrollbar-thumb:hover {
            background: var(--grad-reverse, linear-gradient(135deg, #0C5B00, #1D8208, #39A616));
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 50px rgba(0,0,0,0.3);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .topbar {
                left: 0;
            }
            .menu-toggle {
                display: inline-block;
            }
            .page-content {
                padding: 95px 20px 30px;
            }
            #datetime {
                display: none;
            }
        }
        @media (max-width: 576px) {
            .topbar {
                padding: 0 15px;
                height: 65px;
            }
            #greeting {
                font-size: 15px;
            }
            #greeting i {
                font-size: 19px;
            }
            .page-content {
                padding: 85px 15px 20px;
            }
            .brand-logo {
                width: 60px;
            }
            .menu-toggle {
                font-size: 22px;
                padding: 10px;
            }
        }
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Animations from original */
        @keyframes pulse {
            0%, 100% { box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
            50% { box-shadow: 0 8px 25px rgba(57,166,22,0.4); }
        }
        @keyframes fadeInUp {
            from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('apoteker.dashboard') }}" data-turbo-scroll="false">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" />
                </div>
                <div class="brand-text">Praktek Bersama</div>
                <div class="brand-subtitle">Panel Apoteker</div>
            </a>
        </div>

        <a href="#" class="user-profile" style="pointer-events: none;">
            <div class="avatar-wrapper">
                <i class="fas fa-pills"></i>
            </div>
            <div class="user-info">
                <h4 class="text-truncate">{{ Auth::guard('apoteker')->user()?->name ?? 'Apoteker' }}</h4>
                <p><i class="fas fa-circle"></i> Kelola Obat</p>
            </div>
        </a>

        <div class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <ul class="sidebar-menu">
                <li class="{{ request()->routeIs('apoteker.antrian.*') ? 'active' : '' }}">
                    <a href="{{ route('apoteker.antrian.index') }}" data-turbo-scroll="false">
                        <i class="fas fa-prescription-bottle-alt"></i>Antrian Resep
                    </a>
                </li>
                <li class="{{ request()->routeIs('apoteker.riwayat.*') ? 'active' : '' }}">
                    <a href="{{ route('apoteker.riwayat.index') }}" data-turbo-scroll="false">
                        <i class="fas fa-history"></i>Riwayat Resep
                    </a>
                </li>
            </ul>
            <div class="nav-section-title">Manajemen Apotek</div>
            <ul class="sidebar-menu">
                <li class="{{ request()->routeIs('apoteker.laporan.*') ? 'active' : '' }}">
                    <a href="{{ route('apoteker.laporan.index') }}" data-turbo-scroll="false">
                        <i class="fas fa-chart-pie"></i>Laporan Obat
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <a href="#" class="logout-link" onclick="confirmApotekerLogout(event)">
                <i class="fas fa-sign-out-alt"></i>Keluar
            </a>
            <form id="logout-form-apoteker" action="{{ route('apoteker.logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
            <div class="app-info">
                <p><strong>Praktek Bersama</strong></p>
                <p>Didukung oleh POLBENG</p>
                <p>© 2025 • v2.0</p>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div id="greeting">
                    <i class="fas fa-sun"></i>
                    Selamat Pagi!
                </div>
            </div>
            <div class="topbar-right">
                <span id="datetime">
                    <i class="far fa-calendar-alt"></i>
                    <span id="datetime-text"></span>
                </span>
            </div>
        </div>

        <div class="page-content">
            @yield('content')
        </div>
    </main>

    <script>
        function initPageScripts() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (window.menuToggleClickListener) {
                menuToggle.removeEventListener('click', window.menuToggleClickListener);
            }
            if (window.sidebarOverlayClickListener) {
                sidebarOverlay.removeEventListener('click', window.sidebarOverlayClickListener);
            }

            window.menuToggleClickListener = () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('active');
            };

            window.sidebarOverlayClickListener = () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('active');
            };

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', window.menuToggleClickListener);
                sidebarOverlay.addEventListener('click', window.sidebarOverlayClickListener);
            }

            function setGreeting() {
                const now = new Date();
                const hour = now.getHours();
                let greeting = '', iconClass = 'fas fa-smile', iconColor = '#39A616';

                if (hour >= 5 && hour < 11) {
                    greeting = 'Selamat Pagi, Apoteker!';
                    iconClass = 'fas fa-sun';
                    iconColor = '#ffb300';
                } else if (hour >= 11 && hour < 15) {
                    greeting = 'Selamat Siang, Apoteker!';
                    iconClass = 'fas fa-cloud-sun';
                    iconColor = '#f7c948';
                } else if (hour >= 15 && hour < 18) {
                    greeting = 'Selamat Sore, Apoteker!';
                    iconClass = 'fas fa-cloud-sun-rain';
                    iconColor = '#f57c00';
                } else {
                    greeting = 'Selamat Malam, Apoteker!';
                    iconClass = 'fas fa-moon';
                    iconColor = '#5c6bc0';
                }
                const greetingEl = document.getElementById('greeting');
                if (greetingEl) {
                    greetingEl.innerHTML = `<i class="${iconClass}" style="color:${iconColor};"></i> ${greeting}`;
                }
            }

            function updateDateTime() {
                const now = new Date();
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                const day = days[now.getDay()];
                const date = now.getDate();
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                const hour = String(now.getHours()).padStart(2, '0');
                const minute = String(now.getMinutes()).padStart(2, '0');
                const second = String(now.getSeconds()).padStart(2, '0');

                const datetimeEl = document.getElementById('datetime-text');
                if (datetimeEl) {
                    datetimeEl.textContent = `${day}, ${date} ${month} ${year} • ${hour}:${minute}:${second}`;
                }
            }

            setGreeting();
            updateDateTime();

            if (window.dateTimeInterval) {
                clearInterval(window.dateTimeInterval);
            }
            window.dateTimeInterval = setInterval(updateDateTime, 1000);

            if (window.innerWidth <= 992) {
                const menuLinks = document.querySelectorAll('.sidebar-menu a');
                menuLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('active');
                    });
                });
            }
        }

        document.addEventListener('DOMContentLoaded', initPageScripts);
        document.addEventListener('turbo:load', initPageScripts);

        document.addEventListener('turbo:before-cache', () => {
            if (window.dateTimeInterval) {
                clearInterval(window.dateTimeInterval);
            }
        });

        function confirmApotekerLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Anda akan kembali ke halaman login apoteker.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#39A616',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Ya, Keluar',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal-btn-confirm',
                    cancelButton: 'swal-btn-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('logout-form-apoteker');
                    form.setAttribute('data-turbo', 'false');
                    form.submit();
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .swal-btn-confirm,
        .swal-btn-cancel {
            padding: 10px 24px !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            font-size: 14px !important;
        }
    </style>

    @stack('scripts')
</body>
</html>
