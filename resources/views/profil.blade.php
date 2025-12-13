@extends('layouts.main')
@section('title', 'Profil Saya')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- Header Section --}}
    <div class="profile-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="header-text">
                <h1 class="page-title">Biodata Pasien</h1>
                <p class="page-subtitle">Kelola informasi pribadi Anda</p>
            </div>
            <div class="hero-decoration">
                <div class="pulse-ring pulse-1"></div>
                <div class="pulse-ring pulse-2"></div>
                <div class="pulse-ring pulse-3"></div>
                <i class="fas fa-id-card"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" id="autoHideAlert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @error('profile_photo')
        <div class="alert alert-danger" id="autoHideAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror

    {{-- Profile Card --}}
    {{-- ID "live-profile-card" ditambahkan untuk Auto Refresh --}}
    <div class="profile-card" id="live-profile-card">
        <div class="card-gradient-bg"></div>
        
        {{-- Bagian Foto Profil --}}
        <div class="profile-picture-section">
            <form action="{{ route('profil.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf
                <div class="avatar-wrapper">
                    <div class="avatar-large">
                        @if($user->profile_photo_path)
                           <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Foto Profil" id="photoPreview">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    <div class="avatar-status"></div>
                    <label for="profile_photo" class="edit-picture-btn" title="Ubah Foto Profil">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <input type="file" id="profile_photo" name="profile_photo" style="display: none;" accept="image/*">
                <div class="form-actions photo-actions" id="photoActions" style="display: none;">
                    <button type="submit" class="btn-primary-small">
                        <i class="fas fa-save"></i>
                        Simpan Foto
                    </button>
                </div>
            </form>
            <h3 class="profile-name">{{ $user->name }}</h3>
            <p class="profile-role">Pasien</p>
        </div>

        {{-- Detail Biodata --}}
        <div class="profile-details">
            <div class="detail-grid">
                <div class="info-row">
                    <div class="info-icon-wrapper">
                        <i class="fas fa-user-circle info-icon"></i>
                    </div>
                    <div class="info-text">
                        <span class="label">Nama Lengkap</span>
                        <span class="value">{{ $user->name }}</span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-icon-wrapper">
                        <i class="fas fa-calendar-alt info-icon"></i>
                    </div>
                    <div class="info-text">
                        <span class="label">Tanggal Lahir</span>
                        <span class="value">{{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->isoFormat('DD MMMM YYYY') : '-' }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon-wrapper">
                        <i class="fas fa-phone info-icon"></i>
                    </div>
                    <div class="info-text">
                        <span class="label">No Telepon/WhatsApp</span>
                        <span class="value">{{ $user->no_hp ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="info-row full-width">
                    <div class="info-icon-wrapper">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                    </div>
                    <div class="info-text">
                        <span class="label">Alamat</span>
                        <span class="value">{{ $user->alamat ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tombol Edit --}}
        <div class="profile-actions">
            <button type="button" class="btn-edit-profile" id="openEditModalBtn">
                <span>Edit Biodata</span>
                <i class="fas fa-edit"></i>
            </button>
        </div>
    </div>

{{-- MODAL EDIT BIODATA --}}
<div id="editProfileModal" class="modal-overlay">
    <div class="modal-card">
        <button class="close-modal" id="closeEditModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div id="modalFormContent">
            <div class="form-header">
                <h2 class="form-title">Edit Biodata Pasien</h2>
                <p class="form-subtitle">Perbarui informasi pribadi Anda</p>
            </div>

            @if ($errors->any() && session('form_type') === 'biodata')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="biodata">

                <div class="form-group full-width">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="text" name="tanggal_lahir" id="tanggal_lahir_picker" class="form-control" 
                               value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}" placeholder="Pilih tanggal lahir">
                    </div>

                    <div class="form-group">
                        <label for="no_hp" class="form-label">No Telepon/WhatsApp</label>
                        <input type="tel" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                    <a href="#" class="btn-secondary" id="batalEditModalBtn">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </form>
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

    .profile-wrapper {
        max-width: 920px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER PREMIUM (NO ANIMATION) ===== */
    .profile-header-banner { 
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
        font-weight: 500;
        margin: 0;
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

    /* ===== ALERT STYLES (NO ANIMATION) ===== */
    .alert {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 24px;
        margin-bottom: 28px;
        border-radius: 16px;
        font-weight: 500;
    }

    .alert i {
        font-size: 1.4rem;
    }

    .alert-success {
        color: #065f46;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.25));
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .alert-danger {
        color: #991b1b;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.25));
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    /* ===== PROFILE CARD PREMIUM (NO ANIMATION) ===== */
    .profile-card {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
        border: 1px solid rgba(57, 166, 22, 0.15);
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .card-gradient-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 220px;
        background: var(--grad);
        opacity: 0.08;
        transition: opacity 0.4s ease;
    }

    .profile-card:hover {
        box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
        border-color: rgba(57, 166, 22, 0.4);
        transform: translateY(-5px);
    }

    .profile-card:hover .card-gradient-bg {
        opacity: 0.12;
    }

    /* ===== PROFILE PICTURE SECTION ===== */
    .profile-picture-section {
        text-align: center;
        padding: 45px 28px 35px;
        position: relative;
        z-index: 1;
        border-bottom: 1px solid rgba(57, 166, 22, 0.1);
    }

    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 22px;
    }

    .avatar-large {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        color: var(--p1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        overflow: hidden;
        border: 5px solid var(--bg-primary);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.2);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .profile-card:hover .avatar-large {
        transform: scale(1.08);
    }

    .avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-status {
        position: absolute;
        bottom: 22px;
        right: 8px;
        width: 22px;
        height: 22px;
        background: #10b981;
        border: 4px solid var(--bg-primary);
        border-radius: 50%;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.5);
        animation: pulse 2s ease-in-out infinite;
    }

    .edit-picture-btn {
        position: absolute;
        bottom: 22px;
        right: -8px;
        background: var(--grad);
        color: white;
        border: 4px solid var(--bg-primary);
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .edit-picture-btn::before {
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
    
    .edit-picture-btn:hover::before {
        width: 100px;
        height: 100px;
    }

    .edit-picture-btn:hover {
        transform: scale(1.15) rotate(15deg);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }

    .profile-name {
        color: var(--text-primary);
        font-size: 1.7rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }
    
    .profile-role {
        color: var(--text-secondary);
        font-size: 0.95rem;
        font-weight: 500;
        margin: 0;
    }
    
    .photo-actions {
        margin-top: 18px;
        text-align: center;
    }
    
    .btn-primary-small {
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary-small::before {
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
    
    .btn-primary-small:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary-small:hover { 
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-primary-small i {
        position: relative;
        z-index: 1;
    }
    
    /* ===== PROFILE DETAILS PREMIUM ===== */
    .profile-details {
        padding: 32px 28px;
        background: var(--bg-secondary);
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        padding: 20px;
        background: var(--bg-primary);
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(57, 166, 22, 0.1);
    }

    .info-row.full-width {
        grid-column: 1 / -1;
    }

    .info-row:hover {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.03), rgba(57, 166, 22, 0.05));
        border-color: rgba(57, 166, 22, 0.25);
        transform: translateX(6px);
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.1);
    }

    .info-icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.15), rgba(57, 166, 22, 0.25));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    .info-row:hover .info-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .info-icon {
        color: var(--p1);
        font-size: 20px;
    }

    .info-text {
        display: flex;
        flex-direction: column;
        gap: 6px;
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
        font-size: 1.1rem;
        color: var(--text-primary);
        font-weight: 600;
        word-wrap: break-word;
    }

    /* ===== PROFILE ACTIONS ===== */
    .profile-actions {
        background: var(--bg-primary);
        padding: 28px;
        border-top: 1px solid rgba(57, 166, 22, 0.1);
        text-align: center;
    }

    .btn-edit-profile {
        background: var(--grad);
        color: #fff;
        padding: 16px 40px;
        border-radius: 14px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-edit-profile::before {
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

    .btn-edit-profile:hover::before {
        width: 400px;
        height: 400px;
    }

    .btn-edit-profile:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }

    .btn-edit-profile:active {
        transform: translateY(-2px);
    }

    .btn-edit-profile i {
        transition: transform 0.4s ease;
        position: relative;
        z-index: 1;
    }
    
    .btn-edit-profile span {
        position: relative;
        z-index: 1;
    }

    .btn-edit-profile:hover i {
        transform: translateX(6px);
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

    .close-modal {
        position: absolute;
        top: -18px;
        right: -18px;
        width: 50px;
        height: 50px;
        background: var(--bg-primary);
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
        padding: 45px;
        overflow-y: auto;
    }
    
    #modalFormContent::-webkit-scrollbar { width: 8px; }
    #modalFormContent::-webkit-scrollbar-track { background: var(--bg-secondary); border-radius: 10px; }
    #modalFormContent::-webkit-scrollbar-thumb { background: var(--grad); border-radius: 10px; }

    /* ===== FORM STYLES PREMIUM ===== */
    .form-header {
        text-align: center;
        margin-bottom: 36px;
        padding-bottom: 24px;
        border-bottom: 2px solid rgba(57, 166, 22, 0.15);
    }

    .form-title {
        color: var(--p1);
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 1.05rem;
        font-weight: 500;
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
        font-size: 0.95rem;
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

    .form-control::placeholder {
        color: var(--text-muted);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 110px;
        font-family: 'Inter', sans-serif;
    }

    /* ===== FORM ACTIONS ===== */
    .form-actions {
        text-align: center;
        margin-top: 36px;
        padding-top: 28px;
        border-top: 2px solid rgba(57, 166, 22, 0.15);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .btn-primary {
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 18px 36px;
        border-radius: 14px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.05rem;
        width: 100%;
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
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
        width: 500px;
        height: 500px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(57, 166, 22, 0.45);
    }
    
    .btn-primary i {
        position: relative;
        z-index: 1;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.3s ease;
        padding: 12px 24px;
        border-radius: 12px;
    }

    .btn-secondary:hover {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .profile-wrapper {
            padding: 20px 15px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            padding: 30px 24px;
        }
        
        .hero-decoration {
            width: 90px;
            height: 90px;
        }
        
        .hero-decoration > i {
            font-size: 40px;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        #modalFormContent {
            padding: 30px;
        }

        .modal-card {
            width: 95%;
            margin: 15px;
        }
        
        .close-modal {
            top: -12px;
            right: -12px;
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 480px) {
        .avatar-large {
            width: 120px;
            height: 120px;
            font-size: 50px;
        }

        .edit-picture-btn {
            width: 44px;
            height: 44px;
            font-size: 16px;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .info-icon-wrapper {
            width: 45px;
            height: 45px;
        }

        .info-icon {
            font-size: 18px;
        }
        
        .form-title {
            font-size: 1.6rem;
        }
        
        #modalFormContent {
            padding: 24px;
        }
    }
</style>
@endpush

{{-- SCRIPTS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    // ===== PREVIEW FOTO PROFIL =====
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('profile_photo');
        const photoActions = document.getElementById('photoActions');
        const placeholder = document.querySelector('.avatar-large');

        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        let imgPreview = document.getElementById('photoPreview');

                        if (!imgPreview) {
                            const icon = placeholder.querySelector('.fa-user');
                            if (icon) icon.style.display = 'none';

                            imgPreview = document.createElement('img');
                            imgPreview.id = 'photoPreview';
                            placeholder.appendChild(imgPreview);
                        }

                        imgPreview.src = event.target.result;
                        if (photoActions) photoActions.style.display = 'block';
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    });
    
    // ===== MODAL FUNCTIONS =====
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editProfileModal');
        const openBtn = document.getElementById('openEditModalBtn');
        const closeBtn = document.getElementById('closeEditModalBtn');
        const batalBtn = document.getElementById('batalEditModalBtn');

        // Inisialisasi Flatpickr
        flatpickr("#tanggal_lahir_picker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            allowInput: true,
            maxDate: "today",
            locale: "id"
        });

        // --- AUTO REFRESH SYSTEM ---
        // (Optional for Profile, but keeps consistency)
        if (typeof window.initAutoRefresh === 'function') {
            window.initAutoRefresh(['#live-profile-card']);
        }

        // Fungsi modal
        function openModal() {
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal() {
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Event listeners
        if(openBtn) openBtn.addEventListener('click', openModal);
        if(closeBtn) closeBtn.addEventListener('click', closeModal);
        if(batalBtn) batalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModal();
        });

        // Tutup modal jika klik di luar
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                closeModal();
            }
        });

        // ESC key untuk tutup modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        });

        // Auto-hide alert
        const alert = document.getElementById('autoHideAlert');
        if (alert) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.6s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 600);
            }, 5000);
        }

        // Buka modal jika ada error validasi
        @if ($errors->any() && session('form_type') === 'biodata')
            openModal();
        @endif
    });
</script>
@endpush