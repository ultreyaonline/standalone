<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $who;
    public $by;

    public function __construct(string $who, string $by)
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
            ->subject('User Deleted')
            ->greeting('User Deletion Notification')
            ->line($this->who)
            ->line('has been deleted from the database.')
            ->line('by ' . $this->by);
    }
}
