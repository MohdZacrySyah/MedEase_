@extends('layouts.admin')
@section('title', 'Riwayat Pasien: ' . $user->name)

@section('content')
<div class="container">
    <h2 class="page-title">Riwayat Pemeriksaan: {{ $user->name }}</h2>
    <a href="{{ route('admin.keloladatapasien') }}" class="btn-back">&larr; Kembali ke Kelola Data Pasien</a>

    <div class="riwayat-list">
        @forelse ($riwayats as $riwayat)
            <div class="riwayat-item">
                <div class="riwayat-header">
                    <span class="tanggal">{{ $riwayat->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}</span> 
                    <span class="dokter">Diperiksa oleh: <strong>{{ $riwayat->tenagaMedis->name ?? 'N/A' }}</strong></span>
                </div>
                <div class="riwayat-body">
                    {{-- Tampilkan gabungan data --}}
                    <div class="detail-section">
                        <h4 class="section-title">Hasil Pemeriksaan</h4>
                        <div class="detail-item"><span class="label">Diagnosis</span><p class="value"><strong>{{ $riwayat->assessment ?? '-' }}</strong></p></div>
                        <div class="detail-item"><span class="label">Catatan/Plan</span><p class="value">{{ $riwayat->plan ?? '-' }}</p></div>
                        @if($riwayat->harga)
                        <div class="detail-item harga-item"><span class="label">Biaya</span><span class="value harga">Rp {{ number_format($riwayat->harga, 0, ',', '.') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="no-riwayat-info">Pasien ini belum memiliki riwayat pemeriksaan.</div>
        @endforelse
    </div>
</div>
@endsection

@stack('styles')
{{-- Salin style CSS dari riwayat_pemeriksaan.blade.php (view pasien) --}}
<style>
    .page-title { font-size: 1.8rem; color: #007e6c; margin-bottom: 10px; font-weight: 600; }
    .btn-back { display: inline-block; margin-bottom: 25px; color: #555; text-decoration: none; }
    .riwayat-list { display: flex; flex-direction: column; gap: 25px; }
    .riwayat-item { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.07); border: 1px solid #e9e9e9; overflow: hidden; }
    .riwayat-header { background-color: #f8f9fa; padding: 12px 20px; border-bottom: 1px solid #e0e4e7; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: #555; }
    .riwayat-header .tanggal { font-weight: 500; }
    .riwayat-header .dokter strong { color: #007e6c; }
    .riwayat-body { padding: 25px; }
    .detail-section { margin-bottom: 0; }
    .section-title { font-size: 1rem; color: #007e6c; margin-top: 0; margin-bottom: 15px; font-weight: 600; border-left: 3px solid #007e6c; padding-left: 8px; }
    .detail-item { margin-bottom: 12px; } .detail-item:last-child { margin-bottom: 0; }
    .detail-item .label { display: block; font-size: 0.85rem; color: #777; margin-bottom: 4px; font-weight: 500; }
    .detail-item .value { margin: 0; font-size: 1rem; color: #333; line-height: 1.5; }
    .detail-item .value strong { font-weight: 600; }
    .harga-item { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .harga { font-weight: 600; font-size: 1.1rem; color: #007e6c; }
    .no-riwayat-info { text-align: center; color: #777; padding: 30px; background: #fff; border-radius: 12px; border: 1px solid #e9e9e9; }
</style>
@endstack