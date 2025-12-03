<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanAwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'tekanan_darah',
        'berat_badan',
        'suhu_tubuh',
        'dicatat_oleh',
    ];

    // Relasi: Satu PemeriksaanAwal dimiliki oleh satu Pendaftaran
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    // Relasi: Siapa user (admin/staf) yang mencatat
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}