@extends('layouts.admin')

@section('title', 'Kelola Tenaga Medis')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(15px);
        color: white;
        padding: 16px 28px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
        animation: float 3s ease-in-out infinite;
        overflow: hidden;
    }

    .btn-add::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-add:hover::before {
        left: 100%;
    }

    .btn-add:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
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
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
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
        vertical-align: middle;
    }

    /* Doctor Avatar */
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
        border: 3px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.2);
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

    .btn-edit {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.25);
    }

    .btn-edit:hover {
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
    }

    .modal-card {
        background-color: var(--modal-bg);
        margin: 20px;
        border-radius: 24px;
        width: 90%;
        max-width: 650px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .modal-card-small {
        max-width: 500px;
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
    }

    .modal-header {
        text-align: center;
        margin-bottom: 32px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color);
    }

    .modal-header-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        border-bottom: none;
        padding-bottom: 10px;
    }

    .modal-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .modal-icon-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #e74c3c;
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

    .modal-title-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .modal-description {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 20px 0 30px 0;
        line-height: 1.6;
        text-align: center;
    }

    .modal-description strong {
        color: var(--text-primary);
        font-weight: 700;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .form-label i {
        color: var(--p1);
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

    .form-control:focus {
        outline: none;
        border-color: var(--p1);
        background-color: var(--bg-primary);
        box-shadow: 0 0 0 4px rgba(57, 166, 22, 0.1);
    }

    /* Error validation styling */
    #modalErrors > div {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 18px;
        border-radius: 14px;
        margin-bottom: 24px;
        border: 2px solid #ef4444;
    }

    [data-theme="dark"] #modalErrors > div,
    .dark-mode #modalErrors > div {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.3));
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.5);
    }

    #modalErrors ul {
        margin-top: 8px;
        margin-left: 20px;
        list-style-position: inside;
    }

    /* Info text */
    .info-text {
        color: var(--text-secondary);
        margin: 24px 0 12px 0;
        font-size: 0.9rem;
        border-top: 1px solid var(--border-color);
        padding-top: 16px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--border-color);
    }

    .btn-primary {
        flex: 1;
        background: var(--grad);
        color: #fff;
        border: none;
        padding: 16px 32px;
        border-radius: 16px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
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
        flex: 1;
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 2px solid var(--border-color);
        padding: 16px 32px;
        border-radius: 16px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-secondary:hover {
        background: var(--p1);
        color: white;
        border-color: var(--p1);
        transform: translateY(-3px);
    }

    .btn-danger {
        flex: 1;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        border: none;
        padding: 16px 32px;
        border-radius: 16px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-danger::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-danger:hover::before {
        left: 100%;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.5);
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

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
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

        .btn-add {
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

        .modal-content {
            padding: 24px;
        }

        .form-actions {
            flex-direction: column;
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

        .modal-card {
            width: 95%;
            margin: 10px;
        }

        .action-buttons {
            gap: 6px;
        }

        .btn-action {
            width: 36px;
            height: 36px;
        }
    }
</style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-icon"><i class="fas fa-user-md"></i></div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-hospital"></i>
                    <span>Medical Staff Management</span>
                </div>
                <h1 class="page-title">Kelola Tenaga Medis 👨‍⚕️</h1>
                <p class="page-subtitle"><i class="far fa-id-badge"></i> Manajemen Akun Tenaga Medis</p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <button class="btn-add" id="btnTambahAkun">
                    <span>Tambah Akun</span>
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
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

    {{-- Table card --}}
    <div class="schedule-container-modern">
        <div class="table-card-header">
            <h3 class="table-title"><i class="fas fa-list"></i> Daftar Tenaga Medis</h3>
            <span class="schedule-count"><i class="fas fa-users"></i> {{ $tenagaMedis->count() }} Akun Aktif</span>
        </div>

        <table class="schedule-table" id="tableTenaga">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-user-circle"></i> Foto</th>
                    <th><i class="fas fa-user"></i> Nama</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenagaMedis as $index => $akun)
                    <tr class="schedule-row" style="animation-delay: {{ $index * 0.05 }}s">
                        <td>
                            <span class="number-badge" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; background: linear-gradient(135deg, #6c757d, #5a6268); color: white; border-radius: 12px; font-weight: 700; font-size: 1rem; padding: 0 10px; box-shadow: 0 2px 8px rgba(108, 117, 125, 0.25);">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <div class="doctor-avatar">
                                {{ strtoupper(substr($akun->name,0,1)) }}
                            </div>
                        </td>
                        <td>
                            <span class="doctor-name" style="font-weight: 600; color: var(--text-primary);">
                                {{ $akun->name }}
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--text-secondary);">{{ $akun->email }}</span>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn-action btn-edit"
                                        data-id="{{ $akun->id }}"
                                        data-name="{{ $akun->name }}"
                                        data-email="{{ $akun->email }}"
                                        title="Edit Akun">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('admin.tenaga-medis.destroy', $akun->id) }}" method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action btn-hapus btn-delete" title="Hapus Akun">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-schedule">
                                <i class="fas fa-user-slash"></i>
                                <p>Belum ada akun tenaga medis.</p>
                                <small>Klik tombol "Tambah Akun" untuk membuat akun baru</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- MODAL FORM (CREATE / EDIT) --}}
