@extends('layouts.app')
@section('title')
  {{ config('site.community_acronym') }} {{ Str::title(config('site.payments_accepts_donations', 'donations')) }}
@endsection

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <div class="card border-success">
          <div class="card-header card-title"><b>{{ Str::title(config('site.payments_accepts_donations', 'donations')) }}</b></div>
          <div class="card-body">
            <p>{{ config('site.community_long_name') }} collects {{ Str::lower(Str::title(config('site.payments_accepts_donations', 'donations'))) }} in order to cover the cost of operating its weekends and mission.</p>
            <p>Please use one of our available payment options below.</p>
            <ol>
              <li class="pb-3">
                Mail a <strong>@lang('locale.cheque')</strong> (payable to “{{ config('site.payments_payable_to', config('site.community_long_name')) }}”)
                to {{ config('site.community_mailing_address_for_payments', config('site.community_mailing_address', '(email us for the address)')) }}.
                Please mark on your @lang('locale.cheque') how the payment is to be allocated.
              </li>

@if( config('site.payments_email_transfer_address') || config('ultreya.paypal-donations-enabled') || config('services.stripe.key') )
                <hr>
                <p>NOTE: the following payments will also reach us but <strong>will incur a 2-3% service charge to
                    {{ config('site.community_acronym') }}</strong>. Please consider adding a small extra amount to offset these fees.</p>
                <hr>
@endif


@includeWhen(config('site.payments_email_transfer_address'), 'payments.email_transfer_canada')

@includeWhen(config('site.payments_paypal_hosted_button_id'), 'payments.paypal_hosted_button')

@includeWhen(config('services.stripe.key'), 'payments.stripe_form')

            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

