@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title', '[' . config('site.community_acronym') . '] Sponsoring/inviting people to attend Tres Dias')
@section('teaser_text', 'How To Sponsor/Invite People To Attend Tres Dias')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text', 'About Sponsoring For Tres Dias')
@section('content')
  <h2>Dear Fellow Pescadore,</h2>

  <p>It's a good time again to start thinking about whom you might sponsor for the next Tres Dias weekend!
    While sponsorship is a wonderful blessing to provide, there is also a responsibility that goes along with it,
    so be sure to be understand all the aspects it involves.<br>
    <br>
    Attached to this email is the full document of Sponsorship Responsibilities.
    If you have never sponsored someone before, be sure to read through this BEFORE you invite your candidate.</p>

  <p>Here are a few highlights and reminders:<ul>
    <li>It is important for a couple to attend together.
      The men will attend the weekend before the women, but it is important that they are both ready to attend the same {{ config('site.community_acronym') }} weekend.
      Many couples who have attended testify that one of them was ready to attend before the other, but it was
      beneficial to their marriage and relationship to wait until they were ready to attend together.
      If you think your candidate may be the exception to the rule, speak to someone on the Pre-Weekend committee,
      and they will bring the specific situation before the Secretariat for consideration.<br>&nbsp;</li>
    <li>There is a cost of {{ config('site.candidate_fee_text_for_emails') }}. Often the sponsor pays this fee as a gift to the candidate.
      If this is not possible, there is also a scholarship fund available to assist with the costs, or you may wish to share the costs with a co-sponsor.
      Speak to the pre-weekend committee ({{ config('site.email-preweekend-mailbox') }}) for more details.
      If the candidate wants to pay their own fee, this is also acceptable, although uncommon.</li>
  </ul>
  </p>

  <p>Take a few minutes to open the attached document for more information about:<ul>
      <li>Gathering Palanca letters</li>
      <li>Other responsibilities of a Sponsor</li>
  </ul>
  More information about sponsoring (eg. The specifics of where to send a cheque, sample letter templates, etc),
  can be found on the {{ config('site.community_acronym') }} website at: <a href="{{ config('app.url') }}/sponsoring">How Do I Sponsor Someone?</a><br>
  <br>
  The website also has a <a href="{{ config('site.candidate_application_url') }}">candidate sponsorship application </a> for download.<br>
  </p>
  <br>
  <p>
  <em>Pre-Weekend Committee<br>
    {{ config('site.email-preweekend-mailbox') }}</em>
  </p>
@endsection
