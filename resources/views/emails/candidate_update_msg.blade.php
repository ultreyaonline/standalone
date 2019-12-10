@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title')
  {{ $candidate_name }} - {{ config('site.community_long_name') }} Weekend - {{ $weekend_date }}
@endsection
@section('teaser_text', 'UPDATE about upcoming Tres Dias Weekend details')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text')
  <center>{{ $candidate_name }}</center>
@endsection
@section('content')
<p>
  <font color="red">This is a quick update about your upcoming Tres Dias Weekend.<br>
  Please note the "arrival time". We hope to begin with everyone present for {{ $sendoff_start_time }}.
  </font>
</p>
<hr>
<div>
  <center>
  Your scheduled retreat dates are as follows:<br>
    <strong>
  {{ $weekend_long_name_with_number }}<br>
  {{ $weekend_date }}</strong>
  </center>
</div>
<hr>
<p>
  Your sponsor will be bringing you to the retreat center on the Thursday evening of your weekend.<br>
  <font color="#ff0000"><strong>All participants and sponsors are requested to <u>arrive before {{ $sendoff_start_time }}</u>.</strong></font><br>
  We will be having a time of prayer together before heading to dinner, but not until everyone has arrived.<br>
<br>
  Your sponsor will also provide your return transportation on Sunday evening (event ends around {{ $end_time }} Sunday, and does not include supper on Sunday).<br>
</p>
<hr>
<p><strong>Following is a list of personal items to pack for your weekend:</strong><br>
    <ul>
      <li>Bed Linens (twin-size sheets) and blanket or Sleeping Bag, and a Pillow</li>
      <li>Towel, Washcloth, Shampoo, and Bathrobe if desired</li>
      <li>Sundries and Prescription Medications</li>
      <li>Casual Clothes, Light Jacket or Sweater, Sleeping Apparel</li>
      <li>Hair Dryer, etc</li>
      <li>Umbrella (optional -- see the local forecast)</li>
      <li>Footwear suitable for walking in wet grass, since we will be walking outdoors between buildings for meals and chapel, etc</li>
      <li>Warm jacket/pants for outdoors -- if the weather permits we may share 1-2 times of fellowship by campfire.</li>
    </ul>
  Please limit your bags to one suitcase, sleeping gear, and a sundries bag.<br>
  <br>
  {{ $weather_forecast }}<br>
</p>
<p>You will NOT be sharing a bed, however, typically there are 2-4 people to a room.</p>
<p>
  <strong>Please DO NOT bring a cell phone or other "connected" electronic devices.</strong><br>
  We want you to be able to focus on what God has for you for the weekend.<br>
  (Cell reception is poor at the retreat center anyway, and there is no open WiFi.)<br>
  We ask you to consider leaving your devices at home, or with your sponsor when they drop you off on Thursday evening.
</p>
<hr>
<p>
  Just in case your family needs to get hold of you during your Tres Dias weekend, the following are emergency numbers that they can call:<br>
  <br>
  <font color="#ff0000"><strong>Emergency numbers to call if a message must be relayed:</strong><br>
  {{ $emergency_contact }} (primary contact)<br></font>
</p>
<hr>
<p>
  If you have any questions before the weekend, please discuss directly with your sponsor.
</p>

<p>
  The Tres Dias Community welcomes you to your upcoming weekend!
  We've been praying for you, and are expecting God to minister to you personally during your time here.<br>
</p>
<p>
  Registration Committee,<br>
  {{ config('site.community_long_name') }}
</p>
@endsection
