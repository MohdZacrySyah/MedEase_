<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktek;
use App\Models\Pendaftaran;
use App\Models\DoctorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $jadwals = JadwalPraktek::with('tenagaMedis')
            ->orderBy('layanan')
            ->get()
            ->unique(function ($item) {
                if (!$item->tenaga_medis_id) {
                    return $item['layanan'] . '-';
                }
                return $item['layanan'] . '-' . $item->tenaga_medis_id;
            });
        
        return view('daftar', compact('jadwals'));
    }

    /**
     * Get form data as JSON for modal.
     *
     * @param  \App\Models\JadwalPraktek  $jadwal
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormDataJson(JadwalPraktek $jadwal)
    {
        $dayMap = [
            'Minggu' => 0,
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
        ];
        
        $hariAktif = $jadwal->hari;
        $hariDiizinkan = [];
        
        foreach ($hariAktif as $hari) {
            if (isset($dayMap[$hari])) {
                $hariDiizinkan[] = $dayMap[$hari];
            }
        }
        
        $jadwal->load('tenagaMedis');
        
        // 🔥 TAMBAHAN: Ambil tanggal yang ditutup (Closed Dates)
        $closedDates = [];
        if ($jadwal->tenaga_medis_id) {
            $closedDates = DoctorAvailability::where('tenaga_medis_id', $jadwal->tenaga_medis_id)
                ->where('is_available', false)
                ->whereDate('date', '>=', Carbon::today())
                ->pluck('date')
                ->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();
        }

        $user = Auth::user();

        return response()->json([
            'form_action'    => route('daftar.store'),
            'jadwal_id'      => $jadwal->id,
            'layanan_name'   => $jadwal->layanan,
            'dokter_name'    => $jadwal->tenagaMedis?->name ?? 'N/A',
            'user_no_hp'     => $user?->no_hp ?? '',
            'user_name'      => $user?->name ?? '',
            'user_alamat'    => $user?->alamat ?? '',
            'user_tgl_lahir' => $user?->tanggal_lahir ?? '',
            'enabled_days'   => $hariDiizinkan,
            'closed_dates'   => $closedDates
        ]);
    }

    /**
     * Get daftar tanggal yang ditutup dokter (untuk disable di kalender)
     *
     * @param  int  $jadwalPraktekId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClosedDates($jadwalPraktekId)
    {
        try {
            $jadwalPraktek = JadwalPraktek::findOrFail($jadwalPraktekId);
            
            $closedDates = DoctorAvailability::where('tenaga_medis_id', $jadwalPraktek->tenaga_medis_id)
                                              ->where('is_available', false)
                                              ->whereDate('date', '>=', Carbon::today())
                                              ->pluck('date')
                                              ->map(function($date) {
                                                  return Carbon::parse($date)->format('Y-m-d');
                                              })
                                              ->toArray();
            
            return response()->json([
                'success' => true,
                'closed_dates' => $closedDates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tanggal tertutup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'nama_layanan'      => 'required|string',
            'jadwal_praktek_id' => 'required|exists:jadwal_prakteks,id',
            'nama_lengkap'      => 'required|string|max:255',
            'tanggal_lahir'     => 'required|date',
            'alamat'            => 'required|string',
            'no_telepon'        => 'required|string|max:15',
            'keluhan'           => 'required|string',
            'lama_keluhan'      => 'required|string',
            'jadwal_dipilih'    => 'required|date|after_or_equal:today',
        ]);

        // 2. Cek login
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mendaftar.');
        }
        
        $validatedData['user_id'] = Auth::id();
        
        // 3. Ambil data jadwal praktek & Format Tanggal
        // Pastikan format tanggal Y-m-d agar aman di Database
        $validatedData['jadwal_dipilih'] = Carbon::parse($validatedData['jadwal_dipilih'])->format('Y-m-d');
        
        $jadwalDipilih = $validatedData['jadwal_dipilih'];
        $jadwalPraktek = JadwalPraktek::findOrFail($validatedData['jadwal_praktek_id']);
        $tenagaMedisId = $jadwalPraktek->tenaga_medis_id;
        
        // ==========================================
        // 🔥 VALIDASI 1: CEK TANGGAL DITUTUP/LIBUR 🔥
        // ==========================================
        if ($tenagaMedisId) {
            $availability = DoctorAvailability::where('tenaga_medis_id', $tenagaMedisId)
                ->whereDate('date', $jadwalDipilih)
                ->where('is_available', false)
                ->first();

            if ($availability) {
                $tanggalFormatted = Carbon::parse($jadwalDipilih)->isoFormat('D MMMM YYYY');
                $reason = $availability->reason ?? 'Dokter berhalangan';
                
                return back()
                    ->withInput()
                    ->withErrors([
                        'jadwal_dipilih' => '❌ Maaf, jadwal pada tanggal ' . $tanggalFormatted . ' DITUTUP/DIBATALKAN. Alasan: ' . $reason . '. Silakan pilih tanggal lain.'
                    ]);
            }
        }

        // ==========================================
        // ✅ VALIDASI 2: CEK HARI PRAKTEK
        // ==========================================
        $hariDipilih = Carbon::parse($jadwalDipilih)->locale('id')->translatedFormat('l');
        $hariPraktek = $jadwalPraktek->hari; // Pastikan Model JadwalPraktek men-cast 'hari' ke array
        
        // Jika $hariPraktek berupa string JSON (tergantung setingan Model), decode dulu
        if (is_string($hariPraktek)) {
            $hariPraktek = json_decode($hariPraktek, true);
        }

        if (is_array($hariPraktek) && !in_array($hariDipilih, $hariPraktek)) {
            return back()
                ->withInput()
                ->withErrors([
                    'jadwal_dipilih' => '❌ Dokter tidak praktek pada hari ' . $hariDipilih . '. ' .
                        'Dokter hanya praktek pada: ' . implode(', ', $hariPraktek) . '.'
                ]);
        }

        // ==========================================
        // 🔥 LOGIKA PENYIMPANAN DATA 🔥
        // ==========================================

        // Inisialisasi object dengan data validasi
        $pendaftaran = new Pendaftaran($validatedData);

        // --- [FIX] SET DEFAULT STATUS SECARA EKSPLISIT ---
        // Ini penting agar tidak error "Field doesn't have default value"
        $pendaftaran->status = 'Menunggu'; 
        $pendaftaran->status_panggilan = 'menunggu';
        $pendaftaran->jumlah_panggilan = 0;
        // -------------------------------------------------

        // Generate Nomor Antrian
        $jumlahSebelumnya = Pendaftaran::where('nama_layanan', $pendaftaran->nama_layanan)
            ->whereDate('jadwal_dipilih', $jadwalDipilih)
            ->where('status', '!=', 'Dibatalkan')
            ->count();

        $pendaftaran->no_antrian = $jumlahSebelumnya + 1;

        // Hitung Estimasi Waktu (Setiap Pasien 20 Menit)
        if ($jadwalPraktek->jam_mulai) {
            
            // 1. Tentukan Waktu Mulai Jadwal yang Sesungguhnya
            // Gabungkan tanggal yang dipilih ($jadwalDipilih) dengan jam mulai praktek
            $scheduledStartTime = Carbon::parse($jadwalDipilih . ' ' . $jadwalPraktek->jam_mulai); 
            $currentTime = Carbon::now();
            $baseTimeForAntrian1 = $scheduledStartTime->copy(); // Default: Sesuai jadwal
            
            // Cek apakah pendaftaran hari ini
            $isToday = Carbon::parse($jadwalDipilih)->isToday();

            if ($isToday) {
                // 🔥 PERBAIKAN KRITIS UNTUK WAKTU ESTIMASI 🔥
                // Jika jadwal sudah lewat, gunakan waktu pendaftaran sekarang sebagai Base Time.
                // Jika jadwal belum lewat, gunakan waktu jadwal.
                if ($scheduledStartTime->isBefore($currentTime)) {
                    // Gunakan waktu sekarang (waktu pendaftaran) sebagai waktu awal,
                    // karena jadwal sudah terlewat.
                    $baseTimeForAntrian1 = $currentTime->copy();
                }
            }
            
            // 2. Kalkulasi Durasi Tambahan
            $durasiPerPasien = 20; 
            $menitTambahan = ($pendaftaran->no_antrian - 1) * $durasiPerPasien;
            
            // 3. Hitung Waktu Estimasi Final
            // Gunakan copy() dari Base Time yang sudah ditentukan
            $estimasiWaktu = $baseTimeForAntrian1->addMinutes($menitTambahan);

            $pendaftaran->estimasi_dilayani = $estimasiWaktu->format('H:i:s');
        }

        // Simpan ke Database
        $pendaftaran->save();

        // Siapkan pesan sukses
        $pesanSukses = '✅ Pendaftaran berhasil! Nomor antrian Anda: ' . $pendaftaran->no_antrian;
        
        if ($pendaftaran->estimasi_dilayani) {
            $waktuFormatted = Carbon::parse($pendaftaran->estimasi_dilayani)->format('H:i');
            $pesanSukses .= '. Estimasi dilayani pukul: ' . $waktuFormatted . ' WIB.';
        }

        return redirect()
            ->route('daftar.index')
            ->with('success', $pesanSukses);
    }

    /**
     * Menghentikan suara alarm di sisi pasien.
     * Mengubah status_panggilan menjadi 'menunggu' agar pop-up tertutup.
     */
    public function stopAlarmPasien($id)
    {
        try {
            $pendaftaran = Pendaftaran::findOrFail($id);
            
            // Cek otorisasi (hanya pemilik pendaftaran yang bisa stop alarm)
            if ($pendaftaran->user_id != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Reset status agar notifikasi berhenti berbunyi
            // Status utama tetap 'Menunggu' sampai Admin klik 'Hadir'
            $pendaftaran->status_panggilan = 'menunggu'; 
            $pendaftaran->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Backup fungsi untuk kompatibilitas jika route lama masih dipakai.
     * Logikanya disamakan dengan stopAlarmPasien.
     */
    public function konfirmasiDatang($id)
    {
        return $this->stopAlarmPasien($id);
    }
}