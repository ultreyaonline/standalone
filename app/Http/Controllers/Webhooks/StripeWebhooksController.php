<?php

namespace App\Http\Controllers\Webhooks;

use Symfony\Component\HttpFoundation\Response;

class StripeWebhooksController
{
    public function __invoke()
    {
        $payload = request()->all();

        if (!isset($payload['type'])) {
            return response('Webhook Malformed', Response::HTTP_BAD_REQUEST);
        }

        $method = 'when' . \Str::studly(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            $this->$method($payload);
            return response('Webhook Received', Response::HTTP_CREATED);
        }

        info('hook [' . $payload['type'] . '] not found', $payload);
        return response('Webhook Received', Response::HTTP_ACCEPTED);
    }

    /**
     * Handle when a successful charge has gone through on Stripe's end.
     *
     * @param object $payload
     * @return void
     */
    public function whenChargeSucceeded($payload)
    {
        if (!isset($payload['data']['object']['id'])) {
            return response('Webhook Malformed', Response::HTTP_BAD_REQUEST);
        }

        $details = [
            'charge_id' => $payload['data']['object']['id'],
            'amount' => $payload['data']['object']['amount'],
        ];
        info('Stripe Charge Succeeded: ' . json_encode($details));
    }

    /**
     * Record that a refund occurred
     *
     * @param object $payload
     * @return void
     */
    public function whenChargeRefunded($payload)
    {
        if (!isset($payload['data']['object']['id'])) {
            return response('Webhook Malformed', Response::HTTP_BAD_REQUEST);
        }

        $details = [
            'charge_id' => $payload['data']['object']['id'],
            'amount' => $payload['data']['object']['amount'],
            'last4' => $payload['data']['object']['source']['last4'],
            'name' => $payload['data']['object']['source']['name'],
        ];
        info('Stripe Refund: ' . json_encode($details));
    }
}
