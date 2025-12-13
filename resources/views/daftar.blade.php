@extends('layouts.main')

@section('title', 'Pilih Dokter & Layanan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    <div class="daftar-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="header-text">
                <h1 class="page-title">Daftar Konsultasi</h1>
                <p class="page-subtitle">Pilih dokter dan layanan yang sesuai dengan kebutuhan Anda</p>
            </div>
            <div class="hero-decoration">
                <div class="pulse-ring pulse-1"></div>
                <div class="pulse-ring pulse-2"></div>
                <div class="pulse-ring pulse-3"></div>
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="autoHideAlert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger" id="autoHideAlert">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- ID "layanan-list-container" ditambahkan untuk Auto Refresh --}}
    <div class="layanan-list" id="layanan-list-container">
        @forelse($jadwals as $index => $jadwal)
            <div class="layanan-item" 
                 {{-- Animasi dihapus --}}
                 data-url="{{ route('daftar.form.json', $jadwal->id) }}" 
                 data-jadwal-id="{{ $jadwal->id }}"
                 data-fallback="{{ route('daftar.form', $jadwal->id) }}">

                <div class="layanan-card-inner">
                    <div class="card-left-section">
                        <div class="layanan-avatar">
                            @if($jadwal->tenagaMedis?->profile_photo_path) 
                                <img src="{{ asset('storage/' . $jadwal->tenagaMedis->profile_photo_path) }}" alt="Foto">
                            @else
                                <i class="fas fa-user-md"></i>
                            @endif
                            <div class="avatar-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="layanan-info">
                            <h3 class="nama-dokter">{{ $jadwal->tenagaMedis?->name ?? 'N/A' }}</h3>
                            <div class="nama-layanan">
                                <i class="fas fa-stethoscope"></i>
                                <span>{{ $jadwal->layanan }}</span>
                            </div>
                            
                            @php
                                $hari = is_array($jadwal->hari) ? $jadwal->hari : json_decode($jadwal->hari, true);
                                $hariText = count($hari) == 7 ? 'Setiap Hari' : implode(', ', array_slice($hari, 0, 2)) . (count($hari) > 2 ? '...' : '');
                            @endphp
                            
                            <div class="schedule-info">
                                <div class="schedule-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $hariText }}</span>
                                </div>
                                <div class="schedule-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-right-section">
                        <button class="btn-daftar"> 
                            <span>Daftar Sekarang</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-data">
                <div class="no-data-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3>Belum Ada Layanan</h3>
                <p>Saat ini belum ada layanan/jadwal yang tersedia</p>
            </div>
        @endforelse
    </div>

{{-- MODAL FORM --}}
<div id="formModal" class="modal-overlay">
    <div class="modal-card">
        <button class="close-modal" id="closeModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div id="modalFormContent">
            <div class="loading-spinner"></div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI --}}
