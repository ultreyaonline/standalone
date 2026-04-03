<?php

namespace App\Jobs;

use Exception;
use App\Models\PrayerWheelSignup;
//use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrayerWheelReminderEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Job: SendPrayerWheelReminderEmails
 *
 * Dispatched once daily at 16:00 by the scheduler.
 *
 * PURPOSE
 * -------
 * Send a daily reminder email to community members who have upcoming prayer wheel
 * slots AND have opted in to receive reminders (users.receive_prayer_wheel_reminders = true).
 *
 * FLOW
 * ----
 * 1. Find all PrayerWheelSignup rows where reminded_at IS NULL.
 * 2. Collect all sign-ups for those members.
 * 3. Filter to: future slots, slots during an active weekend window, non-ended weekends,
 *    non-blank email, user has reminders opted in.
 * 4. Group by member; send one PrayerWheelReminderEmail per member.
 *
 * ⚠️  KNOWN ISSUE — reminded_at is NEVER STAMPED
 * ------------------------------------------------
 * The code block that would set `reminded_at = Carbon::now()` is commented out.
 * As a result:
 *   - reminded_at is always NULL
 *   - The whereNull('reminded_at') filter never reduces the member set
 *   - Every day, ALL opted-in members with future slots receive a reminder
 *
 * This produces daily reminder emails, which may be the intended behaviour,
 * but the variable name and filtering logic could potentially get refactored "out"(removed).
 *
 * Future coding considerations:
 *   (a) Remove the reminded_at column and whereNull filter; query by upcoming slot
 *       dates directly (e.g. slots in the next 24–48 hours).
 *   (b) Un-comment the stamping code if "one reminder only" behaviour is desired.
 *   (c) Document explicitly that daily reminders are intentional and rename accordingly. (IN THIS CURRENT CODE BASE IT IS INTENTIONAL)
 *
 * ERROR HANDLING
 * --------------
 * Exceptions per member are caught and logged; other members are not affected.
 */
class SendPrayerWheelReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // @TODO - speed up by replacing this "reminded_at" filter with "future wheels" instead
        // find all un-reminded signups
        $membersWithUnremindedSignups = PrayerWheelSignup::whereNull('reminded_at')
            ->get()
            ->pluck('memberID')->unique()->values();

        // get all signups for members having any un-reminded signups
        $signups = PrayerWheelSignup::whereIn('memberID', $membersWithUnremindedSignups)
            ->orderBy('memberID')
            ->orderBy('wheel_id')
            ->orderBy('timeslot')
            ->get();

        $signups = $signups->filter(function ($time) {
            return
                // filter to keep only future timeslots
                $time->slot_is_future
                &&
                $time->slot_is_during_an_active_weekend
                &&
                // filter out finished weekends (safeguard)
                !$time->wheel->weekend->ended_over_a_month_ago
                &&
                // filter out empty email addresses
                !empty($time->fresh()->user->email)
                &&
                // keep only those who have consented to reminder emails
                $time->user->receive_prayer_wheel_reminders;
        })->groupBy('memberID');

        // loop over each member and email the pending timeslot details
        $signups->each(function ($member_slots) {

            $user = $member_slots->first()->user;

            $times = $member_slots->sortBy('wheel_id')->sortBy('timeslot');

            try {
                Mail::to($user->email)
                    ->queue(new PrayerWheelReminderEmail($user, $times));

//                // set reminded_at flag for each successful email
//                $member_slots->each(function ($slot) {
//                    $slot->reminded_at = Carbon::now();
//                    $slot->update();
//                });
            } catch (Exception $exception) {
                logger($exception->getMessage() . ' on line ' . __LINE__ . '; ' . print_r($member_slots, true));
            }
        });
    }
}
