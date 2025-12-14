@php
    $layout = (Auth::guard('tenaga_medis')->check()) ? 'layouts.tenaga_medis' : 'layouts.main';
@endphp

@extends($layout)

@section('title', 'Chat Konsultasi')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --chat-bg: #e5ddd5;
        --sidebar-bg: #ffffff;
        --my-msg-bg: #d9fdd3;
        --their-msg-bg: #ffffff;
        --border-color: #e9edef;
        --active-chat: #f0f2f5;
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --text-primary: #111b21;
        --text-secondary: #667781;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 30px rgba(57, 166, 22, 0.15);
    }

    [data-theme="dark"] {
        --chat-bg: #0b141a;
        --sidebar-bg: #111b21;
        --my-msg-bg: #005c4b;
        --their-msg-bg: #202c33;
        --border-color: #222d34;
        --active-chat: #2a3942;
        --text-primary: #e9edef;
        --text-secondary: #8696a0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.4);
        --shadow-lg: 0 8px 30px rgba(0,0,0,0.5);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }

    .chat-wrapper {
        width: 100%;
        max-width: 1600px;
        margin: 20px auto;
        padding: 0 20px;
    }

    .app-container {
        display: flex;
        width: 100%;
        height: calc(100vh - 180px);
        max-height: 800px;
        min-height: 600px;
        background-color: var(--sidebar-bg);
        overflow: hidden;
        position: relative;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sidebar-area {
        width: 30%;
        min-width: 280px;
        max-width: 380px;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
        background-color: var(--sidebar-bg);
        height: 100%;
        z-index: 2;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 16px 18px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .sidebar-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .sidebar-header h3 {
        margin: 0;
        color: white;
        font-size: 1.3rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1;
    }

    .sidebar-header h3 i {
        font-size: 1.5rem;
        background: rgba(255,255,255,0.2);
        padding: 8px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .header-actions {
        display: flex;
        gap: 8px;
        z-index: 1;
    }

    .header-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .header-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .search-container {
        padding: 12px 14px;
        background-color: var(--sidebar-bg);
        box-shadow: var(--shadow-sm);
        z-index: 1;
        flex-shrink: 0;
    }

    .search-box {
        background-color: var(--active-chat);
        border-radius: 10px;
        display: flex;
        align-items: center;
        padding: 0 14px;
        height: 42px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .search-box:focus-within {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .search-box i {
        color: var(--text-secondary);
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    .search-box:focus-within i {
        color: var(--p1);
    }

    .search-box input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
        margin-left: 10px;
        font-size: 13px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .search-box input::placeholder {
        color: var(--text-secondary);
        font-weight: 400;
    }

    #searchResults {
        background: var(--sidebar-bg);
        border-radius: 10px;
        margin-top: 8px;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        box-shadow: var(--shadow-md);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .search-item {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item:hover {
        background-color: var(--active-chat);
        padding-left: 18px;
    }

    .search-item img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
    }

    .search-item-info {
        flex: 1;
    }

    .search-item-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 13px;
    }

    .search-item-role {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .contact-list {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .contact-list::-webkit-scrollbar,
    .messages-container::-webkit-scrollbar {
        width: 5px;
    }

    .contact-list::-webkit-scrollbar-track,
    .messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .contact-list::-webkit-scrollbar-thumb,
    .messages-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }

    .contact-list::-webkit-scrollbar-thumb:hover,
    .messages-container::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }

    .contact-item {
        display: flex;
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .contact-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: var(--p1);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .contact-item:hover {
        background-color: var(--active-chat);
        padding-left: 18px;
    }

    .contact-item.active {
        background: linear-gradient(90deg, rgba(57, 166, 22, 0.08) 0%, transparent 100%);
    }

    .contact-item.active::before {
        transform: scaleY(1);
    }

    .contact-avatar {
        position: relative;
        margin-right: 12px;
    }

    .contact-avatar img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .contact-item:hover .contact-avatar img,
    .contact-item.active .contact-avatar img {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 11px;
        height: 11px;
        background: #4ade80;
        border: 2px solid var(--sidebar-bg);
        border-radius: 50%;
        animation: blink 2s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .contact-info {
        flex: 1;
        overflow: hidden;
    }

    .contact-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
        align-items: center;
    }

    .contact-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-primary);
    }

    .contact-time {
        font-size: 10px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .contact-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .last-message {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .unread-badge {
        background: linear-gradient(135deg, var(--p1), var(--p2));
        color: white;
        font-size: 10px;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        padding: 0 5px;
        box-shadow: 0 2px 8px rgba(57, 166, 22, 0.3);
        animation: pop 0.3s ease;
    }

    @keyframes pop {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        background-color: var(--chat-bg);
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        opacity: 0.98;
        overflow: hidden;
    }

    .chat-header {
        height: 65px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        padding: 0 18px;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-md);
        z-index: 10;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .chat-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .btn-back {
        display: none;
        margin-right: 14px;
        color: white;
        font-size: 18px;
        cursor: pointer;
        background: rgba(255,255,255,0.2);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .chat-header-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 14px;
        border: 3px solid rgba(255,255,255,0.5);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .chat-header-info {
        flex: 1;
        z-index: 1;
    }

    .chat-header-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: white;
    }

    .chat-header-info p {
        margin: 2px 0 0 0;
        font-size: 11px;
        color: rgba(255,255,255,0.9);
        font-weight: 500;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
        z-index: 1;
    }

    .chat-action-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .chat-action-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .message-bubble {
        max-width: 65%;
        padding: 9px 13px;
        border-radius: 10px;
        position: relative;
        font-size: 13.5px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        word-wrap: break-word;
        animation: messageSlide 0.3s ease;
    }

    @keyframes messageSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .msg-me {
        align-self: flex-end;
        background: linear-gradient(135deg, #d9fdd3, #c8f0c2);
        border-top-right-radius: 3px;
        border: 1px solid rgba(57, 166, 22, 0.1);
    }

    .msg-them {
        align-self: flex-start;
        background-color: var(--their-msg-bg);
        border-top-left-radius: 3px;
        border: 1px solid var(--border-color);
    }

    .msg-time {
        float: right;
        margin-left: 10px;
        margin-top: 3px;
        font-size: 9px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 3px;
        font-weight: 500;
    }

    .tick-read { 
        color: #4ade80;
        font-size: 11px;
    }
    
    .tick-sent { 
        color: var(--text-secondary);
        font-size: 11px;
    }

    .msg-image img {
        max-width: 100%;
        border-radius: 8px;
        margin-bottom: 5px;
        max-height: 300px;
        object-fit: contain;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .msg-image img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .media-preview-container {
        padding: 12px 16px;
        background: var(--active-chat);
        border-top: 2px solid var(--p1);
        display: none;
        animation: slideUpMedia 0.3s ease;
        flex-shrink: 0;
    }

    @keyframes slideUpMedia {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .media-preview-box {
        position: relative;
        display: inline-block;
    }

    .media-preview-box img {
        height: 100px;
        border-radius: 10px;
        border: 3px solid var(--p1);
        box-shadow: var(--shadow-md);
    }

    .btn-close-preview {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    .btn-close-preview:hover {
        transform: scale(1.15);
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .preview-label {
        font-size: 11px;
        margin-top: 6px;
        color: var(--text-secondary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .preview-label i {
        color: var(--p1);
    }

    .chat-footer {
        padding: 12px 16px;
        background-color: var(--active-chat);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        min-height: 65px;
    }

    .footer-btn {
        background: none;
        border: none;
        font-size: 22px;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 6px;
        transition: all 0.3s ease;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .footer-btn:hover {
        color: var(--p1);
        background: rgba(57, 166, 22, 0.1);
        transform: scale(1.1);
    }

    .input-wrapper {
        flex: 1;
        background-color: var(--sidebar-bg);
        border-radius: 20px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .input-wrapper:focus-within {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .input-wrapper input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 14px;
        background: transparent;
        color: var(--text-primary);
        font-family: inherit;
        font-weight: 500;
    }

    .input-wrapper input::placeholder {
        color: var(--text-secondary);
        font-weight: 400;
    }

    .btn-send {
        background: linear-gradient(135deg, var(--p1), var(--p2));
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.3);
        opacity: 0.6;
        transform: scale(0.9);
        flex-shrink: 0;
    }
    
    .btn-send.active {
        opacity: 1;
        transform: scale(1);
    }

    .btn-send:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.5);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.03), rgba(29, 130, 8, 0.05));
        text-align: center;
        color: var(--text-secondary);
        position: relative;
        overflow: hidden;
        padding: 30px 20px;
    }

    .empty-state::before {
        content: '';
        position: absolute;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(57, 166, 22, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }

    .empty-state-icon {
        font-size: 80px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: float 3s ease-in-out infinite;
        position: relative;
        z-index: 1;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }

    .empty-state h2 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: var(--text-primary);
        position: relative;
        z-index: 1;
    }

    .empty-state p {
        font-size: 0.9rem;
        line-height: 1.5;
        max-width: 450px;
        color: var(--text-secondary);
        position: relative;
        z-index: 1;
    }

    .empty-state-features {
        display: flex;
        gap: 16px;
        margin-top: 24px;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
        justify-content: center;
    }

    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 12px;
        background: var(--sidebar-bg);
        border-radius: 10px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .feature-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .feature-item i {
        font-size: 24px;
        color: var(--p1);
    }

    .feature-item span {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .loading-state {
        text-align: center;
        padding: 20px;
        color: var(--text-secondary);
    }

    .loading-spinner {
        display: inline-block;
        width: 35px;
        height: 35px;
        border: 3px solid var(--border-color);
        border-top-color: var(--p1);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        body {
            background: var(--sidebar-bg);
        }

        .chat-wrapper {
            margin: 0;
            padding: 0;
        }

        .app-container {
            margin: 0;
            border-radius: 0;
            max-width: 100%;
            height: 100vh;
            max-height: 100vh;
            min-height: 100vh;
        }

        .sidebar-area {
            width: 100%;
            max-width: none;
            min-width: auto;
        }

        .chat-area {
            display: none;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
        }
        
        .app-container.chat-open .sidebar-area {
            display: none;
        }

        .app-container.chat-open .chat-area {
            display: flex;
        }

        .btn-back {
            display: flex !important;
        }

        .message-bubble {
            max-width: 75%;
        }

        .sidebar-header h3 {
            font-size: 1.2rem;
        }

        .sidebar-header h3 i {
            font-size: 1.3rem;
            padding: 6px;
        }

        .empty-state h2 {
            font-size: 1.3rem;
        }

        .empty-state-icon {
            font-size: 60px;
        }

        .empty-state p {
            font-size: 0.85rem;
        }

        .empty-state-features {
            flex-direction: column;
            width: 100%;
            max-width: 300px;
        }

        .feature-item {
            width: 100%;
        }

        .contact-name {
            font-size: 13px;
        }

        .last-message {
            max-width: 140px;
            font-size: 11px;
        }

        .chat-footer {
            padding: 10px 12px;
        }

        .input-wrapper {
            padding: 8px 14px;
        }
    }

    [data-theme="dark"] .msg-me {
        background: linear-gradient(135deg, #005c4b, #004d3f);
    }

    [data-theme="dark"] .search-item img,
    [data-theme="dark"] .contact-avatar img {
        border-color: var(--border-color);
    }

    [data-theme="dark"] .empty-state {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(29, 130, 8, 0.08));
    }
</style>
@endpush

@section('content')
<div class="chat-wrapper">
    <div class="app-container" id="appContainer">
        
        <div class="sidebar-area">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-comments"></i>
                    Chat Konsultasi
                </h3>
                <div class="header-actions">
                    <button class="header-btn" title="Refresh" onclick="loadContacts()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="contactSearch" 
                           placeholder="{{ Auth::guard('tenaga_medis')->check() ? 'Cari atau mulai chat baru...' : 'Cari dokter...' }}">
                </div>
                <div id="searchResults"></div>
            </div>

            <div class="contact-list" id="contactList">
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 10px; font-size: 13px;">Memuat percakapan...</p>
                </div>
            </div>
        </div>

        <div class="chat-area" id="chatArea">
            
            <div id="emptyState" class="empty-state">
                <i class="fas fa-comment-medical empty-state-icon"></i>
                <h2>MedEase Chat</h2>
                <p>Kirim dan terima pesan konsultasi kesehatan secara real-time.<br>Pilih kontak untuk memulai percakapan.</p>
                
                <div class="empty-state-features">
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Encrypted</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-image"></i>
                        <span>Kirim Gambar</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bolt"></i>
                        <span>Real-time</span>
                    </div>
                </div>
            </div>

            <div id="activeChatView" style="display: none; flex-direction: column; height: 100%;">
                
                <div class="chat-header">
                    <i class="fas fa-arrow-left btn-back" onclick="closeChat()"></i>
                    <img src="" alt="" class="chat-header-avatar" id="headerAvatar">
                    <div class="chat-header-info">
                        <h4 id="headerName">Nama Pengguna</h4>
                        
                    </div>
                    <div class="chat-header-actions">
                        <button class="chat-action-btn" title="Info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="messages-container" id="messagesContainer">
                </div>

                <div id="mediaPreview" class="media-preview-container">
                    <div class="media-preview-box">
                        <img id="previewImage" src="" alt="Preview">
                        <button class="btn-close-preview" onclick="clearMedia()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="preview-label">
                        <i class="fas fa-check-circle"></i>
                        Gambar siap dikirim
                    </div>
                </div>

                <form id="chatForm" class="chat-footer">
                    @csrf
                    <input type="file" id="mediaInput" name="media" accept="image/*" style="display: none;" onchange="handleFileSelect(this)">
                    
                    <button type="button" class="footer-btn" onclick="document.getElementById('mediaInput').click()" title="Kirim Foto">
                        <i class="fas fa-camera"></i>
                    </button>

                    <div class="input-wrapper">
                        <input type="text" id="messageInput" placeholder="Ketik pesan..." autocomplete="off">
                    </div>

                    <button type="submit" class="btn-send" id="btnSend">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activePartnerId = null;
    let lastMessageId = 0;
    const isDoctor = {{ Auth::guard('tenaga_medis')->check() ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';
    let pollingInterval = null;
    let messageIds = new Set(); // PENTING: Track ID untuk prevent duplicate

    function loadContacts() {
        fetch("{{ route('chat.contacts') }}")
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('contactList');
                list.innerHTML = ''; 
                if(data.length === 0) {
                    const msg = isDoctor ? 'Belum ada pesan. Cari pasien untuk memulai.' : 'Belum ada dokter.';
                    list.innerHTML = `<div style="padding:40px 20px; text-align:center; color:var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size:48px; margin-bottom:12px; opacity:0.3;"></i>
                        <p style="font-weight:500;">${msg}</p>
                    </div>`;
                    return;
                }
                data.forEach(c => {
                    const activeClass = (c.id == activePartnerId) ? 'active' : '';
                    const avatar = c.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(c.name)}&background=39A616&color=fff&bold=true`;
                    const time = c.last_time || '';
                    const lastMsg = c.last_message ? (c.last_message.length > 30 ? c.last_message.substring(0,30)+'...' : c.last_message) : '<i style="font-size:11px; color:#aaa;">Mulai obrolan...</i>';
                    const badge = c.unread > 0 ? `<div class="unread-badge">${c.unread}</div>` : '';
                    const statusIndicator = c.is_online ? '<div class="status-indicator"></div>' : '';
                    const html = `<div class="contact-item ${activeClass}" onclick="openChat(${c.id}, '${escapeHtml(c.name)}', '${avatar}')"><div class="contact-avatar"><img src="${avatar}" alt="${escapeHtml(c.name)}">${statusIndicator}</div><div class="contact-info"><div class="contact-top"><span class="contact-name">${escapeHtml(c.name)}</span><span class="contact-time">${time}</span></div><div class="contact-bottom"><span class="last-message">${lastMsg}</span>${badge}</div></div></div>`;
                    list.innerHTML += html;
                });
            }).catch(err => console.error('Error:', err));
    }

    function openChat(id, name, avatar) {
        if(activePartnerId === id) return;
        activePartnerId = id;
        lastMessageId = 0;
        messageIds.clear(); // PENTING: Reset tracking
        
        document.getElementById('appContainer').classList.add('chat-open');
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('activeChatView').style.display = 'flex';
        document.getElementById('headerName').innerText = name;
        document.getElementById('headerAvatar').src = avatar;
        const msgContainer = document.getElementById('messagesContainer');
        msgContainer.innerHTML = '<div class="loading-state"><div class="loading-spinner"></div><p style="margin-top:10px; font-size:12px;">Memuat pesan...</p></div>';
        fetchMessages(id, true);
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('contactSearch').value = '';
    }

    function fetchMessages(partnerId, isInitial = false) {
        let url = `{{ url('/chat/messages') }}/${partnerId}`;
        if (!isInitial) url += `?last_id=${lastMessageId}`;
        fetch(url).then(res => res.json()).then(data => {
            if(isInitial) { 
                renderMessagesInitial(data.messages); 
            } else if (data.messages.length > 0) { 
                appendMessages(data.messages); 
            }
            if (data.messages.length > 0) { 
                lastMessageId = data.messages[data.messages.length - 1].id; 
            }
        }).catch(err => console.error(err));
    }

    function renderMessagesInitial(messages) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = ''; 
        messageIds.clear(); // PENTING: Clear tracking
        
        if (messages.length === 0) {
            container.innerHTML = `<div style="text-align:center; padding:40px 20px; color:var(--text-secondary);"><div style="background:var(--sidebar-bg); display:inline-block; padding:10px 20px; border-radius:10px; font-size:12px; font-weight:500; box-shadow:var(--shadow-sm);"><i class="fas fa-lock" style="color:var(--p1);"></i> Pesan dienkripsi end-to-end</div><p style="margin-top:16px; font-size:13px;">Mulai percakapan dengan mengirim pesan</p></div>`;
            return;
        }
        
        messages.forEach(msg => { 
            if (!messageIds.has(msg.id)) { // PENTING: Cek duplicate
                messageIds.add(msg.id);
                container.appendChild(createBubble(msg)); 
            }
        });
        scrollToBottom();
    }

    function appendMessages(messages) {
        const container = document.getElementById('messagesContainer');
        messages.forEach(msg => { 
            if (!messageIds.has(msg.id)) { // PENTING: Cek duplicate
                messageIds.add(msg.id);
                container.appendChild(createBubble(msg)); 
            }
        });
        if(messages.length > 0) {
            scrollToBottom(true);
        }
    }

    function createBubble(msg) {
        const div = document.createElement('div');
        const isMe = msg.sender === 'me';
        div.className = `message-bubble ${isMe ? 'msg-me' : 'msg-them'}`;
        div.setAttribute('data-message-id', msg.id); // PENTING: Attribute tracking
        
        let mediaHtml = '';
        if (msg.media_path) {
            const url = `/storage/${msg.media_path}`;
            if (msg.media_type && msg.media_type.startsWith('image/')) {
                mediaHtml = `<div class="msg-image"><a href="${url}" target="_blank"><img src="${url}" loading="lazy" alt="Attachment"></a></div>`;
            } else {
                mediaHtml = `<div class="msg-file"><a href="${url}" target="_blank" style="color:var(--p1); text-decoration:none;"><i class="fas fa-file-alt"></i> Lampiran</a></div>`;
            }
        }
        
        let ticks = '';
        if(isMe) { 
            ticks = msg.is_read ? '<i class="fas fa-check-double tick-read"></i>' : '<i class="fas fa-check tick-sent"></i>'; 
        }
        
        div.innerHTML = `${mediaHtml}${msg.message ? `<span>${escapeHtml(msg.message)}</span>` : ''}<div class="msg-time">${msg.time || ''} ${ticks}</div>`;
        
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.justifyContent = isMe ? 'flex-end' : 'flex-start';
        wrapper.appendChild(div);
        return wrapper;
    }

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const text = document.getElementById('messageInput').value.trim();
        const fileInput = document.getElementById('mediaInput');
        
        if((!text && fileInput.files.length === 0) || !activePartnerId) return;
        
        const formData = new FormData();
        formData.append('receiver_id', activePartnerId);
        formData.append('_token', csrfToken);
        if(text) formData.append('message', text);
        if(fileInput.files.length > 0) formData.append('media', fileInput.files[0]);
        
        // HAPUS optimistic UI - langsung clear input
        document.getElementById('messageInput').value = '';
        clearMedia();
        
        fetch("{{ route('chat.send') }}", { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { 
                if(data.status === 'success') { 
                    // PENTING: Delay singkat untuk menghindari race condition
                    setTimeout(() => {
                        fetchMessages(activePartnerId); 
                        loadContacts(); 
                    }, 150);
                } 
            })
            .catch(err => console.error(err));
    });

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('mediaPreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearMedia() {
        document.getElementById('mediaInput').value = '';
        document.getElementById('mediaPreview').style.display = 'none';
        document.getElementById('previewImage').src = '';
    }

    const searchInput = document.getElementById('contactSearch');
    const searchRes = document.getElementById('searchResults');
    
    if(isDoctor) {
        searchInput.addEventListener('keyup', function() {
            const q = this.value;
            if(q.length < 2) { searchRes.style.display = 'none'; return; }
            
            fetch(`{{ route('chat.search') }}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    searchRes.innerHTML = '';
                    if(data.length > 0) {
                        searchRes.style.display = 'block';
                        data.forEach(u => {
                            const avatar = u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=39A616&color=fff&bold=true`;
                            searchRes.innerHTML += `<div class="search-item" onclick="startNewChat(${u.id}, '${escapeHtml(u.name)}', '${avatar}')"><img src="${avatar}" alt="${escapeHtml(u.name)}"><div class="search-item-info"><div class="search-item-name">${escapeHtml(u.name)}</div><div class="search-item-role">${u.role || 'Pasien'}</div></div></div>`;
                        });
                    } else { 
                        searchRes.style.display = 'none'; 
                    }
                })
                .catch(err => console.error(err));
        });
    } else {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.contact-item').forEach(item => {
                const name = item.querySelector('.contact-name').innerText.toLowerCase();
                item.style.display = name.includes(filter) ? 'flex' : 'none';
            });
        });
    }

    function startNewChat(id, name, avatar) {
        searchRes.style.display = 'none';
        searchInput.value = '';
        openChat(id, name, avatar);
    }

    function scrollToBottom(smooth = false) {
        const c = document.getElementById('messagesContainer');
        if(smooth) c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' });
        else c.scrollTop = c.scrollHeight;
    }

    function closeChat() {
        document.getElementById('appContainer').classList.remove('chat-open');
        document.getElementById('emptyState').style.display = 'flex';
        document.getElementById('activeChatView').style.display = 'none';
        activePartnerId = null;
        messageIds.clear(); // Clear tracking
    }

    function escapeHtml(text) {
        if(!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    const btnSend = document.getElementById('btnSend');
    document.getElementById('messageInput').addEventListener('input', function() {
        if(this.value.trim().length > 0) btnSend.classList.add('active');
        else btnSend.classList.remove('active');
    });

    loadContacts();
    
    // PENTING: Polling 5 detik (bukan 3 detik)
    pollingInterval = setInterval(() => {
        loadContacts();
        if(activePartnerId) { 
            fetchMessages(activePartnerId, false); 
        }
    }, 5000);

    window.addEventListener('beforeunload', () => {
        if(pollingInterval) clearInterval(pollingInterval);
    });
</script>
@endpush
