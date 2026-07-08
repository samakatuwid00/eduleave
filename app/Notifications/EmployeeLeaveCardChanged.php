<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeLeaveCardChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public readonly string $summary)
    {
        $this->onQueue('mail')->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your EduLeave record was updated')
            ->greeting("Hello {$notifiable->name},")
            ->line($this->summary)
            ->line('If this change is unexpected, please contact your administrator.')
            ->action('View your leave card', route('user/dashboard'));
    }
}
