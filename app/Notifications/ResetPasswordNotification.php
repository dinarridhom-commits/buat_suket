<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi — Portal Tata Usaha BWS Kalimantan I')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda pada Portal Tata Usaha BWS Kalimantan I.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line("Tautan atur ulang kata sandi ini berlaku selama {$expireMinutes} menit.")
            ->line('Jika Anda tidak merasa meminta atur ulang kata sandi, abaikan email ini dan kata sandi Anda tidak akan berubah.');
    }
}
