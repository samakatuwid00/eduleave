<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAutomationDigest extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(
        public readonly string $rule,
        public readonly string $window,
        public readonly array $counts,
        public readonly array $items,
    ) {
        $this->onQueue('mail')->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->rule === 'weekly_admin_summary'
                ? 'EduLeave Weekly Action Summary'
                : 'EduLeave Daily Action Digest')
            ->greeting('EduLeave administrative summary')
            ->line("Reporting window: {$this->window}")
            ->line("Open alerts: {$this->counts['total']} · Critical: {$this->counts['critical']} · High: {$this->counts['high']} · Medium: {$this->counts['medium']}");

        foreach ($this->items as $item) {
            $message->line("• {$item['title']} — {$item['employee']}");
        }

        return $message
            ->action('Open Action Center', route('admin.action-center'))
            ->line('This automation only reports issues; it does not change approvals or leave balances.');
    }
}
