<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Opsional: Accessor untuk format waktu
    protected $casts = [
        'created_at' => 'datetime',
    ];
}