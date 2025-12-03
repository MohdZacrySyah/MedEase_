@extends('layouts.admin')

@section('title', 'Kelola Data Pasien')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Banner --}}
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-database"></i>
                    <span>Patient Database</span>
                </div>
                <h1 class="page-title">Kelola Data Pasien 👥</h1>
                <p class="page-subtitle">
                    <i class="far fa-folder-open"></i>
                    Manajemen Data dan Informasi Pasien
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <div class="time-widget">
                    <i class="fas fa-user-friends"></i>
                    <span>Database</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="search-section">
        <div class="section-header">
            <h2><i class="fas fa-search"></i> Pencarian Pasien</h2>
        </div>
        <div class="search-card">
            <form action="{{ route('admin.keloladatapasien') }}" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           placeholder="Cari berdasarkan nama atau email..." 
                           value="{{ $search ?? '' }}"
                           class="search-input">
                    <button type="submit" class="btn-search">
                        <span>Cari</span>
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if($search ?? false)
                    <div class="search-info">
                        <i class="fas fa-info-circle"></i>
                        <span>Menampilkan hasil pencarian: <strong>"{{ $search }}"</strong></span>
                        <a href="{{ route('admin.keloladatapasien') }}" class="btn-clear">
                            <i class="fas fa-times"></i>
                            <span>Hapus Filter</span>
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Alert Success --}}
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

    {{-- Table Card --}}
    <div class="schedule-container-modern">
        <div class="table-card-header">
            <h3 class="table-title">
                <i class="fas fa-list"></i> 
                Daftar Pasien Terdaftar
            </h3>
            <span class="schedule-count">
                <i class="fas fa-users"></i>
                {{ $pasiens->count() }} Pasien
            </span>
        </div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-user"></i> Nama Pasien</th>
                    <th><i class="fas fa-birthday-cake"></i> Tanggal Lahir</th>
                    <th><i class="fas fa-map-marker-alt"></i> Alamat</th>
                    <th><i class="fas fa-phone"></i> No HP/WA</th>
                    <th><i class="fas fa-calendar-plus"></i> Registrasi</th>
                    <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pasiens as $index => $pasien)
                    <tr class="schedule-row" style="animation-delay: {{ $index * 0.05 }}s">
                        <td>
                            <span class="number-badge">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div class="doctor-info">
                                <div class="doctor-avatar">
                                    @if($pasien->profile_photo_path)
                                        <img src="{{ asset('storage/' . $pasien->profile_photo_path) }}" alt="Foto">
                                    @else
                                        {{ substr($pasien->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="patient-details">
                                    <span class="doctor-name">{{ $pasien->name }}</span>
                                    @if($pasien->email)
                                        <span class="patient-email">{{ $pasien->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="date-badge">
                                <i class="fas fa-calendar"></i>
                                {{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->isoFormat('DD-MM-YYYY') : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ Str::limit($pasien->alamat ?? 'N/A', 35) }}</span>
                        </td>
                        <td>
                            @if($pasien->no_hp)
                                <span class="phone-badge">
                                    <i class="fab fa-whatsapp"></i>
                                    {{ $pasien->no_hp }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $pasien->created_at->isoFormat('D MMM YYYY') }}</span>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('admin.pasien.riwayat', $pasien->id) }}" 
                                   class="btn-action btn-info"
                                   title="Lihat Riwayat">
                                    <i class="fas fa-history"></i>
                                </a>
                                <form action="{{ route('admin.pasien.hapus', $pasien->id) }}" 
                                      method="POST" 
                                      class="delete-form"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn-action btn-hapus" 
                                            title="Hapus Pasien">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-schedule">
                                <i class="fas fa-user-slash"></i>
                                <p>Tidak ada data pasien ditemukan</p>
                                @if($search ?? false)
                                    <a href="{{ route('admin.keloladatapasien') }}" class="btn-reset">
                                        <span>Tampilkan Semua Pasien</span>
                                        <i class="fas fa-redo"></i>
                                    </a>
                                @else
                                    <small>Data pasien akan muncul di sini setelah ada yang mendaftar</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
    }

    /* Auto Dark Mode */
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

    /* ===== HEADER BANNER (SAMA DENGAN DASHBOARD) ===== */
    .page-header-banner {
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

    /* ===== SECTION HEADER ===== */
    .section-header {
        margin-bottom: 20px;
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

    /* ===== SEARCH SECTION ===== */
    .search-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .search-card {
        background: var(--bg-primary);
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .search-card:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .search-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .search-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        color: var(--p1);
        font-size: 1.1rem;
        z-index: 1;
    }

    .search-input {
        flex: 1;
        padding: 16px 18px 16px 50px;
        border: 2px solid var(--border-color);
        border-radius: 16px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    .btn-search {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--grad);
        color: white;
        padding: 16px 32px;
        border-radius: 16px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-search::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-search:hover::before {
        left: 100%;
    }

    .btn-search:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.5);
    }

    .search-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.15));
        border-radius: 14px;
        border-left: 4px solid #17a2b8;
        color: var(--text-primary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    [data-theme="dark"] .search-info,
    .dark-mode .search-info {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.25));
    }

    .search-info i {
        font-size: 1.2rem;
        color: #17a2b8;
    }

    .btn-clear {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(231, 76, 60, 0.25);
    }

    .btn-clear:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.35);
    }

    /* ===== ALERT ===== */
    .alert-success-modern {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 28px;
        border-radius: 20px;
        margin-bottom: 30px;
        animation: slideInDown 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }

    [data-theme="dark"] .alert-success-modern,
    .dark-mode .alert-success-modern {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        border-color: #28a745;
    }

    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #28a745;
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

    /* ===== TABLE (SAMA DENGAN DASHBOARD) ===== */
    .schedule-container-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }

    .schedule-container-modern:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .table-card-header {
        background: var(--grad);
        padding: 20px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
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

    .text-center {
        text-align: center !important;
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

    .text-muted {
        color: var(--text-muted);
    }

    /* Number Badge */
    .number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        padding: 0 10px;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.25);
    }

    /* Patient Info */
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

    .patient-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .doctor-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .patient-email {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-style: italic;
    }

    /* Badges */
    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.1), rgba(142, 68, 173, 0.2));
        color: #7b1fa2;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(155, 89, 182, 0.2);
    }

    [data-theme="dark"] .date-badge,
    .dark-mode .date-badge {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.2), rgba(142, 68, 173, 0.3));
        color: #c084fc;
        border-color: rgba(155, 89, 182, 0.4);
    }

    .phone-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.2));
        color: #155724;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(46, 204, 113, 0.2);
    }

    [data-theme="dark"] .phone-badge,
    .dark-mode .phone-badge {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        color: #34d399;
        border-color: rgba(46, 204, 113, 0.4);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-info {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.25);
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #2980b9, #21618c);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.35);
    }

    .btn-hapus {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        box-shadow: 0 2px 8px rgba(231, 76, 60, 0.25);
    }

    .btn-hapus:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.35);
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

    .btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--grad);
        color: white;
        padding: 14px 28px;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        margin-top: 15px;
        position: relative;
        overflow: hidden;
    }

    .btn-reset::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-reset:hover::before {
        left: 100%;
    }

    .btn-reset:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.5);
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-illustration {
            display: none;
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

        .search-input-wrapper {
            flex-direction: column;
        }

        .search-icon {
            position: relative;
            left: 0;
        }

        .search-input {
            padding: 16px 18px;
        }

        .btn-search {
            width: 100%;
            justify-content: center;
        }

        .search-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-clear {
            margin-left: 0;
            width: 100%;
            justify-content: center;
        }

        .table-card-header {
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

        .action-buttons {
            gap: 6px;
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

        .btn-action {
            width: 36px;
            height: 36px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alert
        const alert = document.getElementById('autoHideAlert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.6s ease';
                setTimeout(() => alert.remove(), 600);
            }, 5000);
        }

        // Delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const patientName = this.closest('tr').querySelector('.doctor-name').textContent;
                if (!confirm(`Yakin ingin menghapus pasien ${patientName}?\n\nData yang terhapus tidak dapat dikembalikan.`)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
