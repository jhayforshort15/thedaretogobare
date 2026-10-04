<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotifier;
use App\Services\PrintifyFulfillment;

class OrderObserver
{
    public function __construct(
        protected OrderNotifier $notifier,
        protected PrintifyFulfillment $fulfillment,
    ) {}

    /**
     * Email the customer whenever the order's fulfilment status changes
     * (covers admin edits and the Stripe webhook marking an order paid),
     * and hand paid orders off to Printify for fulfilment.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $this->notifier->notifyStatusChanged($order);

            if ($order->status === 'paid') {
                $this->fulfillment->fulfil($order);
            }
        }
    }
}
