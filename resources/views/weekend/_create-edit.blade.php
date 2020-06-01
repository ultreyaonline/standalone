<div class="form-group row{{ $errors->has('weekend_full_name') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="weekend_full_name">Weekend Name</label>

  <div class="col-md-7">
    <input type="text" class="form-control col-10" name="weekend_full_name" id="weekend_full_name" maxlength="80" value="{{ old('weekend_full_name') ?: $weekend->weekend_full_name }}" placeholder="ie: {{ config('site.community_acronym') }} Men's #1" required autofocus>
    <small>Normal format is: ZZZTD Women's #17<br>(Community abbreviation, gender, # number)</small>
    @if ($errors->has('weekend_full_name'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_full_name') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('weekend_number') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="weekend_number">Weekend Number</label>

  <div class="col-md-7">
    <input type="number" class="form-control col-8" name="weekend_number" id="weekend_number" maxlength="6" value="{{ old('weekend_number') ?: $weekend->weekend_number }}" placeholder="ie: 17" required>
    <small>(Should be just the number.)</small>
    @if ($errors->has('weekend_number'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_number') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('weekend_MF') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="weekend_MF">Men's/Women's</label>
  <div class="col-md-4">
    @include('members._gender_selector', ['fieldname' => 'weekend_MF', 'current' => old('weekend_MF') ?: $weekend->weekend_MF, 'mode' => 'plural'])
    @if ($errors->has('gender'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_MF') }}</strong> </span>
    @endif
  </div>
</div>
{{--@TODO: Pulldown choice, from table? --}}
<div class="form-group row{{ $errors->has('tresdias_community') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="tresdias_community">Tres Dias Community</label>

  <div class="col-md-7">
    <input type="text" class="form-control col-6" name="tresdias_community" id="tresdias_community" maxlength="20" value="{{ old('tresdias_community') ?: $weekend->tresdias_community ?: config('site.community_acronym') }}" placeholder="ie: {{ config('site.community_acronym') }}" required>
    <span class="small">(This is normally your own Community name.<br>Only change it if you are hosting a weekend for another Community.)</span>
  @if ($errors->has('tresdias_community'))
      <span class="form-text"> <strong>{{ $errors->first('tresdias_community') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row{{ $errors->has('start_date') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_start_date">Start Date/Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="start_date">
        <div class="input-group-prepend" data-target="#start_date" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="start_date" id="input_start_date" data-target="#start_date" value="{{ old('start_date') ?: $weekend->start_date }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('end_date') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_end_date">End Date/Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="end_date">
        <div class="input-group-prepend" data-target="#end_date" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="end_date" id="input_end_date" data-target="#end_date" value="{{ old('end_date') ?: $weekend->end_date }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('candidate_arrival_time') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_candidate_arrival_time">Candidate Arrival Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="candidate_arrival_time">
        <div class="input-group-prepend" data-target="#candidate_arrival_time" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="candidate_arrival_time" id="input_candidate_arrival_time" data-target="#candidate_arrival_time" value="{{ old('candidate_arrival_time') ?: $weekend->candidate_arrival_time }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
      <span class="small">(will be shown as this time plus 30 minutes<br>ie: 6:00 would mean 6:00-6:30pm )</span>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('sendoff_start_time') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_sendoff_start_time">Sendoff Start Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="sendoff_start_time">
        <div class="input-group-prepend" data-target="#sendoff_start_time" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="sendoff_start_time" id="input_sendoff_start_time" data-target="#sendoff_start_time" value="{{ old('sendoff_start_time') ?: $weekend->sendoff_start_time }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('serenade_arrival_time') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_serenade_arrival_time">Serenade Arrival Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="serenade_arrival_time">
        <div class="input-group-prepend" data-target="#serenade_arrival_time" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="serenade_arrival_time" id="input_serenade_arrival_time" data-target="#serenade_arrival_time" value="{{ old('serenade_arrival_time') ?: $weekend->serenade_arrival_time }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('serenade_practice_location') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="serenade_practice_location">Serenade Practice Location</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="serenade_practice_location" id="serenade_practice_location" maxlength="255" value="{{ old('serenade_practice_location') ?: $weekend->serenade_practice_location ?: '' }}" placeholder="ie: Below Dining Hall">
    @if ($errors->has('serenade_practice_location'))
      <span class="form-text"> <strong>{{ $errors->first('serenade_practice_location') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row{{ $errors->has('closing_arrival_time') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_closing_arrival_time">Closing Arrival Time</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="closing_arrival_time">
        <div class="input-group-prepend" data-target="#closing_arrival_time" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="closing_arrival_time" id="input_closing_arrival_time" data-target="#closing_arrival_time" value="{{ old('closing_arrival_time') ?: $weekend->closing_arrival_time }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
      <span class="small">(will be displayed to the community)</span>
    </div>
  </div>
</div>

<div class="form-group row{{ $errors->has('closing_scheduled_start_time') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="input_closing_scheduled_start_time">Closing "Start Time"</label>
  <div class="col-md-6">

    <div class="form-group row ml-0">
      <div class="input-group date" data-target-input="nearest" id="closing_scheduled_start_time">
        <div class="input-group-prepend" data-target="#closing_scheduled_start_time" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
        <input type="text" name="closing_scheduled_start_time" id="input_closing_scheduled_start_time" data-target="#closing_scheduled_start_time" value="{{ old('closing_scheduled_start_time') ?: $weekend->closing_scheduled_start_time }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" />
      </div>
      <span class="small">(scheduled)</span>
    </div>
  </div>
</div>

<hr>

<div class="form-group row{{ $errors->has('sendoff_location') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="sendoff_location">Sendoff Location</label>

  <div class="col-md-7">
    <input type="text" class="form-control" name="sendoff_location" id="sendoff_location" maxlength="100" value="{{ old('sendoff_location') ?: $weekend->sendoff_location ?: '' }}" placeholder="ie: Camp Name">
    @if ($errors->has('sendoff_location'))
      <span class="form-text"> <strong>{{ $errors->first('sendoff_location') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('weekend_location') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="weekend_location">Weekend Location</label>

  <div class="col-md-7">
    <input type="text" class="form-control" name="weekend_location" id="weekend_location" maxlength="100" value="{{ old('weekend_location') ?: $weekend->weekend_location ?: '' }}" placeholder="ie: Camp Name">
    @if ($errors->has('weekend_location'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_location') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row{{ $errors->has('candidate_cost') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="candidate_cost">Candidate Cost</label>

  <div class="col-md-3 input-group">
    <div class="input-group-prepend">
      <div class="input-group-text">$</div>
    </div>
    <input type="text" class="form-control" name="candidate_cost" id="candidate_cost" value="{{ old('candidate_cost') ?: $weekend->candidate_cost ?: '250' }}" placeholder="ie: 250" required>
    @if ($errors->has('candidate_cost'))
      <span class="form-text"> <strong>{{ $errors->first('candidate_cost') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('team_fees') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="team_fees">Team Fee Cost</label>

  <div class="col-md-3 input-group">
    <div class="input-group-prepend">
      <div class="input-group-text">$</div>
    </div>
    <input type="text" class="form-control" name="team_fees" id="team_fees" value="{{ old('team_fees') ?: $weekend->team_fees ?: '250' }}" placeholder="ie: 250" required>
    @if ($errors->has('team_fees'))
      <span class="form-text"> <strong>{{ $errors->first('team_fees') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('maximum_candidates') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="maximum_candidates">Maximum Candidates</label>

  <div class="col-md-4">
    <input type="number" class="form-control" name="maximum_candidates" id="maximum_candidates" value="{{ old('maximum_candidates') ?: $weekend->maximum_candidates ?: '24' }}" placeholder="ie: 24">
    @if ($errors->has('maximum_candidates'))
      <span class="form-text"> <strong>{{ $errors->first('maximum_candidates') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row bg-danger py-3">
    <label class="col-md-4 col-form-label" for="rectorID">Rector/Rectora</label>
  <div class="col-md-6">
  @include('members._member_selector', ['fieldname' => 'rectorID', 'current' => old('rectorID') ?: $weekend->rectorID ?: 0])
  </div>
</div>

<div class="form-group row{{ $errors->has('weekend_theme') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="weekend_theme">Weekend Theme</label>

  <div class="col-md-8">
    <input type="text" class="form-control" name="weekend_theme" id="weekend_theme" maxlength="255" value="{{ old('weekend_theme') ?: $weekend->weekend_theme }}" placeholder="">
    @if ($errors->has('weekend_theme'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_theme') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row">
  <label class="col-md-4 col-form-label" for="weekend_verse_text">Weekend Verse (words) </label>
  <div class="col-md-8">
                  <textarea class="form-control" name="weekend_verse_text" id="weekend_verse_text"
                            rows="4"
                            placeholder="Jesus wept.">{{ old('weekend_verse_text') ?: $weekend->weekend_verse_text ?: '' }}</textarea>
    @if ($errors->has('weekend_verse_text'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_verse_text') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row{{ $errors->has('weekend_verse_reference') ? ' is-invalid' : '' }}">
  <label class="col-md-5 col-form-label" for="weekend_verse_reference">Weekend Verse Reference</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="weekend_verse_reference" id="weekend_verse_reference" maxlength="255" value="{{ old('weekend_verse_reference') ?: $weekend->weekend_verse_reference }}" placeholder="ie: Galatians 2:20">
    @if ($errors->has('weekend_verse_reference'))
      <span class="form-text"> <strong>{{ $errors->first('weekend_verse_reference') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row">
  <label class="col-md-3 col-form-label" for="team_meetings">Team Meetings Locations and&nbsp;Dates</label>
  <div class="col-md-9">
                  <textarea class="form-control" name="team_meetings" id="team_meetings"
                            rows="7"
                            placeholder="List the dates, times, locations of team meetings">{{ old('team_meetings') ?: $weekend->team_meetings ?: '' }}</textarea>
    @if ($errors->has('team_meetings'))
      <span class="form-text"> <strong>{{ $errors->first('team_meetings') }}</strong> </span>
    @endif
  </div>
</div>

<hr>
<div class="form-group row{{ $errors->has('table_palanca_guideline_text') ? ' is-invalid' : '' }}">
  <label class="col-12 col-form-label" for="table_palanca_guideline_text">Table Palanca Guidelines (Numbers)</label>

  <div class="col-12">
    <input type="text" class="form-control" name="table_palanca_guideline_text" id="table_palanca_guideline_text" maxlength="255" value="{{ old('table_palanca_guideline_text') ?: $weekend->table_palanca_guideline_text }}">
    @if ($errors->has('table_palanca_guideline_text'))
      <span class="form-text"> <strong>{{ $errors->first('table_palanca_guideline_text') }}</strong> </span>
    @endif
  </div>
  <p class="small mx-3">The Rector should enter some guidance instructions here for the community to know how much Table Palanca to send when preparing Palanca for the Rollo Room. This is displayed on the Weekend page. If left blank we will display a best-guess automatically.</p>
</div>

<hr>
<div class="form-group row{{ $errors->has('visibility_flag') ? ' is-invalid' : '' }} bg-info row p-3">
  <label class="col-md-4 col-form-label" for="visibility_flag">Status. Visible to: </label>
  <div class="col-md-8 mb-2">
    <select class="form-control" name="visibility_flag" id="visibility_flag">
@if(auth()->user()->can('edit weekends'))
      <option value="0"{{ $weekend->visibility_flag === 0 ? 'selected' : '' }}>Admin Only</option>
      <option value="1"{{ $weekend->visibility_flag === 1 ? 'selected' : '' }}>Calendar Only</option>
@endif
      <option value="2"{{ $weekend->visibility_flag === 2 ? 'selected' : '' }}>Theme NOT visible</option>
      <option value="3"{{ $weekend->visibility_flag === 3 ? 'selected' : '' }}>Theme visible, but no Team details</option>
      <option value="4"{{ $weekend->visibility_flag === 4 ? 'selected' : '' }}>Head Cha can see confirmed team roster</option>
      <option value="5"{{ $weekend->visibility_flag === 5 ? 'selected' : '' }}>Section Heads can see team too</option>
      <option value="6"{{ $weekend->visibility_flag === 6 ? 'selected' : '' }}>Everyone can see all confirmed details</option>
    </select>
  </div>
</div>

<hr>
<div class="form-group row{{ $errors->has('sendoff_couple_name') ? ' is-invalid' : '' }}">
  <label class="col-md-4 col-form-label" for="sendoff_couple_name">Sendoff Couple</label>

  <div class="col-md-7">
    <input type="text" class="form-control" name="sendoff_couple_name" id="sendoff_couple_name" maxlength="80" value="{{ old('sendoff_couple_name') ?: $weekend->sendoff_couple_name }}" placeholder="">
    @if ($errors->has('sendoff_couple_name'))
      <span class="form-text"> <strong>{{ $errors->first('sendoff_couple_name') }}</strong> </span>
    @endif
    <span class="small">(Optional)</span>
  </div>
</div>


<div class="form-group row">
  <label class="col-md-4 col-form-label" for="emergency_poc_id">Emergency Contact</label>
  <div class="col-md-6">
    @include('members._member_selector', ['fieldname' => 'emergency_poc_id', 'current' => old('emergency_poc_id') ?: $weekend->emergency_poc_id ?: 0])
    <span class="small">(ie: Head Storeroom?)</span>
  </div>
</div>

<hr>
<p class="small mx-1">The Rector and Administrator can specify some document-sharing URLs for weekend-specific files. Be sure to set proper sharing controls on those files/folders when obtaining the share links that you post here.</p>

<div class="form-group row{{ ($errors->has('share_1_doc_label') || $errors->has('share_1_doc_url')) ? ' is-invalid' : '' }}">
    <label class="col-2 col-form-label" for="share_1_doc_label">Name</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_1_doc_label" id="share_1_doc_label" maxlength="255" value="{{ old('share_1_doc_label') ?: $weekend->share_1_doc_label }}" placeholder="ie: shared folder for public weekend files">
        @if ($errors->has('share_1_doc_label'))
            <span class="form-text"> <strong>{{ $errors->first('share_1_doc_label') }}</strong> </span>
        @endif
    </div>
    <label class="col-2 col-form-label" for="share_1_doc_url">Link URL</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_1_doc_url" id="share_1_doc_url" maxlength="255" value="{{ old('share_1_doc_url') ?: $weekend->share_1_doc_url }}" placeholder="ie: https://drive.google.com/hashcode12345">
        @if ($errors->has('share_1_doc_url'))
            <span class="form-text"> <strong>{{ $errors->first('share_1_doc_url') }}</strong> </span>
        @endif
    </div>
</div>

<div class="form-group row{{ ($errors->has('share_2_doc_label') || $errors->has('share_2_doc_url')) ? ' is-invalid' : '' }}">
    <label class="col-2 col-form-label" for="share_2_doc_label">Name</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_2_doc_label" id="share_2_doc_label" maxlength="255" value="{{ old('share_2_doc_label') ?: $weekend->share_2_doc_label }}" placeholder="ie: rector and head cha shared files">
        @if ($errors->has('share_2_doc_label'))
            <span class="form-text"> <strong>{{ $errors->first('share_2_doc_label') }}</strong> </span>
        @endif
    </div>
    <label class="col-2 col-form-label" for="share_2_doc_url">Link URL</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_2_doc_url" id="share_2_doc_url" maxlength="255" value="{{ old('share_2_doc_url') ?: $weekend->share_2_doc_url }}" placeholder="ie: https://drive.google.com/hashcode4567">
        @if ($errors->has('share_2_doc_url'))
            <span class="form-text"> <strong>{{ $errors->first('share_2_doc_url') }}</strong> </span>
        @endif
    </div>
</div>

<div class="form-group row{{ ($errors->has('share_3_doc_label') || $errors->has('share_3_doc_url')) ? ' is-invalid' : '' }}">
    <label class="col-2 col-form-label" for="share_3_doc_label">Name</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_3_doc_label" id="share_3_doc_label" maxlength="255" value="{{ old('share_3_doc_label') ?: $weekend->share_3_doc_label }}" placeholder="ie: serenade songbook file link">
        @if ($errors->has('share_3_doc_label'))
            <span class="form-text"> <strong>{{ $errors->first('share_3_doc_label') }}</strong> </span>
        @endif
    </div>
    <label class="col-2 col-form-label" for="share_3_doc_url">Link URL</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_3_doc_url" id="share_3_doc_url" maxlength="255" value="{{ old('share_3_doc_url') ?: $weekend->share_3_doc_url }}" placeholder="ie: https://drive.google.com/hashcode6789">
        @if ($errors->has('share_3_doc_url'))
            <span class="form-text"> <strong>{{ $errors->first('share_3_doc_url') }}</strong> </span>
        @endif
    </div>
</div>

<div class="form-group row{{ ($errors->has('share_4_doc_label') || $errors->has('share_4_doc_url')) ? ' is-invalid' : '' }}">
    <label class="col-2 col-form-label" for="share_4_doc_label">Name</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_4_doc_label" id="share_4_doc_label" maxlength="255" value="{{ old('share_4_doc_label') ?: $weekend->share_4_doc_label }}" placeholder="ie: shared folder for public weekend files">
        @if ($errors->has('share_4_doc_label'))
            <span class="form-text"> <strong>{{ $errors->first('share_4_doc_label') }}</strong> </span>
        @endif
    </div>
    <label class="col-2 col-form-label" for="share_4_doc_url">Link URL</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_4_doc_url" id="share_4_doc_url" maxlength="255" value="{{ old('share_4_doc_url') ?: $weekend->share_4_doc_url }}" placeholder="ie: https://drive.google.com/hashcode0303">
        @if ($errors->has('share_4_doc_url'))
            <span class="form-text"> <strong>{{ $errors->first('share_4_doc_url') }}</strong> </span>
        @endif
    </div>
</div>

<div class="form-group row{{ ($errors->has('share_5_doc_label') || $errors->has('share_5_doc_url')) ? ' is-invalid' : '' }}">
    <label class="col-2 col-form-label" for="share_5_doc_label">Name</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_5_doc_label" id="share_5_doc_label" maxlength="255" value="{{ old('share_5_doc_label') ?: $weekend->share_5_doc_label }}" placeholder="ie: shared folder for public weekend files">
        @if ($errors->has('share_5_doc_label'))
            <span class="form-text"> <strong>{{ $errors->first('share_5_doc_label') }}</strong> </span>
        @endif
    </div>
    <label class="col-2 col-form-label" for="share_5_doc_url">Link URL</label>
    <div class="col-10">
        <input type="text" class="form-control" name="share_5_doc_url" id="share_5_doc_url" maxlength="255" value="{{ old('share_5_doc_url') ?: $weekend->share_5_doc_url }}" placeholder="ie: https://drive.google.com/hashcode0005">
        @if ($errors->has('share_5_doc_url'))
            <span class="form-text"> <strong>{{ $errors->first('share_5_doc_url') }}</strong> </span>
        @endif
    </div>
</div>



<div class="form-group text-center">
  <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> {{ $submitButtonText }}</button>
</div>




@section('extra_css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
@endsection

@section('page-js')
{{-- https://tempusdominus.github.io/bootstrap-4/ --}}
{{-- http://momentjs.com/ --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>--}}
  <script src="/js/moment-with-locales.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
  <script>
    $(function () {
      $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
        format: 'YYYY-MM-DD HH:mm',
        disabledHours: [1, 2, 3, 4, 5, 6],
        sideBySide: true,
        allowInputToggle: true,
        useCurrent: false
      });
    });
  </script>
  <script>
    $('#start_date').datetimepicker(defaultDate, "{{ old('start_datetime') ?: $weekend->start_date }}");
    $('#end_date').datetimepicker(defaultDate, "{{ old('end_date') ?: $weekend->end_date }}");
    $("#start_date").on("dp.change", function (e) {
      $('#end_date').data("DateTimePicker").minDate(e.date);
    });
    $("#end_date").on("dp.change", function (e) {
      $('#start_date').data("DateTimePicker").maxDate(e.date);
    });
    $('#candidate_arrival_time').datetimepicker(defaultDate, "{{ old('candidate_arrival_time') ?: $weekend->candidate_arrival_time }}");
    $('#sendoff_start_time').datetimepicker(defaultDate, "{{ old('sendoff_start_time') ?: $weekend->sendoff_start_time }}");
    $('#serenade_arrival_time').datetimepicker(defaultDate, "{{ old('serenade_arrival_time') ?: $weekend->serenade_arrival_time }}");
    $('#closing_arrival_time').datetimepicker(defaultDate, "{{ old('closing_arrival_time') ?: $weekend->closing_arrival_time }}");
    $('#closing_scheduled_start_time').datetimepicker(defaultDate, "{{ old('closing_scheduled_start_time') ?: $weekend->closing_scheduled_start_time }}");
  </script>
@endsection
