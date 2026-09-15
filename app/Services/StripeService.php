<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    /**
     * Whether Stripe is configured (a secret key is present).
     */
    public function enabled(): bool
    {
        return ! empty(config('services.stripe.secret'));
    }

    protected function client(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a hosted Stripe Checkout Session for an order and return its URL.
     */
    public function createCheckoutSession(Order $order): Session
    {
        $currency = config('services.stripe.currency', 'usd');

        $lineItems = $order->items->map(fn ($item) => [
            'quantity' => $item->quantity,
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => (int) round(((float) $item->price) * 100),
                'product_data' => [
                    'name' => $item->name.($item->size ? " (Size {$item->size})" : ''),
                ],
            ],
        ])->all();

        if ((float) $order->shipping_cost > 0) {
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => (int) round(((float) $order->shipping_cost) * 100),
                    'product_data' => ['name' => 'Shipping'],
                ],
            ];
        }

        return $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'customer_email' => $order->email,
            'client_reference_id' => (string) $order->id,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
            'success_url' => route('checkout.confirmation', $order->order_number).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index'),
        ]);
    }

    /**
     * Retrieve a Checkout Session (used to reconcile on the success page).
     */
    public function retrieveSession(string $sessionId): Session
    {
        return $this->client()->checkout->sessions->retrieve($sessionId);
    }
}
