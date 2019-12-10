@if(config('site.payments_email_transfer_address'))
  <li class="pb-3">Send an <strong>email-money-transfer</strong> to <u>{{ config('site.payments_email_transfer_address') }}</u> <br>(it should Auto-Deposit, but if your bank makes you set a password, use 'tresdias')</li>
@endif
