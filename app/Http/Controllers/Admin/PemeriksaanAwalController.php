<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PemeriksaanAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemeriksaanAwalController extends Controller
{
    public function getModalDataJson(Pendaftaran $pendaftaran)
    {
        // Load relasi user jika belum
        $pendaftaran->load('user', 'pemeriksaanAwal');

        // Ambil data pemeriksaan awal jika ada
        $pemeriksaanAwal = $pendaftaran->pemeriksaanAwal;

        // Kembalikan data yang dibutuhkan oleh modal
        return response()->json([
            // URL untuk action form
            'form_action' => route('admin.pemeriksaan-awal.store', $pendaftaran->id),

            // Data Pasien
            'pasien_name'    => $pendaftaran->user->name ?? $pendaftaran->nama_lengkap,
            'layanan_name'   => $pendaftaran->nama_layanan,
            'keluhan'        => $pendaftaran->keluhan,
            'lama_keluhan'   => $pendaftaran->lama_keluhan,

            // (TAMBAHAN) Data Pemeriksaan Awal (jika sudah ada)
            'tekanan_darah'  => $pemeriksaanAwal->tekanan_darah ?? '',
            'berat_badan'    => $pemeriksaanAwal->berat_badan ?? '',
            'suhu_tubuh'     => $pemeriksaanAwal->suhu_tubuh ?? '',
        ]);
    }
    // Menampilkan form pemeriksaan awal
    public function create(Pendaftaran $pendaftaran)
    {
        // Cek apakah sudah ada pemeriksaan awal sebelumnya
        $pemeriksaanAwal = $pendaftaran->pemeriksaanAwal ?? null; 
        return view('admin.pemeriksaan_awal.form', compact('pendaftaran', 'pemeriksaanAwal'));
    }

    // Menyimpan data pemeriksaan awal
    public function store(Request $request, Pendaftaran $pendaftaran)
    {
        $validatedData = $request->validate([
            'tekanan_darah' => 'nullable|string|max:20',
            'berat_badan' => 'nullable|string|max:10',
            'suhu_tubuh' => 'nullable|string|max:10',
        ]);

        $validatedData['pendaftaran_id'] = $pendaftaran->id;
        $validatedData['dicatat_oleh'] = Auth::guard('admin')->id();

        // Simpan atau update data pemeriksaan awal
        PemeriksaanAwal::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            $validatedData
        );

        // --- TAMBAHKAN KODE INI ---
        // Update status pendaftaran menjadi 'Diperiksa Awal'
        $pendaftaran->status = 'Diperiksa Awal';
        $pendaftaran->save();
        // --- AKHIR KODE TAMBAHAN ---

        // Arahkan kembali ke halaman daftar pendaftaran (catatan pemeriksaan)
        return redirect()->route('admin.catatanpemeriksaan')->with('success', 'Data pemeriksaan awal berhasil disimpan.'); // Redirect ke catatanpemeriksaan
    }
}