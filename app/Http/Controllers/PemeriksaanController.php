<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Pendaftaran; // <-- Tambahkan ini
use Illuminate\Http\Request;
use App\Models\PemeriksaanAwal;
use Illuminate\Support\Facades\Auth;
use App\Models\Resep; // <-- Tambahkan ini

class PemeriksaanController extends Controller
{
    /**
     * Menampilkan form untuk mencatat hasil pemeriksaan baru.
     */
 public function getModalDataJson(Pendaftaran $pendaftaran)
    {
        // Muat semua relasi yang dibutuhkan
        $pendaftaran->load('user', 'pemeriksaanAwal', 'pemeriksaan');

        $pemeriksaanAwal = $pendaftaran->pemeriksaanAwal;
        $pemeriksaanSOAP = $pendaftaran->pemeriksaan; // Ini adalah data SOAP

        return response()->json([
            // URL Form
            'form_action' => route('tenaga-medis.pemeriksaan.store', $pendaftaran->id),

            // Data Pasien & Keluhan
            'pasien_name'    => $pendaftaran->user->name ?? $pendaftaran->nama_lengkap,
            'layanan_name'   => $pendaftaran->nama_layanan,
            'keluhan'        => $pendaftaran->keluhan,
            'lama_keluhan'   => $pendaftaran->lama_keluhan,

            // Data Pemeriksaan Awal (untuk ditampilkan)
            'tekanan_darah'  => $pemeriksaanAwal->tekanan_darah ?? 'N/A',
            'berat_badan'    => $pemeriksaanAwal->berat_badan ?? 'N/A',
            'suhu_tubuh'     => $pemeriksaanAwal->suhu_tubuh ?? 'N/A',

            // Data Pemeriksaan SOAP (jika sudah pernah diisi)
            'subjektif'      => $pemeriksaanSOAP->subjektif ?? $pendaftaran->keluhan, // Default ke keluhan
            'objektif'       => $pemeriksaanSOAP->objektif ?? '',
            'assessment'     => $pemeriksaanSOAP->assessment ?? '',
            'plan'           => $pemeriksaanSOAP->plan ?? '',
            'resep_obat'     => $pemeriksaanSOAP->resep_obat ?? '',
            'harga'          => $pemeriksaanSOAP->harga ?? '',
        ]);
    }


    /**
     * (MODIFIKASI DI BAGIAN REDIRECT)
     * Menyimpan data pemeriksaan (SOAP).
     */
    public function store(Request $request, Pendaftaran $pendaftaran)
    {
        $validatedData = $request->validate([
            'subjektif' => 'nullable|string',
            'objektif' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'resep_obat' => 'nullable|string',
            'harga' => 'nullable|numeric',
        ]);
        

        // Simpan atau Update data pemeriksaan
      $pemeriksaan = Pemeriksaan::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                // AMBIL 'pasien_id' DARI PENDAFTARAN
                'pasien_id' =>$pendaftaran->user_id, // <--- TAMBAHKAN BARIS INI
                
                'tenaga_medis_id' => Auth::guard('tenaga_medis')->id(),
                'subjektif' => $validatedData['subjektif'],
                'objektif' => $validatedData['objektif'],
                'assessment' => $validatedData['assessment'],
                'plan' => $validatedData['plan'],
                'resep_obat' => $validatedData['resep_obat'],
                'harga' => $validatedData['harga'],
            ]
        );
        if (!empty($validatedData['resep_obat'])) {
        // Buat (atau update) antrian di tabel 'reseps'
        Resep::updateOrCreate(
            [
                'pemeriksaan_id' => $pemeriksaan->id // Cocokkan dengan ID pemeriksaan
            ],
            [
                'pasien_id' => $pendaftaran->user_id,
                'status' => 'Menunggu', // Set status awal ke 'Menunggu'
                'apoteker_id' => null, // Belum ada apoteker yang proses
                'catatan_apoteker' => null // Kosongkan catatan
            ]
        );
    }

        // Update status pendaftaran
        $pendaftaran->status = 'Selesai';
        $pendaftaran->save();

        // (MODIFIKASI) Redirect kembali ke halaman daftar pasien tenaga medis
        return redirect()->route('tenaga-medis.pasien.index')
                         ->with('success', 'Data pemeriksaan (SOAP) berhasil disimpan.');
    }
}