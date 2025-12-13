@extends($layout)

@section('title', 'Chat Konsultasi')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

{{-- Jika Tenaga Medis, kita bungkus dengan struktur Dashboard agar rapi --}}
@if($myRole == 'medis')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chat Konsultasi</h3>
                <p class="text-subtitle text-muted">Berkomunikasi langsung dengan pasien.</p>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body p-0">
@else
{{-- Jika Pasien, Tampilkan Header Banner Cantik --}}
<div class="chat-header-banner">
    <div class="header-content">
        <div class="header-icon">
            <i class="fas fa-comments"></i>
        </div>
        <div class="header-text">
            <h1 class="page-title">Chat Konsultasi</h1>
            <p class="page-subtitle">
                <i class="fas fa-user-md"></i>
                {{ $myRole == 'pasien' ? 'Konsultasi dengan Tenaga Medis' : 'Konsultasi dengan Pasien' }}
            </p>
        </div>
        <div class="hero-decoration">
            <div class="pulse-ring pulse-1"></div>
            <div class="pulse-ring pulse-2"></div>
            <div class="pulse-ring pulse-3"></div>
            <i class="fas fa-stethoscope"></i>
        </div>
    </div>
</div>
@endif

{{-- MAIN CHAT CONTAINER --}}
<div class="chat-container">
    <div class="chat-wrapper">
        
        {{-- SIDEBAR: DAFTAR KONTAK --}}
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h5 class="sidebar-title">
                    <i class="fas fa-users"></i>
                    {{ $myRole == 'pasien' ? 'Tenaga Medis' : 'Daftar Pasien' }}
                </h5>
                <form method="GET" action="{{ route('chat.index') }}" class="search-form">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="search-input" 
                               placeholder="Cari kontak..." value="{{ $search ?? '' }}">
                    </div>
                </form>
            </div>

            <div class="contacts-list">
                @forelse($contacts as $contact)
                    <a href="{{ route('chat.index', ['partnerId' => $contact->id]) }}" 
                       class="contact-item {{ $partnerId == $contact->id ? 'active' : '' }}">
                        <div class="contact-avatar">
                            @if(isset($contact->profile_photo_path) && $contact->profile_photo_path)
                                <img src="{{ asset('storage/' . $contact->profile_photo_path) }}" alt="Avatar">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                            <span class="status-indicator online"></span>
                        </div>
                        <div class="contact-info">
                            <div class="contact-header">
                                <h6 class="contact-name">{{ $contact->display_name ?? 'User' }}</h6>
                                <span class="contact-time">{{ $contact->last_time ?? '' }}</span>
                            </div>
                            <p class="contact-message">{{ Str::limit($contact->last_message ?? 'Belum ada pesan', 35) }}</p>
                        </div>
                    </a>
                @empty
                    <div class="empty-contacts">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada kontak tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MAIN CHAT AREA --}}
        <div class="chat-main">
            @if(isset($partner) && $partner)
                {{-- CHAT HEADER --}}
                <div class="chat-header">
                    <div class="chat-partner-info">
                        <div class="partner-avatar">
                            @if(isset($partner->profile_photo_path) && $partner->profile_photo_path)
                                <img src="{{ asset('storage/' . $partner->profile_photo_path) }}" alt="Avatar">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                            <span class="status-dot online"></span>
                        </div>
                        <div class="partner-details">
                            <h5 class="partner-name">{{ $partner->display_name ?? 'User' }}</h5>
                            <p class="partner-status">
                                <span class="status-badge online">
                                    <i class="fas fa-circle"></i> Online
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="btn-icon" title="Info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>

                {{-- MESSAGES AREA --}}
                <div id="chat-messages" class="chat-messages">
                    @foreach($messages as $message)
                        @php
                            // FIX: Menggunakan $myId yang dikirim dari controller
                            $isMe = $message->sender_id == $myId && $message->sender_type == $myType;
                            $mediaPath = $message->media_path ?? null;
                            $mediaType = $message->media_type ?? null;
                            $isImage = $mediaType && Str::startsWith($mediaType, 'image');
                        @endphp
                        
                        <div class="message-wrapper {{ $isMe ? 'message-sent' : 'message-received' }}">
                            @if(!$isMe)
                                <div class="message-avatar">
                                    @if(isset($partner->profile_photo_path) && $partner->profile_photo_path)
                                        <img src="{{ asset('storage/' . $partner->profile_photo_path) }}" alt="Avatar">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="message-bubble">
                                {{-- TAMPILAN MEDIA --}}
                                @if($mediaPath)
                                    <div class="message-media {{ $isImage ? 'is-image' : 'is-document' }}">
                                        @if($isImage)
                                            <a href="{{ asset('storage/' . $mediaPath) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $mediaPath) }}" alt="Gambar" class="img-fluid">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $mediaPath) }}" target="_blank" class="{{ $isMe ? 'text-white' : 'text-primary' }}">
                                                <i class="fas fa-file-alt"></i> {{ basename($mediaPath) }} 
                                                <span class="small d-block">{{ $mediaType }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @if($message->message)
                                    <p class="message-text">{{ $message->message }}</p>
                                @endif
                                
                                <div class="message-meta">
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                    @if($isMe)
                                        @if(isset($message->is_read) && $message->is_read)
                                            <i class="fas fa-check-double message-status read" title="Dibaca"></i>
                                        @else
                                            <i class="fas fa-check message-status sent" title="Terkirim"></i>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- INPUT AREA --}}
                <div class="chat-input-area">
                    <form id="chat-form" class="chat-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="receiver-id" value="{{ $partnerId }}">
                        
                        {{-- INPUT FILE TERSEMBUNYI --}}
                        <input type="file" name="media" id="media-input" style="display: none;" 
                               accept="image/*,.pdf,.doc,.docx,.mp4,.mov" />
                        
                        <button type="button" class="btn-attach" title="Lampiran" id="btn-attach-media">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        
                        <div class="input-wrapper">
                            <input type="text" id="message-input" class="message-input" 
                                   placeholder="Ketik pesan..." autocomplete="off">
                            <div id="media-filename" class="text-muted small mt-1"></div>
                        </div>
                        
                        <button type="submit" class="btn-send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            @else
                {{-- EMPTY STATE --}}
                <div class="chat-empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4>Selamat Datang di MedEase Chat</h4>
                    <p>Pilih {{ $myRole == 'pasien' ? 'tenaga medis' : 'pasien' }} untuk memulai konsultasi</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($myRole == 'medis')
            </div>
        </div>
    </section>
