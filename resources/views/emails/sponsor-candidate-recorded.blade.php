@component('mail::message')
# Sponsorship Acknowledgement for: {{ $candidate->names }}

Thank you for sponsoring a candidate for the upcoming Tres Dias weekend {{ $candidate->weekend }}.

We will be inviting your candidate by mail and email using the following information:

@component('mail::panel')
{{ $candidate->names }}

{{ $candidate->address1 }}

@if($candidate->address2){{ $candidate->address2 }}
@endif

{{ $candidate->city }}, {{ $candidate->state }} {{ $candidate->postalcode }}

@if($candidate->man)
  His Cell: {{ $candidate->m_cellphone }}

  His Email: {{ $candidate->m_email }}
@endif

@if($candidate->woman)
  Her Cell: {{ $candidate->w_cellphone }}

  Her Email: {{ $candidate->w_email }}
@endif

@if($candidate->homephone)Home: {{ $candidate->homephone }}
@endif

Church: {{ $candidate->church }}
@endcomponent


##1. Please check the above information.

If there are any errors, please reply to this email with corrections.


## 2. Palanca Letters

 Attached is a sample letter you can adapt (copy/paste its contents into an email, or edit the document and
print it to send by mail) when requesting palanca letters for your candidate from their family and friends.
Consider sending to their: friends, parents, children, siblings, pastor, coworkers, prayer meeting friends.
Doesn't have to be Christian necessarily, but should be someone who would write something encouraging and positive.

##3. Sponsor Responsibilities

 Also attached is a "Sponsor Responsibilities" document which outlines things like these letters, helping their
family with needs they might have during their absence for the weekend, praying for them, and encouraging them
during their Fourth Day, and inviting them to Secuelas and Reunion Groups so they stay connected with supportive
loving Christian friends who understand the beauty of this Tres Dias encounter.


Thanks,<br>
{{ config('site.community_acronym') }} Pre-Weekend Committee
@endcomponent
