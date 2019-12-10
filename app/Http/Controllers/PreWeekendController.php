<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\Weekend;
use Illuminate\Http\Request;

class PreWeekendController extends Controller
{
    public function sendOffCoupleHistoryReport()
    {
        $weekends = Weekend::all();

        return view('preweekend.sendoff_couples_report')
            ->withWeekends($weekends->reverse());
    }

    public function invitationPreparationWorksheet()
    {
        // defaults to first non-finished weekend (rolls over one day after the weekend finishes)
        $weekend = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();

        $candidates = Candidate::where('weekend', $weekend->shortname ?? '')
            ->ReadyToMail()
            ->InvitationNotMailed()
            ->get();

        return view('preweekend.invitation_preparation_worksheet')->withCandidates($candidates)->withWeekend($weekend);
    }
}
