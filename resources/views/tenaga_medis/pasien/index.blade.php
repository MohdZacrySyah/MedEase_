@extends('layouts.tenaga_medis')
@section('title', 'Daftar Pasien Saya')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Banner (SAMA DENGAN DASHBOARD) --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-users"></i>
                    <span id="greeting-time">Selamat Pagi</span>
                </div>
                <h1 class="page-title">Daftar Pasien Anda 📋</h1>
                <p class="page-subtitle">
                    <i class="fas fa-info-circle"></i>
                    Kelola dan input pemeriksaan (SOAP) untuk pasien Anda
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <i class="fas fa-user-injured"></i>
            </div>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success" id="autoHideAlert">
            <i class="fas fa-check-circle"></i> 
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    {{-- Tabel Daftar Pasien (SAMA DENGAN SCHEDULE SECTION DASHBOARD) --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-user-injured"></i> Daftar Pasien Anda</h2>
            <span class="schedule-count">
                <i class="fas fa-users"></i>
                {{ $pendaftarans->count() }} Pasien
            </span>
        </div>
        <div class="schedule-container-modern">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> No</th>
                        <th><i class="fas fa-user"></i> Nama Pasien</th>
                        <th><i class="fas fa-stethoscope"></i> Layanan</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftarans as $index => $pendaftaran)
                        <tr class="schedule-row" style="animation-delay: {{ $index * 0.1 }}s">
                            <td>
                                <div class="no-urut">{{ $loop->iteration }}</div>
                            </td>
                            <td>
                                <div class="doctor-info">
                                    <div class="doctor-avatar">
                                        @if($pendaftaran->user?->profile_photo_path)
                                            <img src="{{ asset('storage/' . $pendaftaran->user->profile_photo_path) }}" alt="Foto">
                                        @else
                                            {{ substr($pendaftaran->user->name ?? $pendaftaran->nama_lengkap ?? 'P', 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="doctor-name">{{ $pendaftaran->user->name ?? $pendaftaran->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="service-badge">
                                    <i class="fas fa-briefcase-medical"></i>
                                    {{ $pendaftaran->nama_layanan }}
                                </span>
                            </td>
                            <td>
                                @if($pendaftaran->status == 'Diperiksa Awal')
                                    <span class="status-badge status-diperiksa-awal">
                                        <i class="fas fa-notes-medical"></i> Diperiksa Awal
                                    </span>
                                @elseif($pendaftaran->status == 'Selesai')
                                    <span class="status-badge status-selesai">
                                        <i class="fas fa-check-double"></i> Selesai
                                    </span>
                                @else
                                    <span class="status-badge status-menunggu">
                                        <i class="fas fa-clock"></i> {{ $pendaftaran->status }}
                                    </span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                <div class="button-group">
                                    {{-- Tombol Input SOAP --}}
                                    <button class="btn-action-modern open-pemeriksaan-modal" 
                                            data-id="{{ $pendaftaran->id }}">
                                        <span>
                                            {{ $pendaftaran->status == 'Selesai' ? 'Lihat/Edit SOAP' : 'Input SOAP' }}
                                        </span>
                                        <i class="fas fa-stethoscope"></i>
                                    </button>
                                    
                                    {{-- Tombol Riwayat --}}
                                    @if($pendaftaran->user_id)
                                        <a href="{{ route('tenaga-medis.riwayat.index', ['pasien_id' => $pendaftaran->user_id]) }}" 
                                           class="btn-action-secondary-history" 
                                           title="Riwayat untuk {{ $pendaftaran->user->name ?? $pendaftaran->nama_lengkap }}">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    @else
                                        <button class="btn-action-secondary-history" 
                                                title="Riwayat tidak tersedia (Pasien tidak terhubung ke akun user)" 
                                                disabled>
                                            <i class="fas fa-history"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-schedule">
                                    <i class="fas fa-inbox"></i>
                                    <p>Tidak ada pasien yang menunggu pemeriksaan Anda</p>
                                    <small>Semua pasien sudah ditangani atau belum ada antrian</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- MODAL FORM PEMERIKSAAN (SOAP) --}}
<div id="pemeriksaanModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <button class="close-modal" id="closePemeriksaanModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div id="pemeriksaanModalContent">
            <div class="loading-spinner"></div> 
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    * { 
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    /* ===== DARK MODE SUPPORT (SAMA DENGAN DASHBOARD) ===== */
    :root {
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
        --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
        
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f3f4f6;
        --border-color: rgba(57, 166, 22, 0.15);
        --shadow-color: rgba(57, 166, 22, 0.1);
        --hover-bg: rgba(57, 166, 22, 0.04);
    }

    [data-theme="dark"] {
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        --bg-primary: #1f2937;
        --bg-secondary: #111827;
        --bg-tertiary: #374151;
        --border-color: rgba(57, 166, 22, 0.3);
        --shadow-color: rgba(0, 0, 0, 0.3);
        --hover-bg: rgba(57, 166, 22, 0.15);
    }

    @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) {
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --text-muted: #9ca3af;
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --bg-tertiary: #374151;
            --border-color: rgba(57, 166, 22, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --hover-bg: rgba(57, 166, 22, 0.15);
        }
    }

    body {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: background 0.3s ease, color 0.3s ease;
    }

    /* ===== HEADER BANNER (100% SAMA DENGAN DASHBOARD) ===== */
    .dashboard-header-banner {
        margin-bottom: 40px;
        animation: fadeInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px; 
        background: var(--grad);
        padding: 35px 40px;
        border-radius: 24px;
        box-shadow: 0 15px 50px rgba(57, 166, 22, 0.25);
        position: relative;
        overflow: hidden;
    }

    .header-content::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-icon {
        width: 75px;
        height: 75px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #fff;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .header-text {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(12px);
        padding: 10px 20px;
        border-radius: 25px;
        color: white;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.8s ease-out 0.2s both;
    }

    .greeting-badge i {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .page-title {
        color: #fff;
        font-weight: 800;
        font-size: 2.2rem;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
        animation: fadeIn 0.8s ease-out 0.3s both;
    }

    .page-subtitle {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.05rem;
        font-weight: 500;
        margin: 0;
        animation: fadeIn 0.8s ease-out 0.4s both;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hero-illustration {
        width: 130px;
        height: 130px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(15px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
        z-index: 1;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .hero-illustration > i {
        font-size: 55px;
        color: white;
        z-index: 2;
        animation: heartbeat 1.5s ease-in-out infinite;
    }
    
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.1); }
        50% { transform: scale(1); }
        75% { transform: scale(1.05); }
    }

    .pulse-circle {
        position: absolute;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        animation: pulse-ring 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    .pulse-1 { width: 100%; height: 100%; animation-delay: 0s; }
    .pulse-2 { width: 120%; height: 120%; animation-delay: 0.8s; }
    .pulse-3 { width: 140%; height: 140%; animation-delay: 1.6s; }
    
    @keyframes pulse-ring {
        0% { transform: scale(0.9); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* ===== ALERT ===== */
    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
        margin-bottom: 30px;
        border-radius: 16px;
        font-weight: 600;
        animation: slideInDown 0.4s ease;
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.2);
    }

    .alert-success {
        color: #155724;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 1px solid #c3e6cb;
    }

    [data-theme="dark"] .alert-success {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        color: #4ade80;
        border-color: rgba(46, 204, 113, 0.4);
    }

    .alert i {
        font-size: 1.4rem;
    }

    /* ===== SECTION HEADER ===== */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .section-header h2 i {
        color: var(--p1);
        font-size: 1.4rem;
    }

    .schedule-count {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--grad);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.95rem;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        animation: fadeInRight 0.6s ease-out;
    }
    
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* ===== SCHEDULE SECTION (100% SAMA DENGAN DASHBOARD) ===== */
    .schedule-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .schedule-container-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .schedule-container-modern:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }

    .schedule-table thead {
        background: var(--grad);
    }

    .schedule-table thead th {
        padding: 20px 24px;
        text-align: left;
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .schedule-table thead th i {
        margin-right: 10px;
        opacity: 0.95;
    }

    .schedule-table thead th.text-center {
        text-align: center;
    }

    .schedule-row {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
        animation: fadeInLeft 0.5s ease forwards;
        opacity: 0;
    }
    
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .schedule-row:hover {
        background: var(--hover-bg);
    }

    .schedule-table tbody td {
        padding: 20px 24px;
        color: var(--text-secondary);
        vertical-align: middle;
    }

    .schedule-table tbody td.text-center {
        text-align: center;
    }

    .no-urut {
        display: inline-flex;
        width: 42px;
        height: 42px;
        background: var(--grad);
        color: white;
        border-radius: 10px;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.25);
    }

    .doctor-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .doctor-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.2);
    }

    .doctor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doctor-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .service-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.2));
        color: #1976d2;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid rgba(52, 152, 219, 0.2);
    }

    [data-theme="dark"] .service-badge {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        color: #60a5fa;
        border-color: rgba(52, 152, 219, 0.3);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid;
    }

    .status-menunggu {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.2));
        color: #856404;
        border-color: rgba(243, 156, 18, 0.2);
    }

    .status-diperiksa-awal {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.2));
        color: #0c5460;
        border-color: rgba(52, 152, 219, 0.2);
    }

    .status-selesai {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.2));
        color: #155724;
        border-color: rgba(46, 204, 113, 0.2);
    }

    [data-theme="dark"] .status-menunggu {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(230, 126, 34, 0.3));
        color: #fbbf24;
        border-color: rgba(243, 156, 18, 0.4);
    }

    [data-theme="dark"] .status-diperiksa-awal {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        color: #60a5fa;
        border-color: rgba(52, 152, 219, 0.4);
    }

    [data-theme="dark"] .status-selesai {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        color: #4ade80;
        border-color: rgba(46, 204, 113, 0.4);
    }

    .button-group {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .btn-action-modern {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--grad);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: none;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-action-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-action-modern:hover::before {
        left: 100%;
    }

    .btn-action-modern:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
    }

    .btn-action-modern i {
        transition: transform 0.3s ease;
    }

    .btn-action-modern:hover i {
        transform: translateX(4px);
    }

    .btn-action-secondary-history {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        background: var(--bg-secondary);
        color: var(--p1);
        border: 2px solid var(--border-color);
        border-radius: 50%;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.15);
    }

    .btn-action-secondary-history:hover {
        background: var(--grad);
        color: white;
        transform: translateY(-3px) rotate(360deg);
        border-color: var(--p1);
        box-shadow: 0 8px 20px rgba(57, 166, 22, 0.3);
    }

    .btn-action-secondary-history:disabled {
        background: var(--bg-tertiary);
        border-color: var(--border-color);
        color: var(--text-muted);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        opacity: 0.5;
    }

    .empty-schedule {
        text-align: center;
        padding: 70px 20px;
        color: var(--text-muted);
    }

    .empty-schedule i {
        font-size: 4.5rem;
        margin-bottom: 24px;
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }

    .empty-schedule p {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text-secondary);
    }

    .empty-schedule small {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    /* ===== MODAL STYLES ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s;
    }

    .modal-card {
        background-color: var(--bg-primary);
        margin: 20px;
        border-radius: 24px;
        width: 90%;
        max-width: 900px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: -18px;
        right: -18px;
        width: 50px;
        height: 50px;
        background: white;
        border: 3px solid var(--border-color);
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .close-modal i {
        font-size: 1.3rem;
        color: var(--text-secondary);
        transition: all 0.3s ease;
    }

    .close-modal:hover {
        background: #ef4444;
        transform: rotate(90deg) scale(1.1);
        border-color: #ef4444;
    }

    .close-modal:hover i {
        color: #fff;
    }

    [data-theme="dark"] .close-modal {
        background: var(--bg-tertiary);
        border-color: var(--border-color);
    }

    [data-theme="dark"] .close-modal:hover {
        background: #ef4444;
        border-color: #ef4444;
    }

    #pemeriksaanModalContent .modal-content-inner {
        padding: 40px;
        overflow-y: auto;
        max-height: calc(90vh - 80px);
    }

    #pemeriksaanModalContent .modal-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border-color);
    }

    #pemeriksaanModalContent .modal-title {
        color: var(--p1);
        font-weight: 800;
        font-size: 1.8rem;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .data-pasien-ringkas {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .data-pasien-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px;
    }

    .data-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .data-item .label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-item .value {
        font-size: 1.05rem;
        color: var(--text-primary);
        font-weight: 700;
    }

    .data-item .value.keluhan {
        font-weight: 600;
        color: #c0392b;
    }

    .soap-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .form-label i {
        color: var(--p1);
        font-size: 1.1rem;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-secondary);
        transition: all 0.3s ease;
        color: var(--text-primary);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--p1);
        background-color: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 35px;
        padding-top: 30px;
        border-top: 2px solid var(--border-color);
    }

    .btn-primary {
        flex: 1.5;
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 16px 32px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
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
        transition: left 0.6s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
        background: var(--grad-reverse);
    }

    .btn-secondary {
        flex: 1;
        background: var(--bg-secondary);
        color: var(--p1);
        border: 2px solid var(--border-color);
        padding: 16px 32px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: var(--grad);
        color: white;
        border-color: var(--p1);
        transform: translateY(-3px);
    }

    .loading-spinner {
        border: 5px solid var(--bg-tertiary);
        border-top: 5px solid var(--p1);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
        margin: 60px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-illustration {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            padding: 30px 24px;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .schedule-table {
            font-size: 0.9rem;
        }

        .schedule-table thead th,
        .schedule-table tbody td {
            padding: 14px 12px;
        }

        .doctor-avatar {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }

        #pemeriksaanModalContent .modal-content-inner {
            padding: 24px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
        }

        .soap-grid {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
            width: 100%;
        }

        .btn-action-modern,
        .btn-action-secondary-history {
            width: 100%;
            justify-content: center;
        }

        .btn-action-secondary-history {
            border-radius: 25px;
            width: auto;
            padding: 12px 24px;
        }

        .btn-action-secondary-history i {
            margin-right: 8px;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .greeting-badge {
            font-size: 0.8rem;
            padding: 8px 16px;
        }

        .modal-card {
            width: 95%;
            margin: 10px;
        }

        .data-pasien-grid {
            grid-template-columns: 1fr;
        }

        .close-modal {
            top: -12px;
            right: -12px;
            width: 44px;
            height: 44px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('pemeriksaanModal');
    const modalContent = document.getElementById('pemeriksaanModalContent');
    const closeModalBtn = document.getElementById('closePemeriksaanModalBtn');

    // Update Greeting
    function updateGreeting() {
        const hour = new Date().getHours();
        const greetingElement = document.getElementById('greeting-time');
        
        let greetingText = '';
        if (hour >= 5 && hour < 11) {
            greetingText = 'Selamat Pagi';
        } else if (hour >= 11 && hour < 15) {
            greetingText = 'Selamat Siang';
        } else if (hour >= 15 && hour < 18) {
            greetingText = 'Selamat Sore';
        } else {
            greetingText = 'Selamat Malam';
        }
        
        if (greetingElement) {
            greetingElement.style.transition = 'opacity 0.3s ease';
            greetingElement.style.opacity = '0';
            
            setTimeout(() => {
                greetingElement.textContent = greetingText;
                greetingElement.style.opacity = '1';
            }, 300);
        }
    }
    
    updateGreeting();

    async function openPemeriksaanModal(pendaftaranId) {
        if (!modal || !modalContent) return;

        modal.style.display = 'flex';
        modalContent.innerHTML = '<div class="loading-spinner"></div>';
        document.body.style.overflow = 'hidden';

        try {
            const jsonUrl = `{{ url('/tenaga-medis/pemeriksaan/json') }}/${pendaftaranId}`;
            const response = await fetch(jsonUrl);
            
            if (!response.ok) throw new Error('Gagal mengambil data pasien.');
            
            const data = await response.json();

            const dataObjektif = `Tekanan Darah: ${data.tekanan_darah || '-'}\nBerat Badan: ${data.berat_badan || '-'}\nSuhu Tubuh: ${data.suhu_tubuh || '-'}`;

            const formHtml = `
                <div class="modal-content-inner">
                    <div class="modal-header">
                        <h2 class="modal-title">
                            <i class="fas fa-stethoscope"></i>
                            Pemeriksaan Pasien (SOAP)
                        </h2>
                    </div>
                    
                    <form action="${data.form_action}" method="POST">
                        @csrf
                        
                        <div class="data-pasien-ringkas" style="margin-bottom: 20px;">
                            <div class="data-pasien-grid" style="grid-template-columns: 1fr;">
                                <div class="data-item">
                                    <span class="label">Pasien</span>
                                    <span class="value">${data.pasien_name || 'N/A'}</span>
                                </div>
                            </div>
                        </div>

                        <div class="soap-grid">
                            <div class="form-group full-width">
                                <label for="subjektif" class="form-label">
                                    <i class="fas fa-comment"></i> S (Subjektif) - Anamnesis
                                </label>
                                
                                <div class="data-pasien-ringkas" style="padding: 18px; margin-bottom: 12px;">
                                    <div class="data-item">
                                        <span class="label">Keluhan Awal (dari Perawat)</span>
                                        <span class="value keluhan">${data.keluhan || 'Tidak ada keluhan'} (${data.lama_keluhan || '-'})</span>
                                    </div>
                                </div>
                                
                                <textarea name="subjektif" id="subjektif" class="form-control" rows="3" placeholder="Tulis hasil anamnesis/wawancara dokter...">${data.subjektif || ''}</textarea>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-file-medical-alt"></i> O (Objektif) - Pemeriksaan Fisik
                                </label>
                                
                                <div class="data-pasien-ringkas" style="padding: 18px; margin-bottom: 0;">
                                    <div class="data-pasien-grid">
                                        <div class="data-item">
                                            <span class="label">Tekanan Darah</span>
                                            <span class="value">${data.tekanan_darah || '-'}</span>
                                        </div>
                                        <div class="data-item">
                                            <span class="label">Berat Badan</span>
                                            <span class="value">${data.berat_badan || '-'}</span>
                                        </div>
                                        <div class="data-item">
                                            <span class="label">Suhu Tubuh</span>
                                            <span class="value">${data.suhu_tubuh || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="objektif" value="${dataObjektif}">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="assessment" class="form-label">
                                    <i class="fas fa-brain"></i> A (Assessment) - Diagnosis
                                </label>
                                <textarea name="assessment" id="assessment" class="form-control" rows="3" placeholder="Tulis diagnosis dokter...">${data.assessment || ''}</textarea>
                            </div>

                            <div class="form-group full-width">
                                <label for="plan" class="form-label">
                                    <i class="fas fa-clipboard-check"></i> P (Plan) - Perencanaan
                                </label>
                                <textarea name="plan" id="plan" class="form-control" rows="3" placeholder="Tulis rencana, rujukan, atau edukasi...">${data.plan || ''}</textarea>
                            </div>
                        </div>

                        <hr style="border: 1px solid var(--border-color); margin: 28px 0;">

                        <div class="soap-grid">
                            <div class="form-group">
                                <label for="resep_obat" class="form-label">
                                    <i class="fas fa-pills"></i> Resep Obat
                                </label>
                                <textarea name="resep_obat" id="resep_obat" class="form-control" rows="3" placeholder="Tulis resep obat jika ada...">${data.resep_obat || ''}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="harga" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Total Biaya Pemeriksaan (Rp)
                                </label>
                                <input type="number" name="harga" id="harga" class="form-control" value="${data.harga || ''}" placeholder="Contoh: 50000">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" id="btnBatalPemeriksaan">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Simpan Pemeriksaan
                            </button>
                        </div>
                    </form>
                </div>
            `;

            modalContent.innerHTML = formHtml.replace('@csrf', '{{ csrf_field() }}');

            document.getElementById('btnBatalPemeriksaan').addEventListener('click', closeModal);

        } catch (error) {
            console.error('Error:', error);
            modalContent.innerHTML = '<p style="padding: 40px; text-align: center; color: red;">Gagal memuat data form.</p>';
        }
    }

    function closeModal() {
        if (modal) modal.style.display = 'none';
        modalContent.innerHTML = '';
        document.body.style.overflow = 'auto';
    }

    const urlParams = new URLSearchParams(window.location.search);
    const pendaftaranIdToOpen = urlParams.get('open_modal_for');

    if (pendaftaranIdToOpen) {
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: cleanUrl}, '', cleanUrl);
        }
        openPemeriksaanModal(pendaftaranIdToOpen);
    }

    document.querySelectorAll('.open-pemeriksaan-modal').forEach(button => {
        button.addEventListener('click', function() {
            const pendaftaranId = this.dataset.id;
            openPemeriksaanModal(pendaftaranId);
        });
    });

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });

    const alert = document.getElementById('autoHideAlert');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
});
</script>
@endpush
