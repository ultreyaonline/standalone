<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Weekend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CandidateConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject_line;
    public $c;
    public $candidate_name;
    public $candidate_first;
    public $sponsor;
    public $weekend;
    public $weekend_shortname;
    public $weekend_date; // April 14-17, 2016
    public $weekend_dates_with_days_of_week;
    public $weekend_date_with_weekdays;
    public $weekend_long_name_with_number; // Community Name Women’s Tres Dias #4
    public $weekend_long_name_with_number_plus_weekend; // Community Name Tres Dias Women’s Weekend #4
    public $arrival_time; // 6:00-6:15 pm
    public $arrive_by;
    public $end_time = '6:00'; // 6:00  (ie: 6pm on Sunday)
    public $sendoff_start_time;


    public function __construct(User $candidate)
    {
        $this->candidate_name = $candidate->name;
        $this->c = $candidate;
        $this->candidate_first = $candidate->first;
        $this->sponsor = $candidate->sponsor;

        $weekend_number = preg_replace('/[A-Z #]/', '', $candidate->weekend);
        $this->weekend = Weekend::where('weekend_number', $weekend_number)->where('weekend_MF', $candidate->gender)->firstOrFail();

        $this->weekend_date = $this->weekend->short_date_range_without_year;
        $this->weekend_dates_with_days_of_week = $this->weekend->long_date_range_with_weekdays;
        $this->weekend_date_with_weekdays = $this->weekend->long_date_range_with_weekdays;
        $this->weekend_long_name_with_number = $this->weekend->long_name_with_number;
        $this->weekend_long_name_with_number_plus_weekend = $this->weekend->long_name_with_number_plus_weekend;
        $this->weekend_shortname = $this->weekend->shortname;
        $this->arrival_time = $this->weekend->candidate_arrival_time->format('g:i') . '-' . $this->weekend->candidate_arrival_time->addMinutes(30)->format('g:i a');
        $this->arrive_by = $this->weekend->candidate_arrival_time->format('g:i');
        $this->sendoff_start_time = $this->weekend->sendoff_start_time->format('g:i');
        $this->end_time = $this->weekend->end_date->format('g:i a');

        $this->subject_line = $this->candidate_name . ': Tres Dias Weekend ' . $this->weekend_date;
    }

    public function build()
    {
        info('Sending Candidate Confirmation email for ' . $this->candidate_name);

        return $this->from(config('site.email_general'), config('site.retreat_name_for_email_subject'))
            ->replyTo(config('site.email-preweekend-mailbox'), config('site.community_acronym') . ' Registration')
            ->subject($this->subject_line)
            ->view('emails.candidate_confirmation');
    }
}
