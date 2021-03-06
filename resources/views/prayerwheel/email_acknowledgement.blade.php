@component('mail::message')
{{ $first ? $first . ',' : '' }}

Thank you for signing up to be a part of the {{ config('site.community_acronym') }} Prayer Wheel for the upcoming Weekend!

@component('mail::panel')
Your prayer time(s) are:

@foreach($prayer_times as $time)
@if($loop->first || $time->wheel_id > $wheel)

**{{ $time->fresh()->weekend_name }}:**
@endif
 - {{ $time->fresh()->slot_datetime_formatted }}

@php($wheel = $time->wheel_id)
@endforeach

@endcomponent

You can pray for the Candidates by name by going to the Weekends tab on our Website. You'll see their names in the right-hand column.



Blessings,



---

You are receiving this message because you signed up for a time slot on the Prayer Wheel indicated above.
Once the time has passed you will no longer receive messages about this time slot.
Thank you for supporting the Tres Dias community in this important way!

@endcomponent
