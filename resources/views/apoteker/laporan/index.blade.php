@extends('layouts.apoteker')
@section('title', 'Laporan Apotek')

{{-- 1. Tambahkan CSS Flatpickr ke Head --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@section('content')
    {{-- HEADER BANNER --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="header-text">
                <h1 class="page-title">Laporan Apotek</h1>
                <p class="page-subtitle">
                    <i class="fas fa-chart-bar"></i>
                    Analisis dan statistik peresepan
                </p>
            </div>
            <div class="time-widget">
                <i class="fas fa-file-medical-alt"></i>
                <span>Report</span>
            </div>
        </div>
    </div>

    {{-- STATS SECTION --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-chart-bar"></i> Statistik Resep</h2>
        </div>
        <div class="kpi-grid-modern">
            <div class="kpi-card-modern card-info">
                <div class="card-gradient-bg card-info-bg"></div>
                <div class="kpi-icon-wrapper kpi-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $resepHariIni }}</div>
                    <div class="kpi-label">Resep Selesai Hari Ini</div>
                    <div class="kpi-trend">
                        <i class="fas fa-arrow-up"></i> Aktif
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="kpi-card-modern card-warning">
                <div class="card-gradient-bg card-warning-bg"></div>
                <div class="kpi-icon-wrapper kpi-warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $resepBulanIni }}</div>
                    <div class="kpi-label">Resep Bulan Ini</div>
                    <div class="kpi-trend">
                        <i class="fas fa-chart-line"></i> Bulanan
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <div class="kpi-card-modern">
                <div class="card-gradient-bg"></div>
                <div class="kpi-icon-wrapper">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $semuaResep }}</div>
                    <div class="kpi-label">Semua Resep Selesai</div>
                    <div class="kpi-trend">
                        <i class="fas fa-database"></i> Total
                    </div>
                </div>
                <div class="kpi-decoration">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="stats-section">
        <div class="section-header">
            <h2><i class="fas fa-filter"></i> Filter Data</h2>
        </div>
        <div class="kpi-card-modern filter-card-custom">
            <div class="filter-controls-modern">
                <div class="filter-buttons-group">
                    <a href="{{ route('apoteker.laporan.index', ['filter' => 'hari_ini']) }}" 
                       class="btn-action-primary {{ $filter == 'hari_ini' ? 'btn-active' : 'btn-filter-style' }}">
                        <i class="fas fa-calendar-day"></i>
                        <span>Hari Ini</span>
                    </a>
                    <a href="{{ route('apoteker.laporan.index', ['filter' => 'bulan_ini']) }}" 
                       class="btn-action-primary {{ $filter == 'bulan_ini' ? 'btn-active' : 'btn-filter-style' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bulan Ini</span>
                    </a>
                    <a href="{{ route('apoteker.laporan.index', ['filter' => 'semua_data']) }}" 
                       class="btn-action-primary {{ $filter == 'semua_data' ? 'btn-active' : 'btn-filter-style' }}">
                        <i class="fas fa-database"></i>
                        <span>Semua Data</span>
                    </a>
                    
                    <div class="input-picker-wrapper">
                        <input type="text" id="bulanFilter" 
                               class="btn-action-primary btn-filter-style input-picker {{ $filter == 'bulan_terpilih' ? 'btn-active' : '' }}" 
                               placeholder="📅 Pilih Bulan" 
                               value="{{ $filter == 'bulan_terpilih' ? \Carbon\Carbon::parse($bulanDipilih)->isoFormat('MMMM YYYY') : '' }}">
                    </div>
                    
                    <div class="input-picker-wrapper">
                        <input type="text" id="tanggalFilter" 
                               class="btn-action-primary btn-filter-style input-picker {{ $filter == 'tanggal' ? 'btn-active' : '' }}" 
                               placeholder="📅 Pilih Tanggal"
                               value="{{ $filter == 'tanggal' ? \Carbon\Carbon::parse($tanggalDipilih)->isoFormat('D MMM YYYY') : '' }}">
                    </div>
                </div>
                <div class="view-toggle-group">
                    <button id="showTableBtn" class="btn-action-primary btn-toggle-active">
                        <i class="fas fa-table"></i> 
                        <span>Lihat Data</span>
                    </button>
                    <button id="showChartBtn" class="btn-action-primary btn-filter-style">
                        <i class="fas fa-chart-line"></i> 
                        <span>Lihat Grafik</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA SECTION --}}
    <div class="tables-section">
        <div class="section-header">
            <h2><i class="fas fa-list-alt"></i> Data Resep Selesai</h2>
        </div>

        {{-- TAMPILAN TABEL --}}
        <div id="tableView" class="view-content active">
            <div class="table-card-modern">
                <div class="table-card-header">
                    <h3 class="table-title">
                        <i class="fas fa-clipboard-list"></i> 
                        Daftar Resep
                    </h3>
                    <button class="badge-count btn-export-style">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
                <div class="table-container-modern">
                    <table class="data-table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-check"></i> Tgl Selesai</th>
                                <th><i class="fas fa-user-injured"></i> Nama Pasien</th>
                                <th><i class="fas fa-user-md"></i> Dokter</th>
                                <th><i class="fas fa-pills"></i> Resep Obat</th>
                                <th><i class="fas fa-file-medical-alt"></i> Catatan Apoteker</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporanReseps as $index => $resep)
                                <tr class="table-row" style="animation-delay: {{ $index * 0.1 }}s">
                                    <td>
                                        <div class="time-badge">
                                            <i class="far fa-clock"></i>
                                            <span>{{ $resep->updated_at->isoFormat('DD MMM YYYY, HH:mm') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            <div class="patient-avatar">
                                                @if($resep->pasien?->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $resep->pasien->profile_photo_path) }}" alt="Foto">
                                                @else
                                                    <i class="fas fa-user"></i>
                                                @endif
                                            </div>
                                            <span class="patient-name">{{ $resep->pasien->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        {{ $resep->pemeriksaan->resep_obat ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $resep->catatan_apoteker ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Tidak ada data resep untuk periode ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    {{ $laporanReseps->links('vendor.pagination.bootstrap-4') }} 
                </div>
            </div>
        </div>

        {{-- TAMPILAN GRAFIK --}}
        <div id="chartView" class="view-content" style="display: none;">
            <div class="table-card-modern">
                <div class="table-card-header">
                    <h3 class="table-title">
                        <i class="fas fa-chart-area"></i> 
                        Grafik Resep ({{ ucfirst(str_replace('_', ' ', $filter)) }})
                    </h3>
                </div>
                <div class="chart-container-wrapper">
                    <canvas id="kunjunganChart"></canvas>
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

    body {
        font-family: 'Poppins', sans-serif; 
        background: linear-gradient(135deg, #f5f7fa 0%, #e8f5f3 100%);
    }
    
    .container-modern {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER BANNER ===== */
    .dashboard-header-banner {
        margin-bottom: 40px;
        animation: fadeInDown 0.6s ease-out;
    }
    .header-content {
        display: flex; align-items: center; gap: 20px;
        background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
        padding: 30px 35px; border-radius: 20px;
        box-shadow: 0 8px 30px rgba(22, 148, 0, 0.2);
        position: relative; overflow: hidden;
    }
    .header-content::before {
        content: ''; position: absolute; top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .header-icon {
        width: 70px; height: 70px;
        background: rgba(255, 255, 255, 0.2); border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; color: #fff; flex-shrink: 0; position: relative; z-index: 1;
    }
    .header-text { flex: 1; position: relative; z-index: 1; }
    .page-title {
        color: #fff; font-weight: 700; font-size: 2rem;
        margin: 0 0 8px 0; letter-spacing: -0.5px;
    }
    .page-subtitle {
        display: flex; align-items: center; gap: 8px;
        color: rgba(255, 255, 255, 0.9); font-size: 1rem; font-weight: 400; margin: 0;
    }
    .time-widget {
        background: rgba(255, 255, 255, 0.2); padding: 12px 24px; border-radius: 20px;
        font-size: 1.1rem; font-weight: 600; backdrop-filter: blur(10px);
        display: flex; align-items: center; gap: 10px; color: white;
        flex-shrink: 0; position: relative; z-index: 1;
    }

    /* ===== SECTION HEADER ===== */
    .section-header { margin-bottom: 25px; }
    .section-header h2 {
        font-size: 1.5rem; font-weight: 700; color: #1f2937;
        display: flex; align-items: center; gap: 10px; margin: 0;
    }
    .section-header h2 i { color: #169400; font-size: 1.3rem; }

    /* ===== STATS SECTION ===== */
    .stats-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    .kpi-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }
    .kpi-card-modern {
        background: #fff; border-radius: 20px; padding: 28px;
        display: flex; align-items: center; gap: 20px;
        box-shadow: 0 4px 20px rgba(22, 148, 0, 0.08);
        border: 1px solid rgba(22, 148, 0, 0.1);
        position: relative; overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .kpi-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(22, 148, 0, 0.18);
        border-color: rgba(22, 148, 0, 0.3);
    }
    .card-gradient-bg {
        position: absolute; top: 0; left: 0; right: 0; height: 100%;
        background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
        opacity: 0.05; transition: opacity 0.4s ease;
    }
    .card-warning-bg { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .card-info-bg { background: linear-gradient(135deg, #3498db, #2980b9); }
    .kpi-card-modern:hover .card-gradient-bg { opacity: 0.08; }
    .kpi-icon-wrapper {
        width: 70px; height: 70px; border-radius: 16px;
        background: linear-gradient(135deg, #169400, #1cc200);
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; color: white; position: relative;
        flex-shrink: 0; z-index: 1;
        box-shadow: 0 4px 12px rgba(22, 148, 0, 0.25);
    }
    .kpi-warning { background: linear-gradient(135deg, #f39c12, #e67e22); box-shadow: 0 4px 12px rgba(243, 156, 18, 0.25); }
    .kpi-info { background: linear-gradient(135deg, #3498db, #2980b9); box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25); }
    .kpi-content { flex: 1; z-index: 1; }
    .kpi-value {
        font-size: 2.5rem; font-weight: 800; color: #1f2937;
        line-height: 1; margin-bottom: 8px;
    }
    .kpi-label {
        font-size: 1rem; font-weight: 600; color: #6b7280;
        margin-bottom: 6px;
    }
    .kpi-trend {
        font-size: 0.85rem; color: #9ca3af; display: flex;
        align-items: center; gap: 6px; font-weight: 600;
    }
    .kpi-decoration {
        position: absolute; bottom: 10px; right: 10px;
        font-size: 70px; color: #f0f0f0; opacity: 0.5; z-index: 0;
    }

    /* ===== FILTER SECTION ===== */
    .filter-card-custom { padding: 28px !important; }
    .filter-controls-modern {
        display: flex; justify-content: space-between; align-items: center;
        gap: 20px; flex-wrap: wrap;
    }
    .filter-buttons-group {
        display: flex; align-items: center; gap: 12px;
        flex-wrap: wrap; flex: 1;
    }
    .view-toggle-group { display: flex; gap: 10px; }
    .btn-action-primary {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 10px 20px; border-radius: 12px; text-decoration: none;
        font-size: 0.9rem; font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer; border: none; position: relative;
        overflow: hidden; white-space: nowrap;
    }
    .btn-filter-style {
        background: #fff; color: #007e6c;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .btn-filter-style:hover {
        background: #f9fafb; border-color: #169400;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 148, 0, 0.15);
    }
    .btn-active,
    .btn-toggle-active {
        background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
        color: white; border: 2px solid transparent;
        box-shadow: 0 4px 12px rgba(22, 148, 0, 0.25);
    }
    .input-picker-wrapper { position: relative; }
    .input-picker { min-width: 180px; cursor: pointer; }
    .input-picker::placeholder { color: #6b7280; }
    .input-picker.btn-active::placeholder { color: rgba(255, 255, 255, 0.9); }

    /* ===== TABLES SECTION ===== */
    .tables-section {
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }
    .table-card-modern {
        background: #fff; border-radius: 20px;
        box-shadow: 0 4px 20px rgba(22, 148, 0, 0.08);
        border: 1px solid rgba(22, 148, 0, 0.1);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .table-card-modern:hover { box-shadow: 0 12px 40px rgba(22, 148, 0, 0.18); }
    .table-card-header {
        background: linear-gradient(135deg, #169400, #1cc200);
        padding: 20px 25px; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 15px;
    }
    .table-title {
        font-size: 1.1rem; font-weight: 600; color: white;
        margin: 0; display: flex; align-items: center; gap: 10px;
    }
    .badge-count {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255, 255, 255, 0.2); color: white;
        padding: 8px 18px; border-radius: 20px;
        font-size: 0.9rem; font-weight: 600;
        backdrop-filter: blur(10px);
    }
    .btn-export-style { cursor: pointer; transition: all 0.3s ease; border: none; }
    .btn-export-style:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); }
    
    /* ===== TABEL STYLING ===== */
    .table-container-modern { 
        overflow-x: auto; 
        background: #fff;
    }
    
    .data-table-modern {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }
    
    .data-table-modern thead { 
        background: linear-gradient(135deg, #169400, #1cc200); 
    }
    
    .data-table-modern thead th {
        padding: 18px 20px; 
        text-align: left; 
        color: white;
        font-weight: 600; 
        font-size: 0.9rem; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .data-table-modern thead th i { 
        margin-right: 8px; 
        opacity: 0.9; 
    }
    
    /* Lebar Kolom */
    .data-table-modern th:nth-child(1), 
    .data-table-modern td:nth-child(1) { 
        width: 16%; 
        min-width: 150px;
    }
    
    .data-table-modern th:nth-child(2), 
    .data-table-modern td:nth-child(2) { 
        width: 20%; 
        min-width: 180px;
    }
    
    .data-table-modern th:nth-child(3), 
    .data-table-modern td:nth-child(3) { 
        width: 16%; 
        min-width: 140px;
    }
    
    .data-table-modern th:nth-child(4), 
    .data-table-modern td:nth-child(4) { 
        width: 26%; 
        min-width: 200px;
    }
    
    .data-table-modern th:nth-child(5), 
    .data-table-modern td:nth-child(5) { 
        width: 22%; 
        min-width: 180px;
    }

    .table-row {
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }
    
    .table-row:hover { 
        background: #f8fffe; 
    }
    
    .data-table-modern tbody td {
        padding: 18px 20px;
        color: #6b7280;
        vertical-align: top;
        word-wrap: break-word;
        word-break: break-word;
        line-height: 1.6;
        background: #fff;
    }
    
    /* Kolom Text Panjang */
    .data-table-modern tbody td:nth-child(4),
    .data-table-modern tbody td:nth-child(5) {
        max-height: 150px;
        overflow-y: auto;
        font-size: 0.9rem;
    }
    
    /* Custom Scrollbar */
    .data-table-modern tbody td:nth-child(4)::-webkit-scrollbar,
    .data-table-modern tbody td:nth-child(5)::-webkit-scrollbar {
        width: 6px;
    }
    
    .data-table-modern tbody td:nth-child(4)::-webkit-scrollbar-track,
    .data-table-modern tbody td:nth-child(5)::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .data-table-modern tbody td:nth-child(4)::-webkit-scrollbar-thumb,
    .data-table-modern tbody td:nth-child(5)::-webkit-scrollbar-thumb {
        background: #169400;
        border-radius: 10px;
    }

    .text-muted { color: #6b7280; }

    /* Patient Info */
    .patient-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .patient-avatar {
        width: 45px; height: 45px; border-radius: 50%;
        background: linear-gradient(135deg, #169400, #1cc200);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 18px; flex-shrink: 0;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(22, 148, 0, 0.15);
    }
    .patient-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .patient-name { 
        font-weight: 600; 
        color: #1f2937;
    }
    
    .time-badge {
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #fff3cd, #ffe8a1);
        color: #856404; 
        border-radius: 20px;
        font-weight: 600; 
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .empty-state { 
        text-align: center; 
        padding: 60px 20px; 
        color: #9ca3af; 
    }
    .empty-state i { 
        font-size: 4rem; 
        margin-bottom: 20px; 
        opacity: 0.3; 
    }
    .empty-state p { 
        margin: 0; 
        font-size: 1.05rem; 
        font-weight: 500; 
    }

    /* ===== CHART CONTAINER - FIXED ===== */
    .chart-container-wrapper { 
        padding: 30px;
    }
    
    .view-content { 
        display: none; 
    }
    .view-content.active { 
        display: block; 
        animation: fadeIn 0.5s ease; 
    }

    /* Pagination */
    .pagination-container { 
        margin-top: 20px; 
        padding: 10px 20px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }
    .pagination { 
        list-style: none; 
        display: flex; 
        gap: 5px; 
    }
    .pagination li a, .pagination li span {
        padding: 8px 15px; 
        text-decoration: none; 
        color: #169400;
        background: #f0f7f6; 
        border-radius: 8px; 
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .pagination li a:hover { 
        background: #169400; 
        color: white; 
    }
    .pagination li.active span { 
        background: #169400; 
        color: white; 
    }
    .pagination li.disabled span { 
        background: #f3f4f6; 
        color: #b0b0b0; 
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInDown { 
        from { opacity: 0; transform: translateY(-20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    @keyframes fadeInUp { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    @keyframes fadeIn { 
        from { opacity: 0; } 
        to { opacity: 1; } 
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .filter-controls-modern { 
            flex-direction: column; 
            align-items: stretch; 
        }
        .filter-buttons-group, 
        .view-toggle-group { 
            width: 100%; 
        }
        .btn-action-primary { 
            flex: 1; 
            justify-content: center; 
        }
    }
    
    @media (max-width: 768px) {
        .header-content { 
            flex-direction: column; 
            padding: 25px 20px; 
        }
        .header-text { 
            text-align: center; 
        }
        .page-title { 
            font-size: 1.6rem; 
        }
        .kpi-grid-modern { 
            grid-template-columns: 1fr; 
        }
        .filter-buttons-group { 
            flex-direction: column; 
        }
        .table-card-header { 
            flex-direction: column; 
            align-items: flex-start; 
        }
        .data-table-modern { 
            font-size: 0.85rem; 
        }
        .data-table-modern thead th, 
        .data-table-modern tbody td { 
            padding: 12px 10px; 
        }
        .patient-avatar { 
            width: 40px; 
            height: 40px; 
            font-size: 16px; 
        }
        .time-badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        .data-table-modern tbody td:nth-child(4),
        .data-table-modern tbody td:nth-child(5) {
            max-height: 120px;
        }
    }
    
    @media (max-width: 576px) {
        .page-title { 
            font-size: 1.4rem; 
        }
        .section-header h2 { 
            font-size: 1.2rem; 
        }
        .view-toggle-group { 
            flex-direction: column; 
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://npmcdn.com/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};
        
        // ===== CHART.JS - SAMA PERSIS DENGAN ADMIN ===== 
        if (chartData.length > 0) {
            const ctx = document.getElementById('kunjunganChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Resep Selesai',
                        data: chartData,
                        backgroundColor: 'rgba(22, 148, 0, 0.1)',
                        borderColor: '#169400',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#169400',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true, // ✅ DIUBAH KE true (sama dengan Admin)
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            // ✅ HAPUS ticks callback - biarkan default
                            grid: {
                                color: 'rgba(22, 148, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: { 
                        tooltip: { 
                            backgroundColor: '#0f7300',
                            padding: 12,
                            titleFont: { 
                                size: 14, 
                                weight: 'bold' 
                            },
                            bodyFont: { 
                                size: 13 
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { 
                                    size: 13, 
                                    weight: '600' 
                                },
                                color: '#556E85'
                            }
                        }
                    }
                }
            });
        }

        // Toggle Tabel/Grafik
        const showTableBtn = document.getElementById('showTableBtn');
        const showChartBtn = document.getElementById('showChartBtn');
        const tableView = document.getElementById('tableView');
        const chartView = document.getElementById('chartView');

        if(showTableBtn) {
            showTableBtn.addEventListener('click', function() {
                tableView.style.display = 'block';
                chartView.style.display = 'none';
                showTableBtn.classList.add('btn-toggle-active');
                showTableBtn.classList.remove('btn-filter-style');
                showChartBtn.classList.add('btn-filter-style');
                showChartBtn.classList.remove('btn-toggle-active');
            });
        }
        
        if(showChartBtn) {
            showChartBtn.addEventListener('click', function() {
                tableView.style.display = 'none';
                chartView.style.display = 'block';
                showTableBtn.classList.add('btn-filter-style');
                showTableBtn.classList.remove('btn-toggle-active');
                showChartBtn.classList.add('btn-toggle-active');
                showChartBtn.classList.remove('btn-filter-style');
            });
        }

        // Filter Tanggal
        flatpickr("#tanggalFilter", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            locale: "id", 
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    window.location.href = `{{ route('apoteker.laporan.index') }}?filter=tanggal&tanggal=${dateStr}`;
                }
            }
        });

        // Filter Bulan
        flatpickr("#bulanFilter", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "F Y",
                    locale: "id"
                })
            ],
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    window.location.href = `{{ route('apoteker.laporan.index') }}?filter=bulan_terpilih&bulan=${dateStr}`;
                }
            }
        });
    });
</script>
@endpush