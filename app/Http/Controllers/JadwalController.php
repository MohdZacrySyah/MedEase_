<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPraktek; // Pastikan ini ada

class JadwalController extends Controller
{
    /**
     * Menampilkan halaman jadwal praktek untuk user.
     */
    public function index()
    {
        // PERBAIKAN: Tambahkan with('tenagaMedis')
        $jadwals = JadwalPraktek::with('tenagaMedis')->get(); // Pastikan relasi dimuat

        // Kirim data tersebut ke view 'jadwal.blade.php'
        return view('jadwal', ['jadwals' => $jadwals]);
    }
}