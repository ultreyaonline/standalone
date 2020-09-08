<?php

namespace App\Notifications;

use App\Models\TeamFeePayments;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamFeePaymentsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $member;
    public $by;
    public $payment;

    public function __construct(TeamFeePayments $payment, User $member, User $by)
    {
        $this->member = $member;
        $this->by = $by;
        $this->payment = $payment;
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
            ->subject('Team Fee Payment Notification')
            ->greeting('Team Fee Payment Notification')
            ->line($this->payment->weekend->weekend_full_name)
            ->line($this->member->name . '.')
            ->line('Amount recorded: $' . number_format($this->payment->total_paid, 2) . ' ' . $this->payment->comments)
            ->line('as of ' . $this->payment->date_paid)
            ->line('Payment recorded by ' . $this->by->name);
    }
}
