@extends('layouts.tenaga_medis')
@section('title', 'Catat Hasil Pemeriksaan')

@section('content')
    <div class="form-container">
        <h1 class="form-title">Detail Pemeriksaan</h1>
        <p class="patient-info">Pasien: <strong>{{ $pendaftaran->nama_lengkap }}</strong></p>

        {{-- Bagian ini di dalam create.blade.php --}}
<div class="vitals-display">
    {{-- Gunakan null coalescing operator (??) untuk menangani jika data belum ada --}}
    <span>Tekanan Darah : <strong>{{ $pemeriksaanAwal->tekanan_darah ?? 'N/A' }}</strong></span>
    <span>Berat Badan : <strong>{{ $pemeriksaanAwal->berat_badan ?? 'N/A' }}</strong></span>
    <span>Suhu Tubuh : <strong>{{ $pemeriksaanAwal->suhu_tubuh ?? 'N/A' }}</strong></span>
</div>

        {{-- Ganti action ke route store nanti --}}
        <form action="{{ route('tenaga-medis.pemeriksaan.store', $pendaftaran->id) }}" method="POST">
            @csrf

            {{-- Input tersembunyi jika masih perlu Subjective/Objective --}}
            {{-- <input type="hidden" name="subjektif" value="{{ $pendaftaran->keluhan }}"> --}}
            {{-- <input type="hidden" name="objektif" value=""> --}}

            <div class="form-grid">
                <div class="form-group">
                    <label for="assessment" class="form-label">Diagnosa</label>
                    <textarea name="assessment" id="assessment" class="form-control" rows="4" required>{{ old('assessment') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="plan" class="form-label">Catatan Tambahan</label>
                    <textarea name="plan" id="plan" class="form-control" rows="4" required>{{ old('plan') }}</textarea>
                </div>
            </div>

            <div class="form-group form-group-harga">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" name="harga" id="harga" class="form-control input-harga" placeholder="Rp" step="any" value="{{ old('harga') }}">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan dan Kirim</button>
                <a href="{{ route('tenaga-medis.pasien.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .page-content { background-color: #f0f4f7; } /* Latar belakang agak abu */
    .form-container { max-width: 750px; margin: 20px auto; background: #fff; border-radius: 15px; padding: 35px 45px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid #e0e4e7; }
    .form-title { font-size: 1.8rem; color: #333; margin-top: 0; margin-bottom: 5px; font-weight: 600; text-align: center; }
    .patient-info { text-align: center; color: #555; margin-bottom: 25px; font-size: 1rem; }
    .vitals-display { background-color: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px; border: 1px dashed #ced4da; text-align: center; }
    .vitals-display span { font-size: 0.95rem; color: #444; }
    .vitals-display strong { color: #000; font-weight: 600; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
    .form-group { margin-bottom: 1rem; } /* Jarak bawah default */
    .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; transition: all 0.2s ease; }
    .form-control:focus { outline: none; border-color: #007e6c; box-shadow: 0 0 0 3px rgba(0,126,108,.2); }
    textarea.form-control { resize: vertical; min-height: 100px; }
    .form-group-harga { display: flex; align-items: center; gap: 10px; }
    .form-group-harga .form-label { margin-bottom: 0; font-size: 1.1rem; }
    .input-harga { max-width: 250px; }
    .form-actions { text-align: right; margin-top: 35px; border-top: 1px solid #eee; padding-top: 25px; }
    .btn-primary { background-color: #007e6c; color: #fff; border: none; padding: 12px 28px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: background-color .3s ease; font-size: 1rem; }
    .btn-secondary { margin-left: 15px; color: #555; text-decoration: none; font-weight: 500; }
</style>
@endpush