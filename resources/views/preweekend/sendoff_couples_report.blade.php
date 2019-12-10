@extends('layouts.app')

@section('title')
  Sendoff Couples History
@endsection

@section('body-class', 'sendoff_history')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <p class="text-center"><strong>Sendoff Couples History</strong></p>
        <table class="table table-hover table-sm small">
          <thead>
          {{--<th>#</th>--}}
          <th>Weekend</th>
          <th>Date</th>
          <th>Sendoff Couple</th>
          <th>Emergency Contact</th>
          </thead>
          <tbody>
          @foreach($weekends as $weekend)
            <tr>
              <td>{{ $weekend->short_name }} {{ $weekend->gender }}</td>
              <td>{{ $weekend->short_date_range }}</td>
              <td>{{ $weekend->sendoff_couple_name }}</td>
              <td>{{ $weekend->emergency_contact1 }}</td>
            </tr>
          @endforeach
          <tr></tr>
          </tbody>
        </table>

      </div>

    </div>
  </div>
  <p>Printed on: {{ \Illuminate\Support\Carbon::now() }}</p>
@endsection
