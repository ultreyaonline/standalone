@extends('layouts.app')

@section('title')
  Service History - Spiritual Directors
@endsection

@section('body-class', 'sd_history')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <p class="text-center"><strong>Service History - Spiritual Directors</strong></p>
        <table class="table table-hover table-sm small">
          <thead>
          {{--<th>#</th>--}}
          <th>Name</th>
          <th colspan="2">Weekend</th>
          <th>Role</th>
          </thead>
          <tbody>
          @foreach($sd_list as $member)
            <tr>
              <td colspan="4"><strong>{{ $member->name }}</strong>
                {{ $member->community === config('site.community_acronym') ? '' : '(' . $member->community . ') '. $member->city . ' ' . $member->state }}
              </td>
            </tr>
            @if ($member->weekendAssignments->count())
              @foreach ($member->weekendAssignments as $p)
                  <tr>
                    <td>&nbsp;</td>
                    <td{!! Str::contains($p->role->RoleName, 'Spiritual Director') ? ' style="font-weight: bold"' : '' !!}><a href="{{url('/weekend/' . $p->weekend->id)}}">
                        {{$p->weekend->weekend_full_name}}</a>
                    </td>
                    <td>{{ $p->weekend->short_date_range }}</td>
                    <td{!! Str::contains($p->role->RoleName, 'Spiritual Director') ? ' style="font-weight: bold"' : '' !!}>{{ $p->role->RoleName }}</td>
                  </tr>
              @endforeach
              @foreach ($member->weekendAssignmentsExternal as $p)
                <tr>
                  <td>&nbsp;</td>
                  <td{!! Str::contains($p->RoleName, 'Spiritual Director') ? ' style="font-weight: bold"' : '' !!}>{{ $p->WeekendName }}</td>
                  <td>&nbsp;</td>
                  <td{!! Str::contains($p->RoleName, 'Spiritual Director') ? ' style="font-weight: bold"' : '' !!}>{{ $p->RoleName }}</td>
                </tr>
              @endforeach
            @endif

          @endforeach



          </tbody>
        </table>

      </div>

    </div>
  </div>
  <p>Printed on: {{ \Illuminate\Support\Carbon::now() }}</p>
@endsection
