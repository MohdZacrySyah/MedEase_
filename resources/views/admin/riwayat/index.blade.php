@extends('layouts.admin')
@section('title', 'Riwayat Pasien - '. $user->name)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Halaman --}}
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-user-injured"></i>
                    <span>Patient History</span>
                </div>
                <h1 class="page-title">Riwayat: {{ $user->name}} 📋</h1>
                <p class="page-subtitle">
                    <i class="far fa-folder-open"></i>
                    Arsip pemeriksaan lengkap pasien
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <div class="time-widget">
                    <i class="fas fa-file-medical-alt"></i>
                    <span>History</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="back-button-container">
        <a href="{{ route('admin.keloladatapasien') }}" class="btn-back-to-list">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Kelola Pasien</span>
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-card-modern">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter Riwayat</h2>
        </div>
        <form action="{{ route('admin.pasien.riwayat', $user->name) }}" method="GET" class="filter-form-grid">
            
            {{-- Filter Tanggal --}}
            <div class="filter-input-group">
                <label class="filter-label">
                    <i class="fas fa-calendar-alt"></i>
                    Tanggal Pemeriksaan
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-calendar-day"></i>
                    <input type="text" name="tanggal" id="tanggalFilter" 
                           placeholder="Pilih Tanggal..." 
                           value="{{ $request->tanggal ?? '' }}">
                </div>
            </div>

            <!-- {{-- Filter Layanan --}}
            <div class="filter-input-group">
                <label class="filter-label">
                    <i class="fas fa-stethoscope"></i>
                    Layanan
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-briefcase-medical"></i>
                    <select name="layanan_id" class="form-control">
                        <option value="">Semua Layanan</option>
                        @foreach($layanans as $layanan)
                            <option value="{{ $layanan->id }}" 
                                    @selected($request->layanan_id == $layanan->id)>
                                {{ $layanan->nama_layanan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div> -->

            {{-- Filter Tenaga Medis --}}
            <div class="filter-input-group">
                <label class="filter-label">
                    <i class="fas fa-user-md"></i>
                    Tenaga Medis
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-user-doctor"></i>
                    <select name="tenaga_medis_id" class="form-control">
                        <option value="">Semua Tenaga Medis</option>
                        @foreach($tenagaMedisList as $tm)
                            <option value="{{ $tm->id }}" 
                                    @selected($request->tenaga_medis_id == $tm->id)>
                                {{ $tm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-button-group">
                <button type="submit" class="btn-filter-modern btn-primary-filter">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.pasien.riwayat', $user->name) }}" class="btn-filter-modern btn-secondary-filter">
                    <i class="fas fa-redo"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Riwayat --}}
    <div class="riwayat-list-modern">
        @forelse ($riwayats as $index => $riwayat)
            <div class="riwayat-card-modern"
                 style="animation-delay: {{ $index * 0.05 }}s"
                 {{-- Data untuk Modal --}}
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
                 data-catatan_apoteker="{{ $riwayat->resep?->catatan_apoteker ?? '-' }}"
                 data-pasien="{{ $pasien->name }}">
                
                <div class="riwayat-timeline-bar"></div>
                
                <div class="riwayat-content-wrapper">
                    <div class="riwayat-header-info">
                        <div class="date-time-group">
                            <span class="date-badge-modern">
                                <i class="fas fa-calendar-day"></i>
                                {{ $riwayat->created_at->isoFormat('D MMM YYYY') }}
                            </span>
                            <span class="time-badge-modern">
                                <i class="fas fa-clock"></i>
                                {{ $riwayat->created_at->format('H:i') }} WIB
                            </span>
                        </div>
                    </div>
                    
                    <div class="riwayat-main-content">
                        <h3 class="layanan-title-modern">
                            <i class="fas fa-stethoscope"></i>
                            {{ $riwayat->pendaftaran->nama_layanan ?? 'Pemeriksaan' }}
                        </h3>
                        <div class="doctor-info-modern">
                            <div class="doctor-avatar-small">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="doctor-name-text">{{ $riwayat->tenagaMedis->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="riwayat-action-area">
                        <button class="btn-view-detail">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-schedule">
                <i class="fas fa-inbox"></i>
                <p>
                    @if($request->tanggal || $request->layanan_id || $request->tenaga_medis_id)
                        Tidak ada riwayat yang sesuai dengan filter
                    @else
                        Pasien ini belum memiliki riwayat pemeriksaan
                    @endif
                </p>
                @if($request->tanggal || $request->layanan_id || $request->tenaga_medis_id)
                    <small>Coba ubah filter pencarian Anda</small>
                @endif
            </div>
        @endforelse
    </div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <button class="close-modal" id="closeModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-file-medical"></i>
                    <span>Detail Pemeriksaan</span>
                </h2>
            </div>
            
            {{-- Info Header --}}
            <div class="modal-info-grid">
                <div class="info-card-modern">
                    <i class="fas fa-user-injured"></i>
                    <div class="info-content">
                        <span class="info-label">Nama Pasien</span>
                        <span class="info-value" id="modalPasien"></span>
                    </div>
                </div>

                <div class="info-card-modern">
                    <i class="fas fa-calendar-check"></i>
                    <div class="info-content">
                        <span class="info-label">Tanggal Pemeriksaan</span>
                        <span class="info-value" id="modalTanggal"></span>
                    </div>
                </div>

                <div class="info-card-modern">
                    <i class="fas fa-user-md"></i>
                    <div class="info-content">
                        <span class="info-label">Dokter Pemeriksa</span>
                        <span class="info-value" id="modalDokter"></span>
                    </div>
                </div>
            </div>

            {{-- Hasil Pemeriksaan --}}
            <div class="detail-section-modern">
                <div class="section-header-modal">
                    <i class="fas fa-stethoscope"></i>
                    <h4>Hasil Pemeriksaan Dokter</h4>
                </div>
                <div class="detail-card-modal">
                    <div class="detail-item-modal">
                        <span class="label-modal">
                            <i class="fas fa-notes-medical"></i>
                            Diagnosis (Assessment)
                        </span>
                        <div class="value-modal diagnosis-highlight" id="modalDiagnosa"></div>
                    </div>
                    <div class="detail-item-modal">
                        <span class="label-modal">
                            <i class="fas fa-clipboard-list"></i>
                            Rencana/Catatan (Plan)
                        </span>
                        <div class="value-modal" id="modalPlan"></div>
                    </div>
                    
                    <div class="detail-item-modal" id="modalResepObatSection">
                        <span class="label-modal">
                            <i class="fas fa-pills"></i>
                            Resep Obat
                        </span>
                        <div class="value-modal" id="modalResepObat"></div>
                    </div>
                </div>
            </div>

            {{-- Catatan Apoteker --}}
            <div class="detail-section-modern" id="modalCatatanApotekerSection" style="display: none;">
                <div class="section-header-modal">
                    <i class="fas fa-prescription-bottle-alt" style="color: #17a2b8;"></i>
                    <h4>Catatan dari Apoteker</h4>
                </div>
                <div class="detail-card-modal apoteker-card">
                    <div class="detail-item-modal">
                        <span class="label-modal">
                            <i class="fas fa-file-prescription" style="color: #17a2b8;"></i>
                            Catatan Pengambilan Obat
                        </span>
                        <div class="value-modal" id="modalCatatanApoteker"></div>
                    </div>
                </div>
            </div>
            
            {{-- Keluhan Awal --}}
            <div class="detail-section-modern">
                <div class="section-header-modal">
                    <i class="fas fa-comment-medical"></i>
                    <h4>Keluhan Awal Pasien</h4>
                </div>
                <div class="detail-card-modal">
                    <div class="detail-item-modal">
                        <span class="label-modal">
                            <i class="fas fa-heartbeat"></i>
                            Keluhan
                        </span>
                        <div class="value-modal" id="modalKeluhan"></div>
                    </div>
                    <div class="detail-item-modal">
                        <span class="label-modal">
                            <i class="fas fa-hourglass-half"></i>
                            Sejak Kapan
                        </span>
                        <div class="value-modal" id="modalLamaKeluhan"></div>
                    </div>
                </div>
            </div>

            {{-- Pemeriksaan Vital --}}
            <div class="detail-section-modern" id="modalVitalsSection">
                <div class="section-header-modal">
                    <i class="fas fa-thermometer-half"></i>
                    <h4>Pemeriksaan Vital</h4>
                </div>
                <div class="vitals-grid-modal">
                    <div class="vital-card-modal blood-pressure">
                        <div class="vital-icon-modal">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="vital-info-modal">
                            <span class="vital-label-modal">Tekanan Darah</span>
                            <strong class="vital-value-modal" id="modalTekananDarah"></strong>
                        </div>
                    </div>
                    <div class="vital-card-modal weight">
                        <div class="vital-icon-modal">
                            <i class="fas fa-weight"></i>
                        </div>
                        <div class="vital-info-modal">
                            <span class="vital-label-modal">Berat Badan</span>
                            <strong class="vital-value-modal" id="modalBeratBadan"></strong>
                        </div>
                    </div>
                    <div class="vital-card-modal temperature">
                        <div class="vital-icon-modal">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="vital-info-modal">
                            <span class="vital-label-modal">Suhu Tubuh</span>
                            <strong class="vital-value-modal" id="modalSuhuTubuh"></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Total Biaya --}}
            <div class="harga-section-modal" id="modalHargaSection">
                <div class="harga-card-modal">
                    <div class="harga-info-modal">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Total Biaya Pemeriksaan</span>
                    </div>
                    <span class="harga-value-modal" id="modalHarga"></span>
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

    /* ===== HEADER BANNER (SAMA DENGAN DASHBOARD) ===== */
    .page-header-banner {
        margin-bottom: 30px;
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

    /* ===== TOMBOL KEMBALI ===== */
    .back-button-container {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }

    .btn-back-to-list {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.95rem;
        background: var(--bg-primary);
        color: var(--p1);
        border: 2px solid var(--border-color);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px var(--shadow-color);
        position: relative;
        overflow: hidden;
    }

    .btn-back-to-list::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(57, 166, 22, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .btn-back-to-list:hover::before {
        left: 100%;
    }

    .btn-back-to-list:hover {
        background: var(--p1);
        color: white;
        border-color: var(--p1);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
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

    /* ===== FILTER CARD ===== */
    .filter-card-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        margin-bottom: 30px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .filter-card-modern:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .filter-input-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
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

    .input-with-icon input,
    .input-with-icon select {
        width: 100%;
        padding: 14px 18px 14px 50px;
        border: 2px solid var(--border-color);
        border-radius: 14px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .input-with-icon input:focus,
    .input-with-icon select:focus {
        outline: none;
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    .form-control {
        width: 100%;
        padding: 14px 18px 14px 50px;
        border: 2px solid var(--border-color);
        border-radius: 14px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-weight: 500;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }

    .filter-button-group {
        display: flex;
        gap: 12px;
    }

    .btn-filter-modern {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        border: none;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.4);
    }

    .btn-secondary-filter {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        border: 2px solid var(--border-color);
    }

    .btn-secondary-filter:hover {
        background: var(--text-muted);
        color: white;
        border-color: var(--text-muted);
        transform: translateY(-3px);
    }

    /* ===== RIWAYAT LIST ===== */
    .riwayat-list-modern {
        display: flex;
        flex-direction: column;
        gap: 20px;
        animation: fadeInUp 0.6s ease-out 0.3s backwards;
    }

    .riwayat-card-modern {
        display: flex;
        background: var(--bg-primary);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px var(--shadow-color);
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

    .riwayat-card-modern::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--grad);
        transition: width 0.3s ease;
    }

    .riwayat-card-modern:hover {
        transform: translateX(8px);
        box-shadow: 0 12px 40px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .riwayat-card-modern:hover::before {
        width: 8px;
    }

    .riwayat-timeline-bar {
        width: 5px;
        background: transparent;
    }

    .riwayat-content-wrapper {
        flex: 1;
        padding: 24px 28px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .riwayat-header-info {
        min-width: 130px;
    }

    .date-time-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .date-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.1), rgba(142, 68, 173, 0.15));
        color: #7b1fa2;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(155, 89, 182, 0.2);
    }

    [data-theme="dark"] .date-badge-modern,
    .dark-mode .date-badge-modern {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.2), rgba(142, 68, 173, 0.25));
        color: #c084fc;
        border-color: rgba(155, 89, 182, 0.3);
    }

    .time-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .riwayat-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .layanan-title-modern {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .layanan-title-modern i {
        color: var(--p1);
    }

    .doctor-info-modern {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .doctor-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(28, 194, 0, 0.2));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--p1);
        font-size: 16px;
    }

    .doctor-name-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .riwayat-action-area {
        margin-left: auto;
    }

    .btn-view-detail {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: var(--grad);
        color: white;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.25);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn-view-detail::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-view-detail:hover::before {
        left: 100%;
    }

    .riwayat-card-modern:hover .btn-view-detail {
        background: var(--grad-reverse);
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.4);
    }

    .btn-view-detail i {
        transition: transform 0.3s ease;
    }

    .riwayat-card-modern:hover .btn-view-detail i {
        transform: translateX(4px);
    }

    /* Empty State */
    .empty-schedule {
        text-align: center;
        padding: 70px 20px;
        background: var(--bg-primary);
        border-radius: 24px;
        border: 2px dashed var(--border-color);
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
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s;
        padding: 20px;
    }

    .modal-card {
        background-color: var(--modal-bg);
        margin: 30px auto;
        border-radius: 24px;
        width: 90%;
        max-width: 900px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    @keyframes slideDown {
        from { transform: translateY(-40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .close-modal {
        position: absolute;
        top: -15px;
        right: -15px;
        width: 48px;
        height: 48px;
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-modal i {
        font-size: 1.2rem;
        color: var(--text-muted);
        transition: color 0.3s ease;
    }

    .close-modal:hover {
        background: #ef4444;
        border-color: #ef4444;
        transform: rotate(90deg);
    }

    .close-modal:hover i {
        color: #fff;
    }

    .modal-content {
        padding: 40px;
        overflow-y: auto;
        max-height: calc(100vh - 140px);
    }

    .modal-header {
        text-align: center;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border-color);
    }

    .modal-title {
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        font-size: 1.8rem;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .modal-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card-modern {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: var(--bg-secondary);
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }

    .info-card-modern i {
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

    .detail-section-modern {
        margin-bottom: 30px;
    }

    .section-header-modal {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .section-header-modal i {
        font-size: 1.3rem;
        color: var(--p1);
    }

    .section-header-modal h4 {
        font-size: 1.15rem;
        color: var(--text-primary);
        margin: 0;
        font-weight: 700;
    }

    .detail-card-modal {
        background: var(--bg-secondary);
        padding: 24px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .apoteker-card {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.05), rgba(23, 162, 184, 0.1));
        border-color: rgba(23, 162, 184, 0.2);
    }

    .detail-item-modal {
        margin-bottom: 20px;
    }

    .detail-item-modal:last-child {
        margin-bottom: 0;
    }

    .label-modal {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .label-modal i {
        color: var(--p1);
    }

    .value-modal {
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

    .diagnosis-highlight {
        font-weight: 600;
        color: var(--p1);
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(28, 194, 0, 0.1));
        border-color: rgba(57, 166, 22, 0.2);
    }

    .vitals-grid-modal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .vital-card-modal {
        background: var(--bg-primary);
        padding: 20px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .vital-card-modal:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.15);
    }

    .vital-icon-modal {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
    }

    .vital-card-modal.blood-pressure .vital-icon-modal {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .vital-card-modal.weight .vital-icon-modal {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .vital-card-modal.temperature .vital-icon-modal {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .vital-info-modal {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .vital-label-modal {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .vital-value-modal {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .harga-section-modal {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid var(--border-color);
    }

    .harga-card-modal {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(28, 194, 0, 0.1));
        padding: 24px 28px;
        border-radius: 16px;
        border: 2px solid rgba(57, 166, 22, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .harga-info-modal {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .harga-info-modal i {
        font-size: 1.5rem;
        color: var(--p1);
    }

    .harga-info-modal span {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .harga-value-modal {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--p1);
        letter-spacing: -0.5px;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-illustration {
            display: none;
        }

        .filter-form-grid {
            grid-template-columns: 1fr;
        }

        .filter-button-group {
            width: 100%;
        }

        .btn-filter-modern {
            flex: 1;
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

        .riwayat-content-wrapper {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            gap: 15px;
        }

        .date-time-group {
            flex-direction: row;
            width: 100%;
        }

        .riwayat-action-area {
            width: 100%;
            margin-left: 0;
        }

        .btn-view-detail {
            width: 100%;
            justify-content: center;
        }

        .modal-card {
            margin: 10px;
            width: 95%;
        }

        .modal-content {
            padding: 24px;
        }

        .modal-info-grid {
            grid-template-columns: 1fr;
        }

        .vitals-grid-modal {
            grid-template-columns: 1fr;
        }

        .harga-card-modal {
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
    
    // Filter Tanggal dengan Flatpickr
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
    const items = document.querySelectorAll('.riwayat-card-modern');
    const closeModalBtn = document.getElementById('closeModalBtn');

    items.forEach(item => {
        item.addEventListener('click', function() {
            const data = this.dataset;
            
            // Isi konten modal
            document.getElementById('modalTanggal').textContent = data.tanggal;
            document.getElementById('modalDokter').textContent = data.dokter;
            document.getElementById('modalPasien').textContent = data.pasien;
            document.getElementById('modalDiagnosa').textContent = data.diagnosa;
            document.getElementById('modalPlan').textContent = data.plan;
            document.getElementById('modalKeluhan').textContent = data.keluhan;
            document.getElementById('modalLamaKeluhan').textContent = data.lama_keluhan;

            // Cek data vitals
            const hasVitals = data.tekanan_darah !== '-' || data.berat_badan !== '-' || data.suhu_tubuh !== '-';
            
            if(!hasVitals) {
                document.getElementById('modalVitalsSection').style.display = 'none';
            } else {
                document.getElementById('modalVitalsSection').style.display = 'block';
                document.getElementById('modalTekananDarah').textContent = data.tekanan_darah;
                document.getElementById('modalBeratBadan').textContent = data.berat_badan;
                document.getElementById('modalSuhuTubuh').textContent = data.suhu_tubuh;
            }

            // Cek Resep Obat
            if(data.resep_obat === '-') {
                document.getElementById('modalResepObatSection').style.display = 'none';
            } else {
                document.getElementById('modalResepObatSection').style.display = 'block';
                document.getElementById('modalResepObat').textContent = data.resep_obat;
            }

            // Logika Catatan Apoteker
            const catatanApotekerSection = document.getElementById('modalCatatanApotekerSection');
            const catatanApotekerValue = document.getElementById('modalCatatanApoteker');

            if (data.catatan_apoteker && data.catatan_apoteker !== '-') {
                catatanApotekerValue.textContent = data.catatan_apoteker;
                catatanApotekerSection.style.display = 'block';
            } else {
                catatanApotekerValue.textContent = '';
                catatanApotekerSection.style.display = 'none';
            }

            // Cek data harga
            if(data.harga === '-') {
                document.getElementById('modalHargaSection').style.display = 'none';
            } else {
                document.getElementById('modalHargaSection').style.display = 'block';
                document.getElementById('modalHarga').textContent = data.harga;
            }
            
            // Tampilkan modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    // Fungsi tutup modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    closeModalBtn.addEventListener('click', closeModal);

    // Tutup modal jika klik di luar area konten
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            closeModal();
        }
    });

    // Tutup dengan tombol ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });
});
</script>
@endpush
