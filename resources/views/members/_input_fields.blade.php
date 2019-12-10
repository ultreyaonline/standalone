<?php $autofocus_firstname=true ?>
<?php $autofocus=false ?>
@if($user->can('edit members') || $user->can('add community member') || $user->id == $member->id)
  @if($member->id === null || empty($member->weekend) || $user->can('edit members'))
      <?php $autofocus_firstname=false ?>
    <div class="form-group row{{ $errors->has('weekend') ? ' is-invalid' : '' }}">
      <label class="col-md-4 text-right col-form-label" for="weekend">Weekend</label>

      <div class="col-md-6">
        <input type="text" class="form-control" name="weekend" id="weekend" maxlength="10" value="{{ old('weekend') ?: $member->weekend }}" required autofocus placeholder="ie: {{ config('site.community_acronym') }} #222">
        <small>(If unknown, enter 'Unknown')</small>
        @if ($errors->has('weekend'))
          <span class="form-text"> <strong>{{ $errors->first('weekend') }}</strong> </span>
        @endif
      </div>
      <div class="col-md-2 col-form-label"><small>(Required)</small></div>
    </div>
  @endif

 @unless($user->cannot('edit members') && ($member->id === $user->id && !config('site.members_may_edit_own_name')))
  <div class="form-group row{{ $errors->has('first') ? ' is-invalid' : '' }}">
    <label class="col-md-4 text-right col-form-label" for="first">First Name</label>

    <div class="col-md-6">
      <input type="text" class="form-control" name="first" id="first" maxlength="45" value="{{ old('first') ?: $member->first }}" required {{ $autofocus_firstname ? 'autofocus' : '' }}>
      @if ($errors->has('first'))
        <span class="form-text"> <strong>{{ $errors->first('first') }}</strong> </span>
      @endif
    </div>
    <div class="col-md-2 col-form-label"><small>(Required)</small></div>
  </div>

  <div class="form-group row{{ $errors->has('last') ? ' is-invalid' : '' }}">
    <label class="col-md-4 text-right col-form-label" for="last">Last Name</label>

    <div class="col-md-6">
      <input type="text" class="form-control" name="last" id="last" maxlength="45" value="{{ old('last') ?: $member->last }}" required>
      @if ($errors->has('last'))
        <span class="form-text"> <strong>{{ $errors->first('last') }}</strong> </span>
      @endif
    </div>
    <div class="col-md-2 col-form-label"><small>(Required)</small></div>
  </div>
 @endunless
@endif

@if($user->can('edit members') || $user->can('add community member'))
<div class="form-group row{{ $errors->has('gender') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="gender">Gender (M/W)</label>

  <div class="col-md-6">
    @include('members._gender_selector', ['fieldname' => 'gender', 'current' => old('gender') ?: $member->gender, 'mode' => 'singular'])
    @if ($errors->has('gender'))
      <span class="form-text"> <strong>{{ $errors->first('gender') }}</strong> </span>
    @endif
  </div>
</div>
@endif

<div class="form-group row{{ $errors->has('email') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="email">E-Mail Address</label>

  <div class="col-md-6">
    <input type="email" class="form-control" name="email" id="email" maxlength="60" value="{{ old('email') ?: $member->email }}">
    @if ($errors->has('email'))
      <span class="form-text"> <strong>{{ $errors->first('email') }}</strong> </span>
    @endif
  </div>
</div>

@if($user->can('edit members') || $user->can('add community member') || $member->id === auth()->id() || $member->id === null)
<hr>
<div class="form-group row{{ $errors->has('username') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="username">Username<br>
      <span style="font-size: smaller; font-weight: normal;">({{ config('site.members_username_default') === 'Email Address' ? 'usually same as email' : 'usually FirstnameLastname' }})</span>
  </label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="username" id="username" maxlength="60" value="{{ old('username') ?: $member->username }}" placeholder="Unique username; {{ config('site.members_username_default') === 'Email Address' ? 'usually the email address' : 'usually FirstnameLastname' }}">
    @if ($errors->has('username'))
      <span class="form-text"> <strong>{{ $errors->first('username') }}</strong> </span>
    @endif
  </div>
  <div class="col-md-2 col-form-label"><small>(Required)</small></div>
</div>
<hr>
@endif


