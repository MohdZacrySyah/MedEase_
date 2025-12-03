<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query untuk resep yang sudah 'Selesai'
        $query = Resep::with('pasien', 'pemeriksaan.tenagaMedis')
                     ->where('status', 'Selesai');

        // Terapkan Filter
        // 1. Filter Nama Pasien
        if ($request->filled('pasien')) {
            $query->whereHas('pasien', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->pasien . '%');
            });
        }

        // 2. Filter Tanggal
        if ($request->filled('tanggal')) {
            // Asumsi $request->tanggal adalah format 'Y-m-d' dari flatpickr
            $query->whereDate('updated_at', $request->tanggal); 
        }

        // Ambil data
        $riwayats = $query->latest('updated_at')->get();

        return view('apoteker.riwayat.index', compact('riwayats'));
    }
}