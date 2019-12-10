@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title')
  Sponsorship Acknowledgement - {{ config('site.community_acronym') }}
@endsection
@section('teaser_text', 'Thank you for inviting a candidate!')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text')
  Sponsorship Acknowledgement for: {{Candidate Name Here}}
@endsection
@section('content')

  Congratulations on inviting a candidate to their upcoming Tres Dias weekend.

  {{ candidate details }}

  Attached is a sample letter you can adapt (copy/paste its contents into an email, or edit the document and print it to send by mail) when requesting palanca letters for your candidate from their family and friends.
  Consider sending to their: friends, parents, children, siblings, pastor, coworkers, prayer meeting friends. Doesn't have to be Christian necessarily, but should be someone who would write something encouraging and positive.

  Also attached is a "Sponsor Responsibilities" document which outlines things like these letters, helping their family with needs they might have during their absence for the weekend, praying for them, and encouraging them
  during their Fourth Day, and inviting them to Secuelas and Reunion Groups so they stay connected with supportive loving Christian friends who understand the beauty of this Tres Dias encounter.

  {{attachments}}
@endsection
