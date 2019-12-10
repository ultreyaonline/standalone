
@if($signups = isset($member) ? $member->prayerWheelSignups : auth()->user()->prayerWheelSignups)
<?php
$prayer_times = $signups->filter(function ($time) {
    return
        // filter to keep only future timeslots
        $time->slot_datetime->greaterThanOrEqualTo(\Illuminate\Support\Carbon::now())
        &&
        // filter out finished weekends (safeguard)
        !$time->wheel->weekend->ended_over_a_month_ago;
});
$wheel = 0;
$name = isset($member) ? $member->name : auth()->user()->name;
?>
 @if($prayer_times->count())
  <div class="card mb-2 border-warning">
    <div class="card-header card-title alert-warning"><strong>Prayer Wheel Signups for {{ $name }}</strong></div>
    <div class="card-body">

        @foreach($prayer_times as $time)
          @if($loop->first || $time->wheel_id > $wheel)
          @if(!$loop->first && $time->wheel_id > $wheel)</ul>@endif

            <h6><a class="" href="/weekend/{{ $time->wheel->weekend->id ?? '' }}">{{ $time->fresh()->weekend_name }}:</a></h6><ul>
          @endif
          <li>{{ $time->fresh()->slot_datetime_formatted }} &nbsp; <a class="small" href="/prayerwheel/{{ $time->wheel_id }}"><i class="fa fa-edit"></i></a></li>
          @php($wheel = $time->wheel_id)
        @endforeach


    </div>
  </div>
 @endif
@endif
