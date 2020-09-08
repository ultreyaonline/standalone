<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\PrayerWheel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PrayerWheelChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $action;
    public $slot;
    public $user;
    public $by;
    public $wheel;

    public function __construct($action, $slot, PrayerWheel $wheel, User $user, User $by)
    {
        $this->action = $action;
        $this->slot   = $slot;
        $this->user   = $user;
        $this->by     = $by;
        $this->wheel  = $wheel;
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
            ->subject('Prayer Wheel Update Notification')
            ->greeting('Prayer Wheel Update Notification')
            ->line($this->wheel->weekend->weekend_full_name)
            ->line($this->action . ' spot ' . $this->slot . ' for/to ' . $this->user->name . '.')
            ->line('Change made by ' . $this->by->name)
            ->action('View Wheel', url('/prayerwheel/' . $this->wheel->id));
    }
}
