@extends('layouts.main')
@section('title', 'Notifikasi Jadwal')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Halaman --}}
    <div class="page-header-container">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="header-text">
                <h1 class="page-title">Jadwal Kunjungan</h1>
                <p class="page-subtitle">Jadwal konsultasi Anda yang akan datang</p>
            </div>
            <div class="hero-decoration">
                <div class="pulse-ring pulse-1"></div>
                <div class="pulse-ring pulse-2"></div>
                <div class="pulse-ring pulse-3"></div>
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>

    {{-- Daftar Notifikasi --}}
    <div class="notifikasi-list-ringkas">
        @forelse ($pendaftarans as $index => $pendaftaran)
            <div class="notifikasi-item-ringkas" style="animation-delay: {{ $index * 0.1 }}s"
                 data-tanggal="{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('dddd, D MMMM YYYY') }}"
                 data-antrian="{{ $pendaftaran->no_antrian ?? '-' }}"
                 data-layanan="{{ $pendaftaran->nama_layanan }}"
                 data-dokter="{{ $pendaftaran->jadwalPraktek?->tenagaMedis?->name ?? 'N/A' }}"
                 data-status-antrian="{{ $pendaftaran->no_antrian ? 'true' : 'false' }}">
                
                <div class="notifikasi-card-inner">
                    <div class="card-left-accent"></div>
                    
                    <div class="card-main-content">
                        <div class="date-section">
                            <div class="date-box">
                                <span class="date-day">{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->format('d') }}</span>
                                <span class="date-month">{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('MMM') }}</span>
                            </div>
                            <div class="date-info">
                                <span class="date-full">{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('dddd') }}</span>
                                <span class="date-year">{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->format('Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="appointment-details">
                            <h3 class="layanan-title">{{ $pendaftaran->nama_layanan }}</h3>
                            <div class="dokter-info">
                                <div class="dokter-avatar">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="dokter-text">
                                    <span class="dokter-label">Dokter</span> 
                                    <span class="dokter-name">{{ $pendaftaran->jadwalPraktek?->tenagaMedis?->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            @if($pendaftaran->no_antrian)
                                <div class="queue-badge">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>Antrian #{{ $pendaftaran->no_antrian }}</span>
                                </div>
                            @else
                                <div class="queue-badge pending">
                                    <i class="fas fa-clock"></i>
                                    <span>Menunggu Nomor Antrian</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-action">
                        <button class="btn-view-ticket">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Lihat</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-notifikasi-info">
                <div class="no-data-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3>Belum Ada Jadwal</h3>
                <p>Anda tidak memiliki jadwal kunjungan yang akan datang</p>
                <a href="{{ route('jadwal') }}" class="btn-book-now">
                    <i class="fas fa-plus-circle"></i>
                    Buat Jadwal Baru
                </a>
            </div>
        @endforelse
    </div>

{{-- MODAL TIKET --}}
<div id="detailModal" class="modal">
    <div class="modal-overlay-bg"></div>
    <div class="modal-content">
        <button class="close-btn" id="closeModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="ticket-card">
            {{-- Ticket Header --}}
            <div class="ticket-header">
                <div class="ticket-pattern"></div>
                <div class="header-content-ticket">
                    <div class="clinic-logo">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h2 class="clinic-name">Praktek Bersama</h2>
                    <p class="ticket-type">Tiket Konsultasi</p>
                </div>
            </div>
            
            {{-- Ticket Separator --}}
            <div class="ticket-separator">
                <div class="separator-circle left"></div>
                <div class="separator-line"></div>
                <div class="separator-circle right"></div>
            </div>
            
            {{-- Ticket Body --}}
            <div class="ticket-body">
                <div class="queue-section">
                    <span class="queue-label">NOMOR ANTRIAN</span>
                    <div class="queue-number" id="modalAntrian">-</div>
                </div>
                
                <div class="ticket-details">
                    <div class="detail-row">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="detail-content">
                            <span class="detail-label">Tanggal Kunjungan</span>
                            <span class="detail-value" id="modalTanggal"></span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <i class="fas fa-stethoscope"></i>
                        <div class="detail-content">
                            <span class="detail-label">Layanan</span>
                            <span class="detail-value" id="modalLayanan"></span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <i class="fas fa-user-md"></i>
                        <div class="detail-content">
                            <span class="detail-label">Dokter</span>
                            <span class="detail-value" id="modalDokter"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Ticket Footer --}}
            <div class="ticket-footer">
                <div class="footer-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <p id="modalFooterText">Harap datang sesuai jadwal</p>
            </div>
            
            {{-- Barcode Decoration --}}
            <div class="ticket-barcode">
                <div class="barcode-line"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line short"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line short"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line short"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line"></div>
                <div class="barcode-line short"></div>
                <div class="barcode-line"></div>
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

    .notifikasi-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER PREMIUM ===== */
    .page-header-container {
        margin-bottom: 40px;
        animation: fadeInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
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

    /* ===== NOTIFIKASI LIST PREMIUM ===== */
    .notifikasi-list-ringkas {
        display: flex;
        flex-direction: column;
        gap: 24px;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .notifikasi-item-ringkas {
        background: var(--bg-primary);
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(57, 166, 22, 0.15);
        animation: fadeInLeft 0.6s ease-out backwards;
    }
    
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .notifikasi-item-ringkas:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }

    .notifikasi-card-inner {
        display: flex;
        align-items: center;
        padding: 28px;
        gap: 28px;
        position: relative;
    }

    .card-left-accent {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: var(--grad);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 15px rgba(57, 166, 22, 0.5);
    }

    .notifikasi-item-ringkas:hover .card-left-accent {
        width: 12px;
    }

    /* Date Section */
    .date-section {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 160px;
    }

    .date-box {
        width: 80px;
        height: 80px;
        background: var(--grad);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        animation: float 4s ease-in-out infinite;
    }

    .date-day {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .date-month {
        font-size: 0.9rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .date-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .date-full {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .date-year {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Appointment Details */
    .appointment-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .layanan-title {
        font-size: 1.35rem;
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
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--p1);
        font-size: 20px;
    }

    .dokter-text {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .dokter-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dokter-name {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    .queue-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.25));
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #065f46;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 700;
        width: fit-content;
    }

    .queue-badge.pending {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(251, 191, 36, 0.25));
        border-color: rgba(251, 191, 36, 0.3);
        color: #92400e;
    }

    .queue-badge i {
        font-size: 0.95rem;
    }

    /* Card Action */
    .card-action {
        display: flex;
        align-items: center;
    }

    .btn-view-ticket {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 28px;
        background: var(--grad);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-view-ticket::before {
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
    
    .btn-view-ticket:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-view-ticket:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-view-ticket i,
    .btn-view-ticket span {
        position: relative;
        z-index: 1;
    }

    .btn-view-ticket i {
        font-size: 1.05rem;
    }

    /* No Data */
    .no-notifikasi-info {
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

    .no-notifikasi-info h3 {
        font-size: 1.6rem;
        color: var(--text-primary);
        margin: 0 0 12px 0;
        font-weight: 700;
    }

    .no-notifikasi-info p {
        font-size: 1.05rem;
        color: var(--text-secondary);
        margin: 0 0 28px 0;
    }

    .btn-book-now {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 32px;
        background: var(--grad);
        color: #fff;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-book-now::before {
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
    
    .btn-book-now:hover::before {
        width: 350px;
        height: 350px;
    }

    .btn-book-now:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    /* ===== MODAL PREMIUM ===== */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        animation: fadeIn 0.3s;
        padding: 20px;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-overlay-bg {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .modal-content {
        position: relative;
        background-color: transparent;
        margin: 30px auto;
        width: 90%;
        max-width: 500px;
        animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes slideUp {
        from { transform: translateY(50px) scale(0.9); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }

    .close-btn {
        position: absolute;
        top: -18px;
        right: -18px;
        width: 50px;
        height: 50px;
        background: #fff;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .close-btn i {
        font-size: 1.3rem;
        color: var(--text-secondary);
        transition: all 0.3s ease;
    }

    .close-btn:hover {
        background: #ef4444;
        transform: rotate(90deg) scale(1.1);
        box-shadow: 0 12px 35px rgba(239, 68, 68, 0.4);
    }

    .close-btn:hover i {
        color: #fff;
    }

    /* ===== TICKET CARD PREMIUM ===== */
    .ticket-card {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .ticket-header {
        background: var(--grad);
        padding: 40px 35px;
        position: relative;
        overflow: hidden;
    }

    .ticket-pattern {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: rotate 20s linear infinite;
    }

    .header-content-ticket {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .clinic-logo {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        font-size: 32px;
        color: #fff;
        animation: pulse 2s ease-in-out infinite;
    }

    .clinic-name {
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
    }

    .ticket-type {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.95);
        margin: 0;
        font-weight: 500;
    }

    /* Ticket Separator */
    .ticket-separator {
        position: relative;
        height: 35px;
        background: var(--bg-primary);
    }

    .separator-line {
        position: absolute;
        top: 50%;
        left: 25px;
        right: 25px;
        height: 2px;
        background: repeating-linear-gradient(
            90deg,
            rgba(57, 166, 22, 0.3) 0,
            rgba(57, 166, 22, 0.3) 8px,
            transparent 8px,
            transparent 16px
        );
        transform: translateY(-50%);
    }

    .separator-circle {
        position: absolute;
        top: 50%;
        width: 35px;
        height: 35px;
        background: var(--bg-secondary);
        border-radius: 50%;
        transform: translateY(-50%);
    }

    .separator-circle.left {
        left: -17px;
    }

    .separator-circle.right {
        right: -17px;
    }

    /* Ticket Body */
    .ticket-body {
        padding: 40px 35px 35px;
        background: var(--bg-primary);
    }

    .queue-section {
        text-align: center;
        margin-bottom: 35px;
    }

    .queue-label {
        display: block;
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 15px;
    }

    .queue-number {
        font-size: 5.5rem;
        font-weight: 900;
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        text-shadow: 2px 2px 4px rgba(57, 166, 22, 0.1);
        animation: pulse 2s ease-in-out infinite;
    }

    .ticket-details {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        padding: 18px;
        background: var(--bg-secondary);
        border-radius: 14px;
        border: 1px solid rgba(57, 166, 22, 0.1);
        transition: all 0.3s ease;
    }
    
    .detail-row:hover {
        background: rgba(57, 166, 22, 0.05);
        border-color: rgba(57, 166, 22, 0.2);
        transform: translateX(4px);
    }

    .detail-row i {
        font-size: 1.4rem;
        color: var(--p1);
        margin-top: 3px;
    }

    .detail-content {
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex: 1;
    }

    .detail-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 1.05rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Ticket Footer */
    .ticket-footer {
        padding: 24px 35px;
        background: var(--bg-secondary);
        border-top: 1px dashed rgba(57, 166, 22, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .footer-icon {
        width: 28px;
        height: 28px;
        background: var(--grad);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .footer-icon i {
        font-size: 0.75rem;
        color: #fff;
    }

    .ticket-footer p {
        color: var(--text-secondary);
        font-size: 0.95rem;
        margin: 0;
        font-weight: 500;
    }

    /* Barcode */
    .ticket-barcode {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        gap: 5px;
        padding: 24px 35px 30px;
        background: var(--bg-primary);
        height: 70px;
    }

    .barcode-line {
        width: 4px;
        height: 40px;
        background: var(--text-primary);
        border-radius: 2px;
        opacity: 0.8;
    }

    .barcode-line.short {
        height: 28px;
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

        .notifikasi-card-inner {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
            gap: 20px;
        }

        .date-section {
            width: 100%;
            justify-content: flex-start;
        }

        .appointment-details {
            width: 100%;
        }

        .card-action {
            width: 100%;
        }

        .btn-view-ticket {
            width: 100%;
            justify-content: center;
        }

        .queue-number {
            font-size: 4.5rem;
        }

        .modal-content {
            margin: 15px auto;
        }

        .close-btn {
            top: -12px;
            right: -12px;
            width: 45px;
            height: 45px;
        }
        
        .ticket-header {
            padding: 35px 28px;
        }
        
        .ticket-body {
            padding: 35px 28px 30px;
        }
        
        .ticket-footer {
            padding: 20px 28px;
        }
        
        .ticket-barcode {
            padding: 20px 28px 25px;
        }
    }
    
    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .date-box {
            width: 70px;
            height: 70px;
        }
        
        .date-day {
            font-size: 1.8rem;
        }
        
        .layanan-title {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('detailModal');
    const items = document.querySelectorAll('.notifikasi-item-ringkas');
    const closeModalBtn = document.getElementById('closeModalBtn');

    items.forEach(item => {
        item.addEventListener('click', function() {
            const data = this.dataset;
            
            // Isi konten modal
            document.getElementById('modalTanggal').textContent = data.tanggal;
            document.getElementById('modalAntrian').textContent = data.antrian;
            document.getElementById('modalLayanan').textContent = data.layanan;
            document.getElementById('modalDokter').textContent = data.dokter;

            // Ubah pesan footer
            if (data.statusAntrian === 'false') {
                document.getElementById('modalFooterText').innerHTML = 'Nomor antrian belum tersedia. Silakan hubungi admin.';
            } else {
                document.getElementById('modalFooterText').innerHTML = 'Harap datang sesuai jadwal yang telah ditentukan';
            }
            
            // Tampilkan modal
            modal.style.display = 'block';
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
        if (event.target == modal || event.target.classList.contains('modal-overlay-bg')) {
            closeModal();
        }
    });

    // Tutup dengan tombol ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
});
</script>
@endpush
