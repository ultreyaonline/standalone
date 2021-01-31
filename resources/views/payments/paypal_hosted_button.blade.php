{{--https://developer.paypal.com/docs/classic/api/buttons/ --}}
@if(config('ultreya.paypal-donations-enabled') && config('site.payments_paypal_hosted_button_id'))
    <li class="pb-3">Payments via PayPal, using debit/credit card or direct from your bank account:
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="{{config('site.payments_paypal_hosted_button_id')}}">
            <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/silver-pill-paypal-34px.png" value="PayPal" alt="Pay with paypal" title="Donate to {{ config('site.community_acronym', 'Tres Dias') }} with PayPal">
        </form>
        You may optionally set up a recurring donation/payment by ticking the checkbox on the PayPal screen.<br>
    </li>
@endif
