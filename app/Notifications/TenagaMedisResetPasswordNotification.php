<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenagaMedisResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        // URL reset password akan mengarah ke rute tenaga-medis
        $resetUrl = route('tenaga-medis.password.reset', [
            'token' => $this->token, 
            'email' => $notifiable->getEmailForPasswordReset()
        ]);

        return (new MailMessage)
                    ->subject('Notifikasi Reset Password Tenaga Medis')
                    ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                    ->action('Reset Password', $resetUrl)
                    ->line('Token reset password ini akan kedaluwarsa dalam 60 menit.')
                    ->line('Jika Anda tidak merasa meminta reset password, abaikan email ini.');
    }
}