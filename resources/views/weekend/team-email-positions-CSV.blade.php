@extends('layouts.app')

@section('title')
Roster: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekend_roster')

@section('content')
  <p>You may copy/paste the following content into a text file. (ie: 'team-emails.csv') And then use File->Import in Excel to load in the data as columns.
  </p>
<pre>
Position,Name,Email
@foreach($weekend->team_and_candidates as $p)
{{ $p['role'] }},{{ $p['name'] }},{{ $p['email'] }}
@endforeach
</pre>

  <p>Printed on: {{ $today }}</p>
@endsection
