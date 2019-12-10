<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebsiteLoginInstructions extends Mailable
{
    use Queueable, SerializesModels;

    public $u;
    public $subject_line;

    /**
     * @return void
     */
    public function __construct(User $user)
    {
        $this->u = $user;
        $this->subject_line = config('site.community_acronym') . ' - Website Access Instructions';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        app()->setLocale(app()->getLocale);

        return $this->subject($this->subject_line)
                    ->view('emails.candidate_website_instructions');
    }
}
