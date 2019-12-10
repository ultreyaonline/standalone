@extends('emails.layout')

{{-- NOTE: sections include:  subject, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}

@section('title', 'Website access to the ' . config('site.community_acronym') . ' Community')
@section('teaser_text', 'Login details for the ' . config('site.community_acronym') . ' website')
@section('view_in_browser__url', '')
@section('view_in_browser__text', '')
@section('headline_text', config('site.community_acronym') . ' Website Access Information')
@section('content')
  <h2>Dear {{ $u->first }},</h2>

  <p>This email is to let you know how to access the {{ config('site.community_acronym') }} website, and keep in touch with our community!</p>

  <p>Our website shows (publicly visible) dates of upcoming Secuelas, {{ config('site.community_acronym') }} Weekends, and application forms for you to use to sponsor your friends to come to a weekend.</p>

  <p>Additionally, if you log in, you will have access to our community roster, details on how the {{ config('site.community_long_name') }} Community works,
    information about how to sponsor people, access to Team Preparation tools for future weekends, information on who the Rector of the next weekend is,
    signing up for the Prayer Wheel and much more! ... including a list of all those crazy Spanish words!
    We encourage you to keep your profile up to date, and add a picture of yourself!</p>
  <br>

  <h3>Website Login</h3>
  <p>
    You can get a password to log in by visiting: <a href="{{ config('app.url') }}/pescadore">{{ config('app.url') }}/pescadore</a><br>
    (or use Forgot Password from the regular login page).<br>
@if(config('site.members_username_default') === 'Email Address')
    <br>
    For most people, your username is your email address.
    NOTE: But if you share an email address with your spouse, your username is your FirstLast name, such as: FredSmith or MarySmith
@endif
  </p>
  <p><br>
    <strong>Your present login username is: {{ $u->username }}</strong><br>
  </p>
  <br>
  <h3>STEPS TO TAKE NOW:</h3>
  <p>
    1. Please login now and click on the link to "Update My Profile" in the top right after you login. Particularly, make sure your email and mobile numbers are correct.<br>
    Your street address is helpful for community members to reach you if you wish. It is also helpful if we are sending tax receipts for eligible donations.<br>
    Your city/state/province details are helpful for Rectors when assembling teams.<br>
    <br>
      2. Would you also <strong>please post a photo</strong>? This is helpful for other members, as well as for Rectors when building teams and trying to remember who they met at the last weekend.
    <br>
    3. The only "personal" information about you on the site is what you see right there on that page.<br>
    (Your name, sponsor, contact details, and any weekends you've "served" on, if any.)<br>
    <br>
    4. Also on the home page is a link to Sign up for the Prayer Wheel.<br>
    Click it and choose the next {{ config('site.community_acronym') }} weekend from the pulldown menu, and pick one (or more!) slots to sign up for.<br>
    The idea is that you would commit to pray during that hour ... for whatever God places on your heart to pray, for the men/women attending that weekend.<br>
    <br>
    5. In your Contact Information screen you can optionally also mention what skills you have to offer or how you'd be interested in serving.<br>
  </p>

  <p>If you have difficulties accessing the {{ config('site.community_acronym') }} website, let us know (reply to this email) and we'll do our best to help you!</p>
  <br>

@if(config('site.facebook_page') && config('site.facebook_page', 'https://www.facebook.com/groups/') !== 'https://www.facebook.com/groups/')
  <h3>Facebook</h3>
  <p>You will receive an invitation to our private {{ config('site.community_acronym') }} Facebook Group within about a week of finishing your Tres Dias weekend.
    This is an ongoing place for you to share prayer requests, encouraging words, and find out the latest news of upcoming Secuelas and serenades.
    It helps extend your interaction with your fellow Tres Dias participants beyond the weekend, all year round.</p>
  <p>We will send this invite to the same email address with which you registered to attend {{ config('site.community_acronym') }}.
    If your Facebook email address is different, please login to your {{ config('site.community_acronym') }} profile and update your email address there.</p>
  <p>If you do not receive an invite, please visit <a href="{{ config('site.facebook_page') }}" target="_blank">{{ config('site.facebook_page') }}</a> and request to join.</p>
  <br>
@endif

  <h3>Mailing List</h3>
  <p>We have also added you to our mailing list so that you are notified of {{ config('site.community_acronym') }} information.<br>
    <em>THIS IS OUR MAIN METHOD OF COMMUNICATING TO YOU.</em><br>
    <br>
    (This list gets pretty busy as each {{ config('site.community_acronym') }} "weekend" approaches, but we are very careful to only send emails when relevant. Our goal is to keep you informed, but not fill your inbox.)<br>
    <br>
    You can control which kinds of emails you receive by logging into the website and updating your profile.
  </p>
  <br>

  <h3>A note about security</h3>
  <p style="font-size: small">(We use encryption to store passwords, so they're not retrievable. You can always reset it using the Forgot Password option on the login page.)</p>

@endsection
