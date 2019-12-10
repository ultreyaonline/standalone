<?php

namespace App\Jobs;

use Exception;
use App\PrayerWheelSignup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrayerWheelAcknowledgementEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPrayerWheelAcknowledgements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cutoffTime;

    public function __construct()
    {
        $this->cutoffTime = Carbon::now(); // ->addHours(12);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // find all un-acknowledged signups
        $membersWithUnacknowledgedSignups = PrayerWheelSignup::whereNull('acknowledged_at')
            ->get()
            ->pluck('memberID')->unique()->values();

        // get all signups for members having any un-acknowledged signups
        $signups = PrayerWheelSignup::whereIn('memberID', $membersWithUnacknowledgedSignups)
            ->orderBy('memberID')
            ->orderBy('wheel_id')
            ->orderBy('timeslot')
            ->get();

        $signups = $signups->filter(function ($time) {
            return
                // filter to keep only future timeslots
                $time->slot_datetime->greaterThanOrEqualTo($this->cutoffTime)
                &&
                // filter out finished weekends (safeguard)
                !$time->wheel->weekend->ended_over_a_month_ago
                &&
                // filter out empty email addresses
                !empty($time->fresh()->user->email);
        })->groupBy('memberID');

        // loop over each member and email the pending timeslot details
        $signups->each(function ($member_slots) {

            $user = $member_slots->first()->user;

            $times = $member_slots->sortBy('wheel_id')->sortBy('timeslot');

            try {
                Mail::to($user->email)
                    ->queue(new PrayerWheelAcknowledgementEmail($user, $times));

                // set acknowledged_at flag for each successful email
                $member_slots->each(function ($slot) {
                    $slot->acknowledged_at = Carbon::now();
                    $slot->update();
                });
            } catch (Exception $exception) {
                logger($exception->getMessage() . ' on line ' . __LINE__ . '; ' . print_r($member_slots, true));
            }
        });
    }
}
