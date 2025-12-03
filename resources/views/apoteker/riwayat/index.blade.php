@extends('layouts.apoteker')
@section('title', 'Riwayat Resep')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* ===== CSS HEADER BANNER (dari Admin Dashboard) ===== */
    .dashboard-header-banner { 
        margin-bottom: 40px; 
        animation: fadeInDown 0.6s ease-out;
    }
    .header-content { display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #169400 0%, #1cc200 100%); padding: 30px 35px; border-radius: 20px; box-shadow: 0 8px 30px rgba(22, 148, 0, 0.2); position: relative; overflow: hidden; }
    .header-content::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; }
    .header-icon { width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #fff; flex-shrink: 0; position: relative; z-index: 1; }
    .header-text { flex: 1; position: relative; z-index: 1; }
    .page-title { color: #fff; font-weight: 700; font-size: 2rem; margin: 0 0 8px 0; letter-spacing: -0.5px; }
    .page-subtitle { display: flex; align-items: center; gap: 8px; color: rgba(255, 255, 255, 0.9); font-size: 1rem; font-weight: 400; margin: 0; }

    /* CSS Filter, Tabel, dan Modal */
    .filter-bar { 
        background: #fff; 
        padding: 20px 25px; 
        border-radius: 16px; 
        border: 1px solid #eef3f7; 
        box-shadow: 0 4px 15px rgba(22, 148, 0, 0.08); 
        margin-bottom: 35px; 
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }
    .filter-form { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .filter-group { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 250px; background: #f8f9fa; padding: 12px 16px; border-radius: 12px; border: 2px solid #e5e7eb; transition: all 0.3s ease; }
    .filter-group:focus-within { border-color: #169400; box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1); }
    .filter-icon { color: #169400; }
    .filter-group input { border: none; background: transparent; font-family: 'Poppins', sans-serif; font-size: 0.95rem; flex: 1; outline: none; color: #333; }
    .filter-actions { display: flex; gap: 10px; }
    .btn-filter-go, .btn-filter-clear { padding: 12px 24px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.3s ease; }
    .btn-filter-go { background: linear-gradient(135deg, #169400 0%, #1cc200 100%); color: white; box-shadow: 0 4px 12px rgba(22, 148, 0, 0.25); }
    .btn-filter-go:hover { background: linear-gradient(135deg, #0f7300 0%, #169400 100%); transform: translateY(-2px); }
    .btn-filter-clear { background: #f3f4f6; color: #6b7280; }
    .btn-filter-clear:hover { background: #e5e7eb; }

    /* Tabel */
    .table-card-modern { 
        background: #fff; 
        border-radius: 20px; 
        box-shadow: 0 4px 20px rgba(22, 148, 0, 0.08); 
        border: 1px solid rgba(22, 148, 0, 0.1); 
        overflow: hidden; 
        margin-bottom: 25px; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        animation: fadeInUp 0.6s ease-out 0.2s backwards;
    }
    .table-card-modern:hover { box-shadow: 0 12px 40px rgba(22, 148, 0, 0.18); border-color: rgba(22, 148, 0, 0.3); }
    .table-card-header { 
        background: linear-gradient(135deg, #169400, #1cc200); /* Header Kartu (Hijau) */
        padding: 20px 25px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }
    .table-title { font-size: 1.1rem; font-weight: 600; color: white; margin: 0; display: flex; align-items: center; gap: 10px; }
    .table-container-modern { overflow-x: auto; }
    .data-table-modern { width: 100%; border-collapse: collapse; }
    
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
        background: transparent; 
    }
    .data-table-modern thead th i {
        margin-right: 8px;
        opacity: 0.9;
        background: transparent !important; 
    }
    
    .table-row { 
        border-bottom: 1px solid #f0f0f0; 
        transition: all 0.3s ease; 
        animation: fadeInUp 0.5s ease forwards; 
        opacity: 0; 
    }
    .table-row:hover { background: #f8fffe; }
    .data-table-modern tbody td { padding: 18px 20px; color: #6b7280; vertical-align: middle; }
    .text-center { text-align: center !important; }
    
    .status-badge.status-selesai { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); color: #2e7d32; }
    
    .btn-action-secondary { display: inline-flex; align-items: center; gap: 10px; background: #6c757d; color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.3s; cursor: pointer; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-action-secondary:hover { background: #5a6268; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
    
    .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
    .empty-state p { margin: 0; font-size: 1.05rem; font-weight: 500; }

    /* Modal Styles */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); justify-content: center; align-items: center; animation: fadeIn 0.3s; }
    .modal-card { background-color: #fff; margin: 20px; border-radius: 24px; width: 90%; max-width: 700px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); animation: slideUp 0.4s; max-height: 90vh; display: flex; flex-direction: column; position: relative; }
    .close-modal { position: absolute; top: -15px; right: -15px; width: 45px; height: 45px; background: #fff; border: none; border-radius: 50%; cursor: pointer; z-index: 10; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
    .close-modal i { font-size: 1.2rem; color: #6b7280; }
    .close-modal:hover { background: #ef4444; transform: rotate(90deg); }
    .close-modal:hover i { color: #fff; }
    
    .modal-content-inner { padding: 40px; overflow-y: auto; }
    .modal-header { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #f3f4f6; }
    .modal-title { color: #343a40; font-weight: 700; font-size: 1.8rem; margin: 0; display: flex; align-items: center; justify-content: center; gap: 12px; }
    
    .resep-detail { margin-bottom: 12px; }
    .resep-detail .label { font-size: 0.8rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
    .resep-detail .value { font-size: 1.1rem; color: #1f2937; font-weight: 600; }
    
    .resep-box { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .resep-box .label { color: #343a40; font-weight: 700; margin-bottom: 10px; display: block; }
    .resep-box .value { white-space: pre-wrap; word-wrap: break-word; line-height: 1.7; color: #333; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')

    {{-- ===== HEADER BANNER DITAMBAHKAN DI SINI ===== --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon"><i class="fas fa-history"></i></div> {{-- Icon Riwayat --}}
            <div class="header-text">
                <h1 class="page-title">Riwayat Resep Selesai</h1>
                <p class="page-subtitle">Arsip resep yang telah diproses.</p>
            </div>
        </div>
    </div>
    
    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form action="{{ route('apoteker.riwayat.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <i class="fas fa-user filter-icon"></i>
                <input type="text" name="pasien" class="filter-input" placeholder="Cari Nama Pasien..." value="{{ request('pasien') }}">
            </div>
            <div class="filter-group">
                <i class="fas fa-calendar-alt filter-icon"></i>
                <input type="text" name="tanggal" id="tanggalFilter" class="filter-input" placeholder="Filter Tanggal Selesai..." value="{{ request('tanggal') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter-go"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('apoteker.riwayat.index') }}" class="btn-filter-clear"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat Resep --}}
    <div class="table-card-modern">
        <div class="table-card-header">
            <h3 class="table-title">
                <i class="fas fa-check-double"></i> 
                Riwayat Resep Selesai
            </h3>
        </div>
        <div class="table-container-modern">
            <table class="data-table-modern">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-check"></i> Tgl Selesai</th>
                        <th><i class="fas fa-user-injured"></i> Nama Pasien</th>
                        <th><i class="fas fa-user-md"></i> Dokter</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayats as $resep) 
                        <tr class="table-row" style="animation-delay: {{ $loop->index * 0.1 }}s">
                            <td>{{ $resep->updated_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                            <td>{{ $resep->pasien->name ?? 'N/A' }}</td>
                            <td>{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-selesai">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn-action-secondary btn-detail"
                                        data-pasien="{{ $resep->pasien->name ?? 'N/A' }}"
                                        data-dokter="{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}"
                                        data-resep="{{ $resep->pemeriksaan->resep_obat ?? 'Tidak ada resep' }}"
                                        data-catatan="{{ $resep->catatan_apoteker ?? '-' }}">
                                    <i class="fas fa-search-plus"></i>
                                    <span>Lihat Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada riwayat resep yang selesai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Modal Detail Riwayat (Read Only) --}}
    <div id="detailModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 700px;">
            <button class="close-modal" id="closeDetailModal">&times;</button>
            <div class="modal-content-inner">
                <div class="modal-header">
                    <h2 class="modal-title"><i class="fas fa-file-invoice"></i> Detail Riwayat Resep</h2>
                </div>
                
                <div class="resep-detail" style="display: flex; justify-content: space-between; gap: 20px;">
                    <div><span class="label">Pasien</span><span class="value" id="modalPasienDetail"></span></div>
                    <div><span class="label">Dokter</span><span class="value" id="modalDokterDetail"></span></div>
                </div>

                <div class="resep-box">
                    <span class="label"><i class="fas fa-file-prescription"></i> Detail Resep dari Dokter:</span>
                    <div class="value" id="modalResepDetail"></div>
                </div>
                
                <div class="resep-box" style="background: #f0f7f6; border-color: #b2dfdb;">
                    <span class="label"><i class="fas fa-file-medical-alt"></i> Catatan dari Apoteker:</span>
                    <div class="value" id="modalCatatanDetail"></div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#tanggalFilter", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        locale: "id"
    });

    const modal = document.getElementById('detailModal');
    const closeModalBtn = document.getElementById('closeDetailModal');
    const openModalBtns = document.querySelectorAll('.btn-detail');

    function openModal(data) {
        document.getElementById('modalPasienDetail').textContent = data.pasien;
        document.getElementById('modalDokterDetail').textContent = data.dokter;
        document.getElementById('modalResepDetail').textContent = data.resep;
        document.getElementById('modalCatatanDetail').textContent = data.catatan;
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    openModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            openModal(this.dataset);
        });
    });

    closeModalBtn.addEventListener('click', closeModal);
    
    window.addEventListener('click', function(event) {
        if (event.target == modal) closeModal();
    });
});
</script>
@endpush