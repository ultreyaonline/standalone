<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HowToSponsor extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.how_to_sponsor')
            ->from(config('site.email_general'), config('site.community_acronym') . ' News')
            ->replyTo(config('site.email-preweekend-mailbox'), config('site.community_acronym') . ' PreWeekend Committee')
            ->subject('[' . config('site.community_acronym') . '] Sponsoring/inviting people to attend Tres Dias')
            ->attach(public_path('/member_files/Sponsor Responsibilities.pdf'), [
                'as' => 'Sponsor Responsibilities.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach(public_path('/Candidate_Application.pdf'), [
                'as' => 'Candidate_Application.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
