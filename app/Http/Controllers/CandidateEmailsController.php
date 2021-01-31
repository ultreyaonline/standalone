<?php

namespace App\Http\Controllers;

use App\Mail\MessageToSponsors;
use App\Models\User;
use App\Models\Weekend;
use App\Models\Candidate;
use App\Mail\CandidateReminderEmail;
use App\Mail\WebsiteLoginInstructions;
use App\Mail\CandidateConfirmationEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class CandidateEmailsController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('password.confirm');
        $this->user = $user;
    }


    /**
     * Send welcome/confirmation letter
     *
     * @TODO - send to BOTH candidate spouses at same time?
     * @TODO - send to Sponsor Spouses too?
     * @TODO - list both Sponsor Spouse names in email?
     *
     * @param User $candidate_user
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function sendCandidateConfirmationEmail(User $candidate_user)
    {
        if (! auth()->user()->can('email candidates')) {
            return redirect('/');
        }

        if (null === $candidate_user) {
            flash('No recipient specified', 'error');

            return false;
        }

        // set candidate TO address
        $mail = Mail::to($candidate_user);

        // set CC to Sponsor
        if ($candidate_user->theirSponsor) { // theirSponsor returns the "sponsor text" if present, else the ID's first+last name
            $mail->cc($candidate_user->theirSponsor);
        }

        // bcc the pre-weekend committee
        $mail->bcc(config('site.email-preweekend-mailbox'));

        $mail->queue(new CandidateConfirmationEmail($candidate_user));

        // update flag to mark as "sent" already
        Candidate::byMemberId($candidate_user->id)->first()->setAttribute(strtolower($candidate_user->gender) . '_confirmation_email_sent', Carbon::now())->save();

        flash('Welcome/Confirmation email sent to ' . $candidate_user->name, 'success');
        event('CandidateConfirmationEmailSentToIndividual', ['recipient' => $candidate_user]);

        return back();
    }

    /**
     * Send reminder emails to all candidates for the specified weekend-id
     */
    public function sendCandidatePackingListToAllCandidatesForWeekend(Weekend $weekend)
    {
        if (! auth()->user()->can('email candidates')) {
            return redirect('/');
        }

        if (!$weekend) {
            flash()->error('Invalid weekend specified');

            return redirect()->back();
        }

        $candidates = $weekend->candidates;

        $candidates = $candidates->filter(function ($candidate, $key) use ($weekend) {
            // skip if already sent
            if (Candidate::byMemberId($candidate->id)->first()->getAttribute(strtolower($weekend->weekend_MF) . '_packing_list_email_sent')) {
                return false;
            }

            $this->sendCandidateReminderToOnePerson($candidate);
            return true;
        });

        flash()->success('CandidatePackingList sent to ' . $candidates->count() . ' recipients.');
        event('CandidatePackingListSentToGroup', ['recipients' => $candidates]);

        return back();
    }

    /**
     * Send candidate packing list etc
     *
     * @param User $user
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function sendCandidateReminderToOnePerson(User $user)
    {
        if (! auth()->user()->can('email candidates')) {
            return redirect('/');
        }

        if (null === $user) {
            flash('No recipient specified', 'error');

            return false;
        }

        // set candidate TO address
        $mail = Mail::to($user);

        // set CC to Sponsor
        if ($user->theirSponsor) {
            $mail->cc($user->theirSponsor);
        }

        // bcc the pre-weekend committee
        $mail->bcc(config('site.email-preweekend-mailbox'));

        $mail->queue(new CandidateReminderEmail($user));

        // update flag to mark as "sent" already
        Candidate::byMemberId($user->id)->first()->setAttribute(strtolower($user->gender) . '_packing_list_email_sent', Carbon::now())->save();

        flash('Reminder email sent to ' . $user->name, 'success');
        event('CandidatePackingListSentToIndividual', ['recipient' => $user]);

        return back();
    }


    public function sendWebsiteWelcomeToCandidatesForWeekend(Weekend $weekend)
    {
        // restrict to authorized administrators
        if (!auth()->user()->can('webmaster-email-how-to-login-msg') && ! auth()->user()->can('email candidates')) {
            return redirect('/');
        }

        if (!$weekend) {
            flash()->error('Invalid weekend specified');

            return redirect()->back();
        }

        $candidates = $weekend->candidates;

        $sent = $candidates->filter(function ($recipient, $key) {

            // skip if already logged in at least once
            if ($recipient->last_login_at) {
                return false;
            }

            // lookup spouse
            // skip them if their spouse's weekend has not "ended" yet and share same email address
            if ($recipient->spouse &&
                $recipient->spouse_weekend_has_ended === false &&
                $recipient->spouse->email == $recipient->email) {
                return false;
            }

            Mail::to($recipient)->queue(new WebsiteLoginInstructions($recipient));
            flash()->info('Welcome sent to ' . $recipient->name);
            return true;
        });

        event('WebsiteWelcomeEmailSentToCandidates', ['recipients' => $candidates]);
        flash()->success('Website welcome email sent to ' . $sent->count() . ' candidates of ' . $weekend->weekend_full_name);

        return back();
    }


    /**
     * Display form
     *
     * @param Weekend $weekend
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function composeSponsorsEmail(Weekend $weekend)
    {
        // restrict to authorized administrators
        if (auth()->user()->can('email sponsors')) {
            return view('emails.sponsor_message_compose', ['weekend' => $weekend]);
        }

        // denied
        flash()->error('Requires emailing privileges. Please contact the site administrator.');
        return redirect('/weekend/' . $weekend->id);
    }

    /**
     * Send message
     *
     * @param Request $request
     * @param Weekend $weekend
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendEmailToSponsorsOfWeekend(Request $request, Weekend $weekend)
    {
        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
        ]);

        $flash_messages = [];
        $attachment = null;

        $subject = $request->input('subject');
        $message = $request->input('message');

        // UploadedFile
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            if ($file->isValid()) {
                $original_filename = $file->getClientOriginalName();
                $stored_filename = $file->storeAs('attachments', $original_filename, 'local');
                $attachment = [
                    'file' => $stored_filename,
                    'name' => $original_filename,
                ];
            }
        }

        // restrict to authorized administrators
        if (!auth()->user()->can('email sponsors')) {
            flash()->error('Requires emailing privileges. Please contact the site administrator.');

            return redirect('/');
        }

        if (!$weekend) {
            flash()->error('Invalid weekend specified');

            return redirect()->back();
        }

        $sponsors = $weekend->sponsors;

        $sent = $sponsors->filter(function ($recipient, $key) use ($weekend, $subject, $message, $attachment) {
            if (empty($recipient->email)) {
                return false;
            }

            Mail::to($recipient)->queue(new MessageToSponsors($weekend, $subject, $message, auth()->user(), $attachment));
//            echo $recipient->name . ' - ' . $recipient->weekend . "\n<br>";

            flash()->info('Message sent to ' . $recipient->name);
            return true;
        });

        flash()->success('Message emailed to ' . $sent->count() . ' sponsors of ' . $weekend->short_name . '-' . $weekend->weekend_MF);

        return redirect('/weekend/' . $weekend->id);
    }
}
