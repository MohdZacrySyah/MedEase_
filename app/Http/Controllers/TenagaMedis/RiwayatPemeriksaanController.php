<?php

namespace App\Http\Controllers\TenagaMedis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemeriksaan;
use App\Models\Layanan;
use App\Models\TenagaMedis;
use App\Models\User; // <-- TAMBAHKAN INI

class RiwayatPemeriksaanController extends Controller
{
    public function index(Request $request)
    {
        // 1. VALIDASI: Pastikan pasien_id ada di request
        if (!$request->filled('pasien_id')) {
            return redirect()->route('tenaga-medis.pasien.index') // Kembali ke daftar pasien
                 ->with('error', 'Silakan pilih pasien terlebih dahulu untuk melihat riwayat.');
        }

        // 2. Cari Pasien untuk judul halaman
        $pasien = User::find($request->pasien_id);
        if (!$pasien) {
            return redirect()->route('tenaga-medis.pasien.index')
                 ->with('error', 'Pasien tidak ditemukan.');
        }

        // 3. Mulai query HANYA untuk pasien ini
        $query = Pemeriksaan::with([
            'pendaftaran.user', 
            'pendaftaran.pemeriksaanAwal', 
            'tenagaMedis'
        ])->where('pemeriksaans.pasien_id', $request->pasien_id); // Filter utama

        // 4. Terapkan Filter Tambahan (tanggal, layanan, nakes)
        if ($request->filled('tanggal')) {
            $query->whereDate('pemeriksaans.created_at', $request->tanggal);
        }
        if ($request->filled('layanan_id')) {
            $query->whereHas('pendaftaran', function($q) use ($request) {
                $q->where('layanan_id', $request->layanan_id);
            });
        }
        if ($request->filled('tenaga_medis_id')) {
            $query->where('tenaga_medis_id', $request->tenaga_medis_id);
        }

        // 5. Ambil data hasil filter
        $riwayats = $query->latest('pemeriksaans.created_at')->get();

        // 6. Ambil data untuk dropdown filter
        $layanans = Layanan::orderBy('nama_layanan')->get();
        $tenagaMedisList = TenagaMedis::orderBy('name')->get();
        
        // 7. Kirim ke View
        return view('tenaga_medis.riwayat.index', [
            'riwayats' => $riwayats,
            'layanans' => $layanans,
            'tenagaMedisList' => $tenagaMedisList,
            'request' => $request, 
            'pasien' => $pasien, // Kirim data pasien ke view
        ]);
    }
}