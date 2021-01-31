<?php

namespace App\Http\Controllers;

use App\Mail\HowToSponsor;
use App\Mail\MessageToCommunity;
use App\Mail\MessageToTeamMembers;
use App\Models\Section;
use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('password.confirm');
    }

    public function index()
    {
        return view('errors.notimplemented');
    }

    /**
     * Display form
     *
     * @param Weekend $weekend
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function composeTeamEmail(Weekend $weekend)
    {
        $sections = Section::all();

        // permitted are Secretariat leaders, Rector and heads of specified weekend
        if (auth()->user()->hasAnyRole(['Secretariat', 'Mens Leader', 'Womens Leader', 'Admin'])
            || auth()->user()->id == $weekend->rectorID
            || auth()->user()->rolesForWeekend($weekend->id, $ignoreVisibleOnly = true)->first()->role->isDeptHead
        ) {
            return view('emails.team_message_compose', ['weekend' => $weekend, 'sections' => $sections]);
        }

        // denied
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
    public function emailTeamMembers(Request $request, Weekend $weekend)
    {
        // only leaders and rector and heads of said weekend are allowed to email the entire team
        if (! auth()->user()->hasAnyRole(['Secretariat', 'Mens Leader', 'Womens Leader', 'Admin'])
            && auth()->user()->id != $weekend->rectorID
            && empty(auth()->user()->rolesForWeekend($weekend->id, $ignoreVisibleOnly = true)->first()->role->isDeptHead)
        ) {
            return redirect('/weekend/' . $weekend->id);
        }

        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
            'section' => 'numeric|gte:-2|lt:99',
        ]);


        $flash_messages = [];
        $attachment = $attachment2 = null;

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
        if ($request->hasFile('attachment2')) {
            $file = $request->file('attachment2');

            if ($file->isValid()) {
                $original_filename = $file->getClientOriginalName();
                $stored_filename = $file->storeAs('attachments', $original_filename, 'local');
                $attachment2 = [
                    'file' => $stored_filename,
                    'name' => $original_filename,
                ];
            }
        }

        // get all confirmed team members regardless of Weekend visibility
        $teamRecipients = $weekend->team_all_visibility->unique('memberID');

        // filter by selected section
        $section = $request->input('section', 0);

        if ($section > 0) {
            $sectionRoleIds = WeekendRoles::where('section_id', $section)->pluck('id');
            $teamRecipients = $teamRecipients->whereIn('roleID', $sectionRoleIds);
        }
        if ($section == -1) {
            $sectionHeadIds = WeekendRoles::where('isDeptHead', 1)->pluck('id');
            $teamRecipients = $teamRecipients->whereIn('roleID', $sectionHeadIds);
        }
        if ($section == -2) {
            $sectionRoleIds = WeekendRoles::where('isDeptHead', 1)->orWhere('section_id', 2)->pluck('id');
            $teamRecipients = $teamRecipients->whereIn('roleID', $sectionRoleIds);
        }

        $team = \App\Models\User::whereIn('id', $teamRecipients->pluck('memberID'))
            ->role('Member')
            ->where('email', '!=', '')// skip blank email addresses
            ->whereNotNull('email')
            ->get();

        if ($request->input('exclude_rector', 0) === '1') {
            $team = $team->reject(function ($person) use ($weekend) {
                return $person->id === $weekend->rectorID;
            });
        }

        $team->each(function ($recipient, $key) use ($weekend, $subject, $message, $attachment, $attachment2, $section) {
            Mail::to($recipient)->queue(new MessageToTeamMembers($weekend, $subject, $message, auth()->user(), $attachment, $attachment2));
            if ($section != 0) {
                flash()->info($recipient->name . ' - ' .
                    $recipient->weekendAssignmentsAnyVisibility->where('weekendID', $weekend->id)->first()->role->RoleName)
                    ->important();
            }
        });

        $flash_messages[] = 'Message sent: ' . $team->count() . ' team recipients.';
        event('MassEmailSentToTeam', ['weekend' => $weekend, 'by' => auth()->user(), 'recipients' => $team]);

        if ($request->input('include_candidates', 0) === '1') {
            $candidates = $weekend->candidates;

            $candidates->each(function ($recipient, $key) use ($weekend, $subject, $message, $attachment, $attachment2) {
                if (empty($recipient->email)) {
                    return;
                }
                Mail::to($recipient)->queue(new MessageToTeamMembers($weekend, $subject, $message, auth()->user(), $attachment, $attachment2));
            });

            $flash_messages[] = 'Message sent: ' . $candidates->count() . ' candidate recipients.';
            event('MassEmailSentToCandidates', ['weekend' => $weekend, 'by' => auth()->user(), 'recipients' => $candidates]);
        }

        if (count($flash_messages)) {
            flash()->success(implode('<br>', $flash_messages));
            if ($section != 0) {
                flash()->important();
            }
        }
        return redirect('/weekend/' . $weekend->id);
    }

    /**
     * Display form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function composeCommunityEmail()
    {
        $notice_types = [
            'community' => 'Community News - General',
            'weekend' => 'Weekend-Specific Communications (sendoff/closing/serenade times, candidate updates)',
            'sequela' => 'Secuela Notices (fellowship events)',
        ];
        if (config('site.features-emailtypes-reunion')) {
            $notice_types['reunion'] = 'Reunion Group invites/announcements/messages/updates';
        }

        // permitted are Secretariat leaders, rectors, etc
        if (auth()->user()->can('email entire community')) {
            return view('emails.community_message_compose', compact('notice_types'));
        }

        // denied
        return redirect('/home');
    }

    /**
     * Send message to entire community
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function emailEntireCommunity(Request $request)
    {
        // @TODO - add moderator queue for non-authorized people, so anyone can request an email be sent, but Admin must release it
        // only allow authorized people to email the entire community
        if (!auth()->user()->can('email entire community')) {
            return redirect('/home');
        }

        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
        ]);

        $sender = auth()->user();

        // @TODO - restrict attachment handling, and only process attachments if authorized

        $subject = $request->input('subject');
        $message = $request->input('message');
        $attachment = null;

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

        $recipients = \App\Models\User::active()
            ->where('email', '!=', '')// skip blank email addresses
            ->whereNotNull('email')
            ->role('Member')
            ->notunsubscribed();

        if (in_array($request->input('mail_to_gender'), ['M', 'W'], false)) {
            $recipients = $recipients->where('gender', $request->input('mail_to_gender'));
        }

        if ($request->input('community_local', 'no') === 'local' && $request->input('community_other', 'no') === 'no') {
            $recipients = $recipients->onlyLocal();
            // @TODO - add in here anyone on a Weekend which hasn't begun yet
        }
        if ($request->input('community_local', 'no') === 'no' && $request->input('community_other', 'no') === 'other') {
            $recipients = $recipients->onlyNonlocal();
        }

        if ($request->input('contains_surprises', 'no') === 'yes') {
            $recipients = $recipients->where('okay_to_send_serenade_and_palanca_details', 1);
        }

        switch ($request->input('notice_type')) {
            case 'community':
                $recipients = $recipients->where('receive_email_community_news', 1);
                break;
            case 'weekend':
                $recipients = $recipients->where('receive_email_weekend_general', 1);
                break;
            case 'sequela':
                $recipients = $recipients->where('receive_email_sequela', 1);
                break;
            case 'reunion':
                $recipients = $recipients->where('receive_email_reunion', 1);
                break;
        }

        $recipients = $recipients->get();

        $recipients->each(function ($recipient, $key) use ($subject, $message, $attachment, $sender) {
            Mail::to($recipient)->queue(new MessageToCommunity($subject, $message, $sender, $attachment));
//            echo $recipient->name . ' - ' . $recipient->weekend . "\n<br>";
        });

        flash()->success('Message queued for delivery to ' . $recipients->count() . ' recipients.');
        event('MassEmailSentToCommunity', ['by' => auth()->user(), 'recipients' => $recipients]);

        return redirect('/home');
    }

    /**
     * Send Email: How To Sponsor instructions to all community
     * @TODO  - ALERT: Remember, this goes to everyone!
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function emailHowToSponsorToEveryone()
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403, 'Only administrators can access this action.');

        $recipients = \App\Models\User::active()
            ->where('email', '!=', '')// skip blank email addresses
            ->whereNotNull('email')
            ->notunsubscribed()
            ->onlyLocal()
            ->where('okay_to_send_serenade_and_palanca_details', 1)
            ->where('receive_email_weekend_general', 1)
            ->role('Member')
            ->get();

        $recipients->each(function ($recipient, $key) {
            Mail::to($recipient)
                ->queue(new HowToSponsor());
        });

        flash()->success('HowToSponsor sent to ' . $recipients->count() . ' recipients.');
        event('HowToSponsorEmailSentToCommunity', ['recipients' => $recipients]);

        return redirect('/home');
    }
}
