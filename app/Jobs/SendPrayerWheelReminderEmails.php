<?php

namespace App\Jobs;

use Exception;
use App\PrayerWheelSignup;
//use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrayerWheelReminderEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
