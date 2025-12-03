@extends('layouts.admin')
@section('title', 'Input Pemeriksaan Awal')

@section('content')
<div class="page-container">
    <div class="form-card">
        <h2 class="form-title">Input Pemeriksaan Awal</h2>
        <p class="patient-info">Pasien: <strong>{{ $pendaftaran->nama_lengkap }}</strong> | Layanan: {{ $pendaftaran->nama_layanan }}</p>

        <form action="{{ route('admin.pemeriksaan-awal.store', $pendaftaran->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="tekanan_darah" class="form-label">Tekanan Darah (cth: 120/80 mmHg)</label>
                <input type="text" name="tekanan_darah" id="tekanan_darah" class="form-control" value="{{ old('tekanan_darah', $pemeriksaanAwal->tekanan_darah ?? '') }}">
            </div>

            <div class="form-group">
                <label for="berat_badan" class="form-label">Berat Badan (cth: 56 kg)</label>
                <input type="text" name="berat_badan" id="berat_badan" class="form-control" value="{{ old('berat_badan', $pemeriksaanAwal->berat_badan ?? '') }}">
            </div>

            <div class="form-group">
                <label for="suhu_tubuh" class="form-label">Suhu Tubuh (cth: 36.5 °C)</label>
                <input type="text" name="suhu_tubuh" id="suhu_tubuh" class="form-control" value="{{ old('suhu_tubuh', $pemeriksaanAwal->suhu_tubuh ?? '') }}">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan Data Awal</button>
                {{-- Arahkan kembali ke daftar pendaftaran admin --}}
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a> 
            </div>
        </form>
    </div>
</div>
@endsection

@stack('styles')
    {{-- Salin style CSS dari form lain di admin --}}
    <style>
        .page-container{display:flex;flex-direction:column;align-items:center;padding:40px 20px}.form-card{background:#fff;padding:30px 40px;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.09);width:100%;max-width:600px;border: 1px solid #e9e9e9;}.form-title{text-align:center;margin-bottom:10px;color:#169400;font-weight:600}.patient-info{text-align:center;color:#555;margin-bottom:25px;font-size:1rem;}.form-group{margin-bottom:1.3rem}.form-label{display:block;margin-bottom:8px;font-weight:500;color:#333}.form-control{width:100%;padding:12px;border:1px solid #ccc;border-radius:8px}.form-actions{text-align:center;margin-top:30px}.btn-primary{background-color:#169400;color:#fff;border:none;padding:12px 28px;border-radius:8px;cursor:pointer;font-weight:600;text-decoration:none}.btn-secondary{margin-left:15px;color:#555;text-decoration:none;font-weight:500}
    </style>
@endstack