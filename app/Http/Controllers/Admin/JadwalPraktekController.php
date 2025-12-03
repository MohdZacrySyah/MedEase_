<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPraktek;
use App\Models\TenagaMedis; // <-- Pastikan ini di-import

class JadwalPraktekController extends Controller
{
    /**
     * Menampilkan daftar jadwal praktek.
     */
   public function index()
{
    $jadwals = JadwalPraktek::with('tenagaMedis')->get();
    $tenagaMedis = TenagaMedis::orderBy('name')->get();

    return view('admin.kelolajadwal.index', [
        'jadwals' => $jadwals,
        'tenagaMedis' => $tenagaMedis
    ]);
}


    /**
     * Menampilkan form tambah jadwal baru.
     */
    public function create()
    {
        // Ambil semua data dari model TenagaMedis untuk dropdown
        $tenagaMedis = TenagaMedis::orderBy('name')->get();
        return view('admin.kelolajadwal.create', compact('tenagaMedis'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input, pastikan user_id merujuk ke tabel tenaga_medis
        $validatedData = $request->validate([
            'tenaga_medis_id' => 'required|exists:tenaga_medis,id',
            'layanan' => 'required|string|max:255',
            'hari' => 'required|array|min:1',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // (Opsional) Hapus baris ini jika Anda sudah menghapus kolom 'nama_dokter'
        // $tenagaMedisUser = TenagaMedis::find($validatedData['user_id']);
        // $validatedData['nama_dokter'] = $tenagaMedisUser->name;

        JadwalPraktek::create($validatedData); // Laravel akan otomatis memetakan tenaga_medis_id

        return redirect()->route('admin.kelolajadwalpraktek.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit($id)
    {
        $jadwal = JadwalPraktek::findOrFail($id);
        // Ambil semua data tenaga medis untuk dropdown
        $tenagaMedis = TenagaMedis::orderBy('name')->get();
        return view('admin.kelolajadwal.edit', compact('jadwal', 'tenagaMedis'));
    }

    /**
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalPraktek::findOrFail($id);

        // Validasi input, pastikan user_id merujuk ke tabel tenaga_medis
        $validatedData = $request->validate([
            'tenaga_medis_id' => 'required|exists:tenaga_medis,id',
            'layanan' => 'required|string|max:255',
            'hari' => 'required|array|min:1',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // (Opsional) Hapus baris ini jika Anda sudah menghapus kolom 'nama_dokter'
        // $tenagaMedisUser = TenagaMedis::find($validatedData['user_id']);
        // $validatedData['nama_dokter'] = $tenagaMedisUser->name;

        $jadwal->update($validatedData);

        return redirect()->route('admin.kelolajadwalpraktek.index')->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Menghapus data jadwal dari database.
     */
    public function destroy($id)
    {
        $jadwal = JadwalPraktek::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('admin.kelolajadwalpraktek.index')->with('success', 'Jadwal berhasil dihapus');
    }
}