<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Tambahkan CSRF Token --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>@yield('title') - Panel Admin</title> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Mengganti semua style lama dengan style Premium */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        /* ===== CSS VARIABLES (WARNA BARU) ===== */
        :root {
            --p1: #39A616;
            --p2: #1D8208;
            --p3: #0C5B00;
            --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
            --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
            
            /* Light Mode Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;
            --text-primary: #374151;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.15);
            
            /* Notification Color */
            --danger: #ef4444;
        }
        
        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --bg-tertiary: #0f1419;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
            --text-muted: #9ca3af;
            --border-color: #374151;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.4);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.5);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.6);
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease; /* Dipercepat */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            position: relative;
        }
        
        /* ===== ANIMATED BACKGROUND PARTICLES ===== */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(57, 166, 22, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(29, 130, 8, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(12, 91, 0, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: backgroundPulse 15s ease-in-out infinite;
        }
        
        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        /* ===== SIDEBAR PREMIUM ===== */
        .sidebar {
            width: 280px;
            background: var(--grad);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding: 0;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Dipercepat */
            z-index: 1000;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .sidebar::-webkit-scrollbar-thumb { 
            background: rgba(255, 255, 255, 0.2); 
            border-radius: 10px;
            transition: background 0.3s;
        }
        .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }
        .sidebar.hidden { transform: translateX(-100%); }
        .sidebar.show { transform: translateX(0); }

        /* Brand Section */
        .sidebar-brand {
            text-align: center;
            padding: 30px 20px;
            background: rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-brand::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
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
        }
        
        .brand-subtitle { 
            font-size: 12px; 
            opacity: 0.9; 
            font-weight: 400;
        }

        /* User Profile Section */
        .user-profile {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 20px;
            margin: 20px;
            border-radius: 18px;
            text-decoration: none;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
            pointer-events: none;
        }
        
        .user-profile::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s;
        }
        
        .user-profile:hover::before {
            left: 100%;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }
        
        .user-profile .avatar-wrapper {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            color: var(--p1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 26px;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); }
            50% { box-shadow: 0 8px 25px rgba(57, 166, 22, 0.4); }
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
            margin: 0 0 4px 0; 
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

        /* Navigation Menu */
        .sidebar-nav {
            padding: 10px 20px;
            flex-grow: 1;
        }
        
        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 600;
            margin: 25px 0 12px 0;
            padding-left: 10px;
        }
        
        .sidebar-menu { 
            list-style: none; 
            padding: 0; 
            margin: 0;
        }
        
        .sidebar-menu li { 
            margin-bottom: 6px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.88);
            text-decoration: none;
            padding: 15px 18px;
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-menu a:hover i {
            transform: scale(1.25) rotate(10deg);
        }
        
        .sidebar-menu a:hover::before {
            transform: scaleY(1);
        }
        
        .sidebar-menu li.active > a {
            background: rgba(255, 255, 255, 0.22);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-menu .active > a::before {
            transform: scaleY(1);
        }

        /* ===== NOTIFICATION BADGE STYLE (NEW) ===== */
        .badge-notification {
            background-color: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 700;
            min-width: 22px;
            height: 22px;
            border-radius: 50%; /* Bulat sempurna */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto; /* Otomatis geser ke kanan */
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation: pulse-red 2s infinite;
        }
        
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); transform: scale(1); }
            50% { transform: scale(1.1); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); transform: scale(1); }
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.18);
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background: linear-gradient(135deg, rgba(198, 40, 40, 0.9), rgba(183, 28, 28, 0.9));
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
        }
        
        .logout-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .logout-link:hover::before {
            width: 350px;
            height: 350px;
        }
        
        .logout-link:hover {
            background: linear-gradient(135deg, #c62828, #b71c1c);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(198, 40, 40, 0.6);
        }
        
        .logout-link i { 
            margin-right: 10px; 
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        .logout-link span {
            position: relative;
            z-index: 1;
        }
        
        .app-info {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 18px;
            text-align: center;
            line-height: 1.8;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 280px);
            position: relative;
            z-index: 1;
        }

        /* ===== TOPBAR PREMIUM ===== */
        .topbar {
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            height: 75px;
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            z-index: 900;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .menu-toggle {
            display: none;
            font-size: 24px;
            background: var(--bg-secondary);
            border: none;
            color: var(--p1);
            cursor: pointer;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .menu-toggle:hover { 
            background: var(--p1);
            color: white;
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 15px rgba(57, 166, 22, 0.3);
        }
        
        #greeting {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 17px;
            color: var(--p1);
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
        
        /* Dark Mode Toggle */
        .theme-toggle {
            width: 52px;
            height: 52px;
            background: var(--bg-secondary);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-primary);
            font-size: 19px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }
        
        .theme-toggle::before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            background: var(--grad);
            border-radius: 50%;
            transition: all 0.4s;
        }
        
        .theme-toggle:hover::before {
            width: 100%;
            height: 100%;
        }
        
        .theme-toggle i {
            position: relative;
            z-index: 1;
            transition: transform 0.4s;
        }
        
        .theme-toggle:hover {
            color: white;
            transform: rotate(180deg) scale(1.15);
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.4);
        }
        
        #datetime {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: var(--bg-secondary);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        #datetime:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        #datetime i { 
            color: var(--p1);
            font-size: 16px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding-top: 105px;
            padding-left: 30px;
            padding-right: 30px;
            padding-bottom: 40px;
            flex-grow: 1;
            overflow-y: auto;
        }
        
        .page-content::-webkit-scrollbar { width: 10px; }
        .page-content::-webkit-scrollbar-track { 
            background: var(--bg-secondary);
            border-radius: 10px;
        }
        .page-content::-webkit-scrollbar-thumb { 
            background: var(--grad); 
            border-radius: 10px;
            transition: all 0.3s;
        }
        .page-content::-webkit-scrollbar-thumb:hover { 
            background: var(--grad-reverse);
        }

        /* ===== OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.active { 
            display: block; 
            opacity: 1;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; }
            .topbar { left: 0; }
            .menu-toggle { display: inline-block; }
            .page-content { padding: 95px 20px 30px; }
            #datetime { display: none; }
        }
        
        @media (max-width: 576px) {
            .topbar { padding: 0 15px; height: 65px; }
            #greeting { font-size: 15px; }
            #greeting i { font-size: 19px; }
            .page-content { padding: 85px 15px 20px; }
            .brand-logo { width: 60px; }
            .theme-toggle { width: 48px; height: 48px; font-size: 17px; }
        }
        
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* SweetAlert Custom Styles */
        .swal-btn-confirm,
        .swal-btn-cancel {
            padding: 12px 28px !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
        }
        
        .swal-btn-confirm:hover {
            transform: translateY(-2px) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>

   <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">
                <div class="brand-logo"><img src="{{ asset('images/logo.png') }}" alt="Logo"></div>
                <div class="brand-text">Praktek Bersama</div>
                <div class="brand-subtitle">Panel Admin</div>
            </a>
        </div>

        <div class="user-profile"> 
            <div class="avatar-wrapper"><i class="fas fa-user-shield"></i></div>
            <div class="user-info">
                <h4>{{ Auth::guard('admin')->user()?->name ?? 'Admin' }}</h4>
                <p><i class="fas fa-circle"></i> Kelola Sistem</p>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <ul class="sidebar-menu">
                <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i>Dashboard</a>
                </li>
                
                {{-- MENU 1: DAFTAR PASIEN (ANTRIAN) --}}
                <li class="{{ request()->is('admin/catatanpemeriksaan*') ? 'active' : '' }}">
                    <a href="{{ route('admin.catatanpemeriksaan') }}">
                        <div style="display: flex; align-items: center; width: 100%;">
                            <i class="fas fa-clipboard-list"></i>
                            <span style="flex-grow: 1;">Daftar Pasien</span>
                            
                            {{-- Container ID untuk Notif Pendaftaran --}}
                            <span id="notif-container-daftar">
                                @if(isset($notifPasienBaru) && $notifPasienBaru > 0)
                                    <span class="badge-notification">
                                        {{ $notifPasienBaru }}
                                    </span>
                                @endif
                            </span>
                        </div>
                    </a>
                </li>
                
                {{-- MENU 2: KELOLA DATA PASIEN (USER BARU) --}}
                <li class="{{ request()->is('admin/keloladatapasien*') ? 'active' : '' }}">
                    <a href="{{ route('admin.keloladatapasien') }}">
                        <div style="display: flex; align-items: center; width: 100%;">
                            <i class="fas fa-users-cog"></i>
                            <span style="flex-grow: 1;">Data Pasien</span>
                            
                            {{-- Container ID untuk Notif User Baru --}}
                            <span id="notif-container-user">
                                @if(isset($notifUserBaru) && $notifUserBaru > 0)
                                    <span class="badge-notification">
                                        {{ $notifUserBaru }}
                                    </span>
                                @endif
                            </span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="nav-section-title">Manajemen Klinik</div>
            <ul class="sidebar-menu">
                <li class="{{ request()->is('admin/kelolajadwalpraktek*') ? 'active' : '' }}">
                    <a href="{{ route('admin.kelolajadwalpraktek.index') }}"><i class="fas fa-calendar-alt"></i>Kelola Jadwal</a>
                </li>
                <li class="{{ request()->is('admin/tenaga-medis*') ? 'active' : '' }}">
                    <a href="{{ route('admin.tenaga-medis.index') }}"><i class="fas fa-user-md"></i>Kelola Tenaga Medis</a>
                </li>
                <li class="{{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <a href="{{ route('admin.laporan') }}"><i class="fas fa-chart-line"></i>Laporan Kunjungan</a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <a href="#" class="logout-link" onclick="confirmAdminLogout(event)">
                <i class="fas fa-sign-out-alt"></i> <span style="margin-left:8px">Keluar</span>
            </a>
            <form id="logout-form-admin" action="{{ route('admin.logout') }}" method="GET" style="display: none;">@csrf</form>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <button class="menu-toggle" id="menuToggle" style="border:none; background:none; font-size:24px; color:var(--p1);">
                <i class="fas fa-bars"></i>
            </button>
            <div id="greeting" style="font-weight:600; color:var(--p1);">Selamat Datang</div>
            <button class="theme-toggle" id="themeToggle" style="border:none; background:none; font-size:18px;">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <div class="page-content">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // =========================
        // DARK MODE FUNCTIONALITY
        // =========================
        
        function initTheme() {
            const themeToggle = document.getElementById('themeToggle');
            if (!themeToggle) return; 

            const html = document.documentElement;
            const icon = themeToggle.querySelector('i');
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', savedTheme);
            updateThemeIcon(icon, savedTheme);
            
            // Remove old listeners
            if (window.themeToggleHandler) {
                themeToggle.removeEventListener('click', window.themeToggleHandler);
            }
            
            window.themeToggleHandler = () => {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(icon, newTheme);
                
                showThemeNotification(newTheme);
            };

            themeToggle.addEventListener('click', window.themeToggleHandler);
        }
        
        function updateThemeIcon(icon, theme) {
            if (theme === 'dark') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
        
        function showThemeNotification(theme) {
            document.querySelectorAll('.theme-notification').forEach(n => n.remove());

            const message = theme === 'dark' ? '🌙 Mode Gelap Aktif' : '☀️ Mode Terang Aktif';
            
            const notification = document.createElement('div');
            notification.classList.add('theme-notification');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: var(--grad);
                color: white;
                padding: 15px 25px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 14px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                animation: slideInRight 0.4s ease-out, slideOutRight 0.4s ease-in 2s forwards;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2500);
        }
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // =========================
        // PAGE SCRIPTS INITIALIZATION
        // =========================
        
        function initPageScripts() {
            // Menu Toggle & Overlay
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (window.menuToggleClickListener) {
                menuToggle?.removeEventListener('click', window.menuToggleClickListener);
            }
            if (window.sidebarOverlayClickListener) {
                sidebarOverlay?.removeEventListener('click', window.sidebarOverlayClickListener);
            }

            window.menuToggleClickListener = () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('active');
                
                if (navigator.vibrate) {
                    navigator.vibrate(10);
                }
            };
            
            window.sidebarOverlayClickListener = () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('active');
            };

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', window.menuToggleClickListener);
                sidebarOverlay.addEventListener('click', window.sidebarOverlayClickListener);
            }

            // Dynamic Greeting
            function setGreeting() {
                const now = new Date();
                const hour = now.getHours();
                let greeting = "";
                let iconClass = "fas fa-smile";
                let iconColor = "#39A616";
                
                if (hour >= 5 && hour < 11) {
                    greeting = "Selamat Pagi, Admin!";
                    iconClass = "fas fa-sun";
                    iconColor = "#ffb300";
                } else if (hour >= 11 && hour < 15) {
                    greeting = "Selamat Siang, Admin!";
                    iconClass = "fas fa-cloud-sun";
                    iconColor = "#f7c948";
                } else if (hour >= 15 && hour < 18) {
                    greeting = "Selamat Sore, Admin!";
                    iconClass = "fas fa-cloud-sun-rain";
                    iconColor = "#f57c00";
                } else {
                    greeting = "Selamat Malam, Admin!";
                    iconClass = "fas fa-moon";
                    iconColor = "#5c6bc0";
                }
                
                const greetingEl = document.getElementById("greeting");
                if (greetingEl) {
                    greetingEl.innerHTML = `<i class="${iconClass}" style="color:${iconColor};"></i> ${greeting}`;
                }
            }

            // Update DateTime
            function updateDateTime() {
                const now = new Date();
                const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                
                const hari = days[now.getDay()];
                const tanggal = now.getDate();
                const bulan = months[now.getMonth()];
                const tahun = now.getFullYear();
                const jam = String(now.getHours()).padStart(2, '0');
                const menit = String(now.getMinutes()).padStart(2, '0');
                const detik = String(now.getSeconds()).padStart(2, '0');
                
                const datetimeEl = document.getElementById("datetime-text");
                if (datetimeEl) {
                    datetimeEl.textContent = `${hari}, ${tanggal} ${bulan} ${tahun} • ${jam}:${menit}:${detik}`;
                }
            }

            setGreeting();
            updateDateTime();
            
            if (window.dateTimeInterval) {
                clearInterval(window.dateTimeInterval);
            }
            window.dateTimeInterval = setInterval(updateDateTime, 1000);

            // Close sidebar when clicking menu item on mobile
            if (window.innerWidth <= 992) {
                const menuLinks = document.querySelectorAll('.sidebar-menu a');
                menuLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('active');
                    });
                });
            }
            
            initTheme();
        }

        // =========================
        // EVENT LISTENERS & POLLING
        // =========================
        
        document.addEventListener('DOMContentLoaded', () => {
            initPageScripts();
            startNotificationPolling(); // 🔥 MULAI POLLING
        });
        
        document.addEventListener('turbo:load', () => {
            initPageScripts();
            startNotificationPolling();
        });
        
        document.addEventListener('turbo:before-cache', () => {
            if (window.dateTimeInterval) {
                clearInterval(window.dateTimeInterval);
            }
            if (window.notifInterval) {
                clearInterval(window.notifInterval);
            }
        });

        // =========================
        // LOGOUT CONFIRMATION WITH PREMIUM SWAL
        // =========================
        
        function confirmAdminLogout(event) {
            event.preventDefault();
            
            Swal.fire({
                title: '🚪 Yakin ingin keluar?',
                text: "Anda akan kembali ke halaman login admin.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#39A616',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Ya, Keluar',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true,
                backdrop: `
                    rgba(0,0,0,0.6)
                    url("/images/logout-bg.png")
                    left top
                    no-repeat
                `,
                customClass: {
                    confirmButton: 'swal-btn-confirm',
                    cancelButton: 'swal-btn-cancel',
                    popup: 'swal-popup-animated'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logging out...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    setTimeout(() => {
                        const form = document.getElementById('logout-form-admin');
                        form.setAttribute('data-turbo', 'false');
                        form.submit();
                    }, 500);
                }
            });
        }
        
        // =========================
        // NOTIFICATION POLLING SYSTEM (AJAX)
        // =========================
        
        let notifInterval;

        function startNotificationPolling() {
            if (notifInterval) clearInterval(notifInterval);

            const checkNotif = () => {
                fetch("{{ route('admin.api.check_notif') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // 1. Update Badge Daftar Pasien
                        updateBadge('notif-container-daftar', data.counts.pendaftaran);
                        
                        // 2. Update Badge Data Pasien (User Baru)
                        updateBadge('notif-container-user', data.counts.user_baru);
                    }
                })
                .catch(err => {
                    // console.error('Gagal cek notif:', err);
                });
            };

            checkNotif();
            notifInterval = setInterval(checkNotif, 3000); // Cek tiap 3 detik
            window.notifInterval = notifInterval;
        }

        function updateBadge(elementId, count) {
            const wrapper = document.getElementById(elementId);
            if (!wrapper) return;

            if (count > 0) {
                let badge = wrapper.querySelector('.badge-notification');
                
                if (badge) {
                    if (badge.textContent != count) {
                        badge.textContent = count;
                        badge.style.transform = 'scale(1.3)';
                        setTimeout(() => badge.style.transform = 'scale(1)', 200);
                    }
                } else {
                    wrapper.innerHTML = `<span class="badge-notification animate__animated animate__bounceIn">${count}</span>`;
                }
            } else {
                wrapper.innerHTML = '';
            }
        }

    </script>

    @stack('scripts')
    
</body>
</html>