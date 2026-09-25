<?php

namespace App\Services;

use App\Mail\NewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotifier
{
    /**
     * Send the emails triggered when an order is first placed:
     * a confirmation to the customer and a notification to the store owner.
     * Never throws — email problems must not break checkout.
     */
    public function notifyPlaced(Order $order): void
    {
        $this->safely(fn () => Mail::to($order->email)->send(new OrderConfirmationMail($order)), $order, 'confirmation');

        $adminEmail = config('services.store.order_notification_email');
        if (! empty($adminEmail)) {
            $this->safely(fn () => Mail::to($adminEmail)->send(new NewOrderMail($order)), $order, 'admin notification');
        }
    }

    /**
     * Notify the customer that their order status changed.
     */
    public function notifyStatusChanged(Order $order): void
    {
        $this->safely(fn () => Mail::to($order->email)->send(new OrderStatusUpdateMail($order)), $order, 'status update');
    }

    protected function safely(callable $send, Order $order, string $label): void
    {
        try {
            $send();
        } catch (\Throwable $e) {
            Log::warning("Order {$label} email failed", ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }
    }
}
