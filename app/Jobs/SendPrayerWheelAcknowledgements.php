<?php

namespace App\Jobs;

use Exception;
use App\Models\PrayerWheelSignup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrayerWheelAcknowledgementEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Job: SendPrayerWheelAcknowledgements
 *
 * Dispatched every 10 minutes by the scheduler.
 *
 * PURPOSE
 * -------
 * For every community member who has prayer-wheel sign-ups that have NOT yet been
 * acknowledged, send them ONE consolidated email listing all their upcoming slots.
 * After a successful send, each slot is stamped with `acknowledged_at = now()` so the
 * member is not emailed again for those slots.
 *
 * FLOW
 * ----
 * 1. Find all PrayerWheelSignup rows where acknowledged_at IS NULL.
 * 2. Collect all sign-ups for those members (not just unacknowledged — full picture).
 * 3. Filter out: past timeslots, weekends that ended > 28 days ago, blank email addresses.
 * 4. Group by member; send one PrayerWheelAcknowledgementEmail per member.
 * 5. On success: stamp acknowledged_at on each slot in that batch.
 *
 * TIMING NOTE
 * -----------
 * $cutoffTime is captured at dispatch time (constructor), not at execution time.
 * If the job sits in the Redis queue for several minutes, "future slot" filtering
 * uses the original dispatch timestamp. Under the 10-minute schedule cadence this is
 * inconsequential, but worth knowing if the queue falls behind.
 *
 * ERROR HANDLING
 * --------------
 * Exceptions during a single member's email are caught and logged; other members
 * in the same job run are not affected.
 */
class SendPrayerWheelAcknowledgements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The timestamp captured at dispatch time, used as the "now" reference for
     * filtering to future prayer slots only.
     *
     * @var Carbon
     */
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
