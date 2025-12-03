<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // TAMBAHKAN ATAU EDIT BLOK INI
    protected $fillable = [
        'pemeriksaan_id',
        'pasien_id',
        'apoteker_id',
        'status',
        'catatan_apoteker',
    ];

    
    // --- TAMBAHKAN JUGA RELASI INI ---
    // (Ini akan sangat berguna nanti di panel Apoteker)

    /**
     * Mendapatkan data pemeriksaan (resep asli)
     */
    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }

    /**
     * Mendapatkan data pasien
     */
    public function pasien()
    {
        // Asumsi model pasien Anda adalah 'User'
        return $this->belongsTo(User::class, 'pasien_id'); 
    }

    /**
     * Mendapatkan data apoteker yang memproses
     */
    public function apoteker()
    {
        return $this->belongsTo(Apoteker::class, 'apoteker_id');
    }
}