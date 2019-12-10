<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class MessageToCommunity extends Mailable
{
    use Queueable, SerializesModels;

    public $message_text, $subject;
    public $attachment;
    protected $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message_text, $sender, $attachment = null)
    {
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
        $sender = $this->sender;
        $message = $this->text('emails.community_message_content')
            ->from(config('site.email_general') ?? $this->sender->email, '[' . config('site.community_acronym') . '] ' . $sender->name)
            ->replyTo($sender->email, $sender->name)
            ->subject($this->subject)
            ;

        if ($this->attachment) {
            $file = Storage::disk('local')->path($this->attachment['file']);
            $message = $message->attach($file);
        }

        return $message;
    }
}
