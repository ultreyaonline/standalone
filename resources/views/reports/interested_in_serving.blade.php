@extends('layouts.app')

@section('title')
  Interested in Serving
@endsection

@section('body-class', 'interested_in_serving')

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
  <div class="container-fluid d-print-none">
    <div class="row" style="outline: 1px solid darkgrey;">
      <div class="col-12 col-md-8 offset-md-2">
        <form action="/reports/interested_in_serving" method="post" class="form-inline">
          @csrf

          <div class="form-group row mr-2">
            <label for="mw">Show </label>
            <select name="mw" id="mw" class="form-control-sm mx-1" autofocus>
              <option value="M"{{ request()->input('mw', $gender) == 'M' ? ' selected' : ''}}>Men</option>
              <option value="W"{{ request()->input('mw', $gender) == 'W' ? ' selected' : ''}}>Women</option>
            </select>
            who were a
            [ <label for="candidate">Candidate</label>
            <input id="candidate" type="checkbox" name="c[]" value="yes"{{ $candidate_filter ? ' checked' : '' }} class="form-control ml-1">
            ] or
            [ <label for="served">Served</label>
            <input id="served" type="checkbox" name="s[]" value="yes"{{ $served_filter ? ' checked' : '' }} class="form-control ml-1">
            ]
            <label for="limit">on any of the last </label>
            <select name="l" class="form-control-sm mx-1" id="limit">
              @foreach(range(0,20) as $l)
                <option value="{{ $l }}"{{ request()->input('l', $recent_filter) == $l ? ' selected' : ''}}>{{ $l }}</option>
              @endforeach
            </select>
            weekends.
          </div>
          <div class="float-right d-inline">
          <button type="submit" class="btn btn-primary btn-sm mr-1">Submit</button>
          <button type="submit" name="csv" class="btn btn-secondary btn-sm" title="Download CSV for Excel">Download</button>
          </div>
        </form>

      </div>
    </div>
    <br><br>
  </div>

  <div class="container-fluid ml-2">
    <div class="row">
      <div class="col-12 custom-col-print-12 printfull">
        <h2 class="text-center"><strong>Members Interested in Serving</strong></h2>
        <table class="table table-hover table-responsive custom-col-print-12 printfull">
          <thead>
          <th width="30%">Name</th>
          <th width="25%">Phone</th>
          <th width="54%">Served&nbsp;on&nbsp;Weekends:</th>
          </thead>
          <tbody>
          @foreach($members as $member)
            <tr>
              <td style="font-weight: bold">
                {{ $member->name }}{{ $member->weekend ? ', ' . $member->weekend : '' }}
                {{ $member->community !== config('site.community_acronym') ? ' (' . $member->community . ')' : ''}}</td>
              <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($member->phone) !!}</td>
              <td style="overflow-wrap: break-word">{{ $member->email }}</td>
            </tr>
            <tr>
              <td colspan="2" style="border-top: 0px; padding-top: 0" class="small">@if($member->skills) Skills: @endif {{ $member->skills }}</td>
              <td style="border-top: 0px; padding-top: 0">
                @foreach($member->serving_history as $h)
                @if($h['id'])
                    <a href="/weekend/{{$h['id']}}" target="_blank">{{$h['name']}}</a>: {{$h['position']}}
                @else
                    {{$h['name']}}: {{$h['position']}}
                @endif
                  @if($loop->remaining)<br>@endif
                @endforeach
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <p class="d-print-block">Printed on: {{ \Illuminate\Support\Carbon::now() }}</p>
  </div>
@endsection
