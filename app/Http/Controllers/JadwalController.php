<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPraktek;
use App\Models\DoctorAvailability;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Menampilkan halaman jadwal praktek untuk user.
     */
    public function index()
    {
        // Ambil semua jadwal praktek dengan relasi tenaga medis
        $jadwals = JadwalPraktek::with('tenagaMedis')->get();
        
        // ✅ Cek apakah dokter tutup hari ini
        $today = Carbon::today()->toDateString();
        
        foreach ($jadwals as $jadwal) {
            if ($jadwal->tenaga_medis_id) {
                // ✅ PERBAIKAN: Ganti 'closed_date' jadi 'date'
                $isClosed = DoctorAvailability::where('tenaga_medis_id', $jadwal->tenaga_medis_id)
                                              ->whereDate('date', $today)
                                              ->where('is_available', false)
                                              ->exists();
                
                $jadwal->is_closed_today = $isClosed;
            } else {
                $jadwal->is_closed_today = false;
            }
        }
        
        
        // Kirim data ke view 'jadwal.blade.php'
        return view('jadwal', ['jadwals' => $jadwals]);
    }
}
