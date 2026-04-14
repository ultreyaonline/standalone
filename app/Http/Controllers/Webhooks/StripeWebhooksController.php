<?php

namespace App\Http\Controllers\Webhooks;

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhooksController
{
    public function __invoke()
    {
        $payload   = request()->getContent();
        $sigHeader = request()->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        if (empty($secret)) {
            info('Stripe webhook secret not configured — ignoring webhook.');
            return response('Webhook secret not configured', Response::HTTP_OK);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', Response::HTTP_UNAUTHORIZED);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        $method = 'when' . \Str::studly(str_replace('.', '_', $event->type));

        if (method_exists($this, $method)) {
            $this->$method($event->data->object);
            return response('Webhook Received', Response::HTTP_CREATED);
        }

        info('Stripe hook [' . $event->type . '] not handled');
        return response('Webhook Received', Response::HTTP_ACCEPTED);
    }

    /**
     * Handle when a successful charge has gone through on Stripe's end.
     */
    public function whenChargeSucceeded($object)
    {
        $details = [
            'charge_id' => $object->id,
            'amount'    => $object->amount,
        ];
        info('Stripe Charge Succeeded: ' . json_encode($details));
    }

    /**
     * Record that a refund occurred
     */
    public function whenChargeRefunded($object)
    {
        $details = [
            'charge_id' => $object->id,
            'amount'    => $object->amount,
            'last4'     => $object->source->last4 ?? null,
            'name'      => $object->source->name ?? null,
        ];
        info('Stripe Refund: ' . json_encode($details));
    }

}