</div>
@endif

@endsection

@push('styles')
<style>
    * { box-sizing: border-box; }
    
    :root {
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f3f4f6;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --border-color: rgba(57, 166, 22, 0.15);
        --shadow: 0 8px 30px rgba(57, 166, 22, 0.1);
    }

    /* Penyesuaian agar tidak bentrok dengan layout Dashboard */
    .page-heading .chat-container {
        padding: 0;
        margin: 0;
        max-width: 100%;
    }
    .page-heading .chat-wrapper {
        border: none;
        box-shadow: none;
        border-radius: 0;
        height: 75vh;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
    }

    /* ===== HEADER BANNER (Only for Patient) ===== */
    .chat-header-banner {
        margin-bottom: 30px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 24px;
        background: var(--grad);
        padding: 28px 40px;
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
        width: 65px;
        height: 65px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
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
        font-size: 1.8rem;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        display: flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.95);
        font-size: 0.95rem;
        margin: 0;
        font-weight: 500;
    }

    .hero-decoration {
        width: 85px;
        height: 85px;
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
        font-size: 38px;
        color: white;
        z-index: 2;
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

    /* ===== CHAT CONTAINER ===== */
    .chat-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px 40px;
    }

    .chat-wrapper {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 0;
        background: var(--bg-primary);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        height: 75vh;
        min-height: 600px;
    }

    /* ===== SIDEBAR ===== */
    .chat-sidebar {
        background: var(--bg-secondary);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .sidebar-header {
        padding: 24px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-title i {
        color: var(--p1);
    }

    .search-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 45px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 0.9rem;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    /* ===== CONTACTS LIST ===== */
    .contacts-list {
        overflow-y: auto;
        flex: 1;
    }

    .contacts-list::-webkit-scrollbar { width: 6px; }
    .contacts-list::-webkit-scrollbar-track { background: var(--bg-secondary); }
    .contacts-list::-webkit-scrollbar-thumb { background: var(--p1); border-radius: 10px; }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        text-decoration: none;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .contact-item:hover {
        background: rgba(57, 166, 22, 0.05);
    }

    .contact-item.active {
        background: var(--grad);
        color: white;
        border-left: 4px solid var(--p3);
    }

    .contact-avatar {
        position: relative;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid rgba(57, 166, 22, 0.2);
    }

    .contact-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .status-indicator.online {
        background: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .contact-info {
        flex: 1;
        min-width: 0;
    }

    .contact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .contact-name {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .contact-item.active .contact-name {
        color: white;
    }

    .contact-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .contact-item.active .contact-time {
        color: rgba(255, 255, 255, 0.8);
    }

    .contact-message {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .contact-item.active .contact-message {
        color: rgba(255, 255, 255, 0.9);
    }

    .empty-contacts {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-contacts i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    /* ===== CHAT MAIN ===== */
    .chat-main {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: var(--bg-primary);
    }

    /* ===== CHAT HEADER ===== */
    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 28px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
    }

    .chat-partner-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .partner-avatar {
        position: relative;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        overflow: hidden;
        border: 3px solid rgba(57, 166, 22, 0.2);
    }

    .partner-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-dot {
        position: absolute;
        bottom: 3px;
        right: 3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 3px solid white;
    }

    .status-dot.online {
        background: #10b981;
    }

    .partner-details {
        flex: 1;
    }

    .partner-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .partner-status {
        margin: 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 12px;
    }

    .status-badge.online {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.25));
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-badge i {
        font-size: 0.6rem;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .chat-actions {
        display: flex;
        gap: 10px;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        background: var(--bg-secondary);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-icon:hover {
        background: var(--p1);
        color: white;
    }

    /* ===== MESSAGES AREA ===== */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: var(--bg-secondary);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .chat-messages::-webkit-scrollbar { width: 8px; }
    .chat-messages::-webkit-scrollbar-track { background: var(--bg-tertiary); }
    .chat-messages::-webkit-scrollbar-thumb { background: var(--p1); border-radius: 10px; }

    .message-wrapper {
        display: flex;
        gap: 12px;
        max-width: 75%;
        animation: messageSlideIn 0.3s ease;
    }

    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-wrapper.message-sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-wrapper.message-received {
        align-self: flex-start;
    }

    .message-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid var(--border-color);
    }

    .message-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 16px;
        position: relative;
        word-wrap: break-word;
    }

    .message-sent .message-bubble {
        background: var(--grad);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-received .message-bubble {
        background: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        border-bottom-left-radius: 4px;
    }

    .message-text {
        margin: 0 0 6px 0;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* Media Styles */
    .message-media img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 8px;
        max-height: 250px;
        object-fit: cover;
    }
    .message-media a {
        text-decoration: none;
        font-weight: 500;
        display: block;
        padding: 8px 0;
    }
    .message-media.is-document a {
        color: var(--p3); 
    }
    .message-sent .message-media.is-document a {
        color: white; 
    }

    .message-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        margin-top: 4px;
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
    }

    .message-status {
        font-size: 0.8rem;
    }

    .message-status.read {
        color: #10b981;
    }
    .message-status.sent {
        color: rgba(255, 255, 255, 0.6);
    }

    /* ===== CHAT INPUT ===== */
    .chat-input-area {
        padding: 20px 24px;
        background: var(--bg-primary);
        border-top: 1px solid var(--border-color);
    }

    .chat-form {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-attach {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        border: none;
        background: var(--bg-secondary);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .btn-attach:hover {
        background: var(--p1);
        color: white;
        transform: rotate(15deg);
    }

    .input-wrapper {
        flex: 1;
    }

    .message-input {
        width: 100%;
        padding: 14px 20px;
        border: 2px solid var(--border-color);
        border-radius: 14px;
        font-size: 0.95rem;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
    }

    .message-input:focus {
        outline: none;
        border-color: var(--p1);
        background: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    .btn-send {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        border: none;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(57, 166, 22, 0.3);
    }

    .btn-send:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(57, 166, 22, 0.45);
    }

    .btn-send:active {
        transform: scale(0.95);
    }

    /* ===== EMPTY STATE ===== */
    .chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 40px;
        background: var(--bg-secondary);
    }

    .empty-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.1), rgba(57, 166, 22, 0.2));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
    }

    .empty-icon i {
        font-size: 4rem;
        color: var(--p1);
        opacity: 0.6;
    }

    .chat-empty-state h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 12px 0;
    }

    .chat-empty-state p {
        font-size: 1rem;
        color: var(--text-secondary);
        margin: 0;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .chat-wrapper {
            grid-template-columns: 1fr;
            height: auto;
            min-height: 70vh;
        }

        .chat-sidebar {
            display: none;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            padding: 24px;
        }

        .hero-decoration {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .chat-header {
            padding: 16px;
        }

        .partner-avatar {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }

        .message-wrapper {
            max-width: 85%;
        }

        .chat-input-area {
            padding: 16px;
        }

        .btn-attach {
            width: 40px;
            height: 40px;
        }

        .btn-send {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chat-messages');
    const partnerId = document.getElementById('receiver-id')?.value;
    const chatForm = document.getElementById('chat-form');
    const btnAttach = document.getElementById('btn-attach-media');
    const mediaInput = document.getElementById('media-input');
    const mediaFilenameDisplay = document.getElementById('media-filename');
    const btnSend = chatForm ? chatForm.querySelector('.btn-send') : null;
    const messageInput = document.getElementById('message-input');
    const csrfToken = '{{ csrf_token() }}';
    const myType = '{{ $myType ?? "" }}';

    // Scroll otomatis ke bawah
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
        
        if (partnerId) {
            markReadStatus(partnerId);
        }
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Format media display
    function formatMediaDisplay(mediaPath, mediaType) {
        if (!mediaPath || !mediaType) return '';

        const mediaUrl = `/storage/${mediaPath}`;
        const isImage = mediaType.startsWith('image');
        const fileName = mediaPath.split('/').pop();
        
        if (isImage) {
            return `
                <div class="message-media is-image">
                    <a href="${mediaUrl}" target="_blank">
                        <img src="${mediaUrl}" alt="Gambar" class="img-fluid">
                    </a>
                </div>
            `;
        } else {
            return `
                <div class="message-media is-document">
                    <a href="${mediaUrl}" target="_blank" class="text-white">
                        <i class="fas fa-file-alt"></i> ${fileName}
                        <span class="small d-block">${mediaType}</span>
                    </a>
                </div>
            `;
        }
    }

    // Add message to chat
    function addMessageToChat(messageText, isMe, mediaPath = null, mediaType = null) {
        if (!chatContainer) return;

        const messageWrapper = document.createElement('div');
        messageWrapper.className = `message-wrapper ${isMe ? 'message-sent' : 'message-received'}`;
        
        const currentTime = new Date().toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const mediaHTML = formatMediaDisplay(mediaPath, mediaType);
        const messageTextHTML = messageText ? 
            `<p class="message-text">${escapeHtml(messageText)}</p>` : '';

        messageWrapper.innerHTML = `
            <div class="message-bubble">
                ${mediaHTML}
                ${messageTextHTML}
                <div class="message-meta">
                    <span class="message-time">${currentTime}</span>
                    ${isMe ? '<i class="fas fa-check message-status sent" title="Terkirim"></i>' : ''}
                </div>
            </div>
        `;

        chatContainer.appendChild(messageWrapper);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Mark read status
    function markReadStatus(id) {
        fetch(`/chat/mark-read/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            document.querySelectorAll('.message-received .message-status.sent').forEach(el => {
                el.classList.add('fa-check-double');
                el.classList.add('read');
                el.classList.remove('fa-check');
            });
        })
        .catch(error => console.error('Error mark read:', error));
    }

    // Button attach media
    if (btnAttach) {
        btnAttach.addEventListener('click', () => {
            mediaInput.click();
        });
    }

    // Show filename
    if (mediaInput) {
        mediaInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                mediaFilenameDisplay.textContent = `File dipilih: ${this.files[0].name}`;
            } else {
                mediaFilenameDisplay.textContent = '';
            }
        });
    }

    // Handle submit
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageText = messageInput.value.trim();
            const mediaFile = mediaInput.files[0];
            
            if(!messageText && !mediaFile) return;

            if (btnSend) {
                const originalHTML = btnSend.innerHTML;
                btnSend.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btnSend.disabled = true;
            }
            
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('receiver_id', partnerId);
            
            if (messageText) {
                formData.append('message', messageText);
            }
            if (mediaFile) {
                formData.append('media', mediaFile);
            }

            fetch('{{ route("chat.send") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success' || data.success) {
                    addMessageToChat(messageText, true, data.media_path || null, data.media_type || null); 
                    
                    messageInput.value = '';
                    mediaInput.value = '';
                    mediaFilenameDisplay.textContent = '';
                } else {
                    alert('Gagal mengirim: ' + (data.message || 'Error tidak diketahui'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengirim pesan. Silakan coba lagi.');
            })
            .finally(() => {
                if (btnSend) {
                    btnSend.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    btnSend.disabled = false;
                }
            });
        });
    }
});
</script>
@endpush
