@extends('layouts.apoteker')
@section('title', 'Antrian Resep')

@push('styles')
<style>
    /* Menggunakan font dari layout induk (Poppins) */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    /* ===== HEADER BANNER (dari Admin Dashboard) ===== */
    .dashboard-header-banner { 
        margin-bottom: 40px; 
        animation: fadeInDown 0.6s ease-out; /* <-- ANIMASI DITAMBAHKAN */
    }
    .header-content { display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #169400 0%, #1cc200 100%); padding: 30px 35px; border-radius: 20px; box-shadow: 0 8px 30px rgba(22, 148, 0, 0.2); position: relative; overflow: hidden; }
    .header-content::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; }
    .header-icon { width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #fff; flex-shrink: 0; position: relative; z-index: 1; }
    .header-text { flex: 1; position: relative; z-index: 1; }
    .page-title { color: #fff; font-weight: 700; font-size: 2rem; margin: 0 0 8px 0; letter-spacing: -0.5px; }
    .page-subtitle { display: flex; align-items: center; gap: 8px; color: rgba(255, 255, 255, 0.9); font-size: 1rem; font-weight: 400; margin: 0; }

    /* ===== TABLE STYLES (dari Admin Dashboard) ===== */
    .table-card-modern { 
        background: #fff; 
        border-radius: 20px; 
        box-shadow: 0 4px 20px rgba(22, 148, 0, 0.08); 
        border: 1px solid rgba(22, 148, 0, 0.1); 
        overflow: hidden; 
        margin-bottom: 25px; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        animation: fadeInUp 0.6s ease-out 0.1s backwards; /* <-- ANIMASI DITAMBAHKAN */
    }
    .table-card-modern:hover { box-shadow: 0 12px 40px rgba(22, 148, 0, 0.18); border-color: rgba(22, 148, 0, 0.3); }
    .table-card-header { background: linear-gradient(135deg, #169400, #1cc200); padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .table-title { font-size: 1.1rem; font-weight: 600; color: white; margin: 0; display: flex; align-items: center; gap: 10px; }
    .badge-count { display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.2); color: white; padding: 8px 18px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); }
    .table-container-modern { overflow-x: auto; }
    .data-table-modern { width: 100%; border-collapse: collapse; }
    .data-table-modern thead { background: linear-gradient(135deg, #169400, #1cc200); }
    .data-table-modern thead th { padding: 18px 20px; text-align: left; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .data-table-modern thead th i { margin-right: 8px; opacity: 0.9; }
    .table-row { 
        border-bottom: 1px solid #f0f0f0; 
        transition: all 0.3s ease; 
        animation: fadeInUp 0.5s ease forwards; /* <-- ANIMASI BARIS TABEL */
        opacity: 0; 
    }
    .table-row:hover { background: #f8fffe; }
    .data-table-modern tbody td { padding: 18px 20px; color: #6b7280; vertical-align: middle; }
    .text-center { text-align: center !important; }

    /* ===== BADGE & BUTTON (dari Admin Dashboard) ===== */
    .status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
    .status-menunggu { background: linear-gradient(135deg, #fff3e0, #ffe0b2); color: #e65100; }
    .status-diproses { background: linear-gradient(135deg, #e3f2fd, #bbdefb); color: #1976d2; }
    
    .btn-action-primary { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #169400 0%, #1cc200 100%); color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: none; box-shadow: 0 4px 12px rgba(22, 148, 0, 0.25); position: relative; overflow: hidden; }
    .btn-action-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s ease; }
    .btn-action-primary:hover::before { left: 100%; }
    .btn-action-primary:hover { background: linear-gradient(135deg, #0f7300 0%, #169400 100%); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(22, 148, 0, 0.35); }
    .btn-action-primary i { transition: transform 0.3s ease; }
    .btn-action-primary:hover i { transform: translateX(4px); }

    .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
    .empty-state p { margin: 0; font-size: 1.05rem; font-weight: 500; }
    
    /* ===== MODAL STYLES (Baru, tapi didesain mirip) ===== */
    .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); justify-content: center; align-items: center; animation: fadeIn 0.3s; }
    .modal-card { background-color: #fff; margin: 20px; border-radius: 24px; width: 90%; max-width: 700px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); animation: slideUp 0.4s; max-height: 90vh; display: flex; flex-direction: column; position: relative; }
    .close-modal { position: absolute; top: -15px; right: -15px; width: 45px; height: 45px; background: #fff; border: none; border-radius: 50%; cursor: pointer; z-index: 10; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
    .close-modal i { font-size: 1.2rem; color: #6b7280; }
    .close-modal:hover { background: #ef4444; transform: rotate(90deg); }
    .close-modal:hover i { color: #fff; }
    
    .modal-content-inner { padding: 40px; overflow-y: auto; }
    .modal-header { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #f3f4f6; }
    .modal-title { color: #169400; font-weight: 700; font-size: 1.8rem; margin: 0; display: flex; align-items: center; justify-content: center; gap: 12px; }
    
    .resep-detail { margin-bottom: 24px; }
    .resep-detail .label { font-size: 0.8rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
    .resep-detail .value { font-size: 1.1rem; color: #1f2937; font-weight: 600; }
    
    .resep-box { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .resep-box .label { color: #169400; font-weight: 700; margin-bottom: 10px; display: block; }
    .resep-box .value { white-space: pre-wrap; word-wrap: break-word; line-height: 1.7; color: #333; }

    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 10px; font-weight: 600; color: #374151; }
    .form-control { width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.95rem; font-family: 'Poppins', sans-serif; background-color: #fafafa; transition: all 0.3s ease; }
    .form-control:focus { outline: none; border-color: #169400; background-color: #fff; box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1); }
    textarea.form-control { resize: vertical; min-height: 100px; }
    
    .form-actions { display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 2px solid #f3f4f6; }
    .btn-primary { flex: 1; background: linear-gradient(135deg, #169400 0%, #1cc200 100%); color: #fff; border: none; padding: 16px 32px; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 1.05rem; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(22, 148, 0, 0.25); }
    .btn-primary:hover { background: linear-gradient(135deg, #0f7300 0%, #169400 100%); transform: translateY(-2px); }
    .btn-secondary { background: #f3f4f6; color: #4b5563; border: none; padding: 16px 32px; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 1.05rem; }
    
    /* ===== ANIMATIONS (dari Admin Dashboard) ===== */
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')

    {{-- Header Banner --}}
    <div class="dashboard-header-banner">
        <div class="header-content">
            <div class="header-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
            <div class="header-text">
                <h1 class="page-title">Antrian Resep Obat</h1>
                <p class="page-subtitle">Proses resep yang masuk dari dokter.</p>
            </div>
        </div>
    </div>

    {{-- Tabel Antrian Resep --}}
    <div class="table-card-modern">
        <div class="table-card-header">
            <h3 class="table-title">
                <i class="fas fa-hourglass-half"></i> 
                Resep Menunggu Diproses
            </h3>
        </div>
        <div class="table-container-modern">
            <table class="data-table-modern">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt"></i> Tgl Masuk</th>
                        <th><i class="fas fa-user-injured"></i> Nama Pasien</th>
                        <th><i class="fas fa-user-md"></i> Dokter</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reseps as $resep) 
                        {{-- ANIMASI DITAMBAHKAN DI SINI --}}
                        <tr class="table-row" style="animation-delay: {{ $loop->index * 0.1 }}s">
                            <td>{{ $resep->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                            <td>{{ $resep->pasien->name ?? 'N/A' }}</td>
                            <td>{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}</td>
                            <td>
                                @if($resep->status == 'Diproses')
                                    <span class="status-badge status-diproses">
                                        <i class="fas fa-sync-alt"></i> Sedang Diproses
                                    </span>
                                @else
                                    <span class="status-badge status-menunggu">
                                        <i class="fas fa-hourglass-start"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn-action-primary btn-proses" 
                                        data-id_resep="{{ $resep->id }}"
                                        data-pasien="{{ $resep->pasien->name ?? 'N/A' }}"
                                        data-dokter="{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}"
                                        data-resep="{{ $resep->pemeriksaan->resep_obat ?? 'Tidak ada resep' }}"
                                        data-catatan_lama="{{ $resep->catatan_apoteker ?? '' }}"
                                        data-action_url="{{ route('apoteker.antrian.selesaikan', $resep->id) }}">
                                    <i class="fas fa-eye"></i>
                                    <span>Lihat & Proses</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <p>Tidak ada resep yang menunggu. Kerja bagus!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Proses Resep --}}
    <div id="prosesModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 700px;">
            <button class="close-modal" id="closeProsesModal">&times;</button>
            <div class="modal-content-inner">
                <div class="modal-header">
                    <h2 class="modal-title"><i class="fas fa-pills"></i> Proses Resep Pasien</h2>
                </div>
                
                <div class="resep-detail" style="display: flex; justify-content: space-between; gap: 20px;">
                    <div>
                        <span class="label">Pasien</span>
                        <span class="value" id="modalPasien"></span>
                    </div>
                    <div>
                        <span class="label">Dokter</span>
                        <span class="value" id="modalDokter"></span>
                    </div>
                </div>

                <div class="resep-box">
                    <span class="label"><i class="fas fa-file-prescription"></i> Detail Resep dari Dokter:</span>
                    <div class="value" id="modalResep"></div>
                </div>

                <form id="formProsesResep" action="" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="catatan_apoteker" class="form-label">Catatan Apoteker (Opsional)</label>
                        <textarea name="catatan_apoteker" id="catatan_apoteker" class="form-control" rows="3" placeholder="Misal: Obat X diganti dengan Y atas persetujuan..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="btnBatalProses">Batal</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle"></i> Selesaikan & Berikan Obat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Skrip JS (tidak berubah) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('prosesModal');
    const closeModalBtn = document.getElementById('closeProsesModal');
    const batalBtn = document.getElementById('btnBatalProses');
    const openModalBtns = document.querySelectorAll('.btn-proses');

    function openModal(data) {
        document.getElementById('modalPasien').textContent = data.pasien;
        document.getElementById('modalDokter').textContent = data.dokter;
        document.getElementById('modalResep').textContent = data.resep;
        document.getElementById('catatan_apoteker').value = data.catatan_lama;
        document.getElementById('formProsesResep').action = data.action_url;
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
    batalBtn.addEventListener('click', closeModal);
    
    window.addEventListener('click', function(event) {
        if (event.target == modal) closeModal();
    });
});
</script>
@endpush