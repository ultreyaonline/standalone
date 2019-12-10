@component('mail::message')
# Candidate Registration: {{ $candidate->names }}

The following candidate/s have been registered for {{ $candidate->weekend }}.

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

Sponsor: {{ $sponsors }}
@endcomponent



This is an automated message.
@endcomponent
