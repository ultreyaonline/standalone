@extends('layouts.app', ['robots_rules'=>'noindex'])
@section('title', 'Prayer Wheel Invitation to all ' . config('site.community_acronym') . ' Community Members')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <div class="card">
          <form class="form-horizontal" role="form" method="POST" action="{{ action('PrayerWheelNotificationsController@emailEntireCommunity', $wheel->id) }}">
            @csrf
            <div class="card-header"><strong>Prayer Wheel Invitation to the Community</strong></div>
            <div class="card-header text-danger"><strong>NOTE: This will go to EVERYONE as soon as you click Send!</strong></div>
            <div class="card-body">

              <div class="form-group row">
                <label class="col-md-3 control-label text-lg-right">From</label>
                <div class="col-md-8">
                  {{ '[' . config('site.community_acronym') . '] ' . Auth::user()->name }} &lt;{{ config('site.email_general') }}&gt;
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 control-label text-lg-right">Reply To</label>
                <div class="col-md-8">
                  {{ Auth::user()->name }} &lt;{!! str_replace('@', '<span style="display:none">***</span>@', Auth::user()->email) !!}&gt;
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 control-label col-form-label text-lg-right" for="notice_type">Email Notice Type</label>
                <div class="col-md-8">
                  <select name="notice_type" id="notice_type" class="form-control">
                    @foreach ($notice_types as $key=>$val)
                      <option value="{{ $key }}"{{ $key == 'prayerwheel' ? ' selected' : '' }}>{{ $val }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="subject" class="col-md-3 control-label text-lg-right">Subject</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="subject" id="subject" value="{{ old('subject') ?: '[' . config('site.community_acronym') . ' Prayer Wheel] ' . $wheel->weekend->weekend_full_name }}" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="message" class="col-md-3 control-label text-lg-right">Message</label>
                <div class="col-md-8">
                  <textarea id="message" rows="10" class="form-control" name="message" required>{{ old('message') ?: $default_message }}</textarea>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-6 offset-md-3">
                  <button type="submit" class="btn btn-primary">
                    Send
                  </button>
                </div>
              </div>

            </div>
            <div class="card-header">
              <strong>Segmenting:</strong> (Optional) Limit which segments of the community should receive your message:
            </div>

            <div class="card-body">

            <div class="row">
              <div class="col-lg-4 offset-lg-3">

                <div class="form-group row">
                  <div class="col-md-11"><h5>Who Should Receive This Message?</h5>
                    <div class="radio">
                      <label for="mail_to_both" class="col-form-label">
                        <input type="radio" name="mail_to_gender" id="mail_to_both" value="B" checked="checked"> Both Men and Women
                      </label>
                    </div>
                    <div class="radio">
                      <label for="mail_to_men">
                        <input type="radio" name="mail_to_gender" id="mail_to_men" value="M" > Men Only
                      </label>
                    </div>
                    <div class="radio">
                      <label for="mail_to_women">
                        <input type="radio" name="mail_to_gender" id="mail_to_women" value="W" > Women Only
                      </label>
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-lg-4">

                <div class="form-group row">
                  <div class="col-md-11"><h5>Which Communities?</h5>
                    <div class="checkbox">
                      <label for="community_local" class="col-form-label">
                        <input type="checkbox" name="community_local" id="community_local" value="local" checked="checked"> {{ config('site.community_acronym') }} members
                      </label>
                    </div>
                    <div class="checkbox">
                      <label for="community_other">
                        <input type="checkbox" name="community_other" id="community_other" value="other" checked="checked"> all non-{{ config('site.community_acronym') }} members
                      </label>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <hr>
              <div class="form-group row">
                <div class="col-lg-9 offset-lg-3">
                  <div class="checkbox">
                    <label for="contains_surprises" class="col-form-label">
                      <input type="checkbox" name="contains_surprises" id="contains_surprises" value="yes" checked> WARNING: Contains "surprises" like serenade/palanca/etc (we'll skip not-yet-attended spouses in this case)
                    </label>
                  </div>
                </div>
              </div>


            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
