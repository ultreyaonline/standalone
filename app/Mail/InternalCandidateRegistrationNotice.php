<?php

namespace App\Mail;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Weekend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class InternalCandidateRegistrationNotice extends Mailable
{
//    use Queueable, SerializesModels;

    public $candidate;
    public $weekends;
    public $weekend;
    public $confirm_url;
    public $subject_line;
    public $sponsors = '';
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

        $sponsors = [];

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
            $candidate->m_sponsorID  = $candidate->man->sponsorID;
            $sponsors[] = optional(User::find($candidate->man->sponsorID))->name;
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
            $candidate->w_sponsorID  = $candidate->woman->sponsorID;
            $sponsor = optional(User::find($candidate->woman->sponsorID))->name;
            if (!empty($sponsor) && !empty($sponsors) && $sponsors[0] != $sponsor) {
                $sponsors[] = $sponsor;
            }
        }

        $this->candidate = $candidate;
        $this->weekends = Weekend::activeDescending()->get();
        $this->weekend  = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();

        $this->sponsors = implode(' and ', $sponsors);

        $this->subject_line = '[' . config('site.community_acronym') . '] Candidate Registration: ' . $candidate->names;
        $this->reply_to = $reply_to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject_line)
            ->replyTo($this->reply_to, config('site.community_acronym') . ' Pre-Weekend')
            ->markdown('emails.internal-candidate-registration-notice');
    }
}
