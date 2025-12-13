<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     * Logika ini memastikan pesan dikirim ke channel penerima yang sesuai.
     */
    public function broadcastOn(): array
    {
        $receiverId = $this->message->receiver_id;
        $receiverType = $this->message->receiver_type;
        
        // Tentukan channel berdasarkan tipe penerima ('user' untuk pasien, 'medis' untuk tenaga medis)
        if ($receiverType == 'medis') {
            // Channel untuk Tenaga Medis
            return [new PrivateChannel('tenagamedis.' . $receiverId)];
        } else {
            // Channel untuk Pasien ('user')
            return [new PrivateChannel('pasien.' . $receiverId)];
        }
    }
    
    /**
     * Opsional: Tentukan data yang akan di-broadcast.
     * Ini penting untuk client-side (Echo)
     */
    public function broadcastWith(): array
    {
        // Muat pengirim pesan untuk ditampilkan di sisi penerima
        $sender = $this->message->sender_type == 'user' 
                  ? \App\Models\User::find($this->message->sender_id)
                  : \App\Models\TenagaMedis::find($this->message->sender_id);

        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'sender_type' => $this->message->sender_type,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->format('H:i'),
                'is_me' => false, // Disesuaikan di client-side
            ],
            'sender' => $sender ? [
                'id' => $sender->id,
                'name' => $sender->name,
            ] : null,
        ];
    }
}