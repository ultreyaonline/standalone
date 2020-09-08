<?php

namespace App\Mail;

use App\Models\Candidate;
use App\Models\Weekend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SponsorAcknowledgeCandidateReminder extends Mailable
{
//    use Queueable, SerializesModels;

    public $candidate;
    public $weekends;
    public $weekend;
    public $confirm_url;
    public $subject_line;
    public $reply_to;

    /**
     * Create a new message instance.
     *
     * @param Candidate $candidate
     */
    public function __construct(Candidate $candidate, $reply_to)
    {
        if (!$candidate) {
            throw new NotFoundResourceException();
        }

        if ($candidate->man && $candidate->man->id) {
            $candidate->m_first     = $candidate->man->first;
            $candidate->m_last      = $candidate->man->last;
            $candidate->m_gender    = $candidate->man->gender;
            $candidate->m_cellphone = $candidate->man->cellphone;
            $candidate->m_email     = $candidate->man->email;
            $candidate->m_username  = $candidate->man->username;

            $candidate->address1   = $candidate->man->address1;
            $candidate->address2   = $candidate->man->address2;
            $candidate->city       = $candidate->man->city;
            $candidate->state      = $candidate->man->state;
            $candidate->postalcode = $candidate->man->postalcode;
            $candidate->homephone  = $candidate->man->homephone;
            $candidate->church     = $candidate->man->church;
            $candidate->weekend    = $candidate->man->weekend;
            $candidate->sponsorID  = $candidate->man->sponsorID;
        }
        if ($candidate->woman && $candidate->woman->id) {
            $candidate->w_first     = $candidate->woman->first;
            $candidate->w_last      = $candidate->woman->last;
            $candidate->w_gender    = $candidate->woman->gender;
            $candidate->w_cellphone = $candidate->woman->cellphone;
            $candidate->w_email     = $candidate->woman->email;
            $candidate->w_username  = $candidate->woman->username;

            $candidate->address1   = $candidate->woman->address1;
            $candidate->address2   = $candidate->woman->address2;
            $candidate->city       = $candidate->woman->city;
            $candidate->state      = $candidate->woman->state;
            $candidate->postalcode = $candidate->woman->postalcode;
            $candidate->homephone  = $candidate->woman->homephone;
            $candidate->church     = $candidate->woman->church;
            $candidate->weekend    = $candidate->woman->weekend;
            $candidate->sponsorID  = $candidate->woman->sponsorID;
        }

        $this->candidate = $candidate;
        $this->weekends = Weekend::activeDescending()->get();
        $this->weekend  = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        $this->confirm_url = action('CandidateController@confirm', ['candidate' => $candidate->id, 'hash' => $candidate->hash_sponsor_confirm]);

        $this->subject_line = '[' . config('site.community_acronym') . '] Sponsor Please Verify Details for: ' . $candidate->names;
        $this->reply_to = $reply_to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->subject($this->subject_line)
            ->replyTo($this->reply_to, config('site.community_acronym') . ' Pre-Weekend')
            ->markdown('emails.sponsor-acknowledge-candidate-reminder');

        $file = strtolower(config('site.community_acronym', 'member_files') . '/preweekend/' . config('site.file_letters_sample'));
        if (file_exists(public_path($file))) {
            $message = $message->attach(public_path($file), [
                'as'   => 'Tres Dias Request for family letters generic.docx',
                'mime' => 'application/docx',
            ]);
        }

        $file = strtolower(config('site.community_acronym', 'member_files') . '/preweekend/' . config('site.file_sponsor_responsibilities'));
        if (file_exists(public_path($file))) {
            $message = $message->attach(public_path($file), [
                'as'   => 'Sponsor Responsibilities.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $message;
    }
}
