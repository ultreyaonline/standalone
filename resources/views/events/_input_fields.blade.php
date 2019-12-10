<div class="col-lg-6">

@if(false)
<div class="form-group row{{ $errors->has('event_key') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="event_key">Event Abbreviation *</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="event_key" id="event_key" value="{{ old('event_key') ?: $event->event_key }}" placeholder="a random unique key like [secuelaMay2015]" autofocus required>
    @if ($errors->has('event_key'))
      <span class="form-text"> <strong>{{ $errors->first('event_key') }}</strong> </span>
    @endif
  </div>
</div>
@endif

<div class="form-group row{{ $errors->has('type') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="type">Event Type</label>

  <div class="col-md-8 col-lg-7">
    @include('events._eventtypes_selector', ['fieldname' => 'type', 'current' => old('type') ?: $event->type])
    @if ($errors->has('type'))
      <span class="form-text"> <strong>{{ $errors->first('type') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('name') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="name">Event Name *</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ?: $event->name }}" placeholder="ie: Spring {{ date('Y') }} Secuela" required>
    @if ($errors->has('name'))
      <span class="form-text"> <strong>{{ $errors->first('name') }}</strong> </span>
    @endif
  </div>
</div>


<div class="form-group row{{ $errors->has('description') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="description">Description</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="description" id="description" value="{{ old('description') ?: $event->description }}" placeholder="brief description">
    @if ($errors->has('description'))
      <span class="form-text"> <strong>{{ $errors->first('description') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('location_name') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="location_name">Venue Name *</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="location_name" id="location_name" value="{{ old('location_name') ?: $event->location_name }}" placeholder="name of venue" required>
    @if ($errors->has('location_name'))
      <span class="form-text"> <strong>{{ $errors->first('location_name') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('location_url') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="location_url">Venue's Website</label>

  <div class="col-md-8 col-lg-7">
    <input type="url" class="form-control" name="location_url" id="location_url" value="{{ old('location_url') ?: $event->location_url }}" placeholder="URL to venue website for more info">
    @if ($errors->has('location_url'))
      <span class="form-text"> <strong>{{ $errors->first('location_url') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('address_street') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="address_street">Street Address</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="address_street" id="address_street" value="{{ old('address_street') ?: $event->address_street }}" placeholder="street address suitable for maps">
    @if ($errors->has('address_street'))
      <span class="form-text"> <strong>{{ $errors->first('address_street') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('address_city') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="address_city">City</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="address_city" id="address_city" value="{{ old('address_city') ?: $event->address_city }}" placeholder="city name, to aid in mapping">
    @if ($errors->has('address_city'))
      <span class="form-text"> <strong>{{ $errors->first('address_city') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('address_province') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="address_province">{{__('locale.province')}}</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="address_province" id="address_province" value="{{ old('address_province') ?: $event->address_province }}">
    @if ($errors->has('address_province'))
      <span class="form-text"> <strong>{{ $errors->first('address_province') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('address_postal') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="address_postal">{{__('locale.postalcode')}}</label>

  <div class="col-md-8 col-lg-7">
    <input type="text" class="form-control" name="address_postal" id="address_postal" value="{{ old('address_postal') ?: $event->address_postal }}">
    @if ($errors->has('address_postal'))
      <span class="form-text"> <strong>{{ $errors->first('address_postal') }}</strong> </span>
    @endif
  </div>
</div>

<div class="form-group row{{ $errors->has('map_url_link') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="map_url_link">Map URL</label>

  <div class="col-md-8 col-lg-7">
    <input type="url" class="form-control" name="map_url_link" id="map_url_link" value="{{ old('map_url_link') ?: $event->map_url_link }}" placeholder="URL to google map (optional)">
    @if ($errors->has('map_url_link'))
      <span class="form-text"> <strong>{{ $errors->first('map_url_link') }}</strong> </span>
    @endif
  </div>
  <div class="col-md-12"><p class="small"><em>If no MAP URL is given, we will attempt to create one based on the address details entered.</em></p></div>
</div>


@if(false)
<hr>
<div class="form-group row">
  <p>Choose a contact person from the list, or enter their details below</p>
  <div class="form-group row{{ $errors->has('contact_id') ? ' is-invalid' : '' }}">
    <label class="col-md-4 control-label" for="contact_id">Contact:</label>

    <div class="col-md-7">
      @include('members._member_selector', ['fieldname' => 'contact_id', 'current' => old('contact_id') ?: $event->contact_id])
      @if ($errors->has('contact_id'))
        <span class="form-text"> <strong>{{ $errors->first('contact_id') }}</strong> </span>
      @endif
    </div>
  </div>

  <div class="form-group row{{ $errors->has('contact_name') ? ' is-invalid' : '' }}">
    <label class="col-md-4 control-label" for="contact_name">Contact Name</label>

    <div class="col-md-7">
      <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name') ?: $event->contact_name }}">
      @if ($errors->has('contact_name'))
        <span class="form-text"> <strong>{{ $errors->first('contact_name') }}</strong> </span>
      @endif
    </div>
  </div>

  <div class="form-group row{{ $errors->has('contact_email') ? ' is-invalid' : '' }}">
    <label class="col-md-4 control-label" for="contact_email">Contact Email</label>

    <div class="col-md-7">
      <input type="email" class="form-control" name="contact_email" id="contact_email" value="{{ old('contact_email') ?: $event->contact_email }}">
      @if ($errors->has('contact_email'))
        <span class="form-text"> <strong>{{ $errors->first('contact_email') }}</strong> </span>
      @endif
    </div>
  </div>

  <div class="form-group row{{ $errors->has('contact_phone') ? ' is-invalid' : '' }}">
    <label class="col-md-4 control-label" for="contact_phone">Contact Phone</label>

    <div class="col-md-7">
      <input type="tel" class="form-control" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') ?: $event->contact_phone }}">
      @if ($errors->has('contact_phone'))
        <span class="form-text"> <strong>{{ $errors->first('contact_phone') }}</strong> </span>
      @endif
    </div>
  </div>
</div>
@endif

</div>

<div class="col-lg-6">


<div class="form-group row{{ $errors->has('start_datetime') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="input_start_datetime">Start Date/Time *</label>
  <div class="col-md-7">

    <div class="form-group row">
      <div class="input-group date" data-target-input="nearest" id="start_datetime">
        <input type="text" name="start_datetime" id="input_start_datetime" data-target="#start_datetime" value="{{ old('start_datetime') ?: $event->start_datetime }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" required>
        <div class="input-group-append" data-target="#start_datetime" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="form-group row{{ $errors->has('end_datetime') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="input_end_datetime">End Date/Time *</label>
  <div class="col-md-7">

    <div class="form-group row">
      <div class="input-group date" data-target-input="nearest" id="end_datetime">
        <input type="text" name="end_datetime" id="input_end_datetime" data-target="#end_datetime" value="{{ old('end_datetime') ?: $event->end_datetime }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:MM" required>
        <div class="input-group-append" data-target="#end_datetime" data-toggle="datetimepicker">
          <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-12"><p class="small"><em>Note: The event will automatically stop showing on the calendar after this day.</em></p></div>
</div>


<div class="form-group row">
  <div class="col-12">
    <div class="checkbox">
      <label for="is_public">
        <input type="checkbox" name="is_public" id="is_public" value="1" {{$event->is_public ? 'checked' : ''}}> Visible to Public (non-Tres Dias members)?
      </label>
    </div>
  </div>
  <div class="col-md-12"><p class="small"><em>Public events will be visible to non-logged-in members and to search engines</em></p></div>
</div>

<div class="form-group row">
  <div class="col-md-7 offset-md-4">
    <div class="checkbox">
      <label for="is_enabled">
        <input type="checkbox" name="is_enabled" id="is_enabled" value="1" {{$event->is_enabled ? 'checked' : ''}}> Active?
      </label>
    </div>
  </div>
  <div class="col-md-12"><p class="small"><em>Inactive events will not show on the calendar.</em></p></div>
</div>

@if(false)
<div class="form-group row{{ $errors->has('expiration_date') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="input_expiration_date">Repeat Expiration Date/Time</label>
  <div class="col-md-7">

    <div class="form-group row">
      <div class="input-group date" data-target-input="nearest" id="expirationdate">
        <input type="text" name="expiration_date" id="input_expiration_date" data-target="#expiration_date" value="{{ old('expiration_date') ?: $event->expiration_date }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD" />
        <span class="input-group-append"><i class="fa fa-calendar" aria-hidden="true"></i></span>
      </div>
    </div>
  </div>
  <div class="col-md-12"><p class="small"><em>This is the date when the repeating event will stop showing on the calendar.</em></p></div>
</div>

<hr>
<div class="form-group row">
  <div class="col-md-7 offset-md-4">
    <div class="checkbox">
      <label for="is_recurring">
        <input type="checkbox" name="is_recurring" id="is_recurring" value="1" {{$event->is_recurring? 'checked' : ''}}> Does this event repeat?
      </label>
    </div>
  </div>
</div>


<div class="form-group row{{ $errors->has('recurring_end_datetime') ? ' is-invalid' : '' }}">
  <label class="col-md-4 control-label" for="input_recurring_end_datetime">Recurring Ends on Date/Time</label>
  <div class="col-md-7">

    <div class="form-group row">
      <div class="input-group date" data-target-input="nearest" id="recurringdatetime">
        <input type="text" name="recurring_end_datetime" id="input_recurring_end_datetime" data-target="#recurring_end_datetime" value="{{ old('recurring_end_datetime') ?: $event->recurring_end_datetime }}" class="form-control datetimepicker-input" placeholder="YYYY-MM-DD HH:mm:ss" />
        <span class="input-group-append"><i class="fa fa-calendar" aria-hidden="true"></i></span>
      </div>
    </div>
  </div>
</div>
@endif
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
    $('#start_datetime').datetimepicker(defaultDate, "{{ old('start_datetime') ?: $event->start_datetime }}");
    $('#end_datetime').datetimepicker(defaultDate, "{{ old('end_date') ?: $event->end_datetime }}");
    $("#start_datetime").on("dp.change", function (e) {
      $('#end_datetime').data("DateTimePicker").minDate(e.date);
    });
    $("#end_datetime").on("dp.change", function (e) {
      $('#start_datetime').data("DateTimePicker").maxDate(e.date);
    });
  </script>
@endsection
