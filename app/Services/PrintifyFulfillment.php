<?php

namespace App\Services;

use App\Mail\PrintifyIssueMail;
use App\Models\Order;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PrintifyFulfillment
{
    public function __construct(protected PrintifyService $printify) {}

    /**
     * Hand a paid order to Printify: create it there, then send it to production
     * (unless auto-production is off). Safe to call again to retry a failure.
     */
    public function fulfil(Order $order): void
    {
        if (! $this->printify->enabled()) {
            return;
        }

        if (empty($order->printify_order_id)) {
            try {
                $printifyOrderId = $this->printify->submitOrder($order);
            } catch (Throwable $e) {
                $this->fail($order, 'failed', 'Could not create the order in Printify', $e);

                return;
            }

            // No Printify-backed items in this order — nothing to fulfil.
            if (! $printifyOrderId) {
                return;
            }

            $order->updateQuietly([
                'printify_order_id' => $printifyOrderId,
                'printify_status' => 'on-hold',
                'printify_error' => null,
            ]);
        }

        if (config('services.printify.auto_production') && $order->printify_status === 'on-hold') {
            $this->sendToProduction($order);
        }
    }

    /**
     * Approve an on-hold Printify order for printing (this charges the Printify account).
     */
    public function sendToProduction(Order $order): bool
    {
        if (empty($order->printify_order_id)) {
            return false;
        }

        try {
            $this->printify->sendToProduction($order->printify_order_id);
        } catch (Throwable $e) {
            // The order exists in Printify but is still on hold.
            $this->fail($order, 'on-hold', 'Order is in Printify but could not be sent to production', $e);

            return false;
        }

        $order->updateQuietly(['printify_status' => 'sending-to-production', 'printify_error' => null]);

        return true;
    }

    /**
     * Record a Printify status reported by webhook, alerting the admin on problems.
     */
    public function recordStatus(Order $order, string $status): void
    {
        if ($order->printify_status === $status) {
            return;
        }

        $order->updateQuietly(['printify_status' => $status]);

        if (in_array($status, Order::PRINTIFY_PROBLEM_STATUSES, true)) {
            $label = Order::PRINTIFY_STATUSES[$status] ?? $status;
            $this->alertAdmin($order, "Printify marked this order as \"{$label}\". Check it in your Printify dashboard.");
        }
    }

    protected function fail(Order $order, string $status, string $summary, Throwable $e): void
    {
        $detail = $e instanceof RequestException
            ? ($e->response->json('message') ?? '').' '.json_encode($e->response->json('errors') ?? [])
            : $e->getMessage();

        $message = Str::limit(trim("{$summary}: {$detail}"), 1000);

        Log::error('Printify fulfilment failed', ['order' => $order->order_number, 'error' => $message]);

        $order->updateQuietly(['printify_status' => $status, 'printify_error' => $message]);

        $this->alertAdmin($order, $message);
    }

    protected function alertAdmin(Order $order, string $message): void
    {
        $adminEmail = config('services.store.order_notification_email');

        if (empty($adminEmail)) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new PrintifyIssueMail($order, $message));
        } catch (Throwable $e) {
            Log::warning('Printify issue email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }
    }
}
