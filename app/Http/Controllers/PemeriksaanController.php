<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Pendaftaran; 
use Illuminate\Http\Request;
use App\Models\PemeriksaanAwal;
use Illuminate\Support\Facades\Auth;
use App\Models\Resep; 
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB; 

class PemeriksaanController extends Controller
{
    // Durasi standar sesi dalam menit
    const DURASI_SESI_STANDAR = 20;

    /**
     * Menampilkan form untuk mencatat hasil pemeriksaan baru.
     */
    public function getModalDataJson(Pendaftaran $pendaftaran)
    {
        // ... (Metode ini tidak diubah)
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
     * NEW: Menandai pendaftaran sebagai "Sedang Diperiksa" dan mencatat waktu mulai.
     */
    public function mulaiPemeriksaan(Pendaftaran $pendaftaran)
    {
        // 1. Catat status dan waktu mulai pemeriksaan
        $pendaftaran->status_antrian = 'Sedang Diperiksa'; 
        $pendaftaran->waktu_mulai_periksa = Carbon::now(); 
        $pendaftaran->save();
        
        // [OPSIONAL] PANGGIL EVENT BROADCASTING DI SINI

        return redirect()->route('tenaga-medis.pemeriksaan.create', $pendaftaran->id)
                         ->with('success', 'Pemeriksaan dimulai. Status antrian diperbarui.');
    }


    /**
     * (MODIFIKASI DI BAGIAN PERHITUNGAN ULANG)
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
        
        DB::transaction(function () use ($pendaftaran, $validatedData, $request) {
            
            // 1. Simpan atau Update data pemeriksaan (SOAP)
            $pemeriksaan = Pemeriksaan::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'pasien_id' =>$pendaftaran->user_id, 
                    'tenaga_medis_id' => Auth::guard('tenaga_medis')->id(),
                    // FIX: Menggunakan Null Coalescing (??) untuk mencegah QueryException jika field kosong
                    'subjektif' => $validatedData['subjektif'] ?? '',
                    'objektif' => $validatedData['objektif'] ?? '',
                    'assessment' => $validatedData['assessment'] ?? '', 
                    'plan' => $validatedData['plan'] ?? '',
                    'resep_obat' => $validatedData['resep_obat'] ?? '',
                    'harga' => $validatedData['harga'] ?? 0, 
                ]
            );

            // 2. Logika pembuatan resep
            if (!empty($validatedData['resep_obat'])) {
                Resep::updateOrCreate(
                    [
                        'pemeriksaan_id' => $pemeriksaan->id 
                    ],
                    [
                        'pasien_id' => $pendaftaran->user_id,
                        'status' => 'Menunggu', 
                        'apoteker_id' => null, 
                        'catatan_apoteker' => null 
                    ]
                );
            }

            // 3. Catat waktu selesai aktual dan update status pendaftaran
            $waktu_selesai_aktual = Carbon::now();
            $pendaftaran->waktu_selesai_periksa = $waktu_selesai_aktual; 
            $pendaftaran->status = 'Selesai';
            $pendaftaran->status_antrian = 'Selesai Dilayani';

            // 4. Hitung Perubahan Waktu (Core Logic)
            
            // FIX TIME SHIFT: Pastikan estimasi_dilayani di-parse sebagai Carbon dari tanggal dan waktu
            $estimasi_dilayani_awal_carbon = Carbon::parse($pendaftaran->jadwal_dipilih . ' ' . $pendaftaran->estimasi_dilayani);

            // Gunakan CLONE agar objek asli tidak berubah
            $estimasi_selesai_awal = clone $estimasi_dilayani_awal_carbon;
            $estimasi_selesai_awal->addMinutes(self::DURASI_SESI_STANDAR); // Waktu SELESAI yang DIHARAPKAN

            // Hitung selisih waktu (dalam menit)
            // $A->diffInMinutes($B, false) adalah B - A
            // Selisih = (Waktu Selesai Aktual) - (Waktu Selesai Diharapkan)
            $selisih_aktual_vs_estimasi = $waktu_selesai_aktual->diffInMinutes($estimasi_selesai_awal, false); 
            
            // Perubahan yang diterapkan harus berlawanan tanda dengan selisih.
            // Jika selisih -10 (lebih cepat), perubahan harus +10 (maju).
            $perubahan_waktu_menit = $selisih_aktual_vs_estimasi * -1; 

            // 5. Update estimasi untuk antrian berikutnya jika ada perubahan
            if ($perubahan_waktu_menit !== 0) {
                
                $tanggal_pemeriksaan = $pendaftaran->jadwal_dipilih; 

                $antrian_berikutnya = Pendaftaran::where('jadwal_praktek_id', $pendaftaran->jadwal_praktek_id)
                                                 ->where('no_antrian', '>', $pendaftaran->no_antrian)
                                                 ->whereDate('jadwal_dipilih', '=', $tanggal_pemeriksaan) 
                                                 ->where('status', '!=', 'Batal') 
                                                 ->where('status', '!=', 'Selesai') 
                                                 ->orderBy('no_antrian', 'asc')
                                                 ->get();

                foreach ($antrian_berikutnya as $antrian) {
                    
                    // Parse estimasi lama dari kolom tanggal dan waktu
                    $estimasi_lama = Carbon::parse($antrian->jadwal_dipilih . ' ' . $antrian->estimasi_dilayani);
                    
                    // FIX TIME SHIFT: Gunakan CLONE agar mutasi tidak merusak loop
                    $estimasi_baru = clone $estimasi_lama;
                    $estimasi_baru->addMinutes($perubahan_waktu_menit);
                    
                    // Simpan kembali sebagai TIME string saja
                    $antrian->estimasi_dilayani = $estimasi_baru->format('H:i:s');
                    $antrian->save();
                }
            }
            
            // Simpan perubahan pada pendaftaran yang baru selesai
            $pendaftaran->save(); 

            // [OPSIONAL] PANGGIL EVENT BROADCASTING DI SINI
        });


        // (MODIFIKASI) Redirect kembali ke halaman daftar pasien tenaga medis
        return redirect()->route('tenaga-medis.pasien.index')
                         ->with('success', 'Data pemeriksaan (SOAP) berhasil disimpan dan estimasi waktu antrian telah diperbarui secara dinamis.');
    }
}