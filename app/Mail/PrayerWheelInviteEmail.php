<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrayerWheelInviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $message_text;
    protected $sender;

    /**
     * Create a new message instance.
     *
     * @param $subject
     * @param $message_text
     * @param $sender
     */
    public function __construct($subject, $message_text, $sender)
    {
        $this->subject = $subject;
        $this->message_text = $message_text;
        $this->sender = $sender;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $sender = $this->sender;
        return $this->text('prayerwheel.invitation_message_content')
            ->from(config('site.email_general'), '[' . config('site.community_acronym') . '] ' . $sender->name)
            ->replyTo($sender->email, $sender->name)
            ->subject($this->subject)
            ;
    }
    public function tags()
    {
        return ['email', 'prayerwheel-invites'];
    }
}
