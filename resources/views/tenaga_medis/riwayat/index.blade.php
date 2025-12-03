@extends('layouts.tenaga_medis')
@section('title', 'Riwayat Pemeriksaan Pasien')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
@endpush

@section('content')

    {{-- Header Banner (100% SAMA DENGAN DASHBOARD) --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-file-medical-alt"></i>
                    <span id="greeting-time">Selamat Pagi</span>
                </div>
                <h1 class="page-title">Riwayat: {{ $pasien->name }} 📜</h1>
                <p class="page-subtitle">
                    <i class="fas fa-info-circle"></i>
                    Menampilkan arsip pemeriksaan untuk <strong>{{ $pasien->name }}</strong>
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <i class="fas fa-notes-medical"></i>
            </div>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="back-button-container">
        <a href="{{ route('tenaga-medis.pasien.index') }}" class="btn-back-to-list">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Pasien</span>
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-section">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter Pencarian</h2>
        </div>
        <div class="filter-bar">
            <form action="{{ route('tenaga-medis.riwayat.index') }}" method="GET" class="date-filter-form">
                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                
                {{-- Filter Tanggal --}}
                <div class="filter-group">
                    <i class="fas fa-calendar-alt filter-icon"></i>
                    <input type="text" name="tanggal" id="tanggalFilter" placeholder="Pilih Tanggal..."
                           value="{{ $request->tanggal ?? '' }}">
                </div>

                {{-- Filter Layanan --}}
                <div class="filter-group">
                    <i class="fas fa-stethoscope filter-icon"></i>
                    <select name="layanan_id" class="filter-select">
                        <option value="">Semua Layanan</option>
                        @foreach($layanans as $layanan)
                            <option value="{{ $layanan->id }}" 
                                    @selected($request->layanan_id == $layanan->id)>
                                {{ $layanan->nama_layanan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tenaga Medis --}}
                <div class="filter-group">
                    <i class="fas fa-user-md filter-icon"></i>
                    <select name="tenaga_medis_id" class="filter-select">
                        <option value="">Semua Tenaga Medis</option>
                        @foreach($tenagaMedisList as $tm)
                            <option value="{{ $tm->id }}" 
                                    @selected($request->tenaga_medis_id == $tm->id)>
                                {{ $tm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter-go">
                        <span>Cari</span>
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('tenaga-medis.riwayat.index', ['pasien_id' => $pasien->id]) }}" class="btn-filter-clear">
                        <span>Reset</span>
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Riwayat (100% SAMA DENGAN SCHEDULE SECTION) --}}
    <div class="schedule-section">
        <div class="section-header">
            <h2><i class="fas fa-clipboard-list"></i> Daftar Riwayat Pemeriksaan</h2>
            <span class="schedule-count">
                <i class="fas fa-check-circle"></i>
                {{ $riwayats->count() }} Riwayat
            </span>
        </div>
        <div class="riwayat-list-ringkas">
            @forelse ($riwayats as $index => $riwayat)
                <div class="riwayat-item-ringkas"
                    style="animation-delay: {{ $index * 0.1 }}s"
                    data-tanggal="{{ $riwayat->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}" 
                    data-dokter="{{ $riwayat->tenagaMedis->name ?? 'N/A' }}"
                    data-layanan="{{ $riwayat->pendaftaran->nama_layanan ?? 'Pemeriksaan' }}"
                    data-diagnosa="{{ $riwayat->assessment ?? '-' }}"
                    data-plan="{{ $riwayat->plan ?? '-' }}"
                    data-keluhan="{{ $riwayat->pendaftaran->keluhan ?? '-' }}"
                    data-lama_keluhan="{{ $riwayat->pendaftaran->lama_keluhan ?? '-' }}"
                    data-tekanan_darah="{{ $riwayat->pendaftaran->pemeriksaanAwal->tekanan_darah ?? '-' }}"
                    data-berat_badan="{{ $riwayat->pendaftaran->pemeriksaanAwal->berat_badan ?? '-' }}"
                    data-suhu_tubuh="{{ $riwayat->pendaftaran->pemeriksaanAwal->suhu_tubuh ?? '-' }}"
                    data-harga="{{ $riwayat->harga ? 'Rp ' . number_format($riwayat->harga, 0, ',', '.') : '-' }}"
                    data-resep_obat="{{ $riwayat->resep_obat ?? '-' }}"
                    data-pasien="{{ $riwayat->pendaftaran->user->name ?? ($riwayat->pendaftaran->nama_lengkap ?? 'Pasien Anonim') }}">
                    
                    <div class="riwayat-timeline-marker"></div>
                    
                    <div class="riwayat-content">
                        <div class="riwayat-main-info">
                            <div class="date-badge">
                                <i class="fas fa-calendar-day"></i>
                                <span>{{ $riwayat->created_at->isoFormat('D MMM YYYY') }}</span>
                            </div>
                            <div class="time-badge">
                                <i class="fas fa-clock"></i>
                                <span>{{ $riwayat->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="riwayat-detail-info">
                            <h3 class="layanan-title">
                                {{ $riwayat->pendaftaran->nama_layanan ?? 'Pemeriksaan' }}
                            </h3>
                            <div class="dokter-info">
                                <div class="dokter-avatar">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <span class="dokter-name">{{ $riwayat->tenagaMedis->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="riwayat-action">
                            <span class="btn-action-modern">
                                <span>Lihat Detail</span>
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-schedule">
                    <i class="fas fa-inbox"></i>
                    <p>Tidak Ada Riwayat Ditemukan</p>
                    <small>
                        @if($request->tanggal || $request->layanan_id || $request->tenaga_medis_id)
                            Tidak ada riwayat pemeriksaan yang sesuai dengan filter pencarian Anda.
                        @else
                            Belum ada riwayat pemeriksaan pasien yang selesai.
                        @endif
                    </small>
                </div>
            @endforelse
        </div>
    </div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-file-medical"></i>
                Detail Pemeriksaan
            </h2>
            <span class="close-btn" id="closeModalBtn">&times;</span>
        </div>
        
        <div class="modal-body">
            {{-- Info Header --}}
            <div class="modal-info-header">
                <div class="info-item">
                    <i class="fas fa-user-injured"></i>
                    <div class="info-content">
                        <span class="info-label">Nama Pasien</span>
                        <span class="info-value" id="modalPasien"></span>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-calendar-check"></i>
                    <div class="info-content">
                        <span class="info-label">Tanggal Pemeriksaan</span>
                        <span class="info-value" id="modalTanggal"></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-user-md"></i>
                    <div class="info-content">
                        <span class="info-label">Dokter Pemeriksa</span>
                        <span class="info-value" id="modalDokter"></span>
                    </div>
                </div>
            </div>

            {{-- Hasil Pemeriksaan --}}
            <div class="detail-section diagnosis-section">
                <div class="section-header">
                    <i class="fas fa-stethoscope"></i>
                    <h4 class="section-title">Hasil Pemeriksaan Dokter</h4>
                </div>
                <div class="detail-card">
                    <div class="detail-item">
                        <span class="label">
                            <i class="fas fa-notes-medical"></i>
                            Diagnosis (Assessment)
                        </span>
                        <div class="value diagnosis-value" id="modalDiagnosa"></div>
                    </div>
                    <div class="detail-item">
                        <span class="label">
                            <i class="fas fa-clipboard-list"></i>
                            Rencana/Catatan (Plan)
                        </span>
                        <div class="value" id="modalPlan"></div>
                    </div>
                    
                    <div class="detail-item" id="modalResepObatSection">
                        <span class="label">
                            <i class="fas fa-pills"></i>
                            Resep Obat
                        </span>
                        <div class="value" id="modalResepObat"></div>
                    </div>
                </div>
            </div>
            
            {{-- Keluhan Awal --}}
            <div class="detail-section">
                <div class="section-header">
                    <i class="fas fa-comment-medical"></i>
                    <h4 class="section-title">Keluhan Awal Pasien</h4>
                </div>
                <div class="detail-card">
                    <div class="detail-item">
                        <span class="label">
                            <i class="fas fa-heartbeat"></i>
                            Keluhan
                        </span>
                        <div class="value" id="modalKeluhan"></div>
                    </div>
                    <div class="detail-item">
                        <span class="label">
                            <i class="fas fa-hourglass-half"></i>
                            Sejak Kapan
                        </span>
                        <div class="value" id="modalLamaKeluhan"></div>
                    </div>
                </div>
            </div>

            {{-- Pemeriksaan Vital --}}
            <div class="detail-section" id="modalVitalsSection">
                <div class="section-header">
                    <i class="fas fa-thermometer-half"></i>
                    <h4 class="section-title">Pemeriksaan Vital</h4>
                </div>
                <div class="vitals-grid">
                    <div class="vital-card">
                        <div class="vital-icon blood-pressure">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="vital-info">
                            <span class="vital-label">Tekanan Darah</span>
                            <strong class="vital-value" id="modalTekananDarah"></strong>
                        </div>
                    </div>
                    <div class="vital-card">
                        <div class="vital-icon weight">
                            <i class="fas fa-weight"></i>
                        </div>
                        <div class="vital-info">
                            <span class="vital-label">Berat Badan</span>
                            <strong class="vital-value" id="modalBeratBadan"></strong>
                        </div>
                    </div>
                    <div class="vital-card">
                        <div class="vital-icon temperature">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="vital-info">
                            <span class="vital-label">Suhu Tubuh</span>
                            <strong class="vital-value" id="modalSuhuTubuh"></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Total Biaya --}}
            <div class="harga-section" id="modalHargaSection">
                <div class="harga-card">
                    <div class="harga-info">
                        <i class="fas fa-money-bill-wave"></i>
                        <span class="harga-label">Total Biaya Pemeriksaan</span>
                    </div>
                    <span class="harga-value" id="modalHarga"></span>
                </div>
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

    /* ===== TOMBOL KEMBALI ===== */
    .back-button-container {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-back-to-list {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        background: var(--bg-primary);
        color: var(--p1);
        border: 2px solid var(--border-color);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px var(--shadow-color);
    }

    .btn-back-to-list:hover {
        background: var(--grad);
        color: #fff;
        border-color: var(--p1);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
    }

    .btn-back-to-list i {
        transition: transform 0.3s ease;
    }

    .btn-back-to-list:hover i {
        transform: translateX(-4px);
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

    .date-filter-form {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 200px;
        background: var(--bg-secondary);
        padding: 14px 18px;
        border-radius: 14px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .filter-group:focus-within {
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    .filter-icon {
        color: var(--p1);
        font-size: 1.1rem;
    }

    .filter-group input[type="text"],
    .filter-select {
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 0.95rem;
        flex: 1;
        outline: none;
        color: var(--text-primary);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .btn-filter-go,
    .btn-filter-clear {
        padding: 14px 28px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }

    .btn-filter-go {
        background: var(--grad);
        color: white;
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-filter-go::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-filter-go:hover::before {
        left: 100%;
    }

    .btn-filter-go:hover {
        background: var(--grad-reverse);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
    }

    .btn-filter-clear {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 2px solid var(--border-color);
    }

    .btn-filter-clear:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--p1);
    }

    /* ===== SCHEDULE SECTION ===== */
    .schedule-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .riwayat-list-ringkas {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .riwayat-item-ringkas {
        display: flex;
        align-items: stretch;
        background: var(--bg-primary);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 8px 30px var(--shadow-color);
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        animation: fadeInLeft 0.5s ease forwards;
        opacity: 0;
    }
    
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .riwayat-item-ringkas::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--grad);
        transition: width 0.3s ease;
    }

    .riwayat-item-ringkas:hover {
        transform: translateX(8px);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .riwayat-item-ringkas:hover::before {
        width: 8px;
    }

    .riwayat-timeline-marker {
        width: 5px;
        background: transparent;
    }

    .riwayat-content {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 24px 28px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .riwayat-main-info {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 120px;
    }

    .date-badge,
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .date-badge {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(57, 166, 22, 0.2));
        color: var(--p1);
        border: 1px solid rgba(57, 166, 22, 0.2);
    }

    .time-badge {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .date-badge i,
    .time-badge i {
        font-size: 0.9rem;
    }

    .riwayat-detail-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 250px;
    }

    .layanan-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .dokter-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dokter-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.2);
    }

    .dokter-name {
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .riwayat-action {
        display: flex;
        align-items: center;
        margin-left: auto;
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

    .riwayat-item-ringkas:hover .btn-action-modern {
        background: var(--grad-reverse);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.45);
    }

    .btn-action-modern i {
        transition: transform 0.3s ease;
    }

    .riwayat-item-ringkas:hover .btn-action-modern i {
        transform: translateX(4px);
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
    .modal {
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
        animation: fadeIn 0.3s;
        padding: 20px;
    }

    .modal-content {
        background-color: var(--bg-primary);
        margin: 30px auto;
        border-radius: 24px;
        width: 90%;
        max-width: 900px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 28px 32px;
        background: var(--grad);
        border-radius: 24px 24px 0 0;
    }

    .modal-title {
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-title i {
        font-size: 1.3rem;
    }

    .close-btn {
        color: rgba(255, 255, 255, 0.8);
        font-size: 32px;
        font-weight: 300;
        cursor: pointer;
        transition: all 0.2s ease;
        line-height: 1;
        padding: 0;
        background: none;
        border: none;
    }

    .close-btn:hover {
        color: #fff;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 32px;
        overflow-y: auto;
        max-height: calc(100vh - 200px);
    }

    .modal-info-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px 20px;
        background: var(--bg-secondary);
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }

    .info-item i {
        font-size: 1.5rem;
        color: var(--p1);
    }

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    .detail-section {
        margin-bottom: 30px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .section-header i {
        font-size: 1.3rem;
        color: var(--p1);
    }

    .section-title {
        font-size: 1.15rem;
        color: var(--text-primary);
        margin: 0;
        font-weight: 700;
    }

    .detail-card {
        background: var(--bg-secondary);
        padding: 24px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .detail-item {
        margin-bottom: 20px;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-item .label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .detail-item .label i {
        color: var(--p1);
    }

    .detail-item .value {
        font-size: 1.05rem;
        color: var(--text-primary);
        line-height: 1.6;
        padding: 12px 16px;
        background: var(--bg-primary);
        border-radius: 10px;
        border: 1px solid var(--border-color);
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .diagnosis-value {
        font-weight: 600;
        color: var(--p1);
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(57, 166, 22, 0.1));
        border-color: rgba(57, 166, 22, 0.2);
    }

    .vitals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .vital-card {
        background: var(--bg-primary);
        padding: 20px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .vital-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px var(--shadow-color);
        border-color: rgba(57, 166, 22, 0.3);
    }

    .vital-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #fff;
    }

    .vital-icon.blood-pressure {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .vital-icon.weight {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .vital-icon.temperature {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .vital-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .vital-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .vital-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .harga-section {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid var(--border-color);
    }

    .harga-card {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(57, 166, 22, 0.1));
        padding: 24px 28px;
        border-radius: 16px;
        border: 2px solid rgba(57, 166, 22, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .harga-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .harga-info i {
        font-size: 1.5rem;
        color: var(--p1);
    }

    .harga-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .harga-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--p1);
        letter-spacing: -0.5px;
    }

    @keyframes slideDown {
        from { transform: translateY(-40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
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

        .filter-group {
            min-width: 100%;
        }

        .filter-actions {
            width: 100%;
        }

        .btn-filter-go,
        .btn-filter-clear {
            flex: 1;
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

        .riwayat-content {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            gap: 15px;
        }

        .riwayat-main-info {
            flex-direction: row;
            gap: 10px;
            width: 100%;
        }

        .riwayat-detail-info {
            min-width: 100%;
        }

        .riwayat-action {
            width: 100%;
            margin-left: 0;
        }

        .btn-action-modern {
            width: 100%;
            justify-content: center;
        }

        .modal-content {
            margin: 10px auto;
            width: 95%;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-info-header {
            grid-template-columns: 1fr;
        }

        .vitals-grid {
            grid-template-columns: 1fr;
        }

        .harga-card {
            flex-direction: column;
            gap: 15px;
            text-align: center;
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
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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
    
    // Filter Tanggal
    flatpickr("#tanggalFilter", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        locale: "id",
        defaultDate: "{{ $request->tanggal ?? '' }}",
        placeholder: "Pilih Tanggal..."
    });

    // Logika Modal
    const modal = document.getElementById('detailModal');
    const items = document.querySelectorAll('.riwayat-item-ringkas');
    const closeModalBtn = document.getElementById('closeModalBtn');

    items.forEach(item => {
        item.addEventListener('click', function() {
            const data = this.dataset;
            
            document.getElementById('modalTanggal').textContent = data.tanggal;
            document.getElementById('modalDokter').textContent = data.dokter;
            document.getElementById('modalPasien').textContent = data.pasien;
            document.getElementById('modalDiagnosa').textContent = data.diagnosa;
            document.getElementById('modalPlan').textContent = data.plan;
            document.getElementById('modalKeluhan').textContent = data.keluhan;
            document.getElementById('modalLamaKeluhan').textContent = data.lama_keluhan;

            const hasVitals = data.tekanan_darah !== '-' || data.berat_badan !== '-' || data.suhu_tubuh !== '-';
            
            if(!hasVitals) {
                document.getElementById('modalVitalsSection').style.display = 'none';
            } else {
                document.getElementById('modalVitalsSection').style.display = 'block';
                document.getElementById('modalTekananDarah').textContent = data.tekanan_darah;
                document.getElementById('modalBeratBadan').textContent = data.berat_badan;
                document.getElementById('modalSuhuTubuh').textContent = data.suhu_tubuh;
            }

            if(data.resep_obat === '-') {
                document.getElementById('modalResepObatSection').style.display = 'none';
            } else {
                document.getElementById('modalResepObatSection').style.display = 'block';
                document.getElementById('modalResepObat').textContent = data.resep_obat;
            }

            if(data.harga === '-') {
                document.getElementById('modalHargaSection').style.display = 'none';
            } else {
                document.getElementById('modalHargaSection').style.display = 'block';
                document.getElementById('modalHarga').textContent = data.harga;
            }
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    closeModalBtn.addEventListener('click', closeModal);

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
});
</script>
@endpush
