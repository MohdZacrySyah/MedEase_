<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;

class AntrianController extends Controller
{
    public function index()
    {
        // LOGIC URUTAN: orderBy('created_at', 'asc') -> Yang duluan masuk, ada di paling atas
        $reseps = Resep::with(['pasien', 'pemeriksaan.tenagaMedis'])
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->orderBy('created_at', 'asc') 
            ->get();

        return view('apoteker.antrian.index', compact('reseps'));
    }

    /**
     * 🔥 METHOD BARU: AUTO LOAD DATA (AJAX)
     */
    public function getUpdates()
    {
        // Gunakan logic urutan yang sama (ASC / Terlama dulu)
        $reseps = Resep::with(['pasien', 'pemeriksaan.tenagaMedis'])
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->orderBy('created_at', 'asc') 
            ->get();

        // Render file partial yang baru kita buat
        $html = view('apoteker.antrian.table_rows', compact('reseps'))->render();

        return response()->json([
            'html' => $html,
            'count' => $reseps->count()
        ]);
    }

    public function selesaikan(Request $request, Resep $resep)
    {
        $request->validate([
            'catatan_apoteker' => 'nullable|string'
        ]);

        $resep->update([
            'status' => 'Selesai', // Atau 'Diambil' sesuai flow Anda
            'catatan_apoteker' => $request->catatan_apoteker,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Resep berhasil diproses dan diselesaikan.');
    }
}