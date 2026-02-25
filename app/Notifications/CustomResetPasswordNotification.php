<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Send a password reset notification to the user.
 *
 * @param  string  $token
 */

class CustomResetPasswordNotification extends Notification
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset your password.')
            ->line('Click the button below to reset your password:')
            ->action('Reset Password', $this->url)
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Best regards, Your EduLeave Team')
            ->view('auth.custom_reset_password', ['url' => $this->url, 'user' => $notifiable]);
    }
}
