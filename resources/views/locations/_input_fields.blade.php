<div class="col-lg-6">

    <div class="form-group row{{ $errors->has('location_name') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="location_name">Venue Name *</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="location_name" id="location_name" value="{{ old('location_name') ?: $location->location_name }}" placeholder="ie: First Baptist Church" required>
        @if ($errors->has('location_name'))
          <span class="form-text"> <strong>{{ $errors->first('location_name') }}</strong> </span>
        @endif
      </div>
        <div class="col-md-7 offset-md-4"><p class="small">TIP: If you spell this EXACTLY the same as the Camp or Sendoff location of a Weekend,
                it will be matched up automatically.</p></div>
    </div>

    @if(false)
    <div class="form-group row{{ $errors->has('slug') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="slug">Slug</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug') ?: $location->slug }}" placeholder="slug">
        @if ($errors->has('slug'))
          <span class="form-text"> <strong>{{ $errors->first('slug') }}</strong> </span>
        @endif
      </div>
    </div>
    @endif

    <div class="form-group row{{ $errors->has('location_url') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="location_url">Venue's Website</label>

      <div class="col-md-8 col-lg-7">
        <input type="url" class="form-control" name="location_url" id="location_url" value="{{ old('location_url') ?: $location->location_url }}" placeholder="URL to venue website">
        @if ($errors->has('location_url'))
          <span class="form-text"> <strong>{{ $errors->first('location_url') }}</strong> </span>
        @endif
      </div>
    </div>

    <div class="form-group row{{ $errors->has('address_street') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="address_street">Street Address</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="address_street" id="address_street" value="{{ old('address_street') ?: $location->address_street }}" placeholder="street address suitable for maps">
        @if ($errors->has('address_street'))
          <span class="form-text"> <strong>{{ $errors->first('address_street') }}</strong> </span>
        @endif
      </div>
    </div>

    <div class="form-group row{{ $errors->has('address_city') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="address_city">City</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="address_city" id="address_city" value="{{ old('address_city') ?: $location->address_city }}" placeholder="city name, to aid in mapping">
        @if ($errors->has('address_city'))
          <span class="form-text"> <strong>{{ $errors->first('address_city') }}</strong> </span>
        @endif
      </div>
    </div>

    <div class="form-group row{{ $errors->has('address_province') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="address_province">{{__('locale.province')}}</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="address_province" id="address_province" value="{{ old('address_province') ?: $location->address_province }}">
        @if ($errors->has('address_province'))
          <span class="form-text"> <strong>{{ $errors->first('address_province') }}</strong> </span>
        @endif
      </div>
    </div>

    <div class="form-group row{{ $errors->has('address_postal') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="address_postal">{{__('locale.postalcode')}}</label>

      <div class="col-md-8 col-lg-7">
        <input type="text" class="form-control" name="address_postal" id="address_postal" value="{{ old('address_postal') ?: $location->address_postal }}">
        @if ($errors->has('address_postal'))
          <span class="form-text"> <strong>{{ $errors->first('address_postal') }}</strong> </span>
        @endif
      </div>
    </div>

    <div class="form-group row{{ $errors->has('map_url_link') ? ' is-invalid' : '' }}">
      <label class="col-md-4 control-label" for="map_url_link">Map URL</label>

      <div class="col-md-8 col-lg-7">
        <input type="url" class="form-control" name="map_url_link" id="map_url_link" value="{{ old('map_url_link') ?: $location->map_url_link }}" placeholder="URL to google map (optional)">
        @if ($errors->has('map_url_link'))
          <span class="form-text"> <strong>{{ $errors->first('map_url_link') }}</strong> </span>
        @endif
      </div>
      <div class="col-md-12"><p class="small"><em>If no MAP URL is given, we will generate one based on the street address details</em></p></div>
    </div>

</div>

<div class="col-lg-6">

    <div class="form-group row{{ $errors->has('contact_name') ? ' is-invalid' : '' }}">
        <label class="col-md-4 control-label" for="contact_name">Contact Name</label>

        <div class="col-md-8 col-lg-7">
          <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name') ?: $location->contact_name }}">
          @if ($errors->has('contact_name'))
            <span class="form-text"> <strong>{{ $errors->first('contact_name') }}</strong> </span>
          @endif
        </div>
        <div class="col-md-7 offset-md-4"><p class="small"><em>This is the name of a Venue contact person.</em></p></div>
    </div>

    <div class="form-group row{{ $errors->has('contact_email') ? ' is-invalid' : '' }}">
        <label class="col-md-4 control-label" for="contact_email">Contact Email</label>

        <div class="col-md-8 col-lg-7">
          <input type="email" class="form-control" name="contact_email" id="contact_email" value="{{ old('contact_email') ?: $location->contact_email }}">
          @if ($errors->has('contact_email'))
            <span class="form-text"> <strong>{{ $errors->first('contact_email') }}</strong> </span>
          @endif
        </div>
    </div>

    <div class="form-group row{{ $errors->has('contact_phone') ? ' is-invalid' : '' }}">
        <label class="col-md-4 control-label" for="contact_phone">Contact Phone</label>

        <div class="col-md-8 col-lg-7">
          <input type="tel" class="form-control" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') ?: $location->contact_phone }}">
          @if ($errors->has('contact_phone'))
            <span class="form-text"> <strong>{{ $errors->first('contact_phone') }}</strong> </span>
          @endif
        </div>
    </div>

</div>
