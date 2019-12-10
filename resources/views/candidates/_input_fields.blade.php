@section('extra_css')
  <style>input::placeholder {font-size: 0.6em;}</style>
@endsection

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-title">Candidate Info</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-title">Pre-Weekend Controls</div>
      <div class="card-body alert-success">

@if(config('site.preweekend_sponsor_confirmations_enabled'))
      <div class="col-md-9 offset-md-2">
      <div class="checkbox">
        <label for="sponsor_yes">
          <input type="checkbox" name="sponsor_confirmed_details" id="sponsor_yes" value="1" {{($candidate->sponsor_confirmed_details ?? old('sponsor_confirmed_details')) ? 'checked' : ''}}> Sponsor Confirmed Details?
        </label><p class="small" style="padding-left: 25px;">(manual override)</p>
      </div>
    </div>
@endif

    <div class="col-md-9 offset-md-2">
      <div class="checkbox">
        <label for="fees_paid">
          <input type="checkbox" name="fees_paid" id="fees_paid" value="1" {{($candidate->fees_paid ?? old('fees_paid')) ? 'checked' : ''}}{{ $user->can('record candidate fee payments') ? '' : ' disabled' }}> Fees Paid?
        </label>
      </div>
    </div>
@if(config('site.preweekend_does_physical_mailing'))
    <div class="col-md-9 offset-md-2">
      <div class="checkbox">
        <label for="ready_to_mail">
          <input type="checkbox" name="ready_to_mail" id="ready_to_mail" value="1" {{($candidate->ready_to_mail ?? old('ready_to_mail')) ? 'checked' : ''}}{{ $user->can('record candidate fee payments') ? '' : ' disabled' }}> Okay To Mail Invite?
        </label>
      </div>
    </div>

    <div class="col-md-9 offset-md-2">
      <div class="checkbox">
        <label for="invitation_mailed">
          <input type="checkbox" name="invitation_mailed" id="invitation_mailed" value="1" {{($candidate->invitation_mailed ?? old('invitation_mailed')) ? 'checked' : ''}}> Invitation Mailed?
        </label>
      </div>
    </div>
