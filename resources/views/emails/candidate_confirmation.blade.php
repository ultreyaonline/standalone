@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title')
  {{ $candidate_name }} - {{ config('site.community_long_name') }} Weekend - {{ $weekend_date }}
@endsection
@section('teaser_text', 'Request to confirm attendance for Tres Dias Weekend')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text')
  <center>You're Registered!</center>
@endsection
@section('content')

  <p>Dear {{ $candidate_first }},</p>

  <p>It’s our pleasure to contact you regarding an invitation extended to you by {{ $sponsor }}.</p>
  <p>You have been invited to attend <strong>{{ $weekend_long_name_with_number }}</strong>.</p>
  <p>The weekend will take place from <strong>{{ $weekend_dates_with_days_of_week }}</strong>.
  Your sponsor has made a reservation for you, indicating your intention to attend.</p>


  <h2>What You Need to Do Right Away!</h2>

  <p style="color:red;">It’s very important that you <strong>reply to this e-mail</strong> <u>to confirm your reservation</u> for the weekend.</p>

  <p>When you reply, please also advise us of any special needs you may have such as food allergies, a doctor-ordered diet, any special physical needs,
    and any prayer requests you would like our team to be praying for.</p>

  <p>Our email address is: <a href="mailto:{{ config('site.email-preweekend-mailbox') }}">{{ config('site.email-preweekend-mailbox') }}</a></p>


  <h2>What to Bring</h2>

  <p>Please be sure to bring the following items with you on your weekend:</p>
  <ul>
    <li>Bed Linens (twin-size sheets) and blanket or Sleeping Bag, and a Pillow</li>
    <li>Towel, Shampoo, Washcloth, and Bathrobe if desired</li>
    <li>Sundries and Prescription Medications</li>
    <li>Warm pajamas to sleep in and something cooler in case of hot weather</li>
    <li>Comfortable casual clothes, Light Jacket or Sweater</li>
    <li>Hair Dryer, etc</li>
    <li>Umbrella (optional -- we don't know the final weather forecast yet!)</li>
    <li>Footwear suitable for walking in wet grass, since we will be walking outdoors between buildings for meals and chapel, etc</li>
    <li>Warm jacket/pants for outdoors -- if the weather permits we may share 1-2 times of fellowship by campfire.</li>
  </ul>

  <p>You will NOT be sharing a bed, however, typically there are 2-4 people to a room.</p>

  <p>Cell phones, laptops, and portable communication devices are not required on the weekend, and in fact <strong>you are encouraged to 'unplug' for the duration of the weekend</strong>.</p>


  <h2>Ready, Set, Go!</h2>

  <p>Your weekend begins when everyone meets for Registration at Sendoff.</p>
  <p>Your sponsor will provide or arrange transportation for you to and from the weekend.
    They will arrange for you <strong>to be at the camp between 5:45-6:15pm on the Thursday.</strong>
  Plan for your weekend to conclude late Sunday afternoon, at approximately 6:00pm</p>

<br>
  <p>We look forward to hearing from you soon.</p>

  Blessings,<br>
  <br>
  {{ config('site.community-acronym') }} Registration Committee<br>
  {{ config('site.email-preweekend-mailbox') }}
@endsection
