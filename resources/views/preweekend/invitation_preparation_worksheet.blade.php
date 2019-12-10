@extends('layouts.app')

@section('title')
  Invitation Preparation
@endsection

@section('body-class', 'invite_prep')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <p class="text-center"><strong>Invitation Preparation</strong></p>
        <table class="table">
          <tbody>
          @foreach($candidates as $c)
            <tr>
              <td>Mailing Address</td>
              <td>{{ $c->names }}<br>{!! $c->address_formatted !!}</td>
            </tr>
            <tr>
              <td>Greeting</td>
              <td>Dear {{ $c->names }}</td>
            </tr>
            <tr>
              <td>Your scheduled weekend dates</td>
              <td>
                @if($c->man)
                {!! $c->weekend_dates_text_m !!}
                @endif
                @if($c->man && $c->woman)
                  &amp;
                @endif
                @if($c->woman)
                {!! $c->weekend_dates_text_w !!}
                @endif

              </td>
            </tr>
            <tr style="border-bottom: 3px solid gray"><td colspan="2">&nbsp;</td></tr>
          @endforeach
          </tbody>
        </table>

      </div>

    </div>
  </div>
  <p class="d-print">Printed on: {{ \Illuminate\Support\Carbon::now() }}</p>
@endsection
