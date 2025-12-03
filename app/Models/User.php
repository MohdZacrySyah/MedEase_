<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\JadwalPraktek;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\PasienResetPasswordNotification; 

class User extends Authenticatable implements \Illuminate\Contracts\Auth\CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'profile_photo_path',
        'google_id',        // TAMBAHAN: Google ID
        'avatar',           // TAMBAHAN: Google Avatar
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function jadwalPrakteks()
    {
        return $this->hasMany(JadwalPraktek::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $this->email]);
        $this->notify(new PasienResetPasswordNotification($token, $resetUrl));
    }
}
