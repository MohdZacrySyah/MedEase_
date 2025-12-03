<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// TAMBAHKAN INI
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

// GANTI 'extends Model' MENJADI 'extends Authenticatable'
class Apoteker extends Authenticatable 
{
    use HasFactory, Notifiable; // TAMBAHKAN Notifiable

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nomor_lisensi',
        'no_telepon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ApotekerResetPasswordNotification($token));
    }

}