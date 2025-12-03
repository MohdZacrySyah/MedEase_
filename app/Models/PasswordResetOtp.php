<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    // Nama tabel
    protected $table = 'password_reset_otps';
    
    // Kunci utama
    protected $primaryKey = 'email';
    public $incrementing = false;
    
    // Hanya 'created_at' yang kita gunakan
    public $timestamps = true;
    const UPDATED_AT = null; 

    protected $fillable = [
        'email',
        'otp',
        'created_at',
    ];
}