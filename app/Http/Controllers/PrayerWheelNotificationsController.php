<?php

namespace App\Http\Controllers;

use App\Jobs\SendPrayerWheelReminderEmails;
use App\Mail\PrayerWheelInviteEmail;
use App\Mail\PrayerWheelReminderEmail;
use App\Models\PrayerWheel;
use App\Models\User;
use App\Models\Weekend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PrayerWheelNotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('password.confirm')->except(['emailPrayerWheelReminders']);
    }

    /**
     * Display form
     *
     * @param PrayerWheel $wheel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function composeCommunityEmail(PrayerWheel $wheel)
    {
        $notice_types = [
            'prayerwheel' => 'Prayer Wheel Invitations',
            'community'   => 'Community News - General',
            'weekend'     => 'Weekend-Specific Communications (sendoff/closing/serenade times, candidate updates)',
        ];

        // @TODO - store this in a table, to allow retrieval of "last message sent":
        $default_message = "
Hello Brothers and Sisters,

Our upcoming weekends begin in just a couple days, and there are still a number of open time slots on the Prayer Wheel.

Prayer is a vital part of what makes a Tres Dias Weekend happen. Without it, we cannot even start planning or preparing, because otherwise it would be what we want and not what He wants. 

You have the opportunity to partner with the Candidates attending a Weekend by praying for them over a 72 hour time span. The entire Weekend is covered in prayer because of our Community. Don't you want to be a part of that? Do you remember what it was like to hear and see that people you didn't even know were praying for you by name and constantly?

Would you consider blessing the Candidates and Team by signing up for one or more open times!
(Note: If you're serving on a given weekend team, we suggest signing up for the other Weekend instead of for the one you're serving on.)

TO SIGN UP ON THE PRAYER WHEEL:
" . config('app.url') . "/prayerwheel

IF YOU NEED HELP LOGGING IN:
You may request a password or a password-reset at " . config('app.url') . "/pescadore by supplying your email address.
Then, once you've set a password you may login and proceed with prayer wheel signups.

Thank you for partnering with us in this!

" . (auth()->check() ? auth()->user()->name : '');

        // permitted to 'send prayer wheel invites'
        if (auth()->user()->can('send prayer wheel invites')) {
            return view('prayerwheel.invitation_message_compose', compact('notice_types', 'wheel', 'default_message'));
        }

        // denied
        return redirect()->back();
    }

    /**
     * Send invites to entire community
     *
     * @param Request $request
     * @param PrayerWheel $wheel
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function emailEntireCommunity(Request $request, PrayerWheel $wheel)
    {
        // only allow authorized people to email the entire community
        if (!auth()->user()->can('send prayer wheel invites')) {
            return redirect('/home');
        }

        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
        ]);

        $subject = $request->input('subject');
        $message = $request->input('message');

        // @TODO: Store $message into clipboard table, as default for next composed message

        $recipients = \App\Models\User::active()
            ->where('email', '!=', '')// skip blank email addresses
            ->whereNotNull('email')
            ->where('receive_prayer_wheel_invites', true)
            ->role('Member')
            ->notunsubscribed();

        if (in_array($request->input('mail_to_gender'), ['M', 'W'], false)) {
            $recipients = $recipients->where('gender', $request->input('mail_to_gender'));
        }

        if ($request->input('community_local', 'no') === 'local' && $request->input('community_other', 'no') === 'no') {
            $recipients = $recipients->onlyLocal();
        }
        if ($request->input('community_local', 'no') === 'no' && $request->input('community_other', 'no') === 'other') {
            $recipients = $recipients->onlyNonLocal();
        }

        if ($request->input('contains_surprises', 'no') === 'yes') {
            $recipients = $recipients->where('okay_to_send_serenade_and_palanca_details', 1);
        }

        switch ($request->input('notice_type')) {
            case 'prayerwheel':
                $recipients = $recipients->where('receive_prayer_wheel_invites', 1);
                break;
            case 'community':
                $recipients = $recipients->where('receive_email_community_news', 1);
                break;
            case 'weekend':
                $recipients = $recipients->where('receive_email_weekend_general', 1);
                break;
        }

        $recipients = $recipients->get();

        $recipients->each(function ($recipient, $key) use ($subject, $message) {
            $sender = auth()->user();
            Mail::to($recipient)->queue(new PrayerWheelInviteEmail($subject, $message, $sender));
//            echo $recipient->name . ' - ' . $recipient->weekend . "\n<br>";
        });

        flash()->success('Message queued for delivery to ' . $recipients->count() . ' recipients.');
        event('MassPrayerWheelInviteSentToCommunity', ['by' => auth()->user(), 'recipients' => $recipients]);

        return redirect('/home');
    }

    public function emailPrayerWheelReminders(PrayerWheel $wheel): void
    {
        SendPrayerWheelReminderEmails::dispatch($wheel);
    }
}
