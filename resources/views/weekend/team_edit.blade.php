@extends('layouts.app')

@section('title')
Edit Team: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'team-editor')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header card-title" onclick="window.location='{{ route('weekend.show', $weekend->id) }}';"><strong>{{ $weekend->weekend_full_name }}</strong></div>
          <div class="card border-info d-print-none">
            <div class="card-body">
              <a href="{{ route('weekend.show', $weekend->id) }}"><button class="btn btn-sm btn-primary"><i class="fa fa-backward" aria-hidden="true"></i> Back to Weekend</button></a>
              @if((config('site.admin_old_weekend_teams_editable') ||! $weekend->ended_over_a_month_ago)
              && (auth()->user()->can('edit team member assignments')
              || auth()->user()->id === $weekend->rectorID
              || $weekend->head_cha->contains(auth()->user()->id)
              || auth()->user()->can('make SD assignments')))
              <a href="/team/{{ $weekend->id }}/add"><button class="btn btn-sm btn-success"><i class="fa fa-user-plus" aria-hidden="true"></i> <strong>Add Person</strong></button></a>
              @endif
              <a href="/weekend/{{ $weekend->id }}/roster"><button class="btn btn-sm btn-secondary"><i class="fa fa-print" aria-hidden="true"></i> Printable</button></a>
            </div>
          </div>
          <div class="card-body">

            <table class="table table-striped table-bordered table-hover">
              <tr>
                @if((config('site.admin_old_weekend_teams_editable') ||! $weekend->ended_over_a_month_ago)
                && (auth()->user()->can('edit team member assignments')
                || auth()->user()->id === $weekend->rectorID
                || $weekend->head_cha->contains(auth()->user()->id)
                || auth()->user()->can('make SD assignments')))
                <th class="d-print-none">Action </th>
                @endif
                <th>Position</th>
                {{--<th>Status</th>--}}
                <th>Name</th>
                <th class="d-print-none">Notes</th>
                <th class="d-print-none">City</th>
                <th>Email</th>
                <th class="d-print-none">Phone</th>
              </tr>
            @if(count($assignments))
              @foreach($assignments as $a)
<?php if (empty($a->memberID)) continue; ?>
              <tr class="{{ $a->confirmed === \App\Enums\TeamAssignmentStatus::Accepted ? 'text-success' : ($a->confirmed < \App\Enums\TeamAssignmentStatus::Accepted ? 'text-danger' : 'text-secondary') }}">
                @if((config('site.admin_old_weekend_teams_editable') ||! $weekend->ended_over_a_month_ago)
                && (auth()->user()->can('edit team member assignments')
                || auth()->user()->id === $weekend->rectorID
                || $weekend->head_cha->contains(auth()->user()->id)
                || auth()->user()->can('make SD assignments')))
                <td class="{{ $a->confirmed === \App\Enums\TeamAssignmentStatus::Accepted ? 'bg-success' : ($a->confirmed < \App\Enums\TeamAssignmentStatus::Accepted ? 'bg-danger' : 'bg-secondary') }} d-print-none" style="width: 120px">
                  @if($positions->contains('RoleName', $a->role->RoleName))
                  <a href="/team/{{ $weekend->id }}/p/{{ $a->roleID }}/m/{{ $a->memberID }}/edit"><button class="btn btn-sm btn-secondary">Edit</button></a>

                  <form class="d-inline" action="/team/{{ $weekend->id }}/p/{{ $a->roleID }}/m/{{ $a->memberID }}/delete" method="POST" onsubmit="return ConfirmDelete();">
                    @csrf @method('delete')
                    <button class="btn btn-sm btn-secondary">Del.</button>
                  </form>
                  @endif
                </td>
                @endif
                <td>{{ $a->role->RoleName }}</td>
                {{--<td>[*] {{ $a->confirmed == \App\Enums\TeamAssignmentStatus::Accepted ? 'Confirmed' : 'WAITING' }}</td>--}}
                  <td>
                  {{ $a->confirmed != \App\Enums\TeamAssignmentStatus::Accepted ? '*' : '' }}{{ $a->user->name }}
                      @if($a->confirmed != \App\Enums\TeamAssignmentStatus::Accepted)
                      <span style="font-size: 0.7em; line-height: 0.7em"><br>({{ \App\Enums\TeamAssignmentStatus::getDescription($a->confirmed) }})</span>
                      @endif
                  </td>
                <td class="d-print-none small">{{ $a->comments }}</td>
                <td class="d-print-none">{{ $a->user->city ?? ''}}{{ $a->user->state ? ', ' . $a->user->state : '' }}</td>
                <td class="">{{ $a->user->email }}</td>
                <td class="d-print-none">{{ $a->user->cellphone ?: $a->user->homephone }}</td>
              </tr>
              @endforeach
            @else
              <tr><td colspan="7">No positions assigned yet. Please use the Add button.</td> </tr>
            @endif
            </table>
          </div>
          <div class="card-footer"></div>
        </div>
      </div>
    </div>

    <div class="row d-print-none">
      <div class="col-md-3">
        <div class="card border-info">
          <div class="card-body">
            @if((config('site.admin_old_weekend_teams_editable') ||! $weekend->ended_over_a_month_ago) && (auth()->user()->can('edit team member assignments') || auth()->user()->id === $weekend->rectorID || auth()->user()->can('make SD assignments')))
            <a href="/team/{{ $weekend->id }}/add"><button class="btn btn-sm btn-success"><i class="fa fa-user-plus" aria-hidden="true"></i> <strong>Add Person</strong></button></a>
            @endif
            <a href="/weekend/{{ $weekend->id }}/roster"><button class="btn btn-sm btn-secondary"><i class="fa fa-print" aria-hidden="true"></i> Printable</button></a>

          </div>
        </div>
      </div>
    </div>

  </div>
@endsection
