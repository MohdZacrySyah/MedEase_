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
                return $item['layanan'] . '-' . $item['tenaga_medis_id'];
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
            'enabled_days'   => $hariDiizinkan
        ]);
    }

    /**
     * ✅ Get daftar tanggal yang ditutup dokter (untuk disable di kalender)
     *
     * @param  int  $jadwalPraktekId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClosedDates($jadwalPraktekId)
    {
        try {
            $jadwalPraktek = JadwalPraktek::findOrFail($jadwalPraktekId);
            
            // ✅ Field database adalah 'date' (bukan 'closed_date')
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
        // Validasi input
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

        // Cek login
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mendaftar.');
        }
        
        $validatedData['user_id'] = Auth::id();
        
        // Ambil data jadwal praktek
        $jadwalDipilih = Carbon::parse($validatedData['jadwal_dipilih'])->format('Y-m-d');
        $jadwalPraktek = JadwalPraktek::findOrFail($validatedData['jadwal_praktek_id']);
        $tenagaMedisId = $jadwalPraktek->tenaga_medis_id;
        
        // ✅ VALIDASI 1: Cek apakah tanggal ini ditutup dokter
        // ⚠️ PERBAIKAN: Ganti 'closed_date' jadi 'date'
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
                    'jadwal_dipilih' => '❌ Maaf, dokter tidak tersedia pada tanggal ' . 
                        $tanggalFormatted . '. Alasan: ' . $reason . '. Silakan pilih tanggal lain.'
                ]);
        }

        // ✅ VALIDASI 2: Cek apakah hari sesuai dengan jadwal praktek dokter
        $hariDipilih = Carbon::parse($jadwalDipilih)->locale('id')->translatedFormat('l');
        $hariPraktek = $jadwalPraktek->hari;
        
        if (!in_array($hariDipilih, $hariPraktek)) {
            return back()
                ->withInput()
                ->withErrors([
                    'jadwal_dipilih' => '❌ Dokter tidak praktek pada hari ' . $hariDipilih . '. ' .
                        'Dokter hanya praktek pada: ' . implode(', ', $hariPraktek) . '.'
                ]);
        }

        // Simpan pendaftaran
        $pendaftaran = Pendaftaran::create($validatedData);

        // Generate nomor antrian
        $tanggalDipilihString = Carbon::parse($pendaftaran->jadwal_dipilih)->toDateString();
        $namaLayanan = $pendaftaran->nama_layanan;

        $jumlahSebelumnya = Pendaftaran::where('nama_layanan', $namaLayanan)
            ->whereDate('jadwal_dipilih', $tanggalDipilihString)
            ->where('id', '<', $pendaftaran->id)
            ->count();

        $pendaftaran->no_antrian = $jumlahSebelumnya + 1;
        $pendaftaran->save();

        return redirect()
            ->route('daftar.index')
            ->with('success', '✅ Pendaftaran berhasil! Nomor antrian Anda: ' . 
                $pendaftaran->no_antrian . ' untuk tanggal ' . 
                Carbon::parse($jadwalDipilih)->isoFormat('D MMMM YYYY'));
    }
}
