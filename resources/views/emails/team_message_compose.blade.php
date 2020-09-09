@extends('layouts.app', ['robots_rules'=>'noindex'])
@section('title', 'Compose message to team')

@section('extra_css')
  <style>LABEL { font-size: 1.08rem; font-weight: bold }</style>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <div class="card">
          <div class="card-header"><strong>Weekend Information</strong></div>
          <div class="card-body">
            <p><strong>Weekend:</strong> <strong><a href="/weekend/{{ $weekend->id }}">{{ $weekend->weekend_full_name }}</a></strong></p>
            <p><strong>Theme:</strong> {{ $weekend->weekend_theme }}</p>
            <p><strong>Verse:</strong> {{ $weekend->weekend_verse_text }}, {{ $weekend->weekend_verse_reference }}</p>
            <p>
              @if($weekend->has_ended)
              <strong>People={{ $weekend->totalteamandcandidates }}</strong>;
              Candidates={{ $weekend->candidates->count() }};
              @else
              <strong>People={{ $weekend->totalteam }}</strong>;
              @endif
              Team={{ $weekend->totalteam ?: $weekend->team_all_visibility->unique('memberID')->count() }}
            </p>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><strong>Message to Team</strong></div>
          <div class="card-body">

            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\CommunicationController@emailTeamMembers', $weekend) }}" enctype="multipart/form-data">
              @csrf

              <div class="form-group row">
                <label class="col-md-3 control-label text-md-right">From</label>
                <div class="col-md-8">
                  {{ '[' . config('site.community_acronym') . '] ' . Auth::user()->name }} &lt;{{ config('site.email_general') }}&gt;
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 control-label text-md-right">Reply To</label>
                <div class="col-md-8">
                  {{ Auth::user()->name }} &lt;{!! str_replace('@', '<span style="display:none">***</span>@', Auth::user()->email) !!}&gt;
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 control-label col-form-label text-md-right" for="section">Section</label>
                <div class="col-sm-8 col-lg-4">
                  <select name="section" id="section" class="form-control" style="font-weight: bolder">
                    <option value="0"{{ 0 === old('section') ? ' selected' : '' }}>Entire Team</option>
                    <option value="-1"{{ -1 === old('section') ? ' selected' : '' }}>Section Heads</option>
                    <option value="-2"{{ -2 === old('section') ? ' selected' : '' }}>Section Heads AND Rollistas</option>

                    @foreach ($sections as $section)
                      <option value="{{ $section->id }}"{{ $section->id === old('section') ? ' selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="subject" class="col-md-3 control-label col-form-label text-md-right">Subject</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="subject" id="subject" value="{{ old('subject') }}" required placeholder=" {{ '['. $weekend->weekend_full_name .'] ' }}">
                </div>
              </div>

              <div class="form-group row">
                <label for="message" class="col-md-3 control-label col-form-label text-md-right">Message</label>
                <div class="col-md-8">
                  <textarea id="message" rows="10" class="form-control" name="message" required>{{ old('message') }}</textarea>
                </div>
              </div>

            @if($weekend->head_cha->contains(auth()->user()->id))
              <div class="row">
                <div class="col-md-9 offset-md-3">
                  <div class="checkbox">
                    <label for="exclude_rector" class="col-form-label">
                      <input type="checkbox" name="exclude_rector" id="exclude_rector" value="1"> Exclude Rector? (for example, to send a message about a Rector Gift)
                    </label>
                  </div>
                </div>
              </div>
            @endif


              @if($weekend->has_ended)
                <div class="row">
                  <div class="col-md-9 offset-md-3">
                    <div class="checkbox">
                      <label for="include_candidates" class="col-form-label">
                        <input type="checkbox" name="include_candidates" id="include_candidates" value="1"> Include Candidates? (Caution: their spouse may not have attended yet. No spoilers!)
                      </label>
                    </div>
                  </div>
                </div>
              @endif

              <hr>

              <div class="form-group row">
                <label for="" class="col-md-5 col-lg-3 offset-lg-3 control-label">Optional Attachments? (PDFs&nbsp;only)</label>
                <div class="col-md-7 col-lg-6">
                  <input id="attachment" type="file" class="form-control" name="attachment" aria-label="first attachment">
                  <input id="attachment2" type="file" class="form-control" name="attachment2" aria-label="second attachment">
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-6 offset-md-3">
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
