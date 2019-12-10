<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $by;

    public function __construct(User $user, string $by)
    {
        $this->user = $user;
        $this->by   = $by;
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
            ->subject('Member Added to Database')
            ->greeting('User-Added Notification')
            ->line($this->user->name . ' (' . $this->user->email . ')')
            ->line('has been added to the members database.')
            ->line('by ' . $this->by)
            ->action('View Member', url('/members/' . $this->user->id));
    }
}
