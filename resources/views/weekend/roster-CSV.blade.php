@extends('layouts.app')

@section('title')
Roster: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekend_roster')

@section('content')
  <p>You may copy/paste the following content into a text file. (ie: 'team-roster.csv') And then use File->Import in Excel to load in the data as columns.
  Or click to <a href="{{url(request()->getUri())}}-all">Download-CSV-with-candidates-included</a>
  </p>
<pre>
Row,Position,Name,Email,Weekend,Church,Phone
@foreach($weekend->team_and_candidates as $p)
{{ $loop->iteration }},{{ $p['role'] }},{{ $p['name'] }},{{ $p['email'] }},{{ $p['weekend'] }},{{ $p['church'] }},{{ $p['phone'] }}
@endforeach
</pre>

  <p>Printed on: {{ $today }}</p>
@endsection
