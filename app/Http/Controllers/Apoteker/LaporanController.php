<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep; // Pastikan model Resep di-import
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal
use Illuminate\Support\Facades\DB; // Import DB facade

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // --- Logika Filter Tanggal ---
        $filter = $request->input('filter', 'bulan_ini'); // Default ke 'bulan_ini'
        $tanggalMulai = Carbon::now()->startOfMonth();
        $tanggalSelesai = Carbon::now()->endOfMonth();
        $tanggalInput = $request->input('tanggal'); // Ambil rentang tanggal kustom

        // ===== PERBAIKAN DI SINI: Inisialisasi variabel =====
        $tanggalDipilih = null;
        $bulanDipilih = null;
        // ===================================================

        if ($request->filled('tanggal')) {
            // --- PRIORITAS 1: Jika ada rentang tanggal kustom ---
            $dates = explode(' to ', $tanggalInput);
            try {
                $tanggalMulai = Carbon::parse(trim($dates[0]))->startOfDay();
                if (isset($dates[1])) {
                    $tanggalSelesai = Carbon::parse(trim($dates[1]))->endOfDay();
                } else {
                    $tanggalSelesai = $tanggalMulai->copy()->endOfDay(); // Jika hanya satu tanggal
                }
                $filter = 'custom'; // Tandai sebagai filter kustom
                $tanggalDipilih = $tanggalInput; // Simpan nilai untuk flatpickr
            } catch (\Exception $e) {
                // Jika format tanggal salah, kembali ke default (bulanan)
                $filter = 'bulan_ini';
                $tanggalMulai = Carbon::now()->startOfMonth();
                $tanggalSelesai = Carbon::now()->endOfMonth();
                $tanggalInput = null; // Hapus input yang salah
            }

        } else if ($filter == 'hari_ini') {
            // --- PRIORITAS 2: Filter Cepat "Hari Ini" ---
            $tanggalMulai = Carbon::now()->startOfDay();
            $tanggalSelesai = Carbon::now()->endOfDay();
        
        } else if ($filter == 'semua_data') { 
            // --- PRIORITAS 3: Filter Cepat "Semua Data" ---
            $query = Resep::where('status', 'Selesai');
            $chartQuery = Resep::where('status', 'Selesai');
            
            // Grafik per bulan untuk semua data
            $chartDataRaw = $chartQuery
                ->select(DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as bulan, COUNT(*) as jumlah'))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('jumlah', 'bulan');
            
            $chartLabels = [];
            $chartData = [];

            // Generate label bulan
            $startDate = $chartDataRaw->keys()->min() ? Carbon::parse($chartDataRaw->keys()->min()) : Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now();
            
            while ($startDate <= $endDate) {
                $monthKey = $startDate->format('Y-m');
                $chartLabels[] = $startDate->isoFormat('MMM YYYY');
                $chartData[] = $chartDataRaw->get($monthKey, 0);
                $startDate->addMonth();
            }

        } else if ($filter == 'bulan_terpilih') {
            // --- PRIORITAS 4: Filter "Pilih Bulan" ---
            $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m'));
            $carbonBulan = Carbon::parse($bulanDipilih);
            $tanggalMulai = $carbonBulan->copy()->startOfMonth();
            $tanggalSelesai = $carbonBulan->copy()->endOfMonth();

        } else {
            // --- DEFAULT: Filter Cepat "Bulan Ini" ---
            $filter = 'bulan_ini';
            $tanggalMulai = Carbon::now()->startOfMonth();
            $tanggalSelesai = Carbon::now()->endOfMonth();
        }
        

        // --- Inisialisasi Query jika belum diset (untuk filter selain 'semua_data') ---
        if ($filter != 'semua_data') {
            $query = Resep::where('status', 'Selesai')
                        ->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
            
            $chartQuery = Resep::where('status', 'Selesai')
                        ->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);

            // --- Logika Grafik untuk filter berbasis rentang ---
            if ($filter == 'hari_ini' || $filter == 'tanggal') {
                // Grafik per jam
                $chartDataRaw = $chartQuery
                    ->select(DB::raw('HOUR(updated_at) as jam, COUNT(*) as jumlah'))
                    ->groupBy('jam')
                    ->orderBy('jam')
                    ->pluck('jumlah', 'jam');
                
                $chartLabels = [];
                $chartData = [];
                for ($i = 0; $i < 24; $i++) {
                    $chartLabels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $chartData[] = $chartDataRaw->get($i, 0);
                }
            } else {
                // Grafik per tanggal (untuk bulanan atau bulan terpilih)
                $chartDataRaw = $chartQuery
                    ->select(DB::raw('DAY(updated_at) as tanggal, COUNT(*) as jumlah'))
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->pluck('jumlah', 'tanggal');
                
                $chartLabels = [];
                $chartData = [];
                $daysInMonth = $tanggalMulai->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $chartLabels[] = $i;
                    $chartData[] = $chartDataRaw->get($i, 0);
                }
            }
        }

        // --- 1. Ambil Data untuk Tabel Laporan (Detail Resep) ---
        $laporanReseps = $query->with('pasien', 'pemeriksaan.tenagaMedis')
                            ->latest('updated_at')
                            ->paginate(10) // Kita gunakan pagination
                            ->withQueryString(); // Membawa parameter filter (misal: ?filter=harian) saat pindah halaman

        // --- 2. Ambil Data untuk KPI Cards (Statistik Cepat) ---
        $resepHariIni = Resep::where('status', 'Selesai')
                            ->whereDate('updated_at', Carbon::today())
                            ->count();
        
        $resepBulanIni = Resep::where('status', 'Selesai')
                            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();

        $totalResepSelesai = Resep::where('status', 'Selesai')
                                ->count();
        
        // 3. Kirim semua data ke view
        return view('apoteker.laporan.index', [
            'laporanReseps' => $laporanReseps,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'filter' => $filter, 
            'tanggalDipilih' => $tanggalDipilih, // Sekarang pasti ada (isinya null atau string tanggal)
            'bulanDipilih' => $bulanDipilih,     // Sekarang pasti ada (isinya null atau string bulan)
            // Variabel baru untuk cards
            'resepHariIni' => $resepHariIni,
            'resepBulanIni' => $resepBulanIni,
            'semuaResep' => $totalResepSelesai 
        ]);
    }
}