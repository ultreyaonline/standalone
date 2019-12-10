@extends('layouts.app')

@section('title')
Roster: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekend_roster')

@section('content')
  <p>You may copy/paste the following content into a an email program to send messages to the whole list.
  </p>
<pre>
@foreach($weekend->team_and_candidates as $p)
{{ $p['name'] }} &lt;{{ $p['email'] }}&gt;,
@endforeach
</pre>

  <p>Printed on: {{ $today }}</p>
@endsection
