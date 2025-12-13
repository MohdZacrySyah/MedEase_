@extends('layouts.main')
@section('title', 'Riwayat Pemeriksaan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Halaman --}}
    <div class="page-header-container">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-file-medical-alt"></i>
            </div>
            <div class="header-text">
                <h1 class="page-title">Riwayat Pemeriksaan</h1>
                <p class="page-subtitle">Semua catatan pemeriksaan Anda tersimpan dengan aman</p>
            </div>
            <div class="hero-decoration">
                <div class="pulse-ring pulse-1"></div>
                <div class="pulse-ring pulse-2"></div>
                <div class="pulse-ring pulse-3"></div>
                <i class="fas fa-notes-medical"></i>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form action="{{ route('riwayat.index') }}" method="GET" class="date-filter-form">
            <div class="filter-group">
                <i class="fas fa-calendar-alt filter-icon"></i>
                <input type="text" name="tanggal" id="tanggalFilter" placeholder="Pilih Tanggal..." value="{{ $tanggal ?? '' }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter-go">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('riwayat.index') }}" class="btn-filter-clear">
                    <i class="fas fa-redo"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Riwayat --}}
    {{-- ID "riwayat-container" ditambahkan untuk Auto Refresh --}}
    <div class="riwayat-list-ringkas" id="riwayat-container">
        @forelse ($riwayats as $index => $riwayat)
            <div class="riwayat-item-ringkas" 
                 {{-- Animasi dihapus --}}
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
                 data-catatan_apoteker="{{ $riwayat->resep?->catatan_apoteker ?? '-' }}">
                
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
                        <h3 class="layanan-title">{{ $riwayat->pendaftaran->nama_layanan ?? 'Pemeriksaan' }}</h3>
                        <div class="dokter-info">
                            <div class="dokter-avatar">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="dokter-name">{{ $riwayat->tenagaMedis->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="riwayat-action">
                        <span class="btn-lihat-detail">
                            Lihat Detail
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-riwayat-info">
                <div class="no-data-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Belum Ada Riwayat</h3>
                <p>
                    @if($tanggal ?? null)
                        Tidak ada riwayat pemeriksaan ditemukan untuk tanggal 
                        <strong>{{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM YYYY') }}</strong>
                    @else
                        Anda belum memiliki riwayat pemeriksaan
                    @endif
                </p>
            </div>
        @endforelse
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
            
            {{-- Catatan Apoteker --}}
            <div class="detail-section" id="modalCatatanApotekerSection" style="display: none;">
                <div class="section-header">
                    <i class="fas fa-prescription-bottle-alt" style="color: var(--p1);"></i>
                    <h4 class="section-title">Catatan dari Apoteker</h4>
                </div>
                <div class="detail-card apoteker-card">
                    <div class="detail-item">
                        <span class="label">
                            <i class="fas fa-file-prescription" style="color: var(--p1);"></i>
                            Catatan Pengambilan Obat
                        </span>
                        <div class="value" id="modalCatatanApoteker"></div>
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
    * { box-sizing: border-box; }
    
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

    .riwayat-container { 
        max-width: 1000px; 
        margin: 0 auto; 
        padding: 40px 20px; 
    }

    /* ===== HEADER PREMIUM (NO ANIMATION) ===== */
    .page-header-container { 
        margin-bottom: 35px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 24px;
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

    .page-title { 
        color: #fff;
        font-weight: 800; 
        font-size: 2.2rem; 
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle { 
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.05rem;
        margin: 0;
        font-weight: 500;
    }
    
    .hero-decoration {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
        z-index: 1;
    }
    
    .hero-decoration > i {
        font-size: 45px;
        color: white;
        z-index: 2;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    
    .pulse-ring {
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

    /* ===== FILTER BAR PREMIUM (NO ANIMATION) ===== */
    .filter-bar { 
        background: var(--bg-primary);
        padding: 24px 28px;
        border-radius: 18px;
        border: 1px solid rgba(57, 166, 22, 0.15);
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        margin-bottom: 35px;
    }

    .date-filter-form { 
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 260px;
        background: var(--bg-secondary);
        padding: 14px 18px;
        border-radius: 14px;
        border: 2px solid rgba(57, 166, 22, 0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-group:focus-within {
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 5px rgba(57, 166, 22, 0.1);
        transform: translateY(-2px);
    }

    .filter-icon {
        color: var(--p1);
        font-size: 1.15rem;
    }

    .filter-group input[type="text"] { 
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 1rem;
        flex: 1;
        outline: none;
        color: var(--text-primary);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
    }

    .btn-filter-go,
    .btn-filter-clear { 
        padding: 14px 26px;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-filter-go {
        background: var(--grad);
        color: white;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
    }
    
    .btn-filter-go::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-filter-go:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-filter-go:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-filter-go i,
    .btn-filter-go span {
        position: relative;
        z-index: 1;
    }

    .btn-filter-clear {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 2px solid rgba(57, 166, 22, 0.2);
    }

    .btn-filter-clear:hover {
        background: var(--p1);
        color: white;
        border-color: var(--p1);
        transform: translateY(-2px);
    }

    /* ===== RIWAYAT LIST PREMIUM (NO ANIMATION) ===== */
    .riwayat-list-ringkas {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .riwayat-item-ringkas {
        display: flex;
        align-items: stretch;
        background: var(--bg-primary);
        border-radius: 20px;
        border: 1px solid rgba(57, 166, 22, 0.15);
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .riwayat-item-ringkas::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: var(--grad);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 15px rgba(57, 166, 22, 0.5);
    }

    .riwayat-item-ringkas:hover {
        transform: translateX(10px); 
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .riwayat-item-ringkas:hover::before {
        width: 10px;
    }

    .riwayat-timeline-marker {
        width: 6px;
        background: transparent;
    }

    .riwayat-content {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 28px 32px;
        gap: 24px;
    }

    .riwayat-main-info {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 140px;
    }

    .date-badge,
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .date-badge {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        color: var(--p3);
        border: 1px solid rgba(57, 166, 22, 0.3);
    }

    .time-badge {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid rgba(57, 166, 22, 0.1);
    } 

    .date-badge i,
    .time-badge i {
        font-size: 0.95rem;
    }

    .riwayat-detail-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .layanan-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .dokter-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .dokter-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--p1);
        font-size: 18px;
    }

    .dokter-name {
        font-size: 1rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .riwayat-action {
        display: flex;
        align-items: center;
    }

    .btn-lihat-detail {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 24px;
        background: var(--grad);
        color: #fff;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-lihat-detail::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-lihat-detail:hover::before {
        width: 300px;
        height: 300px;
    }

    .riwayat-item-ringkas:hover .btn-lihat-detail {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }

    .btn-lihat-detail i {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }
    
    .btn-lihat-detail span {
        position: relative;
        z-index: 1;
    }

    .riwayat-item-ringkas:hover .btn-lihat-detail i {
        transform: translateX(6px);
    }

    /* No Data */
    .no-riwayat-info { 
        text-align: center;
        padding: 80px 40px;
        background: var(--bg-primary);
        border-radius: 24px;
        border: 2px dashed rgba(57, 166, 22, 0.2);
    }

    .no-data-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(57, 166, 22, 0.2));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: float 3s ease-in-out infinite;
    }

    .no-data-icon i {
        font-size: 3.5rem;
        color: var(--p1);
        opacity: 0.6;
    }

    .no-riwayat-info h3 {
        font-size: 1.6rem;
        color: var(--text-primary);
        margin: 0 0 12px 0;
        font-weight: 700;
    }

    .no-riwayat-info p {
        font-size: 1.05rem;
        color: var(--text-secondary);
        margin: 0;
    }
    /* ===== MODAL PREMIUM (NO ANIMATION) ===== */
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
        -webkit-backdrop-filter: blur(8px);
        padding: 20px;
    }

    .modal-content {
        background-color: var(--bg-primary);
        margin: 30px auto;
        border-radius: 24px;
        width: 90%;
        max-width: 920px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        max-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 32px 36px;
        background: var(--grad);
        border-radius: 24px 24px 0 0;
    }

    .modal-title {
        color: #fff;
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .modal-title i {
        font-size: 1.4rem;
    }

    .close-btn {
        color: rgba(255, 255, 255, 0.8);
        font-size: 36px;
        font-weight: 300;
        cursor: pointer;
        transition: all 0.3s ease;
        line-height: 1;
        padding: 0;
        background: none;
        border: none;
    }

    .close-btn:hover {
        color: #fff;
        transform: rotate(90deg) scale(1.1);
    }

    .modal-body {
        padding: 36px;
        overflow-y: auto;
        max-height: calc(100vh - 200px);
    }
    
    .modal-body::-webkit-scrollbar { width: 8px; }
    .modal-body::-webkit-scrollbar-track { background: var(--bg-secondary); border-radius: 10px; }
    .modal-body::-webkit-scrollbar-thumb { background: var(--grad); border-radius: 10px; }

    /* Modal Info Header */
    .modal-info-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        background: var(--bg-secondary);
        border-radius: 14px;
        border: 1px solid rgba(57, 166, 22, 0.15);
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        border-color: rgba(57, 166, 22, 0.3);
        transform: translateY(-2px);
    }

    .info-item i {
        font-size: 1.6rem;
        color: var(--p1);
    }

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.05rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Detail Sections */
    .detail-section {
        margin-bottom: 32px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .section-header i {
        font-size: 1.4rem;
        color: var(--p1);
    }

    .section-title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
        font-weight: 700;
    }

    .detail-card {
        background: var(--bg-secondary);
        padding: 26px;
        border-radius: 18px;
        border: 1px solid rgba(57, 166, 22, 0.15);
    }
    
    .apoteker-card {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(57, 166, 22, 0.1));
        border-color: rgba(57, 166, 22, 0.25);
    }

    .detail-item {
        margin-bottom: 22px;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-item .label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin-bottom: 10px;
        font-weight: 600;
    }

    .detail-item .label i {
        color: var(--p1);
    }

    .detail-item .value {
        font-size: 1.05rem;
        color: var(--text-primary);
        line-height: 1.7;
        padding: 14px 18px;
        background: var(--bg-primary);
        border-radius: 10px;
        border: 1px solid rgba(57, 166, 22, 0.1);
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .diagnosis-value {
        font-weight: 600;
        color: var(--p3);
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(57, 166, 22, 0.15));
        border-color: rgba(57, 166, 22, 0.3);
    }

    /* Vitals Grid */
    .vitals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 18px;
    }

    .vital-card {
        background: var(--bg-primary);
        padding: 22px;
        border-radius: 14px;
        border: 1px solid rgba(57, 166, 22, 0.15);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .vital-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(57, 166, 22, 0.15);
        border-color: rgba(57, 166, 22, 0.3);
    }

    .vital-icon {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        flex-shrink: 0;
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
        gap: 5px;
    }

    .vital-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .vital-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Harga Section */
    .harga-section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 2px solid rgba(57, 166, 22, 0.15);
    }

    .harga-card {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(57, 166, 22, 0.15));
        padding: 26px 32px;
        border-radius: 18px;
        border: 2px solid rgba(57, 166, 22, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .harga-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .harga-info i {
        font-size: 1.6rem;
        color: var(--p1);
    }

    .harga-label {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-primary); 
    }

    .harga-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--p1);
        letter-spacing: -0.5px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            padding: 30px 24px;
        }

        .page-title {
            font-size: 1.8rem;
        }
        
        .hero-decoration {
            width: 90px;
            height: 90px;
        }
        
        .hero-decoration > i {
            font-size: 40px;
        }

        .date-filter-form {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-actions {
            width: 100%;
            justify-content: space-between;
        }

        .btn-filter-go,
        .btn-filter-clear {
            flex: 1;
        }

        .riwayat-content {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }

        .riwayat-main-info {
            flex-direction: row;
            gap: 10px;
            width: 100%;
        }

        .riwayat-action {
            width: 100%;
        }

        .btn-lihat-detail {
            width: 100%;
            justify-content: center;
        }

        .modal-content {
            margin: 15px auto;
            width: 95%;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-info-header {
            grid-template-columns: 1fr;
        }

        .vitals-grid {
            grid-template-columns: 1fr;
        }

        .harga-card {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .layanan-title {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // Filter Tanggal
    flatpickr("#tanggalFilter", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        locale: "id",
        defaultDate: "{{ $tanggal ?? '' }}",
        placeholder: "Pilih Tanggal..."
    });

    const modal = document.getElementById('detailModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // --- AUTO REFRESH SYSTEM ---
    if (typeof window.initAutoRefresh === 'function') {
        window.initAutoRefresh(['#riwayat-container']);
    }

    // --- REBIND EVENTS ---
    window.rebindEvents = function() {
        bindCardEvents();
        console.log('♻️ Riwayat list refreshed & events rebound!');
    };

    function bindCardEvents() {
        const items = document.querySelectorAll('.riwayat-item-ringkas');
        items.forEach(item => {
            // cleanup old
            item.removeEventListener('click', handleCardClick);
            // bind new
            item.addEventListener('click', handleCardClick);
        });
    }

    // Extracted handler
    function handleCardClick() {
        const data = this.dataset;
        
        document.getElementById('modalTanggal').textContent = data.tanggal;
        document.getElementById('modalDokter').textContent = data.dokter;
        document.getElementById('modalDiagnosa').textContent = data.diagnosa;
        document.getElementById('modalPlan').textContent = data.plan;
        document.getElementById('modalKeluhan').textContent = data.keluhan;
        document.getElementById('modalLamaKeluhan').textContent = data.lama_keluhan;

        // Vitals check
        const hasVitals = data.tekanan_darah !== '-' || data.berat_badan !== '-' || data.suhu_tubuh !== '-';
        if(!hasVitals) {
            document.getElementById('modalVitalsSection').style.display = 'none';
        } else {
            document.getElementById('modalVitalsSection').style.display = 'block';
            document.getElementById('modalTekananDarah').textContent = data.tekanan_darah;
            document.getElementById('modalBeratBadan').textContent = data.berat_badan;
            document.getElementById('modalSuhuTubuh').textContent = data.suhu_tubuh;
        }

        // Resep check
        const resepObatSection = document.getElementById('modalResepObatSection');
        if(data.resep_obat === '-') {
            resepObatSection.style.display = 'none';
        } else {
            resepObatSection.style.display = 'block';
            document.getElementById('modalResepObat').textContent = data.resep_obat;
        }

        // Apoteker check
        const catatanApotekerSection = document.getElementById('modalCatatanApotekerSection');
        const catatanApotekerValue = document.getElementById('modalCatatanApoteker');

        if (data.catatan_apoteker && data.catatan_apoteker !== '-') {
            catatanApotekerValue.textContent = data.catatan_apoteker;
            catatanApotekerSection.style.display = 'block';
        } else {
            catatanApotekerValue.textContent = '';
            catatanApotekerSection.style.display = 'none';
        }

        // Harga check
        if(data.harga === '-') {
            document.getElementById('modalHargaSection').style.display = 'none';
        } else {
            document.getElementById('modalHargaSection').style.display = 'block';
            document.getElementById('modalHarga').textContent = data.harga;
        }
        
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    // Initial bind
    bindCardEvents();

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

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