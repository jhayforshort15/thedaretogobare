<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotifier;
use App\Services\PrintifyService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function __construct(
        protected OrderNotifier $notifier,
        protected PrintifyService $printify,
    ) {
    }

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
                $this->fulfillWithPrintify($order);
            }
        }
    }

    protected function fulfillWithPrintify(Order $order): void
    {
        if (! $this->printify->enabled() || ! empty($order->printify_order_id)) {
            return;
        }

        try {
            $printifyOrderId = $this->printify->submitOrder($order);

            if ($printifyOrderId) {
                // Avoid re-triggering status logic — update quietly.
                $order->updateQuietly(['printify_order_id' => $printifyOrderId]);
            }
        } catch (\Throwable $e) {
            Log::error('Printify order submission failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }
    }
}
