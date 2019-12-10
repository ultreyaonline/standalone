<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CandidateDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $who;
    public $by;

    public function __construct($who, User $by)
    {
        $this->who = $who;
        $this->by  = $by;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Candidate Deleted')
            ->greeting('Candidate Deletion Notification')
            ->line($this->who)
            ->line('has been deleted from the database.')
            ->line('by ' . $this->by->name);
    }
}
