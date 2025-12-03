<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasienOtpNotification extends Notification
{
    use Queueable;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Kode OTP Reset Password Anda')
                    ->line('Gunakan kode OTP di bawah ini untuk mereset password Anda.')
                    ->line('Kode OTP: ' . $this->otp)
                    ->line('Kode ini hanya berlaku selama 1 menit.')
                    ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
    }
}