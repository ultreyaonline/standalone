@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title', 'Welcome to the ' . config('site.community_acronym') . ' Community')
@section('teaser_text', 'Welcome to your Fourth Day!')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text', 'Welcome to the ' . config('site.community_acronym') . ' Community')
@section('content')
  <h2>Dear {{ $u->first }},</h2>

  <h3>Welcome to your Fourth Day!</h3>

  <p>It's now been a week since you made your Three Days weekend.
  The Tres Dias community continues to pray for you as you let Father continue the work He did in your heart last weekend.
  We encourage you to regularly call to remembrance the things Father showed you: about Him and about yourself.
  Keep refreshing your mind with those truths, so that you can live in that reality instead of getting caught up in
  the day-to-day "things" life throws at us.  You are deeply loved and respected and valued, no matter what the
  circumstances around you might suggest.</p>

<p>We encourage you to connect with other Tres Dias pescadores by attending our 2-3 Secuela events each year,
  and regular reunion groups held in your own local area (or starting your own reunion group!),
  and of course serving on future weekends!</p>

<p>The key to successfully remembering the good that God has done for you is in regular fellowship with others who
  know their freedom and identity in Christ. Your fellow Tres Dias pescadores understand the journey you've taken,
  and welcome the opportunity to share encouragement with you, and you with them.</p>


<strong>Secuela: {{ $secuela->start_datetime }}</strong>
<p>Our next Secuela will be held at {{ $secuela->location_name }}.
  It's usually a mid-afternoon event with a pot-luck "early supper" following a worship and sharing time.
  Details to follow, by email.</p>


  <strong>Serving on {{ $next_weekend }}</strong>
  <p>In the meantime, some of you have expressed interest in serving on {{ $next_weekend }}.
  We're excited to have you join us in serving our fellow brothers and sisters as they encounter for themselves
  a fresh experience of how richly and unconditionally God loves them!
  The leadership teams for the men's and women's weekends are already hard at work with planning.
  If you're interested in serving on the next {{ $u->gender == 'M' ? 'Men' : 'Women' }}'s weekend,
    email {{ $rector_name }} and let {{ $u->gender == 'M' ? 'him' : 'her' }} know.<br>
<br>
  It's also recommended that you login to the website and update your profile with
  whatever skills you might want to offer when serving on a weekend.
  (We'll send you website login credentials in a separate email.)
</p>

  <strong>REUNION GROUPS</strong>
  <p>If you'd like to start a reunion group, we encourage you to include your Sponsor, talk with them about it,
    connect with the other people you met on your weekend, and let us know if you need any guidance or suggestions.
    And please let us know (reply to this email) if you do start a reunion group, so we can pray for your group,
    and maybe even let others know about it too!</p>

<p>Rejoicing with you, as you celebrate your freedom in Christ!</p>

<p>The {{ config('site.community_long_name') }} Community</p>
@endsection
