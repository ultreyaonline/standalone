<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhooksController extends Controller
{
    /**
     * Handle the incoming Stripe webhook.
     *
     * @return \Response
     */
    public function handle()
    {
        $payload = request()->all();
        $method = $this->eventToMethod($payload['type']);
        if (method_exists($this, $method)) {
            $this->$method($payload);
        } else {
            info('hook ['.$payload['type'].'] not found', request()->all());
            return response()->json(
                ['status' => 'unknown'],
                405
            );
        }
        return response('Webhook Received');
    }
    /**
     * Handle when a successful charge has gone through on Stripe's end.
     *
     * @param  object $payload
     * @return void
     */
    public function whenChargeSucceeded($payload)
    {
        $details = [
            'charge_id' => $payload['data']['object']['id'],
            'amount' => $payload['data']['object']['amount'],
        ];
        info('Stripe Charge Succeeded: ' . json_encode($details));

//        $this->retrieveUser($payload)
//            ->payments()
//            ->create($details);
    }
    /**
     * Record that a refund occurred
     *
     * @param  object $payload
     * @return void
     */
    public function whenChargeRefunded($payload)
    {
        $details = [
            'charge_id' => $payload['data']['object']['id'],
            'amount' => $payload['data']['object']['amount'],
            'last4' => $payload['data']['object']['source']['last4'],
            'name' => $payload['data']['object']['source']['name'],
        ];
        info('Stripe Refund: ' . json_encode($details));
    }
    /**
     * Receive notices about updates to charges
     *
     * @param  object $payload
     * @return void
     */
    public function whenChargeUpdated($payload)
    {
        // ignored for the time being
    }

    /** Payments sent to bank */
    public function whenTransferCreated($payload)
    {
    }
    public function whenTransferPaid($payload)
    {
    }
    public function whenAccountUpdated($payload)
    {
    }
    public function whenPayoutCreated($payload)
    {
    }
    public function whenPayoutPaid($payload)
    {
    }

    /**
     * Handle when a customer's subscription has been deleted.
     *
     * @param array $payload
     */
    public function DISABLEDwhenCustomerSubscriptionDeleted($payload)
    {
        $this->retrieveUser($payload)->deactivate();
    }


    /**
     * Convert a Stripe event name to a method name.
     *
     * @param  string $event
     * @return string
     */
    protected function eventToMethod($event)
    {
        return 'when' . Str::studly(str_replace('.', '_', $event));
    }
    /**
     * Fetch a user by their Stripe id.
     *
     * @param  object $payload
     * @return User
     */
    protected function retrieveUser($payload)
    {
        return User::byStripeId(
            $payload['data']['object']['customer']
        );
    }
}