<div class="form-group row{{ $errors->has('address1') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="address1">Address</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="address1" id="address1" maxlength="60" value="{{ old('address1') ?: $member->address1 }}">
    @if ($errors->has('address1'))
      <span class="form-text"> <strong>{{ $errors->first('address1') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('address2') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="address2">Address 2</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="address2" id="address2" maxlength="60" value="{{ old('address2') ?: $member->address2 }}">
    @if ($errors->has('address2'))
      <span class="form-text"> <strong>{{ $errors->first('address2') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('city') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="city">City</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="city" id="city" maxlength="60" value="{{ old('city') ?: $member->city }}">
    @if ($errors->has('city'))
      <span class="form-text"> <strong>{{ $errors->first('city') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('state') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="state">Province</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="state" id="state" maxlength="60" value="{{ old('state') ?: $member->state }}">
    @if ($errors->has('state'))
      <span class="form-text"> <strong>{{ $errors->first('state') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('postalcode') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="postalcode">Postal Code</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="postalcode" id="postalcode" maxlength="10" value="{{ old('postalcode') ?: $member->postalcode }}">
    @if ($errors->has('postalcode'))
      <span class="form-text"> <strong>{{ $errors->first('postalcode') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('country') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="country">Country</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="country" id="country" maxlength="32" value="{{ old('country') ?: $member->country }}">
    @if ($errors->has('country'))
      <span class="form-text"> <strong>{{ $errors->first('country') }}</strong> </span>
    @endif
  </div>
</div>

@if($member->hasRole('Member'))
<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    <div class="checkbox">
      <label for="allow_address_share" class="text-right col-form-label">
        <input type="checkbox" name="allow_address_share" id="allow_address_share" value="1" {{$member->allow_address_share ? 'checked' : ''}}> Allow my address to be displayed to other members?
      </label>
      <p class="small">This controls whether your address will appear when other members view your profile in our online Directory.</p>
    </div>
  </div>
</div>
@endif

<div class="form-group row{{ $errors->has('cellphone') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="cellphone">Cell Phone</label>

  <div class="col-md-6">
    <input type="tel" class="form-control" name="cellphone" id="cellphone" maxlength="20" value="{{ old('cellphone') ?: $member->cellphone }}">
    @if ($errors->has('cellphone'))
      <span class="form-text"> <strong>{{ $errors->first('cellphone') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('homephone') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="homephone">Home Phone</label>

  <div class="col-md-6">
    <input type="tel" class="form-control" name="homephone" id="homephone" maxlength="20" value="{{ old('homephone') ?: $member->homephone }}">
    @if ($errors->has('homephone'))
      <span class="form-text"> <strong>{{ $errors->first('homephone') }}</strong> </span>
    @endif
  </div>
</div>
<div class="form-group row{{ $errors->has('workphone') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="workphone">Work Phone</label>

  <div class="col-md-6">
    <input type="tel" class="form-control" name="workphone" id="workphone" maxlength="20" value="{{ old('workphone') ?: $member->workphone }}">
    @if ($errors->has('workphone'))
      <span class="form-text"> <strong>{{ $errors->first('workphone') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('church') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="church">Church</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="church" id="church" maxlength="60" value="{{ old('church') ?: $member->church }}">
    @if ($errors->has('church'))
      <span class="form-text"> <strong>{{ $errors->first('church') }}</strong> </span>
    @endif
  </div>
</div>

@if(!$member->hasRole('Member'))
  <hr>
@endif

@if($user->can('edit members') || $user->can('add community member')|| $member->id === null)
<div class="form-group row{{ $errors->has('community') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="community">Tres Dias Community</label>

  <div class="col-md-6">
    <input type="text" class="form-control" name="community" id="community" maxlength="60" value="{{ old('community') ?: $member->community }}" placeholder="ie: {{ config('site.community_acronym') }} or NGTD etc">
    @if ($errors->has('community'))
      <span class="form-text"> <strong>{{ $errors->first('community') }}</strong> </span>
    @endif
  </div>
</div>
@endif

@if($user->can('edit members') || $user->can('add community member') || ($member->id === $user->id && config('site.members_may_edit_own_spouse')))
<div class="form-group row{{ $errors->has('spouseID') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="spouseID">Spouse</label>

  <div class="col-md-6">
    @include('members._member_selector', ['fieldname' => 'spouseID', 'current' => old('spouseID') ?: $member->spouseID, 'users' => ($member->gender === 'M' ? $women : ($member->gender === 'W' ? $men : $users))])
    @if ($errors->has('spouseID'))
      <span class="form-text"> <strong>{{ $errors->first('spouseID') }}</strong> </span>
    @endif
  </div>
</div>
@endif

@if($user->can('edit members') || $user->can('add community member') || ($member->id === $user->id && config('site.members_may_edit_own_sponsor')))
<div class="form-group row{{ $errors->has('sponsorID') ? ' is-invalid' : '' }}">
  <label class="col-md-4 text-right col-form-label" for="sponsorID">Sponsor</label>

  <div class="col-md-6">
    @include('members._member_selector', ['fieldname' => 'sponsorID', 'current' => old('sponsorID') ?: $member->sponsorID])
    @if ($errors->has('sponsorID'))
      <span class="form-text"> <strong>{{ $errors->first('sponsorID') }}</strong> </span>
    @endif
  </div>
</div>
  @if(empty($member->sponsor) || $user->can('edit members') || $user->can('add community member'))
  <div class="form-group row{{ $errors->has('sponsor') ? ' is-invalid' : '' }}">
    <label class="col-md-4 text-right col-form-label" for="sponsor">Sponsor Override</label>
    <div class="col-md-8">
      <input type="text" class="form-control" name="sponsor" id="sponsor" maxlength="60" value="{{ old('sponsor') ?: $member->sponsor_text ?? '' }}" placeholder="(leave blank to use usual sponsor name)">
      @if ($errors->has('sponsor'))
        <span class="form-text"> <strong>{{ $errors->first('sponsor') }}</strong> </span>
      @endif
      <p class="small">(This can be used to list additional co-sponsors or names from outside the community.)</p>
    </div>
  </div>
  @endif
@endif


{{--THE FOLLOWING ARE SKIPPED WHEN ADDING CANDIDATES--}}
@if($member->id !== null && $member->hasRole('Member'))

  @if($member->id === auth()->id() || $member->can('view members') || $member->can('edit members'))
    <hr>
    <div class="form-group row">
      <div class="col-md-8 offset-md-4">
        <div class="checkbox">
          <label for="serving" class="text-right col-form-label">
            <input type="checkbox" name="interested_in_serving" id="serving" value="1" {{$member->interested_in_serving ? 'checked' : ''}}> Interested in serving on weekends?
          </label>
        </div>
      </div>
    </div>

    <div class="form-group row{{ $errors->has('skills') ? ' is-invalid' : '' }}">
    <label class="col-md-4 text-right col-form-label" for="skills">Skills</label>

    <div class="col-md-8">
      <input type="text" class="form-control" name="skills" id="skills" maxlength="191" value="{{ old('skills') ?: $member->skills }}" placeholder="Skills I could offer when serving ">
      @if ($errors->has('skills'))
        <span class="form-text"> <strong>{{ $errors->first('skills') }}</strong> </span>
      @endif
    </div>
  </div>

  <div class="form-group row{{ $errors->has('emergency_contact_details') ? ' is-invalid' : '' }}">
    <label class="col-md-4 text-right col-form-label" for="emergency_contact_details">Emergency Contact Name and Phone</label>
    <div class="col-md-8">
      <input type="text" class="form-control" name="emergency_contact_details" id="emergency_contact_details" value="{{ old('emergency_contact_details') ?: $member->emergency_contact_details }}">
      @if ($errors->has('emergency_contact_details'))
        <span class="form-text"> <strong>{{ $errors->first('emergency_contact_details') }}</strong> </span>
      @endif
      <p class="small"> (in case we need to contact someone when you're serving on a weekend)</p>
    </div>
  </div>

    <hr>
  <h3><strong><i class="fa fa-envelope"></i> Email Settings:</strong></h3>
    <div class="form-group row">
      <div class="col-md-11 offset-md-1">
        <div class="checkbox">
          <label for="receive_email_weekend_general" class="text-right col-form-label">
            <input type="checkbox" name="receive_email_weekend_general" id="receive_email_weekend_general" value="1" {{$member->receive_email_weekend_general ? 'checked' : ''}}> Email: Receive <strong>General weekend updates</strong>?
          </label>
          <p class="small">This includes news about when Sendoff/Serenade/Closing is happening on upcoming weekends. Gets a bunch of messages when a weekend is approaching. Quiet most of the rest of the year.</p>
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-11 offset-md-1">
        <div class="checkbox">
          <label for="receive_prayer_wheel_invites" class="text-right col-form-label">
            <input type="checkbox" name="receive_prayer_wheel_invites" id="receive_prayer_wheel_invites" value="1" {{$member->receive_prayer_wheel_invites ? 'checked' : ''}}> Email: Receive <strong>Prayer Wheel invites</strong>?
          </label>
          <p class="small">When we need more people to fill the prayer wheel for a weekend, we will send a message or two.</p>
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-11 offset-md-1">
        <div class="checkbox">
          <label for="receive_email_sequela" class="text-right col-form-label">
            <input type="checkbox" name="receive_email_sequela" id="receive_email_sequela" value="1" {{$member->receive_email_sequela ? 'checked' : ''}}> Email: Receive notices about <strong>Secuelas</strong>?
          </label>
          <p class="small">This is for messages about any Secuelas or other fellowship gatherings for the whole community. These happen 2-8 times per year.</p>
        </div>
      </div>
    </div>

@if(config('site.features-emailtypes-reunion'))
    <div class="form-group row">
      <div class="col-md-11 offset-md-1">
        <div class="checkbox">
          <label for="receive_email_reunion" class="text-right col-form-label">
            <input type="checkbox" name="receive_email_reunion" id="receive_email_reunion" value="1" {{$member->receive_email_reunion ? 'checked' : ''}}> Email: Receive notices about <strong>Reunion Groups</strong>?
          </label>
          <p class="small">Updates about Reunion Group meetings. Sent when relevant.</p>
        </div>
      </div>
    </div>
@endif

    <div class="form-group row">
      <div class="col-md-11 offset-md-1">
        <div class="checkbox">
          <label for="receive_email_community_news" class="text-right col-form-label">
            <input type="checkbox" name="receive_email_community_news" id="receive_email_community_news" value="1" {{$member->receive_email_community_news ? 'checked' : ''}}> Email: Receive <strong>Community news</strong>?
          </label>
          <p class="small">Sometimes we send messages that are NOT related to upcoming Weekends or Secuelas, but we want to keep everyone informed. <strong>We recommend you DO get these emails.</strong> There aren't very many!</p>
        </div>
      </div>
    </div>
  @endif

  @can('Admin')
  <hr>
  <div class="form-group row bg-info">
    <div class="col-md-8">
      <div class="checkbox">
        <label for="is_sd" class="text-right col-form-label">
          <input type="checkbox" name="qualified_sd" id="is_sd" value="1" {{$member->qualified_sd ? 'checked' : ''}}> Is Qualified Spiritual Director (SD)?
        </label>
      </div>
    </div>
    <p class="small mx-4">(Qualified SDs will show up on the SDs Service History Report, and can be assigned to opposite-gender teams.)</p>
  </div>
  @endcan

  @can('edit members')
  <hr>
  <div class="form-group row bg-warning">
    <div class="col-12">
      <div class="checkbox">
        <label for="is_active" class="text-right col-form-label">
          <input type="checkbox" name="active" id="is_active" value="1" {{$member->active ? 'checked' : ''}}> Is Active?  <small>(uncheck to mark Inactive)</small>
        </label>
      </div>
    </div>
    <label class="col-md-4 text-right col-form-label" for="inactive_comments">Inactive Reason?</label>
    <div class="col-md-8 mb-3">
      <input type="text" class="form-control" name="inactive_comments" id="inactive_comments" maxlength="191" value="{{ old('inactive_comments') ?: $member->inactive_comments }}">
      @if ($errors->has('inactive_comments'))
        <span class="form-text"> <strong>{{ $errors->first('inactive_comments') }}</strong> </span>
      @endif
    </div>
  </div>

  <hr>
  <div class="form-group row bg-danger">
    <div class="col-md-10 offset-md-2">
      <div class="checkbox">
        <label for="is_unsubscribed" class="text-right col-form-label">
          <input type="checkbox" name="unsubscribe" id="is_unsubscribed" value="1" {{$member->unsubscribe ? 'checked' : ''}} class="text-red-darkest"> Is Unsubscribed? <strong>(NO more contact EVER)</strong>
        </label>
      </div>
    </div>
  </div>
  @endcan

  {{--@include('members._avatar_field')--}}
@endif
