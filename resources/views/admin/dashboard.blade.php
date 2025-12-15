@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Banner --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Administrator Panel</span>
                </div>
                <h1 class="page-title">Dashboard Admin 👨‍💼</h1>
                <p class="page-subtitle">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <div class="time-widget">
                    <i class="far fa-clock"></i>
                    <span id="current-time"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik KPI --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-chart-bar"></i> Statistik Hari Ini</h2>
        </div>
        
        {{-- ID "live-stats" untuk Auto-Load --}}
        <div class="stats-grid-modern" id="live-stats">
            
            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahPasienHariIni }}">{{ $jumlahPasienHariIni }}</div>
                    <div class="stat-label-modern">Total Pasien Hari Ini</div>
                    <div class="stat-period"><i class="fas fa-arrow-up"></i> Aktif</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="stat-card-modern card-warning">
                <div class="card-gradient-bg card-warning-bg"></div>
                <div class="stat-icon-wrapper stat-warning-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahMenunggu }}">{{ $jumlahMenunggu }}</div>
                    <div class="stat-label-modern">Pasien Menunggu</div>
                    <div class="stat-period"><i class="fas fa-hourglass-half"></i> Antrian</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="stat-card-modern card-success">
                <div class="card-gradient-bg card-success-bg"></div>
                <div class="stat-icon-wrapper stat-success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahSelesai }}">{{ $jumlahSelesai }}</div>
                    <div class="stat-label-modern">Pemeriksaan Selesai</div>
                    <div class="stat-period"><i class="fas fa-check"></i> Selesai</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="stat-card-modern card-info">
                <div class="card-gradient-bg card-info-bg"></div>
                <div class="stat-icon-wrapper stat-info-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahDokterAktifHariIni }}">{{ $jumlahDokterAktifHariIni }}</div>
                    <div class="stat-label-modern">Dokter Aktif Hari Ini</div>
                    <div class="stat-period"><i class="fas fa-briefcase-medical"></i> Bertugas</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>

            <div class="stat-card-modern card-purple">
                <div class="card-gradient-bg card-purple-bg"></div>
                <div class="stat-icon-wrapper stat-purple-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahTenagaMedis }}">{{ $jumlahTenagaMedis }}</div>
                    <div class="stat-label-modern">Total Tenaga Medis</div>
                    <div class="stat-period"><i class="fas fa-hospital-user"></i> Terdaftar</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>

            <div class="stat-card-modern card-danger">
                <div class="card-gradient-bg card-danger-bg"></div>
                <div class="stat-icon-wrapper stat-danger-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $jumlahTotalPasien }}">{{ $jumlahTotalPasien }}</div>
                    <div class="stat-label-modern">Total Akun Pasien</div>
                    <div class="stat-period"><i class="fas fa-database"></i> Database</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Real-Time --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-list-alt"></i> Data Real-Time</h2>
        </div>

        {{-- Tabel Jadwal Tenaga Medis --}}
        {{-- ID "live-jadwal" untuk Auto-Load --}}
        <div class="schedule-container-modern" id="live-jadwal">
            <div class="table-card-header">
                <h3 class="table-title">
                    <i class="fas fa-calendar-check"></i> 
                    Jadwal Tenaga Medis Hari Ini
                </h3>
                <span class="schedule-count">
                    <i class="fas fa-calendar-alt"></i>
                    {{ $jadwalHariIni->count() }} Jadwal Aktif
                </span>
            </div>
            <div class="table-responsive">
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
                            <tr class="schedule-row">
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

        {{-- Tabel Daftar Antrian --}}
        {{-- ID "live-antrian" untuk Auto-Load --}}
        <div class="schedule-container-modern" id="live-antrian" style="margin-top: 25px;">
            <div class="table-card-header">
                <h3 class="table-title">
                    <i class="fas fa-clipboard-list"></i> 
                    Daftar Antrian (Menunggu)
                </h3>
                <span class="schedule-count">
                    <i class="fas fa-users"></i>
                    {{ $pendaftaranMenunggu->count() }} Pasien
                </span>
            </div>
            <div class="table-responsive">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Nama Pasien</th>
                            <th><i class="fas fa-stethoscope"></i> Layanan</th>
                            <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendaftaranMenunggu as $index => $pendaftaran)
                            <tr class="schedule-row">
                                <td>
                                    <div class="doctor-info">
                                        <div class="doctor-avatar">
                                            @if($pendaftaran->user?->profile_photo_path)
                                                <img src="{{ asset('storage/' . $pendaftaran->user->profile_photo_path) }}" alt="Foto">
                                            @else
                                                <i class="fas fa-user"></i>
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
                                <td class="text-center">
                                    <a href="{{ route('admin.catatanpemeriksaan') }}?open_modal_for={{ $pendaftaran->id }}" 
                                       class="btn-action-primary">
                                        <span>Input Periksa Awal</span>
                                        <i class="fas fa-notes-medical"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-schedule">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada pasien menunggu saat ini</p>
                                        <small>Semua pemeriksaan sudah ditangani</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* Styling Dasar */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    /* Variable Warna akan mengikuti Layout Utama */
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: background 0.3s ease, color 0.3s ease;
    }

    /* Header Banner */
    .dashboard-header-banner { margin-bottom: 40px; }
    .header-content {
        display: flex; align-items: center; gap: 20px; 
        background: var(--grad); padding: 35px 40px;
        border-radius: 24px; box-shadow: 0 15px 50px rgba(57, 166, 22, 0.25);
        position: relative; overflow: hidden;
    }
    .header-content::before {
        content: ''; position: absolute; top: -50%; right: -10%;
        width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%; animation: rotate 20s linear infinite;
    }
    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    
    .header-icon {
        width: 75px; height: 75px; background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px); border-radius: 18px; display: flex;
        align-items: center; justify-content: center; font-size: 36px;
        color: #fff; flex-shrink: 0; position: relative; z-index: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .header-text { flex: 1; position: relative; z-index: 1; }
    .greeting-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(12px);
        padding: 10px 20px; border-radius: 25px; color: white;
        font-size: 14px; font-weight: 600; margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .page-title {
        color: #fff; font-weight: 800; font-size: 2.2rem;
        margin: 0 0 10px 0; letter-spacing: -0.5px;
    }
    .page-subtitle {
        display: flex; align-items: center; gap: 10px;
        color: rgba(255, 255, 255, 0.95); font-size: 1.05rem;
        font-weight: 500; margin: 0;
    }
    .hero-illustration { position: relative; flex-shrink: 0; z-index: 1; }
    .time-widget {
        background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(15px);
        padding: 18px 28px; border-radius: 50px; font-size: 1.2rem;
        font-weight: 700; display: flex; align-items: center; gap: 12px;
        color: white; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        min-width: 160px; justify-content: center;
    }
    .pulse-circle {
        position: absolute; border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%; animation: pulse-ring 2.5s infinite;
        left: 50%; top: 50%; transform: translate(-50%, -50%);
    }
    .pulse-1 { width: 140px; height: 140px; animation-delay: 0s; }
    .pulse-2 { width: 170px; height: 170px; animation-delay: 0.8s; }
    .pulse-3 { width: 200px; height: 200px; animation-delay: 1.6s; }
    @keyframes pulse-ring {
        0% { transform: translate(-50%, -50%) scale(0.9); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
    }

    /* Section Header */
    .section-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 28px; flex-wrap: wrap; gap: 15px;
    }
    .section-header h2 {
        font-size: 1.6rem; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 12px; margin: 0;
    }
    .section-header h2 i { color: var(--p1); font-size: 1.4rem; }

    /* Stats Section */
    .stats-section { margin-bottom: 40px; }
    .stats-grid-modern {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 28px;
    }
    .stat-card-modern {
        background: var(--bg-primary); border-radius: 24px; padding: 32px;
        display: flex; align-items: center; gap: 24px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color); position: relative;
        overflow: hidden; transition: all 0.5s;
    }
    .stat-card-modern:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }
    .card-gradient-bg {
        position: absolute; top: 0; left: 0; right: 0; height: 100%;
        background: var(--grad); opacity: 0.06; transition: opacity 0.5s ease;
    }
    .card-warning-bg { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .card-success-bg { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .card-info-bg { background: linear-gradient(135deg, #3498db, #2980b9); }
    .card-purple-bg { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    .card-danger-bg { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .stat-card-modern:hover .card-gradient-bg { opacity: 0.12; }
    
    .stat-icon-wrapper {
        width: 80px; height: 80px; border-radius: 20px;
        background: var(--grad); display: flex; align-items: center;
        justify-content: center; font-size: 36px; color: white;
        position: relative; flex-shrink: 0; z-index: 1;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.35);
    }
    .stat-warning-icon { background: linear-gradient(135deg, #f39c12, #e67e22); box-shadow: 0 8px 25px rgba(243, 156, 18, 0.35); }
    .stat-success-icon { background: linear-gradient(135deg, #2ecc71, #27ae60); box-shadow: 0 8px 25px rgba(46, 204, 113, 0.35); }
    .stat-info-icon { background: linear-gradient(135deg, #3498db, #2980b9); box-shadow: 0 8px 25px rgba(52, 152, 219, 0.35); }
    .stat-purple-icon { background: linear-gradient(135deg, #9b59b6, #8e44ad); box-shadow: 0 8px 25px rgba(155, 89, 182, 0.35); }
    .stat-danger-icon { background: linear-gradient(135deg, #e74c3c, #c0392b); box-shadow: 0 8px 25px rgba(231, 76, 60, 0.35); }
    
    .stat-content { flex: 1; z-index: 1; }
    .stat-value-modern {
        font-size: 2.8rem; font-weight: 800; color: var(--text-primary);
        line-height: 1; margin-bottom: 10px;
    }
    .stat-label-modern {
        font-size: 1.05rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px;
    }
    .stat-period {
        font-size: 0.9rem; color: var(--text-muted); font-weight: 500;
        display: flex; align-items: center; gap: 6px;
    }
    .stat-decoration {
        position: absolute; bottom: 15px; right: 15px; font-size: 80px;
        color: rgba(57, 166, 22, 0.08); z-index: 0;
    }
    [data-theme="dark"] .stat-decoration { color: rgba(57, 166, 22, 0.15); }

    /* Schedule Section */
    .schedule-section { margin-bottom: 30px; }
    .schedule-container-modern {
        background: var(--bg-primary); border-radius: 24px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color); overflow: hidden;
        transition: all 0.5s; margin-bottom: 25px;
    }
    .schedule-container-modern:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }
    .table-card-header {
        background: var(--grad); padding: 20px 28px; display: flex;
        justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 15px;
    }
    .table-title {
        font-size: 1.15rem; font-weight: 700; color: white; margin: 0;
        display: flex; align-items: center; gap: 10px;
    }
    .schedule-count {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.2); color: white;
        padding: 12px 24px; border-radius: 25px; font-size: 0.95rem;
        font-weight: 600; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
    }

    /* TABLE RESPONSIVE WRAPPER WITH HORIZONTAL SCROLL */
    .table-responsive {
        overflow-x: auto;
        width: 100%;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(57, 166, 22, 0.3) var(--bg-secondary);
    }

    /* Custom Scrollbar untuk Table */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: var(--bg-secondary);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background-color: rgba(57, 166, 22, 0.3);
        border-radius: 10px;
        border: 2px solid var(--bg-secondary);
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background-color: rgba(57, 166, 22, 0.5);
    }

    .schedule-table { 
        width: 100%; 
        border-collapse: collapse; 
        min-width: 700px; /* Minimum width agar tabel tidak terlalu sempit */
    }
    .schedule-table thead { background: var(--grad); }
    .schedule-table thead th {
        padding: 20px 24px; text-align: left; color: white;
        font-weight: 600; font-size: 0.95rem; text-transform: uppercase;
        letter-spacing: 0.8px; white-space: nowrap;
    }
    .schedule-table thead th i { margin-right: 10px; opacity: 0.95; }
    .schedule-table thead th.text-center { text-align: center; }
    .schedule-row { border-bottom: 1px solid var(--border-color); transition: all 0.3s ease; }
    .schedule-row:hover { background: var(--hover-bg); }
    .schedule-table tbody td { 
        padding: 20px 24px; 
        color: var(--text-secondary); 
        white-space: nowrap;
    }
    .schedule-table tbody td.text-center { text-align: center; }
    
    .doctor-info { display: flex; align-items: center; gap: 14px; }
    .doctor-avatar {
        width: 50px; height: 50px; border-radius: 50%; background: var(--grad);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 20px; flex-shrink: 0; overflow: hidden;
        border: 3px solid var(--border-color); box-shadow: 0 4px 12px rgba(57, 166, 22, 0.2);
    }
    .doctor-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .doctor-name { font-weight: 600; color: var(--text-primary); }
    
    .service-badge {
        display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.2));
        color: #1976d2; border-radius: 25px; font-weight: 600; font-size: 0.9rem;
        border: 1px solid rgba(52, 152, 219, 0.2);
    }
    [data-theme="dark"] .service-badge {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        color: #60a5fa; border-color: rgba(52, 152, 219, 0.4);
    }
    .time-badge-modern {
        display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px;
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.2));
        color: #856404; border-radius: 25px; font-weight: 600; font-size: 0.9rem;
        border: 1px solid rgba(243, 156, 18, 0.2);
    }
    [data-theme="dark"] .time-badge-modern {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(230, 126, 34, 0.3));
        color: #fbbf24; border-color: rgba(243, 156, 18, 0.4);
    }
    .btn-action-primary {
        display: inline-flex; align-items: center; gap: 10px;
        background: var(--grad); color: white; padding: 12px 24px;
        border-radius: 25px; text-decoration: none; font-size: 0.9rem;
        font-weight: 600; transition: all 0.4s; cursor: pointer; border: none;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3); position: relative; overflow: hidden;
        white-space: nowrap;
    }
    .btn-action-primary::before {
        content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }
    .btn-action-primary:hover::before { left: 100%; }
    .btn-action-primary:hover {
        background: var(--grad-reverse); transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
    }
    .btn-action-primary i { transition: transform 0.3s ease; }
    .btn-action-primary:hover i { transform: translateX(4px); }
    
    .empty-schedule { text-align: center; padding: 70px 20px; color: var(--text-muted); }
    .empty-schedule i { font-size: 4.5rem; margin-bottom: 24px; opacity: 0.3; }
    .empty-schedule p { font-size: 1.15rem; font-weight: 600; margin-bottom: 10px; color: var(--text-secondary); }
    .empty-schedule small { font-size: 0.95rem; color: var(--text-muted); }

    /* ===== RESPONSIVE DESIGN FOR MOBILE ===== */
    
    /* Tablet */
    @media (max-width: 992px) {
        .stats-grid-modern { 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
        }
        .hero-illustration { 
            display: none; 
        }
        .schedule-table {
            min-width: 650px;
        }
    }

    /* Mobile Landscape & Portrait */
    @media (max-width: 768px) {
        .dashboard-header-banner {
            margin-bottom: 30px;
        }

        .header-content { 
            flex-direction: column; 
            text-align: center; 
            padding: 28px 20px; 
            gap: 16px;
        }

        .header-icon {
            width: 65px;
            height: 65px;
            font-size: 32px;
        }
        
        .page-title { 
            font-size: 1.75rem; 
        }

        .page-subtitle {
            font-size: 0.95rem;
            justify-content: center;
        }

        .section-header { 
            flex-direction: column; 
            align-items: flex-start; 
            gap: 12px;
        }

        .section-header h2 {
            font-size: 1.4rem;
        }

        .stats-section {
            margin-bottom: 30px;
        }

        .stats-grid-modern {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .stat-card-modern { 
            padding: 24px 20px; 
            gap: 18px;
        }

        .stat-icon-wrapper { 
            width: 70px; 
            height: 70px; 
            font-size: 32px; 
        }

        .stat-value-modern { 
            font-size: 2.3rem; 
        }

        .stat-label-modern {
            font-size: 1rem;
        }

        .stat-period {
            font-size: 0.85rem;
        }

        .stat-decoration {
            font-size: 60px;
            bottom: 10px;
            right: 10px;
        }

        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
        }

        .table-title {
            font-size: 1.05rem;
        }

        .schedule-count {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
        
        .schedule-table { 
            font-size: 0.9rem; 
            min-width: 600px;
        }
        
        .schedule-table thead th, 
        .schedule-table tbody td { 
            padding: 16px 14px; 
        }
        
        .doctor-avatar { 
            width: 45px; 
            height: 45px; 
            font-size: 18px; 
        }

        .doctor-name {
            font-size: 0.95rem;
        }

        .service-badge,
        .time-badge-modern {
            font-size: 0.85rem;
            padding: 8px 16px;
        }

        .btn-action-primary {
            padding: 10px 20px;
            font-size: 0.85rem;
        }
    }

    /* Extra Small Mobile */
    @media (max-width: 576px) {
        .header-content {
            padding: 24px 18px;
            border-radius: 20px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            font-size: 28px;
            border-radius: 14px;
        }

        .page-title { 
            font-size: 1.5rem; 
        }

        .page-subtitle {
            font-size: 0.9rem;
        }
        
        .greeting-badge { 
            font-size: 0.8rem; 
            padding: 8px 16px; 
        }

        .section-header h2 {
            font-size: 1.25rem;
        }

        .section-header h2 i {
            font-size: 1.2rem;
        }

        .stats-section {
            margin-bottom: 25px;
        }

        .stat-card-modern { 
            flex-direction: column; 
            text-align: center; 
            padding: 20px 16px;
            gap: 16px;
        }

        .stat-icon-wrapper {
            width: 65px;
            height: 65px;
            font-size: 28px;
            border-radius: 16px;
        }

        .stat-value-modern {
            font-size: 2rem;
        }

        .stat-label-modern {
            font-size: 0.95rem;
        }

        .stat-period {
            font-size: 0.8rem;
            justify-content: center;
        }

        .table-card-header {
            padding: 16px 18px;
        }

        .table-title {
            font-size: 1rem;
        }

        .schedule-count {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        .schedule-table {
            min-width: 550px;
        }
        
        .schedule-table thead th { 
            font-size: 0.8rem; 
            padding: 14px 12px; 
        }
        
        .schedule-table tbody td { 
            padding: 14px 12px; 
            font-size: 0.85rem;
        }

        .doctor-avatar {
            width: 42px;
            height: 42px;
            font-size: 16px;
            border-width: 2px;
        }

        .doctor-name {
            font-size: 0.9rem;
        }
        
        .service-badge, 
        .time-badge-modern { 
            font-size: 0.8rem; 
            padding: 8px 14px; 
        }
        
        .btn-action-primary { 
            padding: 10px 18px; 
            font-size: 0.8rem; 
        }

        .btn-action-primary span {
            display: none;
        }

        .empty-schedule {
            padding: 50px 16px;
        }

        .empty-schedule i {
            font-size: 3.5rem;
        }

        .empty-schedule p {
            font-size: 1rem;
        }

        .empty-schedule small {
            font-size: 0.85rem;
        }
    }

    /* Ultra Small Mobile (< 400px) */
    @media (max-width: 400px) {
        .page-title {
            font-size: 1.35rem;
        }

        .section-header h2 {
            font-size: 1.15rem;
        }

        .stat-value-modern {
            font-size: 1.8rem;
        }

        .schedule-table {
            min-width: 500px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // =============================
    // FUNGSI UTAMA (Di-ekstrak agar bisa dipanggil ulang)
    // =============================
    function initDashboardScripts() {
        // 1. Update Waktu
        updateTime();
        if (window.dashboardTimeInterval) clearInterval(window.dashboardTimeInterval);
        window.dashboardTimeInterval = setInterval(updateTime, 1000);
        
        // 2. Animasi Angka Awal
        document.querySelectorAll('.stat-value-modern[data-count]').forEach(element => {
            animateCounter(element);
        });
        
        // 3. Jalankan Auto Refresh Global
        if (typeof window.initAutoRefresh === 'function') {
            window.initAutoRefresh([
                '#live-stats',    // Card Statistik
                '#live-jadwal',   // Tabel Jadwal
                '#live-antrian'   // Tabel Antrian
            ]);
        }
    }

    // Fungsi Waktu
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
        const timeElement = document.getElementById('current-time');
        if (timeElement) timeElement.textContent = timeString;
    }
    
    // Fungsi Animasi Angka
    function animateCounter(element) {
        const targetAttr = element.getAttribute('data-count');
        const target = targetAttr ? parseInt(targetAttr) : parseInt(element.textContent);
        if (isNaN(target)) return;

        const duration = 1500;
        const start = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (easeOutExpo)
            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            
            element.textContent = Math.floor(start + (target - start) * ease);

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = target;
            }
        }
        requestAnimationFrame(update);
    }

    // Fungsi Rebind (Dipanggil otomatis saat data berubah)
    window.rebindEvents = function() {
        // Logika tambahan setelah refresh (jika ada)
        console.log('✅ Dashboard updated!');
    };

    // =============================
    // EVENT LISTENERS (PENTING!)
    // =============================
    
    // Jalan saat halaman pertama kali dibuka (Refresh F5)
    document.addEventListener('DOMContentLoaded', initDashboardScripts);
    
    // Jalan saat pindah menu (Turbo Drive / SPA navigation)
    document.addEventListener('turbo:load', initDashboardScripts);
    
    // Bersihkan interval saat pindah halaman agar tidak menumpuk
    document.addEventListener('turbo:before-cache', () => {
        if (window.dashboardTimeInterval) clearInterval(window.dashboardTimeInterval);
    });
</script>
@endpush
