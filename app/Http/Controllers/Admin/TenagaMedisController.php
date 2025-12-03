<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenagaMedis; // <-- PERUBAHAN 1: Ganti User menjadi TenagaMedis
use Illuminate\Http\Request;
use App\Models\User; // <-- Tambahkan ini
use App\Models\Pemeriksaan; // <-- Tambahkan ini
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TenagaMedisController extends Controller
{
    /**
     * Menampilkan daftar akun tenaga medis.
     */
    public function index()
    {
        // PERUBAHAN 2: Ambil semua data dari model TenagaMedis
        $tenagaMedis = TenagaMedis::all(); 
        return view('admin.tenaga_medis.index', compact('tenagaMedis'));
    }

    /**
     * Menampilkan form untuk membuat akun baru.
     */
    public function create()
    {
        return view('admin.tenaga_medis.create');
    }

    /**
     * Menyimpan akun baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // PERUBAHAN 3: Cek email unik di tabel 'tenaga_medis'
            'email' => 'required|string|email|max:255|unique:tenaga_medis', 
            'password' => 'required|string|min:8|confirmed',
        ]);

        // PERUBAHAN 4: Buat data baru menggunakan model TenagaMedis dan hapus 'role'
        TenagaMedis::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.tenaga-medis.index')->with('success', 'Akun tenaga medis berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit data.
     */
    public function edit($id)
    {
        // PERUBAHAN 5: Cari data menggunakan model TenagaMedis
        $akun = TenagaMedis::findOrFail($id); 
        return view('admin.tenaga_medis.edit', compact('akun'));
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, $id)
    {
        $akun = TenagaMedis::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            // PERUBAHAN 6: Sesuaikan validasi unik untuk update
            'email' => 'required|string|email|max:255|unique:tenaga_medis,email,' . $akun->id, 
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $akun->name = $request->name;
        $akun->email = $request->email;

        if ($request->filled('password')) {
            $akun->password = bcrypt($request->password);
        }

        $akun->save();

        return redirect()->route('admin.tenaga-medis.index')->with('success', 'Akun tenaga medis berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy($id)
    {
        // PERUBAHAN 7: Hapus data menggunakan model TenagaMedis
        $akun = TenagaMedis::findOrFail($id); 
        $akun->delete();
        return redirect()->route('admin.tenaga-medis.index')->with('success', 'Akun tenaga medis berhasil dihapus.');
    }

    // PERUBAHAN 8: Hapus method lihatPasien() karena tidak relevan di controller ini
    public function riwayatPasien(User $user) // Terima objek User pasien dari URL
{
    // Pastikan user yang diminta memang pasien (opsional, tapi bagus)
    if ($user->role !== 'pasien') {
        abort(404); // Atau redirect dengan error
    }

    // Ambil semua data pemeriksaan untuk pasien ini
    $riwayats = Pemeriksaan::where('pasien_id', $user->id)
                           ->with(['tenagaMedis', 'pendaftaran']) // Load relasi dokter & pendaftaran
                           ->latest('created_at')
                           ->get();

    // Kirim data pasien dan riwayatnya ke view
    return view('tenaga_medis.pasien.riwayat', compact('user', 'riwayats'));
}
}