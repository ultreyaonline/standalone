@extends('layouts.app')

@section('title')
  Prayer Wheel{{ !empty($weekend->id) ? ' for ' . $weekend->weekend_full_name : '' }}
@endsection

@section('body-class', 'prayerwheel')

@section('extra_css')
  <style>
    /*.flex {display:flex} .flex-child {flex:1 1 auto;}*/
    @media print {

      .container {
        max-width: 1024px !important;
      }
      @page {
        size: letter;
        margin: 0mm;
      }

      html, body {
        width: 1024px;
      }

      body {
        margin: 0 auto;
        line-height: 0.8em;
        word-spacing:1px;
        letter-spacing:0.2px;
        font: 14px "Times New Roman", Times, serif;
        background:white;
        color:black;
        width: 100%;
        float: none;
      }

      /* avoid page-breaks inside a listingContainer*/
      .listingContainer{
        page-break-inside: avoid;
      }

      h1 {
        font: 28px "Times New Roman", Times, serif;
      }

      h2 {
        font: 24px "Times New Roman", Times, serif;
      }

      h3 {
        font: 20px "Times New Roman", Times, serif;
      }

      .card-header .large {margin: 0}
      /* Improve colour contrast of links */
      a:link, a:visited {
        color: #781351
      }

      /* URL */
      a:link, a:visited {
        background: transparent;
        color:#333;
        text-decoration:none;
      }

      a[href]:after {
        content: "" !important;
      }

      a[href^="http://"] {
        color:#000;
      }

      strong.normalprint {font-weight: normal}

      #header {
        height:75px;
        font-size: 24pt;
        color:black
      }
    }
    .strong {font-weight: bold;}
    .pw-selector {max-width: 150px}
  </style>
@endsection

@section('content')
  <div class="container">
    @if($member->can('send prayer wheel invites') || $canSeePrayerWheelNames)
    <div class="row d-print-none">
      <div class="col-12 mb-2">
      <div class="card">
      <div class="card-header alert-success float-right">
        <strong>
        @can('send prayer wheel invites')
          <a href="/prayerwheel/{{ $wheel->id }}/invite"><button class="btn btn-outline-secondary"><i class="fa fa-bullhorn"></i> Email Invitations</button></a>
        @endcan
            &nbsp; {{ $countPositionsRemaining }} open positions remaining
          <a class="float-right" href="javascript:print()"><button class="btn btn-outline-secondary"><i class="fa fa-print"></i> Print</button></a>
          </strong>
        @if($canSeePrayerWheelNames && $csvData)
          <a class="float-right" download="{{ 'Prayer Wheel ' . $wheel->weekend->shortname . $wheel->weekend->weekend_MF . ' as of ' . date('Y-m-d') }}.csv" href="{{ $csvData->toInlineCsv(['Position', 'Index', 'Day', 'Hour', 'Hour_To', 'Names']) }}"><button class="btn btn-outline-secondary"><i class="fa fa-file-text-o"></i> CSV</button></a>
        @endif
      </div>
      </div>
      </div>
    </div>
    @endif

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header alert-info">
            <span class="d-print-none d-inline">
              <strong>Please choose an hour to pray for {{ $weekend->weekend_full_name }}</strong>
            </span>
            <span class="d-print-inline d-none">
              <strong>Prayer Wheel for {{ $weekend->weekend_full_name }}</strong>
            </span>
            &nbsp; &nbsp; &nbsp;
            Dates: <strong>{{ $weekend->short_date_range }}</strong>

            <div class="float-right d-print-none">@include('prayerwheel.pulldownselectorbutton', ['route'=>'/prayerwheel'])</div>
          </div>
          <div class="card-body">

            <div class="row">
              @foreach($hours->groupBy('day') as $day)
              <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="card">
                  <div class="card-header alert-info">
                  <h4 class="text-center">{{ $weekend->start_date->addDays($loop->index)->format('D M d') }} </h4>
                  </div>
                  @include('prayerwheel._table', ['spots' => $day])
                </div>
              </div>
              @endforeach
            </div>


          </div>
          <div class="card-footer d-print-inline d-none float-right">Printed on {{ Illuminate\Support\Carbon::today()->toFormattedDateString() }}</div>
        </div>

      </div>


    </div>
  </div>
@endsection
