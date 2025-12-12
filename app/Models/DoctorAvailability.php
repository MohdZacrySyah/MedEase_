<?php

// app/Models/DoctorAvailability.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenaga_medis_id', // 👇 Kunci yang benar
        'date',
        'max_slots',
        'booked_slots',
        'is_available',
        'reason',
    ];
    
    // Relasi ke TenagaMedis (Dokter)
    public function tenagaMedis()
    {
        return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
    }
}
