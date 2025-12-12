<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Pendaftaran; 
use Carbon\Carbon;

class JadwalDibatalkanNotification extends Notification
{
    use Queueable;

    protected $pendaftaran;
    protected $reason;

    public function __construct(Pendaftaran $pendaftaran, string $reason)
    {
        $this->pendaftaran = $pendaftaran;
        $this->reason = $reason;
        // Memuat relasi agar data dokter tersedia saat membuat email
        $pendaftaran->load('jadwalPraktek.tenagaMedis');
    }
    

    public function via(object $notifiable): array
    {
       return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jadwal = $this->pendaftaran->jadwalPraktek;
        $dokter = $jadwal->tenagaMedis;
        
        // Pastikan konfigurasi Carbon ke ID sudah dilakukan
        Carbon::setLocale('id'); 
        $tanggalPendaftaran = Carbon::parse($this->pendaftaran->jadwal_dipilih)->translatedFormat('l, d F Y');

        return (new MailMessage)
                    ->subject('⚠️ PENTING: Pembatalan Jadwal Konsultasi MedEase')
                    ->greeting('Yth. ' . $notifiable->name . ',')
                    ->line('Kami mohon maaf, jadwal konsultasi Anda harus **dibatalkan** karena: ' . $this->reason)
                    ->line(' ')
                    ->line('### Detail Pembatalan')
                    ->line('**Dokter/Bidan:** ' . ($dokter->name ?? 'N/A'))
                    ->line('**Layanan:** ' . ($jadwal->layanan ?? 'N/A'))
                    ->line('**Tanggal Jadwal:** ' . $tanggalPendaftaran)
                    ->line('**Nomor Antrian:** ' . $this->pendaftaran->no_antrian)
                    ->line(' ')
                    ->line('Mohon untuk melakukan pendaftaran ulang di hari atau jadwal lain.')
                    ->action('Buat Pendaftaran Baru', url('/daftar')) 
                    ->line('Hormat kami,')
                    ->salutation('Tim MedEase');
    }public function toDatabase(object $notifiable): array
    {
        $jadwal = $this->pendaftaran->jadwalPraktek;
        $dokter = $jadwal->tenagaMedis;

        return [
            'type' => 'Pembatalan Jadwal',
            'title' => 'Jadwal Anda dibatalkan oleh ' . ($dokter->name ?? 'Admin'),
            'message' => 'Konsultasi Anda pada tanggal ' . 
                         Carbon::parse($this->pendaftaran->jadwal_dipilih)->isoFormat('dddd, D MMMM') . 
                         ' dibatalkan dengan alasan: ' . $this->reason,
            'pendaftaran_id' => $this->pendaftaran->id,
            'date' => $this->pendaftaran->jadwal_dipilih,
        ];
    }
}
