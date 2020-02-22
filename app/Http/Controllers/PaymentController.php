<?php

namespace App\Http\Controllers;

use App\Mail\PaymentOnline_Confirmation;
use App\Secretariat;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function displayForm()
    {
        $currencies = collect(explode('+', config('site.payments_stripe_currencies', 'USD')));

        $member = auth()->user();
        return view('payments.page', compact('member', 'currencies'));
    }

    public function displayThanks()
    {
        return view('payments.stripe_thanks');
    }

    public function create(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $currencies = collect(explode('+', config('site.payments_stripe_currencies', 'USD')));

        $this->validate($request, [
            'stripeToken' => 'required',
            'stripeEmail' => 'email',
        ]);

        // Get the credit card details submitted by the form
        $token       = $request->get('stripeToken');
        $stripeEmail = $request->get('stripeEmail', optional(auth()->user())->email);
// request fields available::
//        stripeCardBrand
//        stripeCardCvcCheck
//        stripeCardAddressLine1Check
//        stripeCardLast4
//        stripeCardFunding
//        stripeCardExpMonth
//        stripeCardExpYear
//        stripeBillingName
//        stripeBillingAddressLine1
//        stripeBillingAddressCity
//        stripeBillingAddressState
//        stripeBillingAddressZip
//        stripeBillingAddressCountry
//        stripeBillingAddressCountryCode

        // read selected currency, and make sure it's one we support
        $currency = $request->get('currency');
        if (!$currencies->contains($currency)) {
            $currency = 'USD';
        }

        // amount must be in cents
        $amount = $request->get('amount') * 100;

        $descriptions = collect([
            'donation'       => 'Donation to ' . config('site.community_long_name'),
            'fees-team'      => 'Team Fees',
            'fees-candidate' => 'Candidate Sponsorship',
            'scholarship'    => 'Scholarship Fund',
        ]);

        $designation = $request->get('designation', 'donation');
        if (!$descriptions->has($designation)) {
            $designation = 'donation';
        }

        // Create a charge: this will actually charge the person's card
        try {
            $charge = \Stripe\Charge::create([
                'amount'        => $amount, // Amount in cents
                'currency'      => $currency,
                'source'        => $token,
                'receipt_email' => $stripeEmail,
                'description'   => $descriptions[$designation],
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json(
                ['status' => $e->getMessage()],
                422
            );
        }

        $name = $request->get('stripeBillingName', $stripeEmail);

        $payer = auth()->user();
        if (!$payer) {
            [$first] = explode(' ', $name);
            $last = substr($name, strlen($first));
            $payer = new User([
                'email' => $stripeEmail,
                'name'  => $name,
                'first'  => $first,
                'last'  => $last,
            ]);
        }

        // Receipt to Payer
        Mail::to($payer)
            ->queue(new PaymentOnline_Confirmation($payer, $charge));

        // Receipt to Finance
        Mail::to(config('site.email-finance-mailbox', config('site.email_general')), config('site.community_acronym') . ' Finance')
            ->queue(new PaymentOnline_Confirmation($payer, $charge, '[COPY] Payment acknowledgement for ' . $payer->name));

        flash('Thank you. Your payment has been completed.', 'success');

        return $this->displayThanks();
    }
}
