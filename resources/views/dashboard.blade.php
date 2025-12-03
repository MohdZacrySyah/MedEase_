@extends('layouts.main')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Banner --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-sun"></i>
                    <span id="greeting-time">Selamat Pagi</span>
                </div>
                <h1 class="page-title">Halo, {{ $user->name }}! 👋</h1>
                <p class="page-subtitle">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-chart-line"></i> Statistik Kesehatan Anda</h2>
        </div>
        <div class="stats-grid-modern">
            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $pemeriksaanTahunIni }}">0</div>
                    <div class="stat-label-modern">Pemeriksaan Selesai</div>
                    <div class="stat-period">Tahun {{ date('Y') }}</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper stat-info-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahDokterDikunjungi }}">0</div>
                    <div class="stat-label-modern">Dokter Dikunjungi</div>
                    <div class="stat-period">Sepanjang Waktu</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-hospital-user"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="info-section">
        <div class="section-header">
            <h2><i class="fas fa-info-circle"></i> Informasi Penting</h2>
        </div>
        <div class="info-grid-modern">
            {{-- Kartu Notifikasi --}}
            <div class="info-card">
                <div class="card-gradient-bg card-notification-bg"></div>
                <div class="card-content-modern">
                    <div class="card-header-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="card-title-modern">Notifikasi Hari Ini</h3>
                    <div class="card-body-modern">
                        @if ($notifikasiHariIni)
                            <div class="info-row notification-row">
                                <div class="info-icon-wrapper notif-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="info-text">
                                    <span class="label">Jadwal Konsultasi</span>
                                    <span class="value">{{ $notifikasiHariIni->nama_layanan }}</span>
                                    @if ($notifikasiHariIni->no_antrian)
                                        <span class="queue-badge">
                                            <i class="fas fa-ticket-alt"></i> 
                                            No. {{ $notifikasiHariIni->no_antrian }}
                                        </span>
                                    @else
                                        <span class="pending-badge">
                                            <i class="fas fa-clock"></i> 
                                            Menunggu nomor antrian
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Tidak ada jadwal hari ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kartu Tips Sehat --}}
            <div class="info-card">
                <div class="card-gradient-bg card-tips-bg"></div>
                <div class="card-content-modern">
                    <div class="card-header-icon tips-icon-header">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="card-title-modern">Tips Sehat Hari Ini</h3>
                    <div class="card-body-modern">
                        <div class="info-row tips-row">
                            <div class="info-icon-wrapper tips-icon">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="info-text">
                                <span class="label">Jaga Hidrasi Tubuh</span>
                                <span class="value">Minumlah minimal 2 liter air setiap hari agar tubuh tetap segar 💧</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Info Praktek --}}
            <div class="info-card">
                <div class="card-gradient-bg card-clinic-bg"></div>
                <div class="card-content-modern">
                    <div class="card-header-icon clinic-icon-header">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h3 class="card-title-modern">Informasi Klinik</h3>
                    <div class="card-body-modern">
                        <div class="info-row clinic-row">
                            <div class="info-icon-wrapper clinic-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-text">
                                <span class="label">Jam Operasional</span>
                                <span class="value">16.00 - 20.00 WIB</span>
                            </div>
                        </div>
                        
                        <div class="info-row clinic-row">
                            <div class="info-icon-wrapper clinic-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-text">
                                <span class="label">Kontak Kami</span>
                                <span class="value">0822 1117 8167</span>
                            </div>
                        </div>
                        
                        <div class="info-row clinic-row">
                            <div class="info-icon-wrapper clinic-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-text">
                                <span class="label">Alamat Klinik</span>
                                <span class="value">Jl. I. Mohammad Ali, Bengkalis, Riau</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Tenaga Medis --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-calendar-alt"></i> Jadwal Tenaga Medis Hari Ini</h2>
            <span class="schedule-count">
                <i class="fas fa-check-circle"></i>
                {{ $jadwalHariIni->count() }} Jadwal Aktif
            </span>
        </div>
        <div class="schedule-container-modern">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user-md"></i> Nama Tenaga Medis</th>
                        <th><i class="fas fa-stethoscope"></i> Layanan</th>
                        <th><i class="far fa-clock"></i> Waktu Praktek</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalHariIni as $index => $jadwal)
                        <tr class="schedule-row" style="animation-delay: {{ $index * 0.1 }}s">
                            <td>
                                <div class="doctor-info">
                                    <div class="doctor-avatar">
                                        @if($jadwal->tenagaMedis?->profile_photo_path)
                                            <img src="{{ asset('storage/' . $jadwal->tenagaMedis->profile_photo_path) }}" alt="Foto">                                        
                                        @else
                                            <i class="fas fa-user-md"></i>
                                        @endif
                                    </div>
                                    <span class="doctor-name">{{ $jadwal->tenagaMedis?->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="service-badge">
                                    <i class="fas fa-briefcase-medical"></i>
                                    {{ $jadwal->layanan }}
                                </span>
                            </td>
                            <td>
                                <div class="time-badge-modern">
                                    <i class="far fa-clock"></i>
                                    <span>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-schedule">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Tidak ada jadwal praktek hari ini</p>
                                    <small>Silakan cek kembali di hari berikutnya</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
    
    /* ===== WARNA BARU ===== */
    :root {
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
        --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .container-fluid-modern {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER BANNER PREMIUM ===== */
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
        /* animation: float 3s ease-in-out infinite; */
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

    /* ===== STATS SECTION PREMIUM ===== */
    .stats-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stats-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 28px;
    }

    .stat-card-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        padding: 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        border: 1px solid rgba(57, 166, 22, 0.15);
        position: relative;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card-modern:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .card-gradient-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: var(--grad);
        opacity: 0.06;
        transition: opacity 0.5s ease;
    }

    .stat-card-modern:hover .card-gradient-bg {
        opacity: 0.12;
    }

    .stat-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        position: relative;
        flex-shrink: 0;
        z-index: 1;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.35);
        /* animation: float 3s ease-in-out infinite; */
    }

    .stat-info-icon {
        background: linear-gradient(135deg, #3498db, #2980b9);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.35);
    }

    .stat-content {
        flex: 1;
        z-index: 1;
    }

    .stat-value-modern {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: 10px;
    }

    .stat-label-modern {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 5px;
    }

    .stat-period {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .stat-decoration {
        position: absolute;
        bottom: 15px;
        right: 15px;
        font-size: 80px;
        color: rgba(57, 166, 22, 0.08);
        z-index: 0;
        /* animation: float 4s ease-in-out infinite; */
    }

    /* ===== INFO SECTION PREMIUM ===== */
    .info-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .info-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 28px;
    }

    .info-card {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        border: 1px solid rgba(57, 166, 22, 0.15);
        position: relative;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .card-notification-bg {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    }

    .card-tips-bg {
        background: var(--grad);
    }

    .card-clinic-bg {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    }

    .info-card:hover .card-gradient-bg {
        opacity: 0.12;
    }

    .card-content-modern {
        padding: 32px 28px;
        position: relative;
        z-index: 1;
    }

    .card-header-icon {
        position: absolute;
        top: 24px;
        right: 24px;
        width: 55px;
        height: 55px;
        border-radius: 14px;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        opacity: 0.15;
        /* animation: float 4s ease-in-out infinite; */
    }

    .tips-icon-header {
        background: var(--grad);
    }

    .clinic-icon-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .card-title-modern {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 2px solid rgba(57, 166, 22, 0.1);
    }

    .card-body-modern {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Info Row Style Premium */
    .info-row {
        display: flex;
        align-items: flex-start;
        padding: 18px;
        background: var(--bg-secondary);
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

    .info-row:hover {
        background: rgba(57, 166, 22, 0.05);
        border-color: rgba(57, 166, 22, 0.2);
        transform: translateX(6px);
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.1);
    }

    .info-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .notif-icon {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.15), rgba(230, 126, 34, 0.25));
    }

    .tips-icon {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
    }

    .clinic-icon {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.15), rgba(41, 128, 185, 0.25));
    }

    .info-icon-wrapper i {
        color: var(--p1);
        font-size: 20px;
    }

    .notif-icon i {
        color: #f39c12;
    }

    .clinic-icon i {
        color: #3498db;
    }

    .info-text {
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex: 1;
    }

    .info-text .label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-text .value {
        font-size: 1.05rem;
        color: var(--text-primary);
        font-weight: 600;
        line-height: 1.6;
    }

    .queue-badge, .pending-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .queue-badge {
        background: var(--grad);
        color: white;
    }

    .pending-badge {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 18px;
        opacity: 0.3;
        /* animation: float 3s ease-in-out infinite; */
    }

    .empty-state p {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 500;
    }

    /* ===== SCHEDULE SECTION PREMIUM ===== */
    .schedule-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .schedule-container-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        border: 1px solid rgba(57, 166, 22, 0.15);
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

    .schedule-row {
        border-bottom: 1px solid rgba(57, 166, 22, 0.08);
        transition: all 0.3s ease;
        animation: fadeInLeft 0.5s ease forwards;
        opacity: 0;
    }
    
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .schedule-row:hover {
        background: rgba(57, 166, 22, 0.04);
    }

    .schedule-table tbody td {
        padding: 20px 24px;
        color: var(--text-secondary);
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
        border: 3px solid rgba(57, 166, 22, 0.1);
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

    .time-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.2));
        color: #856404;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid rgba(243, 156, 18, 0.2);
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

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .stats-grid-modern,
        .info-grid-modern {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .container-fluid-modern {
            padding: 20px 15px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            padding: 30px 24px;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .hero-illustration {
            width: 110px;
            height: 110px;
        }

        .hero-illustration > i {
            font-size: 45px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .stat-card-modern {
            padding: 24px;
        }

        .stat-icon-wrapper {
            width: 70px;
            height: 70px;
            font-size: 32px;
        }

        .stat-value-modern {
            font-size: 2.4rem;
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
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .greeting-badge {
            font-size: 0.8rem;
            padding: 8px 16px;
        }

        .stat-card-modern {
            flex-direction: column;
            text-align: center;
        }

        .card-header-icon {
            top: 18px;
            right: 18px;
            width: 50px;
            height: 50px;
            font-size: 22px;
        }

        .info-row {
            padding: 14px;
        }

        .info-icon-wrapper {
            width: 42px;
            height: 42px;
        }

        .info-icon-wrapper i {
            font-size: 18px;
        }

        .schedule-table thead th {
            font-size: 0.8rem;
            padding: 12px 10px;
        }

        .schedule-table tbody td {
            padding: 12px 10px;
        }

        .service-badge,
        .time-badge-modern {
            font-size: 0.8rem;
            padding: 8px 14px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // =============================
    // UPDATE GREETING DENGAN ANIMASI
    // =============================
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
    
    // =============================
    // COUNTER ANIMATION
    // =============================
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }
    
    // =============================
    // INITIALIZE ON PAGE LOAD
    // =============================
    document.addEventListener('DOMContentLoaded', function() {
        // Update greeting
        updateGreeting();
        
        // Animate counters
        document.querySelectorAll('.stat-value-modern[data-count]').forEach(element => {
            animateCounter(element);
        });
        
        // Add entrance animation observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.stats-section, .info-section, .schedule-section').forEach(section => {
            observer.observe(section);
        });
    });
    
    // For Turbo/Livewire compatibility
    document.addEventListener('turbo:load', function() {
        updateGreeting();
        document.querySelectorAll('.stat-value-modern[data-count]').forEach(element => {
            animateCounter(element);
        });
    });
</script>
@endpush
