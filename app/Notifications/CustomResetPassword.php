<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class CustomResetPassword extends Notification
{
    public $token;
    public $originalEmail;

    public function __construct($token, $originalEmail)
    {
        $this->token = $token;
        $this->originalEmail = $originalEmail;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->originalEmail,
        ], false));

        return (new MailMessage)
            ->subject('Reset Password Akun')
            ->line('Anda menerima email ini karena ada permintaan reset password.')
            ->action('Reset Password', $url)
            ->line("Email yang akan  direset: {$this->originalEmail}");
    }
}
