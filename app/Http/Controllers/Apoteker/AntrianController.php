<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep; // Pastikan Anda import Model Resep
use Illuminate\Support\Facades\Auth;

class AntrianController extends Controller
{
    /**
     * Menampilkan halaman antrian resep (Menunggu & Diproses).
     */
    public function index()
    {
        // Ambil resep yang belum Selesai
        $reseps = Resep::with('pasien', 'pemeriksaan.tenagaMedis')
                    ->whereIn('status', ['Menunggu', 'Diproses'])
                    ->orderBy('created_at', 'asc') // Tampilkan yang paling lama menunggu
                    ->get();
        
        // Kirim data ke view
        return view('apoteker.antrian.index', compact('reseps'));
    }

    /**
     * Memproses dan menyelesaikan resep.
     */
    public function selesaikan(Request $request, Resep $resep)
    {
        $request->validate([
            'catatan_apoteker' => 'nullable|string|max:1000',
        ]);

        // Update resepnya
        $resep->update([
            'status' => 'Selesai',
            'apoteker_id' => Auth::guard('apoteker')->id(),
            'catatan_apoteker' => $request->catatan_apoteker,
        ]);

        // Redirect kembali ke halaman riwayat (karena sudah selesai)
        return redirect()->route('apoteker.riwayat.index')
                         ->with('success', 'Resep untuk pasien ' . $resep->pasien->name . ' telah diselesaikan.');
    }
}