@php
    //$key = config('site.payments_stripe_pkey', 'demo');
    //$key = $key !== 'demo' ? $key : config('services.stripe.key');
    $key = config('services.stripe.key');
@endphp
@if(!empty($key))
<li class="pb-3"><strong>Credit Card: </strong>
    <form action="{{route('stripe-payment')}}" method="POST" id="donateForm" class="form-inline row">
        @csrf
        <label for="amount">Payment Amount: &nbsp;$</label>
        <input name="amount" id="amount" type="number" placeholder="amount" style="width:120px;" required min="5" class="form-inline">


        @if(count($currencies) > 1)
        <select name="currency" id="currency">
            @foreach($currencies as $currency)
            <option value="{{ $currency }}">{{ $currency }}</option>
            @endforeach
        </select>
        @else
            <input type="hidden" name="currency" value="{{ $currencies->first() }}">
        @endif
        <select class="form-control mx-2" name="designation">
            <option value="donation" selected>Donation</option>
            <option value="fees-team">Fees: Team</option>
            <option value="fees-candidate">Fees: Candidate</option>
            <option value="scholarship">Scholarship Fund</option>
        </select>
        <input name="stripeToken" id="stripeToken" type="hidden">
        <input name="stripeEmail" id="stripeEmail" type="hidden">
        <button id="donateButton" class="btn btn-info">Pay by Credit Card</button>
    </form>
    <div id="error_explanation" class="bg-danger" style="background-color: yellow"></div>
</li>

@section('page-js')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script>
    var handler = StripeCheckout.configure({
        key: "{{ $key }}",
        name: "Donate to {{ config('site.community_acronym') }}",
        description: "{{ config('site.community_long_name') }}",
        image: "{{ config('site.template_logo_url', '/favicon.ico') }}",
        locale: "auto",
        currency: "usd",
        billingAddress: true,
        email: "{{ optional(auth()->user())->email }}",
        zipCode: true,
        token: function (token, args) {
            $('input#stripeToken').val(token.id);
            $('input#stripeEmail').val(token.email);
            $('#donateForm').append('<input type="hidden" name="stripeCardBrand" value="' + token.card.brand + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardCvcCheck" value="' + token.card.cvc_check + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardAddressLine1Check" value="' + token.card.address_line1_check + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardLast4" value="' + token.card.last4 + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardFunding" value="' + token.card.funding + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardExpMonth" value="' + token.card.exp_month + '">');
            $('#donateForm').append('<input type="hidden" name="stripeCardExpYear" value="' + token.card.exp_year + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingName" value="' + args.billing_name + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressLine1" value="' + args.billing_address_line1 + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressCity" value="' + args.billing_address_city + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressState" value="' + args.billing_address_state + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressZip" value="' + args.billing_address_zip + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressCountry" value="' + args.billing_address_country + '">');
            $('#donateForm').append('<input type="hidden" name="stripeBillingAddressCountryCode" value="' + args.billing_address_country_code + '">');
            $('#donateForm').submit();
        }
    });
    document.getElementById("donateButton").addEventListener("click", function (e) {
        e.preventDefault();

        $('#error_explanation').html('');

        var currency = $('#currency').val();
        if (currency != 'CAD' && currency != 'USD') currency = 'USD';

        var amount = $('input#amount').val();
        amount = amount.replace(/\$/g, '').replace(/\,/g, '');

        amount = parseFloat(amount);

        if (isNaN(amount)) {
            $('#error_explanation').html('<p>Please enter a valid amount.</p>');
        }
        else if (amount < 5.00) {
            $('#error_explanation').html('<p>Donation amount must be at least $5.</p>');
        }
        else {
            amount = amount * 100; // Needs to be an integer!
            handler.open({
                amount: Math.round(amount),
                currency: currency
            })
        }
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function () {
        handler.close();
    });
</script>

@endsection
@endif
