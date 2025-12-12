@extends('layouts.main')
@section('title', 'Detail Pendaftaran')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <a href="{{ route('notifikasi.list') }}" class="btn btn-secondary mb-4"><i class="fas fa-arrow-left"></i> Kembali ke Notifikasi</a>

                {{-- Tentukan Status dan Warna Kartu --}}
                @php
                    $isCancelled = $pendaftaran->status === 'Dibatalkan';
                    $cardClass = $isCancelled ? 'border-danger' : 'border-success';
                    $iconClass = $isCancelled ? 'fa-calendar-times text-danger' : 'fa-calendar-check text-success';
                @endphp
                
                <div class="card shadow {{ $cardClass }}">
                    <div class="card-header bg-{{ $isCancelled ? 'danger' : 'success' }} text-white">
                        <h4 class="mb-0">
                            <i class="fas {{ $isCancelled ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
                            Detail Pendaftaran #{{ $pendaftaran->id }}
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        @if ($isCancelled)
                            <div class="alert alert-danger text-center">
                                <h5>JADWAL DIBATALKAN</h5>
                                <p>Jadwal ini telah dibatalkan oleh Admin/Tenaga Medis. Silakan buat pendaftaran baru.</p>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Dokter/Bidan:</strong><br>
                                {{ $pendaftaran->jadwalPraktek->tenagaMedis->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Layanan:</strong><br>
                                {{ $pendaftaran->nama_layanan }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Tanggal Kunjungan:</strong><br>
                                {{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('dddd, D MMMM YYYY') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Nomor Antrian:</strong><br>
                                <span class="badge bg-primary fs-5">{{ $pendaftaran->no_antrian ?? 'BELUM ADA' }}</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Keluhan:</strong><br>
                                {{ $pendaftaran->keluhan }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection