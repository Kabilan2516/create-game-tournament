<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Delivery channels
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Generate verification URL
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(
                Config::get('auth.verification.expire', 60)
            ),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Email content
     */
public function toMail($notifiable)
{
    $verifyUrl = $this->verificationUrl($notifiable);

    return (new MailMessage)
        ->subject('🎮 Verify Your ' . config('app.name') . ' Account')
        ->markdown('emails.auth.verify-email', [
            'user' => $notifiable,   // ✅ THIS FIXES IT
            'url'  => $verifyUrl,
        ]);
}

}
