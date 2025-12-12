<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- Penting
use Illuminate\Notifications\Notifiable;
// 1. TAMBAHKAN DUA 'USE' STATEMENT INI
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\TenagaMedisResetPasswordNotification; // (Akan kita buat)

class TenagaMedis extends Authenticatable implements \Illuminate\Contracts\Auth\CanResetPassword
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $guard = 'tenaga_medis'; // <-- Tentukan guard

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // <-- Pastikan ini ada
    ];
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new TenagaMedisResetPasswordNotification($token));
    }

// Tambahkan relasi ke JadwalPraktek
    public function jadwalPrakteks()
    {
        return $this->hasMany(JadwalPraktek::class, 'tenaga_medis_id');
    }
    
    // Tambahkan relasi ke DoctorAvailability (dari Langkah 1B sebelumnya)
    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class, 'tenaga_medis_id');
    }
}