<div id="confirmModal" class="modal-overlay">
    <div class="modal-card modal-card-confirm">
        <button class="close-modal" id="closeConfirmModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content-confirm">
            <div class="confirm-icon-wrapper">
                <i class="fas fa-question-circle"></i>
            </div>
            <h2 class="modal-title-confirm">Konfirmasi Pendaftaran</h2>
            <p class="modal-text-confirm">
                Apakah data yang Anda masukkan sudah benar?<br>
                Pastikan kembali data Anda sebelum mengirim.
            </p>

            <div class="form-actions-confirm">
                <button type="button" class="btn-secondary-confirm" id="btnCancelConfirm">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </button>
                <button type="button" class="btn-primary-confirm" id="btnConfirmSubmit">
                    <i class="fas fa-paper-plane"></i>
                    <span>Ya, Kirim</span>
                </button>
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
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f3f4f6;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
    }
    
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .daftar-container { 
        max-width: 1000px; 
        margin: 0 auto; 
        padding: 40px 20px; 
    }

    /* ===== HEADER PREMIUM (NO ANIMATION) ===== */
    .daftar-header { 
        margin-bottom: 40px; 
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
        animation: heartbeat 1.5s ease-in-out infinite;
    }
    
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.1); }
        50% { transform: scale(1); }
        75% { transform: scale(1.05); }
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

    /* ===== ALERT PREMIUM (NO ANIMATION) ===== */
    .alert { 
        display: flex; 
        align-items: center; 
        gap: 14px; 
        padding: 18px 24px; 
        margin-bottom: 28px; 
        border-radius: 16px; 
        font-weight: 600; 
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.2);
    }
    
    .alert-success { 
        color: var(--p3);
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        border: 2px solid rgba(57, 166, 22, 0.3);
    }
    
    .alert-danger {
        color: #721c24;
        background: linear-gradient(135deg, rgba(231, 76, 60, 0.15), rgba(231, 76, 60, 0.25));
        border: 2px solid rgba(231, 76, 60, 0.3);
    }
    
    .alert i { 
        font-size: 1.5rem;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }

    /* ===== LAYANAN LIST PREMIUM (NO ANIMATION) ===== */
    .layanan-list { 
        display: flex; 
        flex-direction: column; 
        gap: 24px; 
    }
    
    .layanan-item { 
        background: var(--bg-primary);
        border-radius: 20px; 
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        border: 1px solid rgba(57, 166, 22, 0.15);
        cursor: pointer; 
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; 
        overflow: hidden;
    }
    
    .layanan-item::before { 
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
    
    .layanan-item:hover { 
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
    }
    
    .layanan-item:hover::before { 
        width: 12px; 
    }
    
    .layanan-card-inner { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 28px 32px; 
        gap: 28px; 
    }
    
    .card-left-section { 
        display: flex; 
        align-items: center; 
        gap: 24px; 
        flex: 1; 
    }
    
    .layanan-avatar { 
        width: 90px; 
        height: 90px; 
        border-radius: 50%; 
        background: var(--grad);
        color: white;
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 40px; 
        overflow: hidden; 
        flex-shrink: 0; 
        border: 4px solid rgba(57, 166, 22, 0.2);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative; 
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        animation: float 4s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
    
    .layanan-item:hover .layanan-avatar { 
        transform: scale(1.1) rotate(5deg);
    }
    
    .layanan-avatar img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    
    .avatar-badge { 
        position: absolute; 
        bottom: 4px; 
        right: 4px; 
        width: 28px; 
        height: 28px; 
        background: white;
        border: 3px solid var(--p1);
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.4);
    }
    
    .avatar-badge i { 
        font-size: 0.7rem; 
        color: var(--p1);
    }
    
    .layanan-info { 
        display: flex; 
        flex-direction: column; 
        gap: 12px; 
        flex: 1; 
    }
    
    .nama-dokter { 
        font-size: 1.4rem; 
        font-weight: 700; 
        color: var(--text-primary);
        margin: 0; 
        letter-spacing: -0.3px; 
    }
    
    .nama-layanan { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        font-size: 1.05rem; 
        color: var(--text-secondary);
        font-weight: 600; 
    }
    
    .nama-layanan i { 
        color: var(--p1);
        font-size: 1rem; 
    }
    
    .schedule-info { 
        display: flex; 
        gap: 24px; 
        margin-top: 6px; 
    }
    
    .schedule-item { 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        font-size: 0.9rem; 
        color: var(--text-muted);
        font-weight: 500; 
        padding: 6px 12px;
        background: var(--bg-secondary);
        border-radius: 8px;
    }
    
    .schedule-item i { 
        color: var(--p1);
        font-size: 0.95rem; 
    }
    
    .btn-daftar { 
        display: inline-flex; 
        align-items: center; 
        gap: 12px; 
        padding: 16px 32px; 
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
    
    .btn-daftar::before { 
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
    
    .btn-daftar:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-daftar:hover { 
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-daftar i { 
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }
    
    .btn-daftar:hover i { 
        transform: translateX(6px); 
    }
    
    .btn-daftar span {
        position: relative;
        z-index: 1;
    }
    
    .no-data { 
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
    
    .no-data h3 { 
        font-size: 1.6rem; 
        color: var(--text-primary);
        margin: 0 0 12px 0; 
        font-weight: 700; 
    }
    
    .no-data p { 
        font-size: 1.05rem; 
        color: var(--text-secondary);
        margin: 0; 
    }

    /* ===== MODAL PREMIUM (NO ANIMATION) ===== */
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
        -webkit-backdrop-filter: blur(8px);
        justify-content: center; 
        align-items: center; 
        padding: 20px; 
    }
    
    .modal-card { 
        background-color: var(--bg-primary);
        margin: auto; 
        border-radius: 24px; 
        width: 90%; 
        max-width: 750px; 
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        max-height: 90vh; 
        display: flex; 
        flex-direction: column; 
        position: relative;
    }
    
    .modal-card-confirm {
        max-width: 520px;
    }
    
    .close-modal { 
        position: absolute; 
        top: -18px; 
        right: -18px; 
        width: 50px; 
        height: 50px; 
        background: white;
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
    
    .close-modal i { 
        font-size: 1.3rem; 
        color: var(--text-secondary);
        transition: all 0.3s ease;
    }
    
    .close-modal:hover { 
        background: #ef4444;
        transform: rotate(90deg) scale(1.1);
        box-shadow: 0 12px 35px rgba(239, 68, 68, 0.4);
    }
    
    .close-modal:hover i { 
        color: #fff; 
    }
    
    #modalFormContent { 
        padding: 45px 40px; 
        overflow-y: auto;
    }
    
    #modalFormContent::-webkit-scrollbar { width: 8px; }
    #modalFormContent::-webkit-scrollbar-track { background: var(--bg-secondary); border-radius: 10px; }
    #modalFormContent::-webkit-scrollbar-thumb { background: var(--grad); border-radius: 10px; }
    
    .form-header { 
        text-align: center; 
        margin-bottom: 35px; 
        padding-bottom: 25px; 
        border-bottom: 2px solid rgba(57, 166, 22, 0.15);
    }
    
    .form-title { 
        color: var(--p1);
        font-weight: 700; 
        font-size: 2rem; 
        margin: 0 0 12px 0;
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .form-subtitle { 
        color: var(--text-secondary);
        font-size: 1.1rem; 
        font-weight: 600; 
        margin: 0; 
    }
    
    .form-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 24px; 
    }
    
    .form-group.full-width { 
        grid-column: 1 / -1; 
    }
    
    .form-group { 
        margin-bottom: 0; 
    }
    
    .form-label { 
        display: block; 
        margin-bottom: 12px; 
        font-weight: 600; 
        color: var(--text-primary);
        font-size: 1rem;
    }
    
    .form-control { 
        width: 100%; 
        padding: 16px 18px; 
        border: 2px solid rgba(57, 166, 22, 0.2);
        border-radius: 14px; 
        font-size: 1rem; 
        font-family: 'Inter', sans-serif; 
        background-color: var(--bg-secondary);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        color: var(--text-primary);
    }
    
    .form-control:focus { 
        outline: none; 
        border-color: var(--p1);
        background-color: var(--bg-primary);
        box-shadow: 0 0 0 5px rgba(57, 166, 22, 0.1);
        transform: translateY(-2px);
    }
    
    textarea.form-control { 
        resize: vertical; 
        min-height: 110px; 
        font-family: 'Inter', sans-serif; 
    }
    
    .form-actions { 
        text-align: center; 
        margin-top: 35px; 
        padding-top: 28px; 
        border-top: 2px solid rgba(57, 166, 22, 0.15);
    }
    
    .btn-primary { 
        background: var(--grad);
        color: #fff; 
        border: none; 
        padding: 18px 36px; 
        border-radius: 14px; 
        cursor: pointer; 
        font-weight: 600; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.1rem; 
        width: 100%; 
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary::before {
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
    
    .btn-primary:hover::before {
        width: 400px;
        height: 400px;
    }
    
    .btn-primary:hover { 
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(57, 166, 22, 0.45);
    }
    
    .btn-secondary { 
        margin-top: 18px; 
        display: inline-block; 
        color: var(--text-secondary);
        text-decoration: none; 
        font-weight: 600; 
        font-size: 1rem; 
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover { 
        color: var(--p1);
        transform: translateX(-3px);
    }
    
    .loading-spinner { 
        border: 6px solid var(--bg-secondary);
        border-top: 6px solid var(--p1);
        border-radius: 50%; 
        width: 70px; 
        height: 70px; 
        animation: spin 1s linear infinite; 
        margin: 80px auto; 
    }
    
    @keyframes spin { 
        0% { transform: rotate(0deg); } 
        100% { transform: rotate(360deg); } 
    }
    
    /* ===== MODAL KONFIRMASI PREMIUM ===== */
    .modal-content-confirm {
        padding: 40px 45px;
        text-align: center;
    }
    
    .confirm-icon-wrapper {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        animation: pulse 2s ease-in-out infinite;
    }
    
    .confirm-icon-wrapper i {
        font-size: 3rem;
        color: var(--p1);
    }
    
    .modal-title-confirm {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.6rem;
        margin: 0 0 18px 0;
    }
    
    .modal-text-confirm {
        color: var(--text-secondary);
        font-size: 1.05rem;
        margin: 0 0 35px 0;
        line-height: 1.7;
    }
    
    .form-actions-confirm {
        display: flex;
        gap: 15px;
        padding-top: 28px;
        border-top: 2px solid rgba(57, 166, 22, 0.15);
    }
    
    .btn-primary-confirm {
        flex: 1;
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 16px 32px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary-confirm::before {
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
    
    .btn-primary-confirm:hover::before {
        width: 350px;
        height: 350px;
    }
    
    .btn-primary-confirm:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-primary-confirm i,
    .btn-primary-confirm span {
        position: relative;
        z-index: 1;
    }
    
    .btn-secondary-confirm {
        flex: 1;
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 2px solid rgba(57, 166, 22, 0.3);
        padding: 16px 32px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-secondary-confirm:hover {
        background: var(--p1);
        color: white;
        border-color: var(--p1);
        transform: translateY(-2px);
    }
    
    .btn-primary-confirm:disabled {
        background: var(--text-muted);
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
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
        
        .layanan-card-inner { 
            flex-direction: column; 
            align-items: flex-start; 
            padding: 24px; 
        }
        
        .card-left-section { 
            width: 100%; 
            flex-direction: column; 
            align-items: center; 
            text-align: center; 
        }
        
        .layanan-info { 
            align-items: center; 
        }
        
        .nama-layanan { 
            justify-content: center; 
        }
        
        .schedule-info { 
            flex-direction: column; 
            gap: 10px; 
            align-items: center; 
        }
        
        .card-right-section { 
            width: 100%; 
        }
        
        .btn-daftar { 
            width: 100%; 
            justify-content: center; 
        }
        
        .form-grid { 
            grid-template-columns: 1fr; 
        }
        
        #modalFormContent { 
            padding: 30px 24px; 
        }
        
        .close-modal { 
            top: -12px; 
            right: -12px; 
            width: 45px; 
            height: 45px; 
        }
        
        .form-actions-confirm { 
            flex-direction: column; 
        }
        
        .modal-content-confirm {
            padding: 35px 28px;
        }
    }
    
    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .layanan-avatar {
            width: 80px;
            height: 80px;
            font-size: 36px;
        }
        
        .nama-dokter {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('formModal');
    const modalContent = document.getElementById('modalFormContent');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const items = document.querySelectorAll('.layanan-item');
    let flatpickrInstance = null;
    let closedDates = [];

    const confirmModal = document.getElementById('confirmModal');
    const closeConfirmBtn = document.getElementById('closeConfirmModalBtn');
    const cancelConfirmBtn = document.getElementById('btnCancelConfirm');
    const submitConfirmBtn = document.getElementById('btnConfirmSubmit');
    let formToSubmit = null;

    // --- AUTO REFRESH LOGIC ---
    if (typeof window.initAutoRefresh === 'function') {
        window.initAutoRefresh(['#layanan-list-container']);
    }

    window.rebindEvents = function() {
        bindServiceEvents();
        console.log('♻️ Service list refreshed and events rebound!');
    };

    function bindServiceEvents() {
        const items = document.querySelectorAll('.layanan-item');
        items.forEach(item => {
            // Remove old listener to avoid duplicates
            item.removeEventListener('click', handleServiceClick);
            // Add new listener
            item.addEventListener('click', handleServiceClick);
        });
    }

    async function handleServiceClick(event) {
        event.preventDefault();
        
        // Use currentTarget because the listener is on the item wrapper
        const item = event.currentTarget;
        const jsonUrl = item.dataset.url;
        
        modal.style.display = 'flex';
        modalContent.innerHTML = '<div class="loading-spinner"></div>';
        document.body.style.overflow = 'hidden';

        try {
            const response = await fetch(jsonUrl);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            closedDates = data.closed_dates || [];
            console.log('Tanggal tertutup:', closedDates);

            const formHtml = `
                <div class="form-header">
                    <h2 class="form-title">Form Pendaftaran</h2>
                    <p class="form-subtitle">
                        <strong>${data.dokter_name}</strong> - ${data.layanan_name}
                    </p>
                </div>
                <form action="${data.form_action}" method="POST" id="pendaftaranForm">
                    @csrf
                    <input type="hidden" name="nama_layanan" value="${data.layanan_name}">
                    <input type="hidden" name="jadwal_praktek_id" value="${data.jadwal_id}">
                    
                    <div class="form-group full-width">
                        <label class="form-label">Layanan</label>
                        <input type="text" class="form-control" value="${data.layanan_name} (${data.dokter_name})" readonly style="background-color: var(--bg-tertiary); color: var(--text-muted); cursor: not-allowed;">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="modal_nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="modal_nama_lengkap" class="form-control" value="${data.user_name}" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="modal_tanggal_lahir" class="form-control" value="${data.user_tgl_lahir}" required>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="modal_alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="modal_alamat" class="form-control" rows="3" required>${data.user_alamat}</textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="modal_no_telepon" class="form-label">No Telepon/Whatsapp</label>
                            <input type="tel" name="no_telepon" id="modal_no_telepon" class="form-control" value="${data.user_no_hp}" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_jadwal_datepicker" class="form-label">Pilih Tanggal Kunjungan <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="jadwal_dipilih" id="modal_jadwal_datepicker" class="form-control" placeholder="Pilih tanggal..." required>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="modal_keluhan" class="form-label">Jenis Keluhan/Gejala <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="keluhan" id="modal_keluhan" class="form-control" placeholder="Jelaskan keluhan Anda" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_lama_keluhan" class="form-label">Sejak Kapan? <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="lama_keluhan" id="modal_lama_keluhan" class="form-control" placeholder="cth: Sejak 2 hari lalu" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                        </button>
                        <a href="#" class="btn-secondary" id="batalModalBtn">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            `;
            
            modalContent.innerHTML = formHtml.replace('@csrf', '{{ csrf_field() }}');

            // Initialize Flatpickr
            if (flatpickrInstance) flatpickrInstance.destroy();
            
            flatpickrInstance = flatpickr("#modal_jadwal_datepicker", {
                locale: { firstDayOfWeek: 1 },
                minDate: "today",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        return (!data.enabled_days.includes(date.getDay()));
                    },
                    function(date) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const dateString = `${year}-${month}-${day}`;
                        return closedDates.includes(dateString);
                    }
                ],
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const date = dayElem.dateObj;
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const dateString = `${year}-${month}-${day}`;

                    if (closedDates.includes(dateString)) {
                        dayElem.innerHTML += "<span class='dot'></span>";
                        dayElem.style.backgroundColor = "#fee2e2";
                        dayElem.style.color = "#ef4444";
                        dayElem.style.textDecoration = "line-through";
                        dayElem.title = "Jadwal Dibatalkan/Tutup";
                    }
                }
            });

            document.getElementById('batalModalBtn').addEventListener('click', (e) => {
                e.preventDefault();
                closeModal();
            });

            const dynamicForm = document.getElementById('pendaftaranForm');
            if (dynamicForm) {
                dynamicForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const selectedDate = document.getElementById('modal_jadwal_datepicker').value;
                    
                    if (closedDates.includes(selectedDate)) {
                        alert('❌ Dokter tidak tersedia pada tanggal yang Anda pilih. Silakan pilih tanggal lain.');
                        return false;
                    }
                    
                    formToSubmit = this;
                    openConfirmModal();
                });
            }

        } catch (error) {
            console.error('Gagal mengambil data form:', error);
            modalContent.innerHTML = `
                <div style="text-align:center; padding: 60px 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: #ef4444; margin-bottom: 20px; display: block;"></i>
                    <h3 style="color: var(--text-primary); margin-bottom: 10px;">Gagal Memuat Form</h3>
                    <p style="color: var(--text-secondary);">Silakan coba lagi atau hubungi administrator</p>
                </div>
            `;
        }
    }

    // Initialize events initially
    bindServiceEvents();

    // Auto-hide Alert
    const alert = document.getElementById('autoHideAlert');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-30px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }

    function closeModal() {
        modal.style.display = 'none';
        modalContent.innerHTML = '';
        document.body.style.overflow = 'auto';
        if (flatpickrInstance) {
            flatpickrInstance.destroy();
            flatpickrInstance = null;
        }
    }

    function openConfirmModal() {
        confirmModal.style.display = 'flex';
        modal.style.display = 'none';
    }

    function closeConfirmModal() {
        confirmModal.style.display = 'none';
        modal.style.display = 'flex';
    }

    function submitRealForm() {
        if (formToSubmit) {
            submitConfirmBtn.disabled = true;
            submitConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Mengirim...</span>';
            formToSubmit.submit();
        }
    }

    closeConfirmBtn.addEventListener('click', closeConfirmModal);
    cancelConfirmBtn.addEventListener('click', closeConfirmModal);
    submitConfirmBtn.addEventListener('click', submitRealForm);
    closeModalBtn.addEventListener('click', closeModal);

    window.addEventListener('click', function(event) {
        if (event.target == modal) closeModal();
        if (event.target == confirmModal) closeConfirmModal();
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (modal.style.display === 'flex') closeModal();
            if (confirmModal.style.display === 'flex') closeConfirmModal();
        }
    });
});
</script>
@endpush