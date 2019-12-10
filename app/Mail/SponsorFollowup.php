<?php

namespace App\Mail;

use App\Event;
use App\User;
use App\Weekend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorFollowup extends Mailable
{
    use Queueable, SerializesModels;

    public $u;
    public $subject_line = 'Sponsor Follow-Up - ';
    public $weekend;
    public $weekend_name;
    public $next_secuela_date;
    public $next_secuela_venue;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Weekend $weekend)
    {
        $this->u = $user;
        $this->weekend = $weekend;
        $this->weekend_name = $weekend->shortname;
        $this->subject_line .= $weekend->shortname;

        $event = Event::active()->ofType('secuela')->orderBy('start_datetime')->first();
        $this->next_secuela_date = $event->start_date;
        $this->next_secuela_venue = $event->location_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject_line)
            ->view('emails.sponsor_followup');
    }
}
