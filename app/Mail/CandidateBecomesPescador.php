<?php

namespace App\Mail;

use App\Event;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CandidateBecomesPescador extends Mailable
{
    use Queueable, SerializesModels;

    public $u;
    public $subject_line;

    public $secuela = [];
    public $next_weekend = '';
    public $rector_name = '';
    public $rector_email = '';


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->u = $user;
        $this->secuela = Event::ofType('secuela')->active()->first();
        $this->next_weekend = ''; // @TODO: calculate next weekend for this gender
        $this->rector_name = ''; // pull from weekend data
        $this->rector_email = ''; // from weekend data
        $this->subject_line = 'Welcome to the ' . config('site.community_acronym') . ' Community';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject_line)
                    ->view('emails.candidate_becomes_pescador');
    }
}
