@extends('layouts.app')

@section('title', 'Prayer Wheel')

@section('body-class', 'prayerwheel')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-10 offset-sm-1">
        <div class="jumbotron">
          <h1><strong>72-hour Prayer Wheel for Upcoming {{ config('site.community_acronym') }} Weekends</strong></h1>
          <p>Throughout the Tres Dias Weekend, there are 72 one-hour prayer times,
            where people from the Tres Dias community around the world are praying for
            those who are making their 3-day weekend for the first time.</p>
          <p>Choose an upcoming weekend below to indicate which hour you will be praying through.</p>
          <p>Thank you for blessing the community in this way!</p>
        </div>

      </div>
      <div class="col-sm-8 offset-sm-2">
        <div class="card">
          <div class="card-body">

            @if ($wheels->count())
                <ul class="list-unstyled">
                  @foreach ($wheels as $w)
                    <li>
                      <a href="/prayerwheel/{{$w->id}}"><strong>{{ $w->weekend->weekend_full_name ?? $w->customwheel_name}}</strong></a>
                      &nbsp; &nbsp;
                       <strong>{{ $w->weekend ? $w->weekend->short_date_range : $w->custom_starttime . ' - ' . $w->custom_endtime }}</strong>
                    </li>
                  @endforeach
                </ul>
            @endif


          </div>
        </div>

      </div>

    </div>
  </div>
@endsection
