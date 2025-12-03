@extends('layouts.admin')
@section('title', 'Daftar Pendaftaran Pasien')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 20px;">Daftar Pendaftaran Pasien</h2>

    {{-- Loop untuk setiap layanan --}}
    @forelse ($pendaftarans->groupBy('nama_layanan') as $layanan => $listPendaftaran)
        <div class="layanan-group">
            <h3 class="layanan-header">{{ $layanan }}</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Jadwal Dipilih</th>
                            <th>Tgl Daftar</th>
                            <th>Status</th> {{-- Kolom Baru --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop untuk setiap pendaftaran dalam grup layanan ini --}}
                        @forelse ($listPendaftaran as $pendaftaran)
                            <tr>
                                <td>{{ $pendaftaran->user->name ?? $pendaftaran->nama_lengkap }}</td>
                                <td>{{ Str::limit($pendaftaran->keluhan, 40) }}</td>
                                <td>{{ \Carbon\Carbon::parse($pendaftaran->jadwal_dipilih)->isoFormat('dddd, D MMMM Y') }}</td>
                                <td>{{ $pendaftaran->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                                {{-- Menampilkan Status --}}
                                <td>
                                    <span class="status-badge status-{{ Str::slug($pendaftaran->status) }}">
                                        {{ $pendaftaran->status }}
                                    </span>
                                </td> 
                                <td>
                                    <a href="{{ route('admin.pemeriksaan-awal.create', $pendaftaran->id) }}" class="btn-action btn-periksa-awal">
                                        Input Periksa Awal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Belum ada pasien mendaftar untuk layanan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p style="text-align: center; color: #777; padding: 30px;">Belum ada pendaftaran pasien sama sekali.</p>
    @endforelse

</div>
@endsection

@push('styles')
<style>
    /* Style untuk grouping */
    .layanan-group { margin-bottom: 30px; }
    .layanan-header { 
        font-size: 1.4rem; 
        color: #007e6c; 
        margin-bottom: 15px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #007e6c; 
        font-weight: 600;
    }

    /* Style Tabel (Sama seperti sebelumnya, mungkin perlu disesuaikan) */
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.07); overflow: hidden; border: 1px solid #e9e9e9; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    .data-table th, .data-table td { padding: 12px 18px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .data-table th { background-color: #007e6c; color: white; font-weight: 500; white-space: nowrap; }
    .data-table tbody tr:hover { background-color: #f7fafc; }
    .data-table td[colspan] { text-align: center; color: #777; padding: 20px; } /* Penyesuaian colspan */
    .btn-action { /* ... style tombol ... */ }
    .btn-periksa-awal { background-color: #ffb300; color: white; /* Oranye */ }
    .btn-detail { background-color: #1e88e5; /* Biru */ }
    .btn-action:hover { opacity: 0.85; }

    /* Style untuk Badge Status */
    .status-badge {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .status-menunggu { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-diperiksa-awal { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .status-sedang-diperiksa { background-color: #cfe2ff; color: #084298; border: 1px solid #b6d4fe; }
    .status-selesai { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

</style>
@endpush