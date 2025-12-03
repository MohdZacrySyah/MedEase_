<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini untuk konsistensi
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable; // Tambahkan HasFactory

    /**
     * Beritahu model ini untuk menggunakan guard 'admin'.
     * @var string
     */
    protected $guard = 'admin'; // <-- PERUBAHAN 1: Tambahkan properti ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // <-- PERUBAHAN 2: Tambahkan ini
    ];
}