<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotifier;

class OrderObserver
{
    public function __construct(protected OrderNotifier $notifier)
    {
    }

    /**
     * Email the customer whenever the order's fulfilment status changes
     * (covers admin edits and the Stripe webhook marking an order paid).
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $this->notifier->notifyStatusChanged($order);
        }
    }
}
