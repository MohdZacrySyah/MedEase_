<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Pemeriksaan extends Model
{
    //
   protected $fillable = [
    'pendaftaran_id', 'pasien_id', 'tenaga_medis_id', 
    'subjektif', 'objektif', 'assessment', 'plan', 
    'harga', 'resep_obat', // <-- Tambahkan ini
];

public function pendaftaran()
{
    return $this->belongsTo(Pendaftaran::class);
}
public function tenagaMedis()
{
    // Pemeriksaan.tenaga_medis_id merujuk ke User.id
   return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
}
public function pasien()
{
    return $this->belongsTo(User::class, 'pasien_id');
}
protected $table = 'pemeriksaans';
public function resep()
    {
        return $this->hasOne(Resep::class, 'pemeriksaan_id');
    }
}

