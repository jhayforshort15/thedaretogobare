<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();

        // Verify the signature when a webhook secret is configured.
        if (! empty($secret)) {
            try {
                $event = Webhook::constructEvent($payload, $request->header('Stripe-Signature'), $secret);
            } catch (\Throwable $e) {
                Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);

                return response('Invalid signature', 400);
            }
        } else {
            $event = json_decode($payload);
        }

        $type = $event->type ?? null;

        if ($type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? $session->client_reference_id ?? null;

            if ($orderId && ($session->payment_status ?? null) === 'paid') {
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'payment_status' => 'paid',
                        'payment_method' => 'stripe',
                        'payment_reference' => $session->id ?? $order->payment_reference,
                    ]);
                }
            }
        }

        return response('OK', 200);
    }
}
