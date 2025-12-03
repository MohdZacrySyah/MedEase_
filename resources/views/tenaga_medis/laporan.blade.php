@extends('layouts.tenaga_medis')
@section('title', 'Laporan Kunjungan Saya')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Banner (DESIGN MODERN) --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-chart-bar"></i>
                    <span id="greeting-time">Selamat Pagi</span>
                </div>
                <h1 class="page-title">Laporan Kunjungan Saya 📊</h1>
                <p class="page-subtitle">
                    <i class="fas fa-info-circle"></i>
                    Analisis dan statistik pasien yang telah Anda periksa
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    {{-- Stats Section (DESIGN MODERN) --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-chart-line"></i> Statistik Pemeriksaan</h2>
        </div>
        <div class="stats-grid-modern">
            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper stat-info-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $kunjunganHariIni }}">0</div>
                    <div class="stat-label-modern">Pasien Diperiksa Hari Ini</div>
                    <div class="stat-period">Aktif Sekarang</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $kunjunganBulanIni }}">0</div>
                    <div class="stat-label-modern">Pasien Diperiksa Bulan Ini</div>
                    <div class="stat-period">Periode Bulanan</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <div class="stat-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="stat-icon-wrapper stat-warning-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value-modern" data-count="{{ $semuaKunjungan }}">0</div>
                    <div class="stat-label-modern">Total Pasien Diperiksa</div>
                    <div class="stat-period">Keseluruhan</div>
                </div>
                <div class="stat-decoration">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section (DESIGN MODERN) --}}
    <div class="filter-section">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter & Tampilan Data</h2>
        </div>
        <div class="filter-bar">
            <div class="filter-controls-modern">
                <div class="filter-buttons-group">
                    <a href="{{ route('tenaga-medis.laporan', ['filter' => 'hari_ini']) }}" 
                       class="btn-filter-modern {{ $filter == 'hari_ini' ? 'btn-active' : '' }}">
                        <i class="fas fa-calendar-day"></i>
                        <span>Hari Ini</span>
                    </a>
                    <a href="{{ route('tenaga-medis.laporan', ['filter' => 'bulan_ini']) }}" 
                       class="btn-filter-modern {{ $filter == 'bulan_ini' ? 'btn-active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bulan Ini</span>
                    </a>
                    <a href="{{ route('tenaga-medis.laporan', ['filter' => 'semua_data']) }}" 
                       class="btn-filter-modern {{ $filter == 'semua_data' ? 'btn-active' : '' }}">
                        <i class="fas fa-database"></i>
                        <span>Semua Data</span>
                    </a>
                    
                    <div class="input-picker-wrapper">
                        <input type="text" id="bulanFilter" 
                               class="btn-filter-modern input-picker {{ $filter == 'bulan_terpilih' ? 'btn-active' : '' }}" 
                               placeholder="📅 Pilih Bulan" 
                               value="{{ $filter == 'bulan_terpilih' ? \Carbon\Carbon::parse($bulanDipilih)->isoFormat('MMMM YYYY') : '' }}">
                    </div>
                    
                    <div class="input-picker-wrapper">
                        <input type="text" id="tanggalFilter" 
                               class="btn-filter-modern input-picker {{ $filter == 'tanggal' ? 'btn-active' : '' }}" 
                               placeholder="📅 Pilih Tanggal"
                               value="{{ $filter == 'tanggal' ? \Carbon\Carbon::parse($tanggalDipilih)->isoFormat('D MMM YYYY') : '' }}">
                    </div>
                </div>
                <div class="view-toggle-group">
                    <button id="showTableBtn" class="btn-filter-modern btn-toggle-active">
                        <i class="fas fa-table"></i> 
                        <span>Data</span>
                    </button>
                    <button id="showChartBtn" class="btn-filter-modern btn-filter-style">
                        <i class="fas fa-chart-line"></i> 
                        <span>Grafik</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Section (DESIGN MODERN) --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-clipboard-list"></i> Data Pemeriksaan Pasien</h2>
            <span class="schedule-count">
                <i class="fas fa-check-circle"></i>
                {{ $kunjunganData->count() }} Data
            </span>
        </div>

        {{-- TAMPILAN TABEL --}}
        <div id="tableView" class="view-content active">
            <div class="schedule-container-modern">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID Pasien</th>
                            <th><i class="fas fa-user"></i> Nama Pasien</th>
                            <th><i class="fas fa-hospital"></i> Layanan</th>
                            <th><i class="far fa-clock"></i> Tanggal Pemeriksaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjunganData as $index => $data)
                            <tr class="schedule-row" style="animation-delay: {{ $index * 0.1 }}s">
                                <td>
                                    <div class="no-antrian-badge">
                                        {{ $data->pasien_id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="doctor-info">
                                        <div class="doctor-avatar">
                                            @if($data->profile_photo_path)
                                                <img src="{{ asset('storage/' . $data->profile_photo_path) }}" alt="Foto">
                                            @else
                                                {{ substr($data->nama_pasien ?? 'P', 0, 1) }}
                                            @endif
                                        </div>
                                        <span class="doctor-name">{{ $data->nama_pasien }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-diperiksa-awal">
                                        <i class="fas fa-briefcase-medical"></i>
                                        {{ $data->layanan }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-selesai">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-schedule">
                                        <i class="fas fa-inbox"></i>
                                        <p>Tidak ada data pemeriksaan</p>
                                        <small>Untuk periode yang dipilih</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAMPILAN GRAFIK --}}
        <div id="chartView" class="view-content" style="display: none;">
            <div class="schedule-container-modern">
                <div class="table-card-header">
                    <h3 class="table-title">
                        <i class="fas fa-chart-area"></i> 
                        Grafik Pemeriksaan ({{ ucfirst(str_replace('_', ' ', $filter)) }})
                    </h3>
                </div>
                <div class="chart-container-wrapper">
                    <canvas id="kunjunganChart"></canvas>
                </div>
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
    
    /* ===== DARK MODE SUPPORT ===== */
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

    /* ===== HEADER BANNER ===== */
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

    /* ===== STATS SECTION ===== */
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
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
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
        animation: float 3s ease-in-out infinite;
    }

    .stat-info-icon {
        background: linear-gradient(135deg, #3498db, #2980b9);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.35);
    }

    .stat-warning-icon {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.35);
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
        animation: float 4s ease-in-out infinite;
    }

    [data-theme="dark"] .stat-decoration {
        color: rgba(57, 166, 22, 0.15);
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .filter-bar {
        background: var(--bg-primary);
        padding: 24px 28px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 8px 30px var(--shadow-color);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-bar:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .filter-controls-modern {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .filter-buttons-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
    }

    .view-toggle-group {
        display: flex;
        gap: 10px;
    }

    .btn-filter-modern {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 2px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-secondary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .btn-filter-modern:hover {
        background: var(--hover-bg);
        border-color: var(--p1);
        color: var(--text-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.15);
    }

    .btn-filter-modern.btn-active,
    .btn-toggle-active {
        background: var(--grad);
        color: white;
        border-color: transparent;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
    }

    .btn-filter-style {
        background: var(--bg-secondary);
        color: var(--text-secondary);
    }

    .input-picker {
        min-width: 180px;
        cursor: pointer;
        background: var(--bg-secondary);
    }

    .input-picker::placeholder {
        color: var(--text-muted);
    }

    .input-picker.btn-active {
        background: var(--grad);
    }

    .input-picker.btn-active::placeholder {
        color: rgba(255, 255, 255, 0.9);
    }

    /* ===== SCHEDULE SECTION ===== */
    .schedule-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
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
    }

    .no-antrian-badge {
        display: inline-flex;
        width: 50px;
        height: 50px;
        background: var(--grad);
        color: white;
        border-radius: 12px;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
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

    /* ===== CHART CONTAINER ===== */
    .table-card-header {
        background: var(--grad);
        padding: 20px 28px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .table-title i {
        font-size: 1.3rem;
    }

    .chart-container-wrapper {
        padding: 40px;
        background: var(--bg-primary);
    }

    .view-content {
        display: none;
    }

    .view-content.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-illustration {
            display: none;
        }

        .filter-controls-modern {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-buttons-group,
        .view-toggle-group {
            width: 100%;
        }

        .btn-filter-modern {
            flex: 1;
            justify-content: center;
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

        .stats-grid-modern {
            grid-template-columns: 1fr;
        }

        .filter-buttons-group {
            flex-direction: column;
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

        .chart-container-wrapper {
            padding: 20px;
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
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
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

    // Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
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
    
    document.querySelectorAll('.stat-value-modern[data-count]').forEach(element => {
        animateCounter(element);
    });
    
    // Chart.js - JAVASCRIPT DARI KODE LAMA YANG BERHASIL
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartData = {!! json_encode($chartData) !!};
    
    if (chartData.length > 0) {
        const ctx = document.getElementById('kunjunganChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Pemeriksaan',
                    data: chartData,
                    backgroundColor: 'rgba(57, 166, 22, 0.1)',
                    borderColor: '#39A616',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#39A616',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(57, 166, 22, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: '#0C5B00',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 13, weight: '600' },
                            color: '#556E85'
                        }
                    }
                }
            }
        });
    }

    // Toggle Tabel/Grafik - SCRIPT DARI KODE LAMA
    const showTableBtn = document.getElementById('showTableBtn');
    const showChartBtn = document.getElementById('showChartBtn');
    const tableView = document.getElementById('tableView');
    const chartView = document.getElementById('chartView');

    if(showTableBtn) {
        showTableBtn.addEventListener('click', function() {
            tableView.style.display = 'block';
            chartView.style.display = 'none';
            showTableBtn.classList.add('btn-toggle-active');
            showTableBtn.classList.remove('btn-filter-style');
            showChartBtn.classList.add('btn-filter-style');
            showChartBtn.classList.remove('btn-toggle-active');
        });
    }
    if(showChartBtn) {
        showChartBtn.addEventListener('click', function() {
            tableView.style.display = 'none';
            chartView.style.display = 'block';
            showTableBtn.classList.add('btn-filter-style');
            showTableBtn.classList.remove('btn-toggle-active');
            showChartBtn.classList.add('btn-toggle-active');
            showChartBtn.classList.remove('btn-filter-style');
        });
    }

    // Filter Tanggal
    flatpickr("#tanggalFilter", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        onChange: function(selectedDates, dateStr, instance) {
            if (dateStr) {
                window.location.href = `{{ route('tenaga-medis.laporan') }}?filter=tanggal&tanggal=${dateStr}`;
            }
        }
    });

    // Filter Bulan
    flatpickr("#bulanFilter", {
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y",
            })
        ],
        onChange: function(selectedDates, dateStr, instance) {
            if (dateStr) {
                window.location.href = `{{ route('tenaga-medis.laporan') }}?filter=bulan_terpilih&bulan=${dateStr}`;
            }
        }
    });
});
</script>
@endpush
