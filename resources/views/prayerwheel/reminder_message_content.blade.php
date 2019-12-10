@component('mail::message')
{{ $first ? $first . ',' : '' }}

REMINDER!

This is a friendly reminder that you signed up to be a part of
the {{ config('site.community_acronym') }} Prayer Wheel for the upcoming Weekend!

@component('mail::panel')
Your upcoming prayer time(s) are:

@foreach($prayer_times as $time)
@if($loop->first || $time->wheel_id > $wheel)

**{{ $time->fresh()->weekend_name }}:**
@endif
- {{ $time->fresh()->slot_datetime_formatted }}

@php($wheel = $time->wheel_id)
@endforeach

@endcomponent

You will find a list of candidate names and team members on the Weekends page of our website at {{ config('app.url') }} .


You may also add this reminder to your calendar using the attached calendar invite(s).

We will send reminders at 4:00pm each day of the Weekend.


Blessings,

Palanca Committee


---

You are receiving this message because you signed up for a prayer time on the Prayer Wheel(s) indicated above.
Once each time has passed you will no longer receive messages about that signup.
Thank you for supporting the Tres Dias community in this important way!

@endcomponent
