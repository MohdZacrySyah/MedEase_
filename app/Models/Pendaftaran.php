<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

   protected $fillable = [
        'user_id',
        'jadwal_praktek_id', 
        'nama_layanan',
        'nama_lengkap',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'keluhan',
        'lama_keluhan',
        'jadwal_dipilih',
        'status',
        'no_antrian',

    ];

    /**
     * Mendefinisikan relasi bahwa Pendaftaran dimiliki oleh User (Pasien).
     */
    public function user() // <-- TAMBAHKAN METHOD INI
    {
        // Pendaftaran.user_id merujuk ke User.id
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Pemeriksaan Awal (jika sudah dibuat).
     */
    public function pemeriksaanAwal()
    {
        return $this->hasOne(PemeriksaanAwal::class);
    }

    /**
     * Relasi ke Pemeriksaan Utama (jika sudah dibuat).
     */
    public function pemeriksaan()
    {
        return $this->hasOne(Pemeriksaan::class);
    }
   public function jadwalPraktek() // <-- DIPERLUKAN UNTUK NOTIFIKASI
    {
        return $this->belongsTo(JadwalPraktek::class, 'jadwal_praktek_id');
    }
}