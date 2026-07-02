<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('mail')->afterCommit();
    }

    public function build()
    {
        return $this->subject('Your Account Has Been Rejected!')
            ->view('admin.notif.user_rejected'); // Make sure to create this view
    }
}
