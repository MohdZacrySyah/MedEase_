<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TenagaMedis; // <-- Pastikan ini di-import

class JadwalPraktek extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'tenaga_medis_id',
        'layanan',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hari' => 'array',
    ];

    /**
     * Mendefinisikan relasi bahwa JadwalPraktek dimiliki oleh TenagaMedis.
     */
    public function tenagaMedis() // <-- DIPERLUKAN UNTUK NOTIFIKASI
    {
        return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
    }
}