<div id="akunModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="close-modal" id="closeModalBtn"><i class="fas fa-times"></i></button>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">
                    <i class="fas fa-user-plus" id="modalTitleIcon"></i> 
                    <span id="modalTitleText">Tambah Akun Tenaga Medis</span>
                </h2>
            </div>

            <form id="akunForm" method="POST" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="">
                <input type="hidden" name="record_id" id="record_id" value="">
                <input type="hidden" name="form_mode" id="form_mode" value="">

                <div id="modalErrors">
                    @if($errors->any() && old('form_mode'))
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul>
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                
                <p class="info-text">
                    <i class="fas fa-info-circle"></i> Kosongkan password jika tidak ingin mengubahnya.
                </p>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="btnCancelModal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> <span id="btnSubmitText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="deleteConfirmModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card modal-card-small">
        <button class="close-modal" id="closeDeleteModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content">
            <div class="modal-header modal-header-center">
                <div class="modal-icon modal-icon-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-title modal-title-danger">
                    <span>Konfirmasi Hapus</span>
                </h2>
            </div>

            <p class="modal-description">
                Apakah Anda yakin ingin menghapus akun ini? <br>
                <strong>Tindakan ini tidak dapat dibatalkan.</strong>
            </p>

            <div class="form-actions">
                <button type="button" class="btn-secondary" id="btnCancelDelete">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button type="button" class="btn-danger" id="btnConfirmDelete">
                    <i class="fas fa-trash"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Variabel Modal Buat/Edit ===
    const modal = document.getElementById('akunModal');
    const btnTambah = document.getElementById('btnTambahAkun');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('btnCancelModal');
    const form = document.getElementById('akunForm');
    const modalTitleText = document.getElementById('modalTitleText');
    const modalTitleIcon = document.getElementById('modalTitleIcon'); 
    const btnSubmitText = document.getElementById('btnSubmitText');
    const formMethod = document.getElementById('formMethod');
    const formModeInput = document.getElementById('form_mode');
    const recordIdInput = document.getElementById('record_id');

    // === Variabel Modal Konfirmasi Hapus ===
    const deleteModal = document.getElementById('deleteConfirmModal');
    const closeDeleteBtn = document.getElementById('closeDeleteModalBtn');
    const cancelDeleteBtn = document.getElementById('btnCancelDelete');
    const confirmDeleteBtn = document.getElementById('btnConfirmDelete');
    let formToDelete = null; 

    // Helper routes
    const storeRoute = '{{ route("admin.tenaga-medis.store") }}';
    const baseUpdateUrl = '{{ url("admin/tenaga-medis") }}'; 

    // === Fungsi Modal Buat/Edit ===
    function openModal(mode = 'create', data = null) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        const errorsBox = document.getElementById('modalErrors');
        errorsBox.innerHTML = '';

        form.reset();
        formMethod.value = '';
        formModeInput.value = mode;
        recordIdInput.value = '';

        if (mode === 'create') {
            modalTitleText.textContent = 'Tambah Akun Tenaga Medis';
            modalTitleIcon.className = 'fas fa-user-plus';
            btnSubmitText.textContent = 'Buat Akun';
            form.action = storeRoute;
        } else if (mode === 'edit' && data) {
            modalTitleText.textContent = 'Edit Akun Tenaga Medis';
            modalTitleIcon.className = 'fas fa-user-edit';
            btnSubmitText.textContent = 'Simpan Perubahan';
            form.action = baseUpdateUrl + '/' + data.id;
            formMethod.value = 'PUT';
            formModeInput.value = 'edit';
            recordIdInput.value = data.id;

            document.getElementById('name').value = data.name || '';
            document.getElementById('email').value = data.email || '';
        }
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // === Fungsi Modal Hapus ===
    function openDeleteModal() {
        deleteModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        formToDelete = null; 
    }

    // === Event Listener ===
    btnTambah.addEventListener('click', function() { openModal('create'); });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = {
                id: this.dataset.id,
                name: this.dataset.name,
                email: this.dataset.email
            };
            openModal('edit', data);
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            formToDelete = this.closest('form'); 
            openDeleteModal(); 
        });
    });

    closeDeleteBtn.addEventListener('click', closeDeleteModal);
    cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    confirmDeleteBtn.addEventListener('click', function() {
        if (formToDelete) {
            formToDelete.submit(); 
        }
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    window.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
        if (e.target === deleteModal) closeDeleteModal();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
        if (e.key === 'Escape' && deleteModal.style.display === 'flex') closeDeleteModal();
    });

    // Auto-hide success alert
    const alert = document.getElementById('autoHideAlert');
    if (alert) {
        setTimeout(() => { 
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.6s ease';
            setTimeout(() => alert.remove(), 600);
        }, 5000);
    }

    // Validation failed (re-open modal with old data)
    @if($errors->any() && old('form_mode'))
        (function() {
            const mode = @json(old('form_mode'));
            const oldId = @json(old('record_id'));
            const data = {
                id: oldId || null,
                name: @json(old('name')),
                email: @json(old('email'))
            };
            openModal(mode === 'edit' ? 'edit' : 'create', data);
        })();
    @endif
});
</script>
@endpush
