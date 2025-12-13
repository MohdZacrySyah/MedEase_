<?php

// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Mengizinkan mass assignment untuk semua kolom (cara paling aman dan cepat untuk development)
    protected $guarded = ['id'];

    // Atau jika Anda ingin spesifik, gunakan fillable:
    /*
    protected $fillable = [
        'sender_id', 'sender_type', 'receiver_id', 'receiver_type', 
        'message', 'is_read', 'media_path', 'media_type'
    ];
    */

    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];

}