<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <--- Wajib ada
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast // <--- Jangan lupa "implements"
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
{
    // Jika pengirim Pasien -> Kirim ke channel Tenaga Medis
    if ($this->message->sender_type == 'pasien') {
        return [new PrivateChannel('tenagamedis.' . $this->message->tenaga_medis_id)];
    } 
    // Jika pengirim Tenaga Medis -> Kirim ke channel Pasien
    else {
        return [new PrivateChannel('pasien.' . $this->message->user_id)];
    }
}
}