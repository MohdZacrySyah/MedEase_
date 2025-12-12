<?php
// app/Http/Controllers/Admin/JadwalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran; 
use App\Models\JadwalPraktek; 
use App\Models\DoctorAvailability; // ASUMSI Model ini sudah dibuat
use App\Notifications\JadwalDibatalkanNotification; // ASUMSI Notifikasi ini sudah dibuat
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth; 

class JadwalController extends Controller
{
    /**
     * Membatalkan jadwal dokter pada tanggal tertentu dan memberi notifikasi kepada pasien.
     */
    public function cancelJadwal(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tenaga_medis_id' => 'required|exists:tenaga_medis,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today', 
            'reason' => 'required|string|min:10',
        ]);

        $tenagaMedisId = $request->tenaga_medis_id;
        $date = $request->date;
        $reason = $request->reason;
        $notifiedCount = 0;

        // Mendapatkan nama hari dalam Bahasa Indonesia (PERBAIKAN KRUSIAL LOGIC)
        Carbon::setLocale('id');
        $namaHariIndo = Carbon::parse($date)->translatedFormat('l'); 

        DB::beginTransaction();

        try {
            // 2. Update status ketersediaan (DoctorAvailability)
            // Kolom tenaga_medis_id di sini HARUS ada di tabel doctor_availabilities Anda
            $availability = DoctorAvailability::firstOrNew([
                'tenaga_medis_id' => $tenagaMedisId, 
                'date' => $date
            ]);
            
            $availability->is_available = false;
            $availability->max_slots = 0;
            $availability->booked_slots = 0;
            $availability->reason = $reason;
            $availability->save();

            // 3. Cari Pendaftaran yang Terdampak
            
            // A. Cari JadwalPraktek ID (menggunakan nama hari yang benar)
            $jadwalIds = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                      // 👇 PERBAIKAN: Menggunakan whereJsonContains dan nama hari Indo
                                      ->whereJsonContains('hari', $namaHariIndo) 
                                      ->pluck('id');

            // B. Cari pendaftaran yang sesuai
            $pendaftarans = Pendaftaran::whereIn('jadwal_praktek_id', $jadwalIds)
                                     // 👇 PERBAIKAN: Filter berdasarkan tanggal jadwal yang dipilih
                                     ->whereDate('jadwal_dipilih', $date) 
                                     ->whereIn('status', ['Menunggu', 'Diperiksa Awal'])
                                     ->with('user') 
                                     ->get();

            // 4. Batalkan Pendaftaran dan Kirim Notifikasi
            foreach ($pendaftarans as $pendaftaran) {
                if ($pendaftaran->user) {
                    // Pendaftaran harus dimuat relasinya ke jadwalPraktek dan user
                    $pendaftaran->user->notify(new JadwalDibatalkanNotification($pendaftaran, $reason));
                    $notifiedCount++;
                }
                
                $pendaftaran->status = 'Dibatalkan';
                $pendaftaran->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal dokter berhasil dibatalkan dan pasien dinotifikasi.',
                'pasien_terdampak' => $notifiedCount
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Pembatalan Jadwal GAGAL oleh Admin. Detail: " . $e->getMessage() . " | Request: " . json_encode($request->all()));
            
            return response()->json([
                'status' => 'error',
                'message' => 'Pembatalan gagal di server. Harap periksa log detail.',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}