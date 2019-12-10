@extends('layouts.app')

@section('title')
  Service History by Position
@endsection

@section('body-class', 'service_by_position')

@section('extra_css')
  <style>
    @media print {
      .custom-col-print-12 {
        font-size: 11px !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
        width: 100% !important;
      }
      body, .printfull {margin: 0 !important; padding: 0 !important; width: 100% !important; font-size: 11px !important;}
    }
  </style>
@endsection


@section('content')
  @if($user->hasRole('Admin'))
  <div class="container-fluid d-print-none">
    <div class="row" style="outline: 1px solid darkgrey;">
      <div class="col-12 col-md-8 offset-md-2">
        <form action="/reports/byposition" method="post" class="form-inline float-right">
          @csrf
          <div class="form-group row mr-2">
            <label for="g">Show </label>
            <select name="g" id="g" class="form-control-sm mx-1" autofocus>
              <option value="M"{{ request()->input('g', $gender) == 'M' ? ' selected' : ''}}>Men</option>
              <option value="W"{{ request()->input('g', $gender) == 'W' ? ' selected' : ''}}>Women</option>
            </select>
          </div>
          <div class="float-right d-inline">
            <button type="submit" class="btn btn-primary btn-sm mr-1">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <br><br>
  </div>
  @endif

  <div class="container">
    <div class="row">
      <div class="col-12 custom-col-print-12 printfull">
        <h2 class="text-center"><strong>{{ config('site.community_acronym') }} Service History By Position</strong></h2>
        <table class="table table-hover custom-col-print-12 printfull">
          <thead>
          <th width="35%">Position</th>
          <th width="64%">Service History</th>
          </thead>
          <tbody>
          @foreach($assignments as $role => $service)
            <tr>
              <td style="padding-left:2em">{{ $role }}</td>
              <td>
              @foreach($service as $assignment)
                {{ $assignment->user->name . ' on ' . $assignment->weekend_full_name }}<br>
              @endforeach
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <p class="d-print-block">Generated on: {{ \Illuminate\Support\Carbon::now() }}</p>
  </div>
@endsection
