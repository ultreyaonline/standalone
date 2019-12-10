@component('mail::message')
# Payment Acknowledgement for: {{ ucwords($payer->name) }}

Thank you for your online payment to {{ config('site.community_long_name') }}.

@component('mail::panel')
Name: {{ ucwords($payer->name) }}

Amount: ${{ number_format($charge['amount']/100, 2) }} {{ strtoupper($charge['currency']) }}

With: {{ $charge['source']['brand'] }} {{ $charge['source']['last4'] }}

For: {{ $charge['description'] }}
@endcomponent

Regards,<br>
{{ config('site.community_acronym') }} Finance Committee
@endcomponent
