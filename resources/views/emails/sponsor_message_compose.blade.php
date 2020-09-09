@extends('layouts.app', ['robots_rules'=>'noindex'])
@section('title', 'Compose message to Sponsors')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header"><strong>Weekend Information</strong></div>
          <div class="card-body">
            <p><strong>Weekend:</strong> <strong><a href="/weekend/{{ $weekend->id }}">{{ $weekend->weekend_full_name }}</a></strong></p>
            <p><strong>Theme:</strong> {{ $weekend->weekend_theme }}</p>
            <p><strong>Candidates={{ $weekend->candidates->count() }}</strong><br>
            <strong>Arrival Time: {{ $weekend->candidate_arrival_time }}</strong><br>
            <strong>Sendoff Starts At: {{ $weekend->sendoff_start_time }}</strong>
            </p>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><strong>Message to Sponsors</strong></div>
          <div class="card-body">

            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\CandidateEmailsController@sendEmailToSponsorsOfWeekend', $weekend) }}" enctype="multipart/form-data">
              @csrf

              <div class="form-group row">
                <label class="col-md-3 control-label">From</label>
                <div class="col-md-8">
                  {{ '[' . config('site.community_acronym') . '] ' . Auth::user()->name }} &lt;{{ config('site.email_general') }}&gt;
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 control-label">Reply To</label>
                <div class="col-md-8">
                  {{ Auth::user()->name }} &lt;{!! str_replace('@', '<span style="display:none">***</span>@', Auth::user()->email) !!}&gt;
                </div>
              </div>

              <div class="form-group row">
                <label for="subject" class="col-md-3 control-label">Subject</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="subject" id="subject" value="{{ old('subject') }}" required placeholder=" {{ '['. $weekend->weekend_full_name .'] ' }}">
                </div>
              </div>

              <div class="form-group row">
                <label for="message" class="col-md-3 control-label">Message</label>
                <div class="col-md-8">
                  <textarea id="message" rows="10" class="form-control" name="message" required>{{ old('message') }}</textarea>
                </div>
              </div>

              <hr>

              <div class="form-group row">
                <label for="attachment" class="col-md-4 control-label">Optional Attachment?<br>(PDFs only)</label>
                <div class="col-md-6">
                  <input id="attachment" type="file" class="form-control" name="attachment">
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-6 offset-md-6">
                  <button type="submit" class="btn btn-primary">
                    Send
                  </button>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
