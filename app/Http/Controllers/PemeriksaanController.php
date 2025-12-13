<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Pendaftaran; // <-- Tambahkan ini
use Illuminate\Http\Request;
use App\Models\PemeriksaanAwal;
use Illuminate\Support\Facades\Auth;
use App\Models\Resep; // <-- Tambahkan ini
use Carbon\Carbon; // Pastikan ini ter-import
use Illuminate\Support\Facades\DB; // Pastikan ini ter-import

class PemeriksaanController extends Controller
{
    // Durasi standar sesi dalam menit
    const DURASI_SESI_STANDAR = 20;

    /**
     * Menampilkan form untuk mencatat hasil pemeriksaan baru.
     */
    public function getModalDataJson(Pendaftaran $pendaftaran)
    {
        // ... (Metode ini tetap sama)
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
                    'subjektif' => $validatedData['subjektif'],
                    'objektif' => $validatedData['objektif'],
                    'assessment' => $validatedData['assessment'],
                    'plan' => $validatedData['plan'],
                    'resep_obat' => $validatedData['resep_obat'],
                    'harga' => $validatedData['harga'],
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
            // Save di akhir block transaction

            // 4. Hitung Perubahan Waktu (Core Logic)
            
            // Perbaikan: Pastikan $pendaftaran->estimasi_dilayani di-parse sebagai Carbon.
            // Jika estimasi_dilayani hanya berisi "H:i:s" (TIME), kita kombinasikan dengan tanggal hari ini.
            $estimasi_dilayani_awal = Carbon::parse($pendaftaran->jadwal_dipilih . ' ' . $pendaftaran->estimasi_dilayani);

            // Hitung perkiraan waktu selesai awal (Estimasi Dilayani Awal + Durasi Standar 20 menit)
            $estimasi_selesai_awal = $estimasi_dilayani_awal->addMinutes(self::DURASI_SESI_STANDAR);

            // Hitung selisih waktu (dalam menit)
            // Selisih NEGATIF berarti pemeriksaan selesai lebih cepat dari estimasi
            $perubahan_waktu_menit = $estimasi_selesai_awal->diffInMinutes($waktu_selesai_aktual, false) * -1; 
            
            // 5. Update estimasi untuk antrian berikutnya jika ada perubahan
            if ($perubahan_waktu_menit !== 0) {
                
                $tanggal_pemeriksaan = $pendaftaran->jadwal_dipilih; // Menggunakan kolom tanggal yang pasti string/tanggal

                $antrian_berikutnya = Pendaftaran::where('jadwal_praktek_id', $pendaftaran->jadwal_praktek_id)
                                                 ->where('no_antrian', '>', $pendaftaran->no_antrian)
                                                 ->whereDate('jadwal_dipilih', '=', $tanggal_pemeriksaan) // Menggunakan kolom tanggal yang aman
                                                 ->where('status', '!=', 'Batal') 
                                                 ->where('status', '!=', 'Selesai') 
                                                 ->orderBy('no_antrian', 'asc')
                                                 ->get();

                foreach ($antrian_berikutnya as $antrian) {
                    
                    // Perbaikan: Parse estimasi lama sebelum ditambahkan
                    $estimasi_lama = Carbon::parse($antrian->jadwal_dipilih . ' ' . $antrian->estimasi_dilayani);
                    
                    $estimasi_baru = $estimasi_lama->addMinutes($perubahan_waktu_menit);
                    
                    // Simpan kembali sebagai TIME string saja jika kolomnya hanya TIME
                    $antrian->estimasi_dilayani = $estimasi_baru->format('H:i:s');
                    $antrian->save();
                }
            }
            
            // Simpan perubahan pada pendaftaran yang baru selesai (termasuk waktu_selesai_periksa)
            $pendaftaran->save(); // 👈 Baris ini memastikan data pendaftaran yang baru selesai juga tersimpan

            // [OPSIONAL] PANGGIL EVENT BROADCASTING DI SINI
        });


        // (MODIFIKASI) Redirect kembali ke halaman daftar pasien tenaga medis
        return redirect()->route('tenaga-medis.pasien.index')
                         ->with('success', 'Data pemeriksaan (SOAP) berhasil disimpan dan estimasi waktu antrian telah diperbarui secara dinamis.');
    }
}