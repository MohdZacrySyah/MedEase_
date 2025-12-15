@extends('layouts.admin')
@section('title', 'Laporan Kunjungan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Loading Overlay Style */
        .loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
            border-radius: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }
        .loading-active .loading-overlay {
            opacity: 1;
            pointer-events: all;
        }
        .spinner {
            width: 40px; height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #39A616;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* Smooth Fade Animation */
        .fade-enter { opacity: 0; transform: translateY(10px); }
        .fade-enter-active { opacity: 1; transform: translateY(0); transition: all 0.4s ease-out; }

        /* Cursor pointer for buttons */
        .filter-btn { cursor: pointer; }

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

        /* ===== HEADER BANNER ===== */
        .page-header-banner {
            margin-bottom: 40px;
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
        }

        .page-title {
            color: #fff;
            font-weight: 800;
            font-size: 2.2rem;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0;
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

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* ===== SECTION HEADER ===== */
        .section-header {
            margin-bottom: 25px;
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

        /* ===== STATS SECTION ===== */
        .stats-section-modern {
            margin-bottom: 40px;
        }
        
        .kpi-grid-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .kpi-card-modern {
            background: var(--bg-primary);
            border-radius: 20px;
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 8px 30px var(--shadow-color);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .kpi-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
            border-color: rgba(57, 166, 22, 0.4);
        }

        .card-gradient-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: var(--grad);
            opacity: 0.05;
            transition: opacity 0.4s ease;
        }

        .card-warning-overlay { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .card-info-overlay { background: linear-gradient(135deg, #3498db, #2980b9); }
        .card-success-overlay { background: var(--grad); }

        .kpi-card-modern:hover .card-gradient-overlay {
            opacity: 0.08;
        }

        .kpi-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: var(--grad);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            position: relative;
            flex-shrink: 0;
            z-index: 1;
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        }

        .kpi-warning { background: linear-gradient(135deg, #f39c12, #e67e22); box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3); }
        .kpi-info { background: linear-gradient(135deg, #3498db, #2980b9); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3); }
        .kpi-success { background: var(--grad); box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3); }

        .kpi-content {
            flex: 1;
            z-index: 1;
        }

        .kpi-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 8px;
        }

        .kpi-label {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .kpi-trend {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .kpi-decoration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 70px;
            color: var(--bg-tertiary);
            opacity: 0.5;
            z-index: 0;
        }

        /* ===== FILTER SECTION ===== */
        .filter-section-modern {
            margin-bottom: 40px;
        }

        .filter-card-modern {
            background: var(--bg-primary);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 30px var(--shadow-color);
            border: 1px solid var(--border-color);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-card-modern:hover {
            box-shadow: 0 20px 60px rgba(57, 166, 22, 0.25);
            border-color: rgba(57, 166, 22, 0.4);
        }

        .filter-controls-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-buttons-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex: 1;
        }

        .view-toggle-group {
            display: flex;
            gap: 10px;
        }

        .btn-filter-modern {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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
            background: linear-gradient(90deg, transparent, rgba(57, 166, 22, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .btn-filter-modern:hover::before {
            left: 100%;
        }

        .btn-filter-modern:hover {
            background: var(--hover-bg);
            border-color: var(--p1);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(57, 166, 22, 0.2);
        }

        .btn-filter-active {
            background: var(--grad);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        }

        .btn-filter-active:hover {
            background: var(--grad-reverse);
            box-shadow: 0 10px 30px rgba(57, 166, 22, 0.5);
        }

        .input-picker-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1rem;
            pointer-events: none;
            z-index: 1;
        }

        .btn-filter-active .input-icon {
            color: rgba(255, 255, 255, 0.9);
        }

        .input-picker {
            min-width: 200px;
            padding-left: 48px;
            cursor: pointer;
        }

        .search-wrapper .input-picker {
            cursor: text;
        }

        .input-picker::placeholder {
            color: var(--text-muted);
        }

        .input-picker.btn-filter-active::placeholder {
            color: rgba(255, 255, 255, 0.9);
        }

        .btn-toggle-modern {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn-toggle-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(57, 166, 22, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .btn-toggle-modern:hover::before {
            left: 100%;
        }

        .btn-toggle-modern:hover {
            background: var(--hover-bg);
            border-color: var(--p1);
            transform: translateY(-2px);
        }

        .btn-toggle-active {
            background: var(--grad);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(57, 166, 22, 0.3);
        }

        .btn-toggle-active:hover {
            background: var(--grad-reverse);
            box-shadow: 0 10px 30px rgba(57, 166, 22, 0.5);
        }

        /* ===== DATA SECTION ===== */
        .data-section-modern {
            margin-bottom: 40px;
        }

        .view-content-modern {
            display: none;
        }

        .view-active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .schedule-container-modern {
            background: var(--bg-primary);
            border-radius: 24px;
            box-shadow: 0 8px 30px var(--shadow-color);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
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

        .btn-export-modern {
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-export-modern:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* TABLE RESPONSIVE WRAPPER */
        .table-responsive {
            overflow-x: auto;
            width: 100%;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(57, 166, 22, 0.3) var(--bg-secondary);
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: rgba(57, 166, 22, 0.3);
            border-radius: 10px;
            border: 2px solid var(--bg-secondary);
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background-color: rgba(57, 166, 22, 0.5);
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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
            white-space: nowrap;
        }

        .schedule-table thead th i {
            margin-right: 10px;
            opacity: 0.95;
        }

        .schedule-row {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .schedule-row:hover {
            background: var(--hover-bg);
        }

        .schedule-table tbody td {
            padding: 20px 24px;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .text-secondary-color {
            color: var(--text-secondary);
        }

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

        .doctor-name {
            font-weight: 600;
            color: var(--text-primary);
            min-width: 120px;
        }

        .service-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.15));
            color: #1976d2;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        [data-theme="dark"] .service-badge,
        .dark-mode .service-badge {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.25));
            color: #60a5fa;
            border-color: rgba(52, 152, 219, 0.3);
        }

        .time-badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.15));
            color: #856404;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        [data-theme="dark"] .time-badge-modern,
        .dark-mode .time-badge-modern {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.25));
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
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

        .chart-container-wrapper {
            padding: 40px;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        
        /* Tablet */
        @media (max-width: 992px) {
            .hero-illustration {
                display: none;
            }

            .filter-controls-grid {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-buttons-group,
            .view-toggle-group {
                width: 100%;
            }

            .btn-filter-modern,
            .btn-toggle-modern {
                flex: 1;
                justify-content: center;
            }

            .kpi-grid-modern {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .schedule-table {
                min-width: 850px;
            }
        }

        /* Mobile Landscape & Portrait */
        @media (max-width: 768px) {
            .container-fluid-modern {
                padding: 25px 16px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
                padding: 28px 20px;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .page-subtitle {
                font-size: 0.95rem;
                justify-content: center;
            }

            .kpi-grid-modern {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .kpi-card-modern {
                padding: 24px;
            }

            .kpi-value {
                font-size: 2.2rem;
            }

            .kpi-icon-wrapper {
                width: 65px;
                height: 65px;
                font-size: 28px;
            }

            .filter-card-modern {
                padding: 24px;
            }

            .filter-buttons-group {
                flex-direction: column;
            }

            .btn-filter-modern,
            .input-picker-wrapper {
                width: 100%;
            }

            .input-picker {
                width: 100%;
                min-width: unset;
            }

            .view-toggle-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-toggle-modern {
                width: 100%;
                justify-content: center;
            }

            .table-card-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 18px 20px;
            }

            .schedule-table {
                font-size: 0.9rem;
                min-width: 800px;
            }

            .schedule-table thead th,
            .schedule-table tbody td {
                padding: 16px 14px;
            }

            .doctor-avatar {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .doctor-name {
                min-width: 100px;
            }

            .service-badge,
            .time-badge-modern {
                font-size: 0.8rem;
                padding: 8px 14px;
            }

            .chart-container-wrapper {
                padding: 24px 16px;
            }
        }

        /* Extra Small Mobile */
        @media (max-width: 576px) {
            .container-fluid-modern {
                padding: 20px 12px;
            }

            .header-content {
                padding: 24px 18px;
                border-radius: 20px;
            }

            .header-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
                border-radius: 14px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .page-subtitle {
                font-size: 0.9rem;
            }

            .greeting-badge {
                font-size: 0.8rem;
                padding: 8px 16px;
            }

            .section-header h2 {
                font-size: 1.3rem;
            }

            .section-header h2 i {
                font-size: 1.2rem;
            }

            .kpi-card-modern {
                padding: 20px;
                gap: 16px;
            }

            .kpi-icon-wrapper {
                width: 60px;
                height: 60px;
                font-size: 26px;
            }

            .kpi-value {
                font-size: 2rem;
            }

            .kpi-label {
                font-size: 0.9rem;
            }

            .kpi-trend {
                font-size: 0.8rem;
            }

            .filter-card-modern {
                padding: 20px;
            }

            .btn-filter-modern,
            .btn-toggle-modern {
                font-size: 0.85rem;
                padding: 10px 20px;
            }

            .table-card-header {
                padding: 16px 18px;
            }

            .table-title {
                font-size: 1rem;
            }

            .schedule-count {
                padding: 8px 16px;
                font-size: 0.85rem;
            }

            .schedule-table {
                min-width: 750px;
            }

            .schedule-table thead th {
                font-size: 0.8rem;
                padding: 14px 12px;
            }

            .schedule-table tbody td {
                padding: 14px 12px;
                font-size: 0.85rem;
            }

            .doctor-avatar {
                width: 42px;
                height: 42px;
                font-size: 16px;
                border-width: 2px;
            }

            .doctor-name {
                font-size: 0.9rem;
                min-width: 90px;
            }

            .number-badge {
                min-width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }

            .service-badge,
            .time-badge-modern {
                font-size: 0.75rem;
                padding: 7px 12px;
            }

            .empty-schedule {
                padding: 50px 16px;
            }

            .empty-schedule i {
                font-size: 3.5rem;
            }

            .empty-schedule p {
                font-size: 1rem;
            }

            .empty-schedule small {
                font-size: 0.85rem;
            }

            .chart-container-wrapper {
                padding: 20px 12px;
            }
        }

        /* Ultra Small Mobile */
        @media (max-width: 400px) {
            .page-title {
                font-size: 1.35rem;
            }

            .schedule-table {
                min-width: 700px;
            }
        }
    </style>
@endpush

@section('content')
    {{-- HEADER BANNER --}}
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics Report</span>
                </div>
                <h1 class="page-title">Laporan Kunjungan 📊</h1>
                <p class="page-subtitle">
                    <i class="far fa-chart-bar"></i>
                    Analisis dan statistik kunjungan pasien
                </p>
            </div>
            <div class="hero-illustration">
                <div class="pulse-circle pulse-1"></div>
                <div class="pulse-circle pulse-2"></div>
                <div class="pulse-circle pulse-3"></div>
                <div class="time-widget">
                    <i class="fas fa-file-medical-alt"></i>
                    <span>Report</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS SECTION (KPI CARDS) --}}
    <div class="stats-section-modern">
        <div class="section-header">
            <h2><i class="fas fa-chart-bar"></i> Statistik Kunjungan</h2>
        </div>
        <div class="kpi-grid-modern" id="live-kpi">
            
            <div class="kpi-card-modern card-info">
                <div class="card-gradient-overlay card-info-overlay"></div>
                <div class="kpi-icon-wrapper kpi-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="kpi-hari-ini">{{ $kunjunganHariIni }}</div>
                    <div class="kpi-label">Kunjungan Hari Ini</div>
                    <div class="kpi-trend">
                        <i class="fas fa-arrow-up"></i> Aktif
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="kpi-card-modern card-warning">
                <div class="card-gradient-overlay card-warning-overlay"></div>
                <div class="kpi-icon-wrapper kpi-warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="kpi-bulan-ini">{{ $kunjunganBulanIni }}</div>
                    <div class="kpi-label">Kunjungan Bulan Ini</div>
                    <div class="kpi-trend">
                        <i class="fas fa-chart-line"></i> Bulanan
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <div class="kpi-card-modern card-success">
                <div class="card-gradient-overlay card-success-overlay"></div>
                <div class="kpi-icon-wrapper kpi-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="kpi-total">{{ $semuaKunjungan }}</div>
                    <div class="kpi-label">Semua Kunjungan</div>
                    <div class="kpi-trend">
                        <i class="fas fa-database"></i> Total
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="filter-section-modern">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter Data</h2>
        </div>
        <div class="filter-card-modern">
            <div class="filter-controls-grid">
                <div class="filter-buttons-group">
                    <button type="button" onclick="loadData('hari_ini')" 
                       class="btn-filter-modern filter-btn {{ $filter == 'hari_ini' ? 'btn-filter-active' : '' }}"
                       data-filter="hari_ini">
                        <i class="fas fa-calendar-day"></i>
                        <span>Hari Ini</span>
                    </button>

                    <button type="button" onclick="loadData('bulan_ini')" 
                       class="btn-filter-modern filter-btn {{ $filter == 'bulan_ini' ? 'btn-filter-active' : '' }}"
                       data-filter="bulan_ini">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bulan Ini</span>
                    </button>

                    <button type="button" onclick="loadData('semua_data')" 
                       class="btn-filter-modern filter-btn {{ $filter == 'semua_data' ? 'btn-filter-active' : '' }}"
                       data-filter="semua_data">
                        <i class="fas fa-database"></i>
                        <span>Semua Data</span>
                    </button>
                    
                    {{-- Picker Bulan --}}
                    <div class="input-picker-wrapper">
                        <i class="fas fa-calendar-week input-icon"></i>
                        <input type="text" id="bulanFilter" 
                               class="btn-filter-modern input-picker filter-input {{ $filter == 'bulan_terpilih' ? 'btn-filter-active' : '' }}" 
                               placeholder="Pilih Bulan" 
                               value="{{ $filter == 'bulan_terpilih' ? \Carbon\Carbon::parse($bulanDipilih)->format('Y-m') : '' }}">
                    </div>
                    
                    {{-- Picker Tanggal --}}
                    <div class="input-picker-wrapper">
                        <i class="fas fa-calendar-day input-icon"></i>
                        <input type="text" id="tanggalFilter" 
                               class="btn-filter-modern input-picker filter-input {{ $filter == 'tanggal' ? 'btn-filter-active' : '' }}" 
                               placeholder="Pilih Tanggal"
                               value="{{ $filter == 'tanggal' ? \Carbon\Carbon::parse($tanggalDipilih)->format('Y-m-d') : '' }}">
                    </div>

                    {{-- INPUT PENCARIAN --}}
                    <div class="input-picker-wrapper search-wrapper">
                        <i class="fas fa-search input-icon"></i>
                        <input type="text" id="searchInput" 
                               class="btn-filter-modern input-picker" 
                               placeholder="Cari Pasien/Dokter..."
                               style="min-width: 220px;">
                    </div>
                </div>
                
                <div class="view-toggle-group">
                    <button id="showTableBtn" class="btn-toggle-modern btn-toggle-active">
                        <i class="fas fa-table"></i> 
                        <span>Lihat Data</span>
                    </button>
                    <button id="showChartBtn" class="btn-toggle-modern">
                        <i class="fas fa-chart-line"></i> 
                        <span>Lihat Grafik</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA SECTION --}}
    <div class="data-section-modern" id="dataContainer" style="position: relative; min-height: 400px;">
        
        {{-- Loading Spinner --}}
        <div class="loading-overlay">
            <div class="spinner"></div>
        </div>

        <div class="section-header">
            <h2><i class="fas fa-list-alt"></i> Data Kunjungan Pasien</h2>
        </div>

        {{-- TAMPILAN TABEL --}}
        <div id="tableView" class="view-content-modern view-active">
            <div class="schedule-container-modern">
                <div class="table-card-header">
                    <h3 class="table-title">
                        <i class="fas fa-clipboard-list"></i> 
                        Daftar Kunjungan
                    </h3>
                    <button class="schedule-count btn-export-modern">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>

                {{-- RESPONSIVE TABLE WRAPPER --}}
                <div class="table-responsive">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID Pasien</th>
                                <th><i class="fas fa-user"></i> Nama Pasien</th>
                                <th><i class="fas fa-user-md"></i> Nama Dokter</th>
                                <th><i class="fas fa-hospital"></i> Layanan</th>
                                <th><i class="far fa-clock"></i> Tanggal Kunjungan</th>
                            </tr>
                        </thead>
                        
                        <tbody id="live-table-body">
                            @forelse ($kunjunganData as $index => $data)
                                <tr class="schedule-row">
                                    <td>
                                        <span class="number-badge">{{ $data->pasien_id }}</span>
                                    </td>
                                    <td>
                                        <div class="doctor-info">
                                            <div class="doctor-avatar">
                                                @if($data->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $data->profile_photo_path) }}" alt="Foto">
                                                @else
                                                    {{ substr($data->nama_pasien, 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="doctor-name">{{ $data->nama_pasien }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary-color">{{ $data->nama_dokter ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="service-badge">
                                            <i class="fas fa-briefcase-medical"></i>
                                            {{ $data->layanan }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="time-badge-modern">
                                            <i class="far fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="no-data-row">
                                    <td colspan="5">
                                        <div class="empty-schedule">
                                            <i class="fas fa-inbox"></i>
                                            <p>Tidak ada data kunjungan untuk periode ini</p>
                                            <small>Silakan pilih filter lain untuk melihat data</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAMPILAN GRAFIK --}}
        <div id="chartView" class="view-content-modern">
            <div class="schedule-container-modern">
                <div class="table-card-header">
                    <h3 class="table-title">
                        <i class="fas fa-chart-area"></i> 
                        <span id="chart-title">Grafik Kunjungan ({{ ucfirst(str_replace('_', ' ', $filter)) }})</span>
                    </h3>
                </div>
                <div class="chart-container-wrapper">
                    <canvas id="kunjunganChart"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
    // --- GLOBAL VARIABLES ---
    let myChartInstance = null;
    let currentFilter = '{{ $filter }}';

    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. INISIALISASI CHART AWAL ---
        const ctx = document.getElementById('kunjunganChart').getContext('2d');
        const initialLabels = {!! json_encode($chartLabels) !!};
        const initialData = {!! json_encode($chartData) !!};
        
        initChart(ctx, initialLabels, initialData);

        // --- 2. FLATPICKR SETTINGS ---
        flatpickr("#tanggalFilter", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    loadData('tanggal', { tanggal: dateStr });
                }
            }
        });

        flatpickr("#bulanFilter", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "F Y",
                })
            ],
            onChange: function(selectedDates, dateStr, instance) {
                 if (dateStr) {
                    loadData('bulan_terpilih', { bulan: dateStr });
                }
            }
        });

        // --- 3. TOGGLE TABEL/GRAFIK ---
        const showTableBtn = document.getElementById('showTableBtn');
        const showChartBtn = document.getElementById('showChartBtn');

        if(showTableBtn) {
            showTableBtn.addEventListener('click', function() {
                switchView('table');
            });
        }
        if(showChartBtn) {
            showChartBtn.addEventListener('click', function() {
                switchView('chart');
            });
        }

        // --- 4. SEARCH FILTER FUNCTION (Client Side) ---
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#live-table-body tr.schedule-row');
                const noDataRow = document.getElementById('no-data-row');
                let hasVisibleRow = false;

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = '';
                        hasVisibleRow = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (noDataRow) {
                    noDataRow.style.display = hasVisibleRow ? 'none' : '';
                }
            });
        }
    });

    // --- MAIN FUNCTION: LOAD DATA VIA AJAX ---
    function loadData(filterType, params = {}) {
        document.getElementById('dataContainer').classList.add('loading-active');
        
        updateActiveButton(filterType);

        let url = `{{ route('admin.laporan') }}?filter=${filterType}`;
        if(params.tanggal) url += `&tanggal=${params.tanggal}`;
        if(params.bulan) url += `&bulan=${params.bulan}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            animateValue("kpi-hari-ini", data.kpi.hari_ini);
            animateValue("kpi-bulan-ini", data.kpi.bulan_ini);
            animateValue("kpi-total", data.kpi.total);

            updateChart(data.chart.labels, data.chart.data);
            
            const judul = filterType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const chartTitle = document.getElementById('chart-title');
            if(chartTitle) chartTitle.innerText = `Grafik Kunjungan (${judul})`;

            updateTable(data.table_data);

            setTimeout(() => {
                document.getElementById('dataContainer').classList.remove('loading-active');
            }, 300);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('dataContainer').classList.remove('loading-active');
        });
    }

    // --- HELPER FUNCTIONS ---

    function switchView(view) {
        const tableView = document.getElementById('tableView');
        const chartView = document.getElementById('chartView');
        const showTableBtn = document.getElementById('showTableBtn');
        const showChartBtn = document.getElementById('showChartBtn');

        if(view === 'table') {
            tableView.classList.add('view-active');
            chartView.classList.remove('view-active');
            showTableBtn.classList.add('btn-toggle-active');
            showChartBtn.classList.remove('btn-toggle-active');
        } else {
            tableView.classList.remove('view-active');
            chartView.classList.add('view-active');
            showChartBtn.classList.add('btn-toggle-active');
            showTableBtn.classList.remove('btn-toggle-active');
        }
    }

    function updateActiveButton(filterType) {
        document.querySelectorAll('.filter-btn, .filter-input').forEach(el => el.classList.remove('btn-filter-active'));
        
        if(filterType === 'hari_ini' || filterType === 'bulan_ini' || filterType === 'semua_data') {
            const btn = document.querySelector(`button[data-filter="${filterType}"]`);
            if(btn) btn.classList.add('btn-filter-active');
        } else if (filterType === 'tanggal') {
            const picker = document.getElementById('tanggalFilter');
            if(picker && picker.nextSibling) picker.nextSibling.classList.add('btn-filter-active');
        } else if (filterType === 'bulan_terpilih') {
            const picker = document.getElementById('bulanFilter');
            if(picker && picker.nextSibling) picker.nextSibling.classList.add('btn-filter-active');
        }
    }

    function initChart(ctx, labels, data) {
        const isDarkMode = document.documentElement.classList.contains('dark-mode') || 
                          (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const bgColor = isDarkMode ? 'rgba(57, 166, 22, 0.2)' : 'rgba(57, 166, 22, 0.1)';
        const gridColor = isDarkMode ? 'rgba(57, 166, 22, 0.15)' : 'rgba(57, 166, 22, 0.1)';
        const textColor = isDarkMode ? '#d1d5db' : '#6b7280';
        const tooltipBg = isDarkMode ? '#1f2937' : '#0C5B00';

        myChartInstance = new Chart(ctx, {
            type: 'line', 
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: data,
                    backgroundColor: bgColor,
                    borderColor: '#39A616',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#39A616',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    }
                },
                plugins: { 
                    tooltip: { 
                        backgroundColor: tooltipBg,
                        padding: 14,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 13, weight: '600' },
                            color: isDarkMode ? '#f9fafb' : '#556E85'
                        }
                    }
                }
            }
        });
    }

    function updateChart(labels, data) {
        if(myChartInstance) {
            myChartInstance.data.labels = labels;
            myChartInstance.data.datasets[0].data = data;
            myChartInstance.update();
        }
    }

    function updateTable(data) {
        const tbody = document.getElementById('live-table-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr id="no-data-row">
                    <td colspan="5">
                        <div class="empty-schedule">
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data kunjungan untuk periode ini</p>
                            <small>Silakan pilih filter lain untuk melihat data</small>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            let avatarContent = '';
            if (item.profile_photo_url) {
                avatarContent = `<img src="${item.profile_photo_url}" alt="Foto" style="width:100%; height:100%; object-fit:cover;">`;
            } else {
                avatarContent = item.nama_pasien.charAt(0).toUpperCase();
            }

            html += `
                <tr class="schedule-row fade-enter">
                    <td>
                        <span class="number-badge">${item.pasien_id}</span>
                    </td>
                    <td>
                        <div class="doctor-info">
                            <div class="doctor-avatar">
                                ${avatarContent}
                            </div>
                            <span class="doctor-name">${item.nama_pasien}</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-secondary-color">${item.nama_dokter}</span>
                    </td>
                    <td>
                        <span class="service-badge">
                            <i class="fas fa-briefcase-medical"></i>
                            ${item.layanan}
                        </span>
                    </td>
                    <td>
                        <div class="time-badge-modern">
                            <i class="far fa-clock"></i>
                            <span>${item.tanggal_formatted}</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        
        setTimeout(() => {
            document.querySelectorAll('.fade-enter').forEach(el => el.classList.add('fade-enter-active'));
        }, 50);
    }

    function animateValue(id, end) {
        const obj = document.getElementById(id);
        const start = parseInt(obj.innerHTML.replace(/\D/g,'')) || 0;
        if(start === end) return;
        
        let current = start;
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const step = Math.abs(Math.floor(1000 / range));
        
        const timer = setInterval(() => {
            current += increment;
            obj.innerHTML = current;
            if (current == end) clearInterval(timer);
        }, Math.max(step, 10));
        
        obj.innerHTML = end;
    }
</script>
@endpush
