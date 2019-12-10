<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\Mail\InternalCandidateRegistrationNotice;
use App\Mail\SponsorAcknowledgeCandidate;
use App\User;
use App\Weekend;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CandidatePaymentsController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('permission:record candidate fee payments');
        $this->user = $user;
    }

    public function index($slug = null)
    {
        $weekend = null;

        if ($slug) {
            $community = substr($slug, 0, 4);
            $number    = substr($slug, 4);
            $weekend   = Weekend::where('tresdias_community', strtoupper($community))
                ->where('weekend_number', $number)
                ->first();
        }
        if (empty($weekend)) {
            $weekend  = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        }
        $candidates = Candidate::where('weekend', $weekend->shortname ?? '')->orderBy('fees_paid')->get();
        $candidates = $candidates->sort(
            function ($a, $b) {
                // sort by column1 first, then 2, and so on
                return strcmp($a->fees_paid, $b->fees_paid)
                    ?: strcmp($a->sponsor, $b->sponsor);
            }
        );

        $date = Carbon::now()->format('Y-m-d h:ia');

        $weekends = Weekend::activeDescending()->where('weekend_MF', 'M')->get();


        $csvData = null;
        foreach($candidates as $c) {
            $csvData[] = [
                'name' => ($c->man->name ?? '') . ($c->man && $c->woman ? ' and ' : '')  . ($c->woman->name ?? ''),
                'status' => $c->fees_paid ? 'PAID' : 'UNPAID',
                'sponsor' => $c->sponsor,
                'details' => str_replace(',', ';', $c->payment_details),
            ];
        }
        $csvData = collect($csvData);

        return view('finance.candidates', compact('candidates', 'date', 'weekends', 'weekend', 'csvData'));
    }

    /**
     * Display the form for editing candidate payments
     *
     * @param  \App\Candidate $candidate
     * @return \Illuminate\Http\Response
     */
    public function edit($slug = null, Candidate $candidate)
    {
        $weekend = null;

        // retrieve Candidate record using route-model-binding
//        if ($candidate === null) {
//            $candidate = new Candidate();
//        }

        $weekends = Weekend::activeDescending()->where('weekend_MF', 'M')->get();

        if ($slug) {
            $community = substr($slug, 0, 4);
            $number    = substr($slug, 4);
            $weekend   = Weekend::where('tresdias_community', strtoupper($community))
                ->where('weekend_number', $number)
                ->first();
        }

        if (!$weekend) {
            $weekend = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        }

        return view('finance.edit_candidate', compact('candidate', 'weekends', 'weekend'));
    }

    /**
     * Update the candidate payment details
     *
     * @param  \Illuminate\Http\Request $request
     * @param Candidate $candidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Candidate $candidate)
    {
        $this->validate($request, [
            'fees_paid' => 'boolean',
            'payment_details' => 'string|nullable',
        ]);

        $fields = [
            'fees_paid',
            'payment_details',
        ];

        $candidate->update($request->only($fields));

        flash()->success('Updated payment details for ' . $candidate->names);

        // @TODO:
        return redirect('/finance/candidates/' . preg_replace('/[^a-z0-9]/', '', strtolower($request->input('weekend'))));
    }

    // @TODO - send email acknowledgement of payment ?
}
