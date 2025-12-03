<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasienResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $resetUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $resetUrl)
    {
        $this->token = $token;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Notifikasi Reset Password Akun Pasien')
                    ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                    ->action('Reset Password', $this->resetUrl)
                    ->line('Token reset password ini akan kedaluwarsa dalam 60 menit.')
                    ->line('Jika Anda tidak merasa meminta reset password, abaikan email ini.');
    }
}