@endif
      </div>
    </div>

  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-title">Man</div>
      <div class="card-body">

        <div class="form-group row{{ $errors->has('m_first') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_first">First Name</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="m_first" id="m_first" maxlength="45" value="{{ old('m_first') ?: $candidate->m_first }}">
            @if ($errors->has('m_first'))
              <span class="form-text"> <strong>{{ $errors->first('m_first') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_last') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_last">Last Name</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="m_last" id="m_last" maxlength="45" value="{{ old('m_last') ?: $candidate->m_last }}">
            @if ($errors->has('m_last'))
              <span class="form-text"> <strong>{{ $errors->first('m_last') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_pronunciation') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_pronunciation">Pronunciation?</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="m_pronunciation" id="m_pronunciation" maxlength="191" value="{{ old('m_pronunciation') ?: $candidate->m_pronunciation }}">
            @if ($errors->has('m_pronunciation'))
              <span class="form-text"> <strong>{{ $errors->first('m_pronunciation') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_gender') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_gender">Gender (M/W)</label>
          <div class="col-md-3">
            <input type="text" class="form-control" name="m_gender1" id="m_gender" value="{{ old('m_gender') ?: $candidate->m_gender ?: 'M' }}" disabled>
            <input type="hidden" class="form-control" name="m_gender" id="m_gender1" value="{{ old('m_gender') ?: $candidate->m_gender ?: 'M' }}">
            @if ($errors->has('m_gender'))
              <span class="form-text"> <strong>{{ $errors->first('m_gender') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_age') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_age">Age</label>

          <div class="col-md-6">
            <input type="tel" class="form-control" name="m_age" id="m_age" maxlength="10" value="{{ old('m_age') ?: $candidate->m_age }}">
            @if ($errors->has('m_age'))
              <span class="form-text"> <strong>{{ $errors->first('m_age') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_email') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_email">E-Mail Address</label>

          <div class="col-md-7">
            <input type="email" class="form-control" name="m_email" id="m_email" maxlength="60" value="{{ old('m_email') ?: $candidate->m_email }}">
            @if ($errors->has('m_email'))
              <span class="form-text"> <strong>{{ $errors->first('m_email') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('m_username') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_username">Username</label>

          <div class="col-md-7">
            <input type="text" class="form-control" name="m_username" id="m_username" maxlength="60" value="{{ old('m_username') ?: $candidate->m_username }}" placeholder="Unique username; {{ config('site.members_username_default') === 'Email Address' ? 'usually the email address' : 'usually FirstnameLastname' }}">
            @if ($errors->has('m_username'))
              <span class="form-text"> <strong>{{ $errors->first('m_username') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('m_cellphone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_cellphone">@lang('locale.mobilephone')</label>

          <div class="col-md-6">
            <input type="tel" class="form-control" name="m_cellphone" id="m_cellphone" maxlength="20" value="{{ old('m_cellphone') ?: $candidate->m_cellphone }}">
            @if ($errors->has('m_cellphone'))
              <span class="form-text"> <strong>{{ $errors->first('m_cellphone') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('m_emergency_name') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_emergency_name">Alt. Contact</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="m_emergency_name" id="m_emergency_name" maxlength="191" value="{{ old('m_emergency_name') ?: $candidate->m_emergency_name }}" placeholder="alt contact name">
            @if ($errors->has('m_emergency_name'))
              <span class="form-text"> <strong>{{ $errors->first('m_emergency_name') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_emergency_phone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_emergency_phone">and Phone</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="m_emergency_phone" id="m_emergency_phone" maxlength="191" value="{{ old('m_emergency_phone') ?: $candidate->m_emergency_phone }}" placeholder="alt contact phone">
            @if ($errors->has('m_emergency_phone'))
              <span class="form-text"> <strong>{{ $errors->first('m_emergency_phone') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('m_sponsorID') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_sponsorID">Sponsor</label>

          <div class="col-md-6">
            @include('members._member_selector', ['fieldname' => 'm_sponsorID', 'current' => old('m_sponsorID') ?: $candidate->m_sponsorID])
            @if ($errors->has('m_sponsorID'))
              <span class="form-text"> <strong>{{ $errors->first('m_sponsorID') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="m_in_ministry">
              <input type="checkbox" name="m_vocational_minister" id="m_in_ministry" value="1" {{($candidate->m_vocational_minister ?? old('m_vocational_minister')) ? 'checked' : ''}}> In Full-Time Vocational Ministry?
            </label>
          </div>
        </div>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="m_is_married">
              <input type="checkbox" name="m_married" id="m_is_married" value="1" {{($candidate->m_married ?? old('m_married')) ? 'checked' : ''}}> Is Married?
            </label>
          </div>
        </div>

      </div>

      <div class="card-body alert-warning">

        <div class="form-group row{{ $errors->has('m_special_diet') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_special_diet">Special Diet?</label>
          <div class="col-md-8">
            <textarea rows="2" class="form-control" name="m_special_diet" id="m_special_diet">{{ old('m_special_diet') ?: $candidate->m_special_diet }}</textarea>
            @if ($errors->has('m_special_diet'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('m_special_diet') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_special_dorm') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_special_dorm">Dorm Needs</label>
          <div class="col-md-8">
            <textarea rows="2" class="form-control" name="m_special_dorm" id="m_special_dorm">{{ old('m_special_dorm') ?: $candidate->m_special_dorm }}</textarea>
            @if ($errors->has('m_special_dorm'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('m_special_dorm') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_special_prayer') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_special_prayer">Prayer Requests</label>
          <div class="col-md-8">
            <textarea rows="3" class="form-control" name="m_special_prayer" id="m_special_prayer">{{ old('m_special_prayer') ?: $candidate->m_special_prayer }}</textarea>
            @if ($errors->has('m_special_prayer'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('m_special_prayer') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_special_medications') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_special_medications">Timed Meds?</label>
          <div class="col-md-8">
            <input type="text" class="form-control" name="m_special_medications" id="m_special_medications" maxlength="191" value="{{ old('m_special_medications') ?: $candidate->m_special_medications }}">
            @if ($errors->has('m_special_medications'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('m_special_medications') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('m_special_notes') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_special_notes">Important Notes</label>
          <div class="col-md-8">
            <textarea rows="3" class="form-control" name="m_special_notes" id="m_special_notes">{{ old('m_special_notes') ?: $candidate->m_special_notes }}</textarea>
            <p class="small">Any notes for the Head Cha about special needs for the candidate?</p>
            @if ($errors->has('m_special_notes'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('m_special_notes') }}</strong> </span>
            @endif
          </div>
        </div>




        <div class="col-md-6 offset-md-3">
          <div class="checkbox">
            <label for="m_is_smoker">
              <input type="checkbox" name="m_smoker" id="m_is_smoker" value="1" {{($candidate->m_smoker ?? old('m_smoker')) ? 'checked' : ''}}> Smoker?
            </label>
          </div>
        </div>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="m_response_card">
              <input type="checkbox" name="m_response_card_returned" id="m_response_card" value="1" {{($candidate->m_response_card_returned ?? old('m_response_card_returned')) ? 'checked' : ''}}> Response Card Returned?
            </label>
          </div>
        </div>
      </div>

      <div class="card-body alert-info">
        <div class="form-group row{{ $errors->has('m_arrival_poc_person') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_arrival_poc_person">Driver to Camp</label>
          <div class="col-md-6">
            <input type="tel" class="form-control" name="m_arrival_poc_person" id="m_arrival_poc_person" maxlength="191" value="{{ old('m_arrival_poc_person') ?: $candidate->m_arrival_poc_person }}">
            @if ($errors->has('m_arrival_poc_person'))
              <span class="form-text"> <strong>{{ $errors->first('m_arrival_poc_person') }}</strong> </span>
            @endif
          </div>
        </div>
        <div class="form-group row{{ $errors->has('m_arrival_poc_phone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="m_arrival_poc_phone">Driver Cell#</label>
          <div class="col-md-6">
            <input type="tel" class="form-control" name="m_arrival_poc_phone" id="m_arrival_poc_phone" maxlength="191" value="{{ old('m_arrival_poc_phone') ?: $candidate->m_arrival_poc_phone }}">
            @if ($errors->has('m_arrival_poc_phone'))
              <span class="form-text"> <strong>{{ $errors->first('m_arrival_poc_phone') }}</strong> </span>
            @endif
          </div>
        </div>


      </div>
    </div>
  </div>



  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-title">Woman</div>
      <div class="card-body">

        <div class="form-group row{{ $errors->has('w_first') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_first">First Name</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="w_first" id="w_first" maxlength="45" value="{{ old('w_first') ?: $candidate->w_first }}">
            @if ($errors->has('w_first'))
              <span class="form-text"> <strong>{{ $errors->first('w_first') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_last') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_last">Last Name</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="w_last" id="w_last" maxlength="45" value="{{ old('w_last') ?: $candidate->w_last }}">
            @if ($errors->has('w_last'))
              <span class="form-text"> <strong>{{ $errors->first('w_last') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_pronunciation') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_pronunciation">Pronunciation?</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="w_pronunciation" id="w_pronunciation" maxlength="191" value="{{ old('w_pronunciation') ?: $candidate->w_pronunciation }}">
            @if ($errors->has('w_pronunciation'))
              <span class="form-text"> <strong>{{ $errors->first('w_pronunciation') }}</strong> </span>
            @endif
          </div>
        </div>


        <div class="form-group row{{ $errors->has('w_gender') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_gender">Gender (M/W)</label>
          <div class="col-md-6">
            <input type="text" class="form-control" name="w_gender1" id="w_gender" value="{{ old('w_gender') ?: $candidate->w_gender ?: 'W' }}" disabled>
            <input type="hidden" class="form-control" name="w_gender" id="w_gender1" value="{{ old('w_gender') ?: $candidate->w_gender ?: 'W' }}">
            @if ($errors->has('w_gender'))
              <span class="form-text"> <strong>{{ $errors->first('w_gender') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_age') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_age">Age</label>

          <div class="col-md-6">
            <input type="tel" class="form-control" name="w_age" id="w_age" maxlength="10" value="{{ old('w_age') ?: $candidate->w_age }}">
            @if ($errors->has('w_age'))
              <span class="form-text"> <strong>{{ $errors->first('w_age') }}</strong> </span>
            @endif
          </div>
        </div>


        <div class="form-group row{{ $errors->has('w_email') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_email">E-Mail Address</label>

          <div class="col-md-7">
            <input type="email" class="form-control" name="w_email" id="w_email" maxlength="60" value="{{ old('w_email') ?: $candidate->w_email }}">
            @if ($errors->has('w_email'))
              <span class="form-text"> <strong>{{ $errors->first('w_email') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('w_username') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_username">Username</label>

          <div class="col-md-7">
            <input type="text" class="form-control" name="w_username" id="w_username" maxlength="60" value="{{ old('w_username') ?: $candidate->w_username }}" placeholder="Unique username; {{ config('site.members_username_default') === 'Email Address' ? 'usually the email address' : 'usually FirstnameLastname' }}">
            @if ($errors->has('w_username'))
              <span class="form-text"> <strong>{{ $errors->first('w_username') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('w_cellphone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_cellphone">@lang('locale.mobilephone')</label>

          <div class="col-md-6">
            <input type="tel" class="form-control" name="w_cellphone" id="w_cellphone" maxlength="20" value="{{ old('w_cellphone') ?: $candidate->w_cellphone }}">
            @if ($errors->has('w_cellphone'))
              <span class="form-text"> <strong>{{ $errors->first('w_cellphone') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('w_emergency_name') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_emergency_name">Alt. Contact</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="w_emergency_name" id="w_emergency_name" maxlength="191" value="{{ old('w_emergency_name') ?: $candidate->w_emergency_name }}" placeholder="alt contact name">
            @if ($errors->has('w_emergency_name'))
              <span class="form-text"> <strong>{{ $errors->first('w_emergency_name') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_emergency_phone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_emergency_phone">and Phone</label>

          <div class="col-md-6">
            <input type="text" class="form-control" name="w_emergency_phone" id="w_emergency_phone" maxlength="191" value="{{ old('w_emergency_phone') ?: $candidate->w_emergency_phone }}" placeholder="alt contact phone">
            @if ($errors->has('w_emergency_phone'))
              <span class="form-text"> <strong>{{ $errors->first('w_emergency_phone') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="form-group row{{ $errors->has('w_sponsorID') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_sponsorID">Sponsor</label>

          <div class="col-md-6">
            @include('members._member_selector', ['fieldname' => 'w_sponsorID', 'current' => old('w_sponsorID') ?: $candidate->w_sponsorID])
            @if ($errors->has('w_sponsorID'))
              <span class="form-text"> <strong>{{ $errors->first('w_sponsorID') }}</strong> </span>
            @endif
          </div>
        </div>

        <hr>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="w_in_ministry">
              <input type="checkbox" name="w_vocational_minister" id="w_in_ministry" value="1" {{($candidate->w_vocational_minister  ?? old('w_vocational_minister')) ? 'checked' : ''}}> In Full-Time Vocational Ministry?
            </label>
          </div>
        </div>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="w_is_married">
              <input type="checkbox" name="w_married" id="w_is_married" value="1" {{($candidate->w_married ?? old('w_married')) ? 'checked' : ''}}> Is Married?
            </label>
          </div>
        </div>

      </div>


      <div class="card-body alert-warning">

        <div class="form-group row{{ $errors->has('w_special_diet') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_special_diet">Special Diet?</label>
          <div class="col-md-8">
            <textarea rows="2" class="form-control" name="w_special_diet" id="w_special_diet">{{ old('w_special_diet') ?: $candidate->w_special_diet }}</textarea>
            @if ($errors->has('w_special_diet'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('w_special_diet') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_special_dorm') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_special_dorm">Dorm Needs</label>
          <div class="col-md-8">
            <textarea rows="2" class="form-control" name="w_special_dorm" id="w_special_dorm">{{ old('w_special_dorm') ?: $candidate->w_special_dorm }}</textarea>
            @if ($errors->has('w_special_dorm'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('w_special_dorm') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_special_prayer') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_special_prayer">Prayer Requests</label>
          <div class="col-md-8">
            <textarea rows="3" class="form-control" name="w_special_prayer" id="w_special_prayer">{{ old('w_special_prayer') ?: $candidate->w_special_prayer }}</textarea>
            @if ($errors->has('w_special_prayer'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('w_special_prayer') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_special_medications') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_special_medications">Timed Meds?</label>
          <div class="col-md-8">
            <input type="text" class="form-control" name="w_special_medications" id="w_special_medications" maxlength="191" value="{{ old('w_special_medications') ?: $candidate->w_special_medications }}">
            @if ($errors->has('w_special_medications'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('w_special_medications') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="form-group row{{ $errors->has('w_special_notes') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_special_notes">Important Notes</label>
          <div class="col-md-8">
            <textarea rows="3" class="form-control" name="w_special_notes" id="w_special_notes">{{ old('w_special_notes') ?: $candidate->w_special_notes }}</textarea>
            <p class="small">Any notes for the Head Cha about special needs for the candidate?</p>
            @if ($errors->has('w_special_notes'))
              <span class="form-text text-danger"> <strong>{{ $errors->first('w_special_notes') }}</strong> </span>
            @endif
          </div>
        </div>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="w_is_smoker">
              <input type="checkbox" name="w_smoker" id="w_is_smoker" value="1" {{($candidate->w_smoker ?? old('w_smoker')) ? 'checked' : ''}}> Smoker?
            </label>
          </div>
        </div>

        <div class="col-md-9 offset-md-3">
          <div class="checkbox">
            <label for="w_response_card">
              <input type="checkbox" name="w_response_card_returned" id="w_response_card" value="1" {{($candidate->w_response_card_returned ?? old('w_response_card_returned')) ? 'checked' : ''}}> Response Card Returned?
            </label>
          </div>
        </div>
      </div>

      <div class="card-body alert-info">
        <div class="form-group row{{ $errors->has('w_arrival_poc_person') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_arrival_poc_person">Driver to Camp</label>
          <div class="col-md-6">
            <input type="tel" class="form-control" name="w_arrival_poc_person" id="w_arrival_poc_person" maxlength="191" value="{{ old('w_arrival_poc_person') ?: $candidate->w_arrival_poc_person }}">
            @if ($errors->has('w_arrival_poc_person'))
              <span class="form-text"> <strong>{{ $errors->first('w_arrival_poc_person') }}</strong> </span>
            @endif
          </div>
        </div>
        <div class="form-group row{{ $errors->has('w_arrival_poc_phone') ? ' is-invalid' : '' }}">
          <label class="col-md-4 control-label" for="w_arrival_poc_phone">Driver Cell#</label>
          <div class="col-md-6">
            <input type="tel" class="form-control" name="w_arrival_poc_phone" id="w_arrival_poc_phone" maxlength="191" value="{{ old('w_arrival_poc_phone') ?: $candidate->w_arrival_poc_phone }}">
            @if ($errors->has('w_arrival_poc_phone'))
              <span class="form-text"> <strong>{{ $errors->first('w_arrival_poc_phone') }}</strong> </span>
            @endif
          </div>
        </div>


      </div>
    </div>
  </div>
</div>

<hr>


<div class="form-group row{{ $errors->has('address1') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="address1">Address</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="address1" id="address1" maxlength="60" value="{{ old('address1') ?: $candidate->address1 }}">
    @if ($errors->has('address1'))
      <span class="form-text"> <strong>{{ $errors->first('address1') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('address2') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="address2">Address 2</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="address2" id="address2" maxlength="60" value="{{ old('address2') ?: $candidate->address2 }}">
    @if ($errors->has('address2'))
      <span class="form-text"> <strong>{{ $errors->first('address2') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('city') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="city">City</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="city" id="city" maxlength="60" value="{{ old('city') ?: $candidate->city }}">
    @if ($errors->has('city'))
      <span class="form-text"> <strong>{{ $errors->first('city') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('state') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="state">@lang('locale.province')</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="state" id="state" maxlength="60" value="{{ old('state') ?: $candidate->state }}">
    @if ($errors->has('state'))
      <span class="form-text"> <strong>{{ $errors->first('state') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('postalcode') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="postalcode">@lang('locale.postalcode')</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="postalcode" id="postalcode" maxlength="10" value="{{ old('postalcode') ?: $candidate->postalcode }}">
    @if ($errors->has('postalcode'))
      <span class="form-text"> <strong>{{ $errors->first('postalcode') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('homephone') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="homephone">Home Phone</label>

  <div class="col-md-6">
    <input type="tel" class="form-control" name="homephone" id="homephone" maxlength="20" value="{{ old('homephone') ?: $candidate->homephone }}">
    @if ($errors->has('homephone'))
      <span class="form-text"> <strong>{{ $errors->first('homephone') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('church') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="church">Church</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="church" id="church" maxlength="60" value="{{ old('church') ?: $candidate->church }}">
    @if ($errors->has('church'))
      <span class="form-text"> <strong>{{ $errors->first('church') }}</strong> </span>
    @endif
  </div>
</div>

<hr>

<div class="form-group row{{ $errors->has('payment_details') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label text-right col-form-label" for="payment_details">Payment Notes</label>

  <div class="col-md-8">
    <input type="text" class="form-control" name="payment_details" id="payment_details" maxlength="191" value="{{ old('payment_details') ?: $candidate->payment_details }}"{{ $user->can('record candidate fee payments') ? '' : ' disabled' }}>
    @if ($errors->has('payment_details'))
      <span class="form-text"> <strong>{{ $errors->first('payment_details') }}</strong> </span>
    @endif
  </div>
</div>

