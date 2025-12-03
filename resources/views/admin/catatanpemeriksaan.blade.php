@extends('layouts.admin')
@section('title', 'Catatan Pemeriksaan Awal')

{{-- 1. Import CSS Flatpickr --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- HEADER BANNER (SAMA DENGAN DASHBOARD ADMIN) --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-user-injured"></i>
                    <span>Medical Records</span>
                </div>
                <h1 class="page-title">Catatan Pemeriksaan Awal 📋</h1>
                <p class="page-subtitle">
                    <i class="far fa-calendar-alt"></i>
                    Kelola dan input pemeriksaan awal pasien
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <div class="time-widget">
                    <i class="fas fa-notes-medical"></i>
                    <span>Input Data</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter Pasien</h2>
        </div>
        <div class="filter-card-modern">
            <form action="{{ route('admin.catatanpemeriksaan') }}" method="GET" class="filter-form-modern">
                <div class="filter-input-wrapper">
                    <label for="tanggalFilter" class="filter-label">
                        <i class="fas fa-calendar-alt"></i>
                        Tampilkan Pasien untuk Tanggal:
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-calendar-day"></i>
                        <input type="text" name="tanggal" id="tanggalFilter" placeholder="Pilih Tanggal...">
                    </div>
                </div>
                <div class="filter-button-group">
                    <button type="submit" class="btn-filter-modern btn-primary-filter">
                        <i class="fas fa-search"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.catatanpemeriksaan') }}" class="btn-filter-modern btn-secondary-filter">
                        <i class="fas fa-redo"></i>
                        <span>Tampilkan Semua</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert-success-modern" id="autoHideAlert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="alert-text">{{ session('success') }}</span>
            <button class="alert-close-btn" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- TABLES SECTION --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-list-alt"></i> Daftar Pendaftaran Pasien</h2>
        </div>

        @forelse ($pendaftarans as $layanan => $listPendaftaran)
            <div class="layanan-group-modern">
                <div class="layanan-header-modern">
                    <div class="layanan-title-wrapper">
                        <i class="fas fa-hospital"></i>
                        <h3>{{ $layanan }}</h3>
                    </div>
                    <span class="schedule-count">
                        <i class="fas fa-users"></i>
                        {{ count($listPendaftaran) }} Pasien
                    </span>
                </div>

                <div class="schedule-container-modern">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> Antrian</th>
                                <th><i class="fas fa-user"></i> Nama Pasien</th>
                                <th><i class="fas fa-user-md"></i> Nama Dokter</th>
                                <th><i class="fas fa-comment-medical"></i> Keluhan</th>
                                <th><i class="fas fa-calendar-check"></i> Jadwal Dipilih</th>
                                <th><i class="far fa-clock"></i> Tgl Daftar</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                                <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listPendaftaran as $index => $pendaftaran)
                                <tr class="schedule-row" style="animation-delay: {{ $index * 0.05 }}s">
                                    <td>
                                        <span class="queue-number-badge">
                                            {{ $pendaftaran->no_antrian ?? '-' }}
                                        </span>
                                    </td>
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
                                        <span class="doctor-name-text">
                                            {{ $pendaftaran->jadwalPraktek?->tenagaMedis?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="keluhan-badge">
                                            {{ Str::limit($pendaftaran->keluhan, 40) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-badge-modern">
                                            <i class="fas fa-calendar"></i>
                                            <span>{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('D MMM YYYY') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="time-text">
                                            {{ $pendaftaran->created_at->isoFormat('D MMM Y, HH:mm') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pendaftaran->status == 'Menunggu')
                                            <span class="status-badge-modern status-waiting">
                                                <i class="fas fa-clock"></i>
                                                {{ $pendaftaran->status }}
                                            </span>
                                        @elseif($pendaftaran->status == 'Diperiksa Awal')
                                            <span class="status-badge-modern status-checking">
                                                <i class="fas fa-stethoscope"></i>
                                                {{ $pendaftaran->status }}
                                            </span>
                                        @else
                                            <span class="status-badge-modern status-done">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $pendaftaran->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($pendaftaran->status == 'Menunggu')
                                            <button type="button" 
                                                    class="btn-action-primary open-periksa-modal"
                                                    data-url="{{ route('admin.pemeriksaan-awal.json', $pendaftaran->id) }}">
                                                <span>Input Periksa Awal</span>
                                                <i class="fas fa-clipboard-check"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn-action-disabled" disabled>
                                                <i class="fas fa-check"></i>
                                                <span>{{ $pendaftaran->status }}</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-schedule">
                                            <i class="fas fa-inbox"></i>
                                            <p>Belum ada pasien mendaftar untuk layanan ini</p>
                                            <small>Silakan cek kembali nanti</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="alert-info-modern">
                <div class="alert-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="alert-text">
                    @if($tanggal ?? null)
                        Tidak ada pendaftaran pasien untuk tanggal {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM YYYY') }}.
                    @else
                        Belum ada pendaftaran pasien sama sekali.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

{{-- MODAL PEMERIKSAAN AWAL --}}
<div id="periksaAwalModal" class="modal-overlay">
    <div class="modal-card">
        <span class="close-modal" id="closeModalBtn">&times;</span>
        <div id="modalFormContent">
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
    
    /* ===== DARK MODE SUPPORT ===== */
    :root {
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
        --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
        
        /* Light Mode Colors */
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f3f4f6;
        --border-color: rgba(57, 166, 22, 0.15);
        --shadow-color: rgba(57, 166, 22, 0.1);
        --hover-bg: rgba(57, 166, 22, 0.04);
        --modal-bg: #ffffff;
        --modal-overlay: rgba(0,0,0,0.7);
    }

    /* Dark Mode Colors */
    [data-theme="dark"],
    .dark-mode {
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        --bg-primary: #1f2937;
        --bg-secondary: #111827;
        --bg-tertiary: #374151;
        --border-color: rgba(57, 166, 22, 0.3);
        --shadow-color: rgba(0, 0, 0, 0.3);
        --hover-bg: rgba(57, 166, 22, 0.15);
        --modal-bg: #1f2937;
        --modal-overlay: rgba(0,0,0,0.85);
    }

    /* Auto Dark Mode (sistem preference) */
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
            --modal-bg: #1f2937;
            --modal-overlay: rgba(0,0,0,0.85);
        }
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: background 0.3s ease, color 0.3s ease;
    }
    
    .container-fluid-modern {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER BANNER PREMIUM (SAMA DENGAN DASHBOARD) ===== */
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
        position: relative;
        flex-shrink: 0;
        z-index: 1;
    }

    .time-widget {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(15px);
        padding: 18px 28px;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        animation: float 3s ease-in-out infinite;
        min-width: 160px;
        justify-content: center;
    }

    .pulse-circle {
        position: absolute;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        animation: pulse-ring 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    .pulse-1 { width: 140px; height: 140px; animation-delay: 0s; }
    .pulse-2 { width: 170px; height: 170px; animation-delay: 0.8s; }
    .pulse-3 { width: 200px; height: 200px; animation-delay: 1.6s; }
    
    @keyframes pulse-ring {
        0% { transform: translate(-50%, -50%) scale(0.9); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
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

    /* ===== FILTER SECTION ===== */
    .stats-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .filter-card-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-card-modern:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .filter-form-modern {
        display: flex;
        align-items: flex-end;
        gap: 25px;
        flex-wrap: wrap;
    }

    .filter-input-wrapper {
        flex: 1;
        min-width: 300px;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
        margin-bottom: 12px;
    }

    .filter-label i {
        color: var(--p1);
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--p1);
        font-size: 1.1rem;
    }

    .input-with-icon input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid var(--border-color);
        border-radius: 16px;
        font-size: 0.95rem;
        font-family: inherit;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .input-with-icon input:focus {
        outline: none;
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    .filter-button-group {
        display: flex;
        gap: 12px;
    }

    .btn-filter-modern {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 28px;
        border: none;
        border-radius: 16px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .btn-filter-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-filter-modern:hover::before {
        left: 100%;
    }

    .btn-primary-filter {
        background: var(--grad);
        color: white;
    }

    .btn-primary-filter:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.4);
    }

    .btn-secondary-filter {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }

    .btn-secondary-filter:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(108, 117, 125, 0.4);
    }

    /* ===== ALERTS ===== */
    .alert-success-modern,
    .alert-info-modern {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 28px;
        border-radius: 20px;
        margin-bottom: 30px;
        animation: slideInDown 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .alert-success-modern {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }

    .alert-info-modern {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 2px solid #17a2b8;
    }

    /* Dark mode alerts */
    [data-theme="dark"] .alert-success-modern,
    .dark-mode .alert-success-modern {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        border-color: #28a745;
    }

    [data-theme="dark"] .alert-info-modern,
    .dark-mode .alert-info-modern {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        border-color: #17a2b8;
    }

    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .alert-success-modern .alert-icon {
        background: #28a745;
        color: white;
        font-size: 22px;
    }

    .alert-info-modern .alert-icon {
        background: #17a2b8;
        color: white;
        font-size: 22px;
    }

    .alert-text {
        flex: 1;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
    }

    .alert-close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1.4rem;
        padding: 5px;
        transition: all 0.3s ease;
    }

    .alert-close-btn:hover {
        color: var(--text-primary);
        transform: rotate(90deg);
    }

    /* ===== SCHEDULE SECTION (TABLE SAMA DENGAN DASHBOARD) ===== */
    .schedule-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .layanan-group-modern {
        margin-bottom: 30px;
    }

    .layanan-header-modern {
        background: var(--grad);
        padding: 24px 30px;
        border-radius: 24px 24px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.2);
    }

    .layanan-title-wrapper {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .layanan-title-wrapper i {
        font-size: 1.6rem;
        color: white;
    }

    .layanan-title-wrapper h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .schedule-count {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.95rem;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
    }

    .schedule-container-modern {
        background: var(--bg-primary);
        border-radius: 0 0 24px 24px;
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
    }

    .schedule-table tbody td.text-center {
        text-align: center;
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

    /* Badge Nomor Antrian */
    .queue-number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border-radius: 14px;
        font-weight: 800;
        font-size: 1.2rem;
        padding: 0 12px;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .doctor-name-text {
        color: var(--text-secondary);
        font-weight: 600;
    }

    .keluhan-badge {
        color: var(--text-secondary);
        font-style: italic;
        font-size: 0.9rem;
    }

    .date-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.2));
        color: #856404;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(243, 156, 18, 0.2);
    }

    /* Dark mode date badge */
    [data-theme="dark"] .date-badge-modern,
    .dark-mode .date-badge-modern {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(230, 126, 34, 0.3));
        color: #fbbf24;
        border-color: rgba(243, 156, 18, 0.4);
    }

    .time-text {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Status Badge */
    .status-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .status-waiting {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.15), rgba(230, 126, 34, 0.25));
        color: #856404;
        border: 1px solid rgba(243, 156, 18, 0.3);
    }

    .status-checking {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.15), rgba(41, 128, 185, 0.25));
        color: #0c5460;
        border: 1px solid rgba(52, 152, 219, 0.3);
    }

    .status-done {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.15), rgba(39, 174, 96, 0.25));
        color: #155724;
        border: 1px solid rgba(46, 204, 113, 0.3);
    }

    /* Dark mode status badges */
    [data-theme="dark"] .status-waiting,
    .dark-mode .status-waiting {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(230, 126, 34, 0.3));
        color: #fbbf24;
        border-color: rgba(243, 156, 18, 0.4);
    }

    [data-theme="dark"] .status-checking,
    .dark-mode .status-checking {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        color: #60a5fa;
        border-color: rgba(52, 152, 219, 0.4);
    }

    [data-theme="dark"] .status-done,
    .dark-mode .status-done {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        color: #34d399;
        border-color: rgba(46, 204, 113, 0.4);
    }

    /* Action Buttons */
    .btn-action-primary {
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

    .btn-action-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-action-primary:hover::before {
        left: 100%;
    }

    .btn-action-primary:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
    }

    .btn-action-primary i {
        transition: transform 0.3s ease;
    }

    .btn-action-primary:hover i {
        transform: translateX(4px);
    }

    .btn-action-disabled {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        color: var(--text-muted);
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Empty State */
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

    /* ===== MODAL ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: var(--modal-overlay);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s;
        backdrop-filter: blur(8px);
    }

    .modal-card {
        background-color: var(--modal-bg);
        margin: auto;
        border-radius: 24px;
        width: 90%;
        max-width: 650px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: slideDown 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .close-modal {
        color: var(--text-muted);
        align-self: flex-end;
        font-size: 36px;
        font-weight: bold;
        cursor: pointer;
        padding: 15px 25px;
        transition: all 0.3s ease;
    }

    .close-modal:hover, .close-modal:focus {
        color: var(--p1);
        transform: rotate(90deg);
    }

    #modalFormContent {
        padding: 0 40px 40px 40px;
        overflow-y: auto;
    }

    /* Form Styles */
    .form-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 3px solid var(--border-color);
    }

    .form-title {
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 1.05rem;
        font-weight: 600;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 12px;
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--border-color);
        border-radius: 14px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .form-control[readonly] {
        background-color: var(--bg-tertiary);
        color: var(--text-muted);
        cursor: not-allowed;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--p1);
        background-color: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.15);
    }

    .form-actions {
        text-align: center;
        margin-top: 35px;
        padding-top: 30px;
        border-top: 3px solid var(--border-color);
    }

    .btn-primary {
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 16px 45px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.05rem;
        width: 100%;
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
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.5);
    }

    .btn-secondary {
        margin-top: 18px;
        display: inline-block;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        color: var(--p1);
    }

    /* Loading Spinner */
    .loading-spinner {
        border: 6px solid var(--bg-tertiary);
        border-top: 6px solid var(--p1);
        border-radius: 50%;
        width: 70px;
        height: 70px;
        animation: spin 1s linear infinite;
        margin: 80px auto;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-illustration {
            display: none;
        }

        .filter-form-modern {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-input-wrapper {
            width: 100%;
        }

        .filter-button-group {
            width: 100%;
        }

        .btn-filter-modern {
            flex: 1;
            justify-content: center;
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

        .layanan-header-modern {
            flex-direction: column;
            gap: 12px;
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

        .modal-card {
            width: 95%;
            margin: 20px;
        }

        #modalFormContent {
            padding: 0 20px 30px 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
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

        .section-header h2 {
            font-size: 1.3rem;
        }

        .filter-button-group {
            flex-direction: column;
        }

        .btn-action-primary span {
            display: none;
        }

        .schedule-table thead th {
            font-size: 0.8rem;
            padding: 12px 10px;
        }

        .schedule-table tbody td {
            padding: 12px 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Inisialisasi Flatpickr dengan tema hijau
    flatpickr("#tanggalFilter", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        defaultDate: "{{ $tanggal ?? '' }}",
        placeholder: "Pilih Tanggal...",
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            },
        }
    });

    // --- Logika Modal ---
    const modal = document.getElementById('periksaAwalModal');
    const modalContent = document.getElementById('modalFormContent');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const links = document.querySelectorAll('.open-periksa-modal');

    links.forEach(link => {
        link.addEventListener('click', async function(event) {
            event.preventDefault();
            const jsonUrl = this.dataset.url;
            
            modal.style.display = 'flex';
            modalContent.innerHTML = '<div class="loading-spinner"></div>';

            try {
                const response = await fetch(jsonUrl);
                if (!response.ok) throw new Error('Gagal mengambil data');
                const data = await response.json();

                const formHtml = `
                    <div class="form-header">
                        <h2 class="form-title">Input Pemeriksaan Awal</h2>
                        <p class="form-subtitle">Pasien: <strong>${data.pasien_name}</strong> (${data.layanan_name})</p>
                    </div>
                    <form action="${data.form_action}" method="POST">
                        @csrf
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tekanan_darah" class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" name="tekanan_darah" id="tekanan_darah" class="form-control" placeholder="cth: 120/80" required>
                            </div>
                            <div class="form-group">
                                <label for="berat_badan" class="form-label">Berat Badan (Kg)</label>
                                <input type="number" step="0.1" name="berat_badan" id="berat_badan" class="form-control" placeholder="cth: 55.5" required>
                            </div>
                            <div class="form-group">
                                <label for="suhu_tubuh" class="form-label">Suhu Tubuh (°C)</label>
                                <input type="number" step="0.1" name="suhu_tubuh" id="suhu_tubuh" class="form-control" placeholder="cth: 36.5" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Simpan Pemeriksaan Awal</button>
                            <a href="#" class="btn-secondary" id="batalModalBtn">Batal</a>
                        </div>
                    </form>
                `;
                
                modalContent.innerHTML = formHtml.replace('@csrf', '{{ csrf_field() }}');
                
                document.getElementById('batalModalBtn').addEventListener('click', (e) => {
                    e.preventDefault();
                    closeModal();
                });

            } catch (error) {
                console.error('Gagal memuat form modal:', error);
                modalContent.innerHTML = '<p style="text-align:center; color:red; padding:40px;">Gagal memuat form. Silakan muat ulang halaman.</p>';
            }
        });
    });

    // Fungsi tutup modal
    function closeModal() {
        modal.style.display = 'none';
        modalContent.innerHTML = '';
    }
    
    if(closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            closeModal();
        }
    });

    // Auto-hide alert sukses
    const alertElement = document.getElementById('autoHideAlert');
    if (alertElement) {
        setTimeout(() => {
            alertElement.style.opacity = '0';
            alertElement.style.transition = 'opacity 0.6s ease';
            setTimeout(() => alertElement.remove(), 600);
        }, 5000); 
    }
</script>
@endpush
