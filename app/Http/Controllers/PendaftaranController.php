<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktek;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <-- PERBAIKAN DI SINI (Sudah ada)

class PendaftaranController extends Controller
{
    // Method index()
    public function index()
    {
        $jadwals = JadwalPraktek::with('tenagaMedis')
                               ->orderBy('layanan')
                               ->get()
                               ->unique(function ($item) {
                                   if (!$item->tenaga_medis_id) {
                                        return $item['layanan'] . '-';
                                   }
                                   return $item['layanan'] . '-' . $item['tenaga_medis_id'];
                               });
        return view('daftar', compact('jadwals'));
    }

    // Method showForm()
  public function getFormDataJson(JadwalPraktek $jadwal)
    {
        // 1. Logika untuk mendapatkan hari yang diizinkan (sama seperti di showForm)
        $dayMap = [
            'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6,
        ];
        $hariAktif = $jadwal->hari;
        $hariDiizinkan = [];
        foreach ($hariAktif as $hari) {
            if (isset($dayMap[$hari])) {
                $hariDiizinkan[] = $dayMap[$hari];
            }
        }
        
        // Muat relasi tenaga medis
        $jadwal->load('tenagaMedis');

        // 2. Kembalikan semua data yang dibutuhkan sebagai JSON
        return response()->json([
            'form_action'    => route('daftar.store'),
            'jadwal_id'      => $jadwal->id, // <-- TAMBAHKAN INI
            'layanan_name'   => $jadwal->layanan, // <-- TAMBAHKAN INI
            'dokter_name'    => $jadwal->tenagaMedis?->name ?? 'N/A',
            'user_no_hp'     => Auth::user()?->no_hp ?? '', // Ambil no_hp user (jika ada)
            'user_name'      => Auth::user()?->name ?? '', // Ambil nama user (jika ada)
            'user_alamat'    => Auth::user()?->alamat ?? '', // Ambil alamat user (jika ada)
            'user_tgl_lahir' => Auth::user()?->tanggal_lahir ?? '', // Ambil tgl lahir (jika ada)
            'enabled_days'   => $hariDiizinkan // cth: [1, 2, 3]
        ]);
    }


    // Method store()
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_layanan'  => 'required|string',
            'jadwal_praktek_id' => 'required|exists:jadwal_prakteks,id',
            'nama_lengkap'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_telepon'    => 'required|string|max:15',
            'keluhan'       => 'required|string',
            'lama_keluhan'  => 'required|string',
            'jadwal_dipilih'=> 'required|date',
        ]);

        if (!Auth::check()) {
             return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar.');
        }
        $validatedData['user_id'] = Auth::id();

        // Simpan pendaftaran awal
        $pendaftaran = Pendaftaran::create($validatedData);

        // --- LOGIKA PEMBUATAN NOMOR ANTRIAN ---
        $tanggalDipilih = Carbon::parse($pendaftaran->jadwal_dipilih)->toDateString(); // Carbon sekarang dikenali
        $namaLayanan = $pendaftaran->nama_layanan;

        $jumlahSebelumnya = Pendaftaran::where('nama_layanan', $namaLayanan)
                                   ->whereDate('jadwal_dipilih', $tanggalDipilih)
                                   ->where('id', '<', $pendaftaran->id)
                                   ->count();

        $pendaftaran->no_antrian = $jumlahSebelumnya + 1;
        $pendaftaran->save(); // Simpan nomor antrian
        // --- AKHIR LOGIKA NOMOR ANTRIAN ---

        // =========================================================================
        // PERUBAHAN DI SINI: Arahkan kembali ke halaman daftar (daftar.index)
        // =========================================================================
        return redirect()->route('daftar.index')->with('success', 'Pendaftaran berhasil dikirim! Nomor antrian Anda: ' . $pendaftaran->no_antrian);
    }
}