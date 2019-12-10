@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title')
  Sponsor Follow-up {{ $weekend_name }}
@endsection
@section('teaser_text', 'Support your pescadores in their 4th day')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text')
  Sponsor Follow-up {{ $weekend_name }}
@endsection
@section('content')
<p>The new Pescadores of {{ $weekend_name }} are enjoying their 4th day.</p>

<p>As a sponsor, please remember that your responsibilities don’t end with the weekend. Sponsorship includes the “fourth day” too!</p>

<p>Here are a few things that you can do to encourage him or her to continue what God began at the Tres Dias experience.</p>
<ul>
<li>Encourage your candidate to join a reunion group (perhaps you might consider starting one with them?).</li>
<li>Invite them to attend the Secuela with you. (If you are unable to attend, have someone within the community contact your candidate and invite him/her to attend.)</li>

<li>The next Secuela is {{ $next_secuela_date }}at {{ $next_secuela_venue }}. (Time will likely be 4-6pm.)
  You will find details on the {{ config('site.community_acronym') }} website: <a href="{{ config('app.url') }}" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;">{{ config('app.url') }}</a>
  as they become available and another email will be sent to coordinate pot-luck information, etc.</li>

<li>Encourage him or her to serve on an upcoming weekend if asked.</li>

<li>Explain palanca for future weekends (even if he or she isn’t on a team).<br>
  ie: Palanca includes writing letters for new candidates, participating on the Prayer Wheel, sending Bed Palanca and Team Palanca, Oven Palanca, etc.</li>

<li>Provide guidance on how to sponsor someone if he or she wishes to pass the blessing on.
  Review the sponsor responsibilities with them, using the document sent to you a few weeks ago.
  (It is also available on the {{ config('site.community_acronym') }} website.)</li>
</ul>

<p>Most of all, continue to pray for your Pescadore and encourage him or her in the work that Father began on the weekend.<br>
  As we sat in closing and heard the awesome things God did in each man and woman’s life, it was a reminder of
  what a joy it is to sponsor someone on the weekend; however we don’t want to just abandon them.
  The enemy wants to rob them of the victories they experienced on these past weekends.</p>

<p>Please take the above things to heart and reach out to your candidate. Be a regular ongoing example of grace and love to them throughout their 4th day.</p>

<p>Pre-Weekend Committee</p>
@endsection
