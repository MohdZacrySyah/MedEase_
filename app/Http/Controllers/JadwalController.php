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
        
        // 1. Cek tanggal hari ini
        $today = Carbon::today()->toDateString();
        
        // 2. Ambil daftar ID tenaga medis yang TUTUP HARI INI
        $unavailableDoctorIds = DoctorAvailability::whereDate('date', $today)
                                                 ->where('is_available', false)
                                                 ->pluck('tenaga_medis_id')
                                                 ->toArray();

        // 3. Iterasi dan tambahkan flag is_closed_today (tanpa memfilter baris jadwal)
        foreach ($jadwals as $jadwal) {
            // Cek apakah jadwal ini memiliki tenaga medis dan apakah ID-nya ada di daftar yang tutup hari ini
            $isClosed = $jadwal->tenaga_medis_id && in_array($jadwal->tenaga_medis_id, $unavailableDoctorIds);
            
            // Properti is_closed_today akan digunakan di view
            $jadwal->is_closed_today = $isClosed;
        }
        
        // Kirim data ke view 'jadwal.blade.php'
        return view('jadwal', ['jadwals' => $jadwals]);
    }
}