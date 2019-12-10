@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Activity</h1>

        {!! $logItems->render() !!}

        <table class="">
          <thead>
          <tr>
            <th width="200px">Time</th>
            <th width="25%">Subject</th>
            <th width="40%">Description</th>
            <th>By</th>
          </tr>
          </thead>
          <tbody>

          @foreach($logItems as $logItem)
            <tr>
              <td>{{ $logItem->created_at }}</td>
              <td>{!! class_basename($logItem->subject_type) !!}
                @if($logItem->subject_id)
                ({{ $logItem->subject_id }})
                @endif
              </td>
              <td>
                <div title="{{ $logItem->changes() }}">{!! $logItem->description !!}</div>
              </td>
              <td>@if($logItem->causer)
                  {{ $logItem->causer_type !== 'App\User' ? $logItem->causer_type : '' }}
                  <a href="{!! action('MembersController@show', [$logItem->causer->id]) !!}">{{ $logItem->causer->name }}</a>
                  @endif
                @if($logItem->subject_type === 'App\FailedLoginAttempt')
                  <span class="small">{{ $logItem->changes()['attributes']['username'] ?? '' }}</span>
                @endif
                &nbsp;</td>
            </tr>
          @endforeach


          </tbody>
        </table>

        {!! $logItems->render() !!}
      </div>
    </div>
  </div>

@stop
