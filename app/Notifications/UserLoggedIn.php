<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserLoggedIn extends Notification
{
    use Queueable;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
   public function via($notifiable)
   {
       $slackRoute = $notifiable->routeNotificationFor('slack', $this);

       return $slackRoute ? ['slack'] : [];
   }

    /**
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Slack\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->text($this->user->name . ' logged in' . (app()->environment() !== 'production' ? ' (DEV)' : '') . '.');
    }
}
