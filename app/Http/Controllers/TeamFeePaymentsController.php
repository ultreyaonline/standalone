<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Mail\InternalCandidateRegistrationNotice;
use App\Mail\SponsorAcknowledgeCandidate;
use App\Notifications\TeamFeePaymentsNotification;
use App\Models\TeamFeePayments;
use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendAssignments;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TeamFeePaymentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Member');
    }

/**
    $table->unsignedInteger('weekendID')->references('id')->on('weekends')->index('feesforweekendid');
    $table->unsignedInteger('memberID')->references('id')->on('users')->index('feesformember');
    $table->double('total_paid', 4, 2)->nullable();
    $table->date('date_paid')->nullable();
    $table->integer('complete')->nullable()->index('bycomplete');
    $table->string('recorded_by', 60);
    $table->text('comments')->nullable();
 *
    Route::get('finance/team/{weekend?}', 'TeamFeePaymentsController@index')->where(['weekend' => '[0-9]+'])->name('list_team_fees');
    Route::post('finance/team/{id}', 'TeamFeePaymentsController@update')->where(['id' => '[0-9]+'])->name('record_team_fee_payment');
 */

    /**
     * @param Weekend|null $weekend
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Weekend $weekend = null)
    {
        if (empty($weekend)) {
            $weekend  = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        }
        if (empty($weekend)) {
            abort(403, 'Sorry, you need to create a weekend first!');
        }

        // restrict to Head, AsstHead, PreWeekend, FinancialSecretary
        if (! $weekend->mayTrackTeamPayments && ! auth()->user()->can('record team fees paid')) {
            abort(403, 'Must be authorized to record team fees.');
        }

        // retrieve payment and assignment details for the Weekend
        [$payments, $assignments] = $weekend->getFeePaymentsData();

        // Normalize list, and return sorted list of members for display
        $payments = $assignments->map(function ($row) use ($payments) {
            // Inject empty payments for those who haven't paid, so the View renders safely
            $val = $row;
            $payment = $payments->where('memberID', $row->memberID)->where('weekendID', $row->weekendID)->first();
            if (empty($payment)) {
                $payment = new TeamFeePayments([
                    'memberID' => $row->memberID,
                    'weekendID' => $row->weekendID,
                ]);
            }
            $val->payment = $payment;
            return $val;
        })->sortBy('user.lastfirst');

        // Assets for View
        $date = Carbon::now()->format('Y-m-d h:ia');
        $weekends = Weekend::activeDescending()->get();

        // Prepare CSV version
        $csvData = null;
        foreach($payments as $p) {
            $csvData[] = [
                'first' => $p->user->first,
                'last' => $p->user->last,
                'amount' => $p->payment->total_paid,
                'date' => $p->payment->date_paid,
                'notes' => str_replace(',', ';', $p->payment->comments),
                'recorded_by' => $p->payment->recorded_by,
                'recorded_at' => $p->payment->created_at,
                'last_updated' => $p->payment->updated_at,
            ];
        }
        $csvData = collect($csvData);

        return view('finance.team', compact('payments', 'date', 'weekends', 'weekend', 'csvData'));
    }

    public function store(Request $request, Weekend $weekend)
    {
        // restrict to Head, AsstHead, PreWeekend, FinancialSecretary
        if (! $weekend->mayTrackTeamPayments && ! auth()->user()->can('record team fees paid')) {
            abort(403, 'Must be authorized to record team fees.');
        }

        $this->validate($request, [
            'memberID' => 'required|int|exists:users,id|exists:weekend_assignments,memberID',
            'total_paid' => 'currency',
            'date_paid' => 'date|nullable',
            'comments' => 'string|nullable',
        ]);

        // restrict to Head, AsstHead, PreWeekend, FinancialSecretary
        if (! $weekend->mayTrackTeamPayments && ! auth()->user()->can('record team fees paid')) {
            abort(403, 'Must be authorized to record team fees.');
        }

        $attributes = $request->only(['total_paid', 'date_paid', 'complete', 'comments']);
        $attributes['recorded_by'] = auth()->user()->name;

        $payment = TeamFeePayments::updateOrCreate(
            ['weekendID' => $weekend->id, 'memberID' => $request->input('memberID')],
            $attributes
        );

        // @TODO - update ActivityLog handling for this model to track old/new values, for audit purposes

        //@TODO - send an email to AsstHeadCha?

        if (config('site.notify_TeamfeePayments1')) {
            Notification::route('mail', config('site.notify_TeamfeePayments1'))
                ->notify(new TeamFeePaymentsNotification(
                    $payment,
                    User::find($request->input('memberID')),
                    auth()->user()
                ));
        }

        if (config('site.notify_TeamfeePayments2')) {
            Notification::route('mail', config('site.notify_TeamfeePayments2'))
                ->notify(new TeamFeePaymentsNotification(
                    $payment,
                    User::find($request->input('memberID')),
                    auth()->user()
                ));
        }

        flash()->success('Updated payment details for ' . User::find($request->input('memberID'))->name)->important();

        return redirect(route('list_team_fees', $weekend->id));
    }

    public function unpaid_fees(Weekend $weekend)
    {
        // restrict to Head, AsstHead, PreWeekend, FinancialSecretary
        abort_unless('see reports about team fees', 403, 'Must be authorized to see team fees reports.');

        // retrieve payment and assignment details for the Weekend
        [$payments, $assignments] = $weekend->getFeePaymentsData();

        // Get unpaid records only
        $unpaid = $assignments->filter(function ($row) use ($payments) {
            $payment = $payments->where('memberID', $row->memberID)->where('weekendID', $row->weekendID)->first();
            if (empty($payment)) {
                return true;
            }
            return false;
        })->sortBy('user.lastfirst')->groupBy('user.community');

        return view('weekend.report_fees_unpaid', compact('unpaid', 'weekend'));
    }


}
