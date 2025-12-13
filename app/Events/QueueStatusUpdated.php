<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pendaftaran; // Pastikan menggunakan model Pendaftaran Anda



class QueueStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $pendaftaran;

    public function __construct(Pendaftaran $pendaftaran)
    {
        $this->pendaftaran = $pendaftaran;
    }

    // Siarkan ke channel spesifik Jadwal Praktek agar hanya pasien di jadwal itu yang menerima
    public function broadcastOn()
    {
        // Asumsi: Anda ingin men-trigger update untuk semua pasien dalam jadwal ini
        // Ganti 'jadwal_praktek_id' dengan nama kolom yang benar jika berbeda
        return new Channel('antrian-update.' . $this->pendaftaran->jadwal_praktek_id);
    }
}