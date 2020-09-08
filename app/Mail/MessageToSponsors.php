<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Weekend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class MessageToSponsors extends Mailable
{
    use Queueable, SerializesModels;

    public $message_text, $subject;
    public $weekend;
    public $attachment;
    protected $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Weekend $weekend, $subject, $message_text, User $sender, $attachment = null)
    {
        $this->weekend = $weekend;
        $this->subject = $subject;
        $this->message_text = $message_text;
        $this->sender = $sender;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->text('emails.sponsor_message_content')
            ->from(config('site.email_general'), '[' . config('site.community_acronym') . '] ' . $this->sender->name)
            ->replyTo($this->sender->email, $this->sender->name)
            ->subject($this->subject);

        if ($this->attachment) {
            $file = Storage::disk('local')->path($this->attachment['file']);
            $message = $message->attach($file);
        }

        return $message;
    }
}
