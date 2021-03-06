<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrayerWheelReminderEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $recipient;
    public $name;
    public $first;
    public $prayer_times;

    public function __construct($recipient, $prayer_times)
    {
        $this->recipient    = $recipient;
        $this->name         = $recipient->name;
        $this->first        = $recipient->first;
        $this->prayer_times = $prayer_times;
        $this->subject      = '[' . config('site.community_acronym') . '] Prayer Wheel Reminder';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        info('Building and sending Prayer Wheel Reminder email for ' . $this->recipient->email);

        // sort for logical display
        $this->prayer_times = $this->prayer_times->sortBy('timeslot')->sortBy('wheel_id');

        $message = $this->markdown('prayerwheel.reminder_message_content')
            ->from(config('site.email_general'), '[' . config('site.community_acronym') . '] Prayer Wheel')
            ->replyTo(config('site.email_general'), config('site.community_acronym') . ' General Mailbox');

        // add Calendar invites to emails
        foreach ($this->prayer_times as $time) {
            $vCalendar = new \Eluceo\iCal\Component\Calendar(config('app.url') . '/prayerwheel');
            $vEvent    = new \Eluceo\iCal\Component\Event();

            $vEvent
                ->setDtStart(new \DateTime($time->slot_datetime->toDateTimeString()))
                ->setDtEnd(new \DateTime($time->slot_datetime->addHour()->toDateTimeString()))
                ->setSummary('Prayer Wheel')
//                ->setTimezoneString(config('site.timezone', config('app.timezone')))
//                ->setUseTimezone(true)
                ->setDescription($time->weekend_name);

            $vCalendar->addComponent($vEvent);

            $message->attachData(
                $vCalendar->render(),
                'cal-' . config('site.community_acronym') . $time->slot_datetime->format('-F-j-y-ga') . '.ics',
                [
                    'mime' => 'text/calendar',
                ]
            );
        }

        return $message;
    }

    public function tags()
    {
        return ['email', 'prayerwheel-reminders'];
    }
}
