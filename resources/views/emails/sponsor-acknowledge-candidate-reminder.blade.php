@component('mail::message')
# Please verify registration for: {{ $candidate->names }}

Thank you for inviting a candidate to the upcoming Tres Dias weekend {{ $candidate->weekend }}.

Please confirm the following information by clicking the appropriate button below ...

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


##1. Confirm the above information.

 Please confirm that the above information is correct, as we will be mailing an
invitation to your candidate at this address, and we will be sending a reminder email a couple days
before the weekend. Accuracy of the information is important.
If there are any errors, please click "No, there are errors" and you will be directed to reply to this email with the corrections.

@component('mail::button', ['url' => $confirm_url, 'color' => 'green'])
Yes it is correct
@endcomponent

@component('mail::button', ['url' => 'mailto:' . config('site.email-preweekend-mailbox') . '?subject=Candidate%20Errors&body=Please make the following corrections to ' . $candidate->names . ' registration:', 'color' => 'red'])
No, there are errors
@endcomponent


##2. Palanca Letters

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
