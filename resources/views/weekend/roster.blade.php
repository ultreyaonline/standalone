@extends('layouts.app')

@section('title')
Roster: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekend_roster')

@section('content')
  <p class="float-right d-print-none bg-info"><a href="{{url(request()->getUri())}}/csv">CSV-team-only</a> </p>
  <p class="float-right d-print-none bg-info"><a href="{{url(request()->getUri())}}/csv-all">Download-CSV-with-candidates</a> |</p>
  <p class="float-right d-print-none bg-info"><a href="{{url(request()->getUri())}}/team-emails">Team-Emails</a> |</p>
  <p class="float-right d-print-none bg-info"><a href="{{url(request()->getUri())}}/team-email-positions">Team-Email-Positions</a> |</p>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <p class="text-center"><strong>Team Roster: {{ $weekend->weekend_full_name }}</strong></p>
        <table class="table table-hover table-sm small">
          <thead>
            {{--<th>#</th>--}}
            <th>Position</th>
            <th>Name</th>
            <th>Email</th>
            <th>Weekend</th>
            <th>Phone</th>
          </thead>
          <tbody>
@foreach($team as $p)
  <tr>
{{--    <th scope="row">{{ $loop->iteration }}</th>--}}
    <td><strong>{{ $p->role->RoleName }}</strong></td>
    <td><a href="{{url('/members/' . $p->user->id)}}">{{ $p->user->name }}</a>
        @if(request('s'))
          &nbsp;({{ $p->user->spouse->first }})
        @endif
    </td>
    <td>{{ $p->user->email }}</td>
    <td>{{ $p->user->weekend }}</td>
    <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($p->user->cellphone ?: $p->user->homephone) !!}</td>
    <td>{{ $p->modified_in_last_three_weeks ? '|' : '' }}{{ $p->user->modified_in_last_three_weeks ? '*' : '' }}</td>
  </tr>
@endforeach

@if($weekend->has_started)
@foreach($weekend->candidates as $c)
            <tr class="bg-warning">
              <td><strong>Candidate</strong></td>
              <td><a href="{{url('/members/' . $c->id)}}">{{ $c->name }}</a>
                @if(request('s') && $c->spouse)
                  &nbsp;({{ $c->spouse->first }})
                @endif
              </td>
              <td>{{ $c->email }}</td>
              <td>{{ $c->weekend }}</td>
              <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($c->cellphone ?: $c->homephone) !!}</td>
              <td>{{ $c->modified_in_last_three_weeks ? '*' : '' }}</td>
            </tr>
@endforeach
@endif
          </tbody>
        </table>




      </div>

    </div>
  </div>
  <p>Totals:
    People={{ $team->unique('memberID')->count() + $weekend->candidates->count() }};
    Candidates={{ $weekend->candidates->count() }};
    Team={{ $team->unique('memberID')->count() }}
  </p>
  <p style="font-style: italic; font-size: smaller;">Printed on: {{ $today }} <span class="float-right">Legend: | = team assignment change in last 21 days. * = member data change in last 21 days</span> </p>
@endsection
