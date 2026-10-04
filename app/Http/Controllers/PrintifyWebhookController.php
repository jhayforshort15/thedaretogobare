<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PrintifyFulfillment;
use App\Services\PrintifyProductImporter;
use App\Services\PrintifyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class PrintifyWebhookController extends Controller
{
    public function handle(Request $request, PrintifyService $printify, PrintifyProductImporter $importer, PrintifyFulfillment $fulfillment): Response
    {
        $secret = config('services.printify.webhook_secret');
        $payload = $request->getContent();

        // Verify HMAC signature when a secret is configured.
        if (! empty($secret)) {
            $signature = $request->header('X-Pfy-Signature', '');
            $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

            if (! hash_equals($expected, $signature)) {
                Log::warning('Printify webhook signature mismatch');

                return response('Invalid signature', 400);
            }
        }

        $event = json_decode($payload, true) ?: [];
        $type = $event['type'] ?? null;
        $resource = $event['resource'] ?? [];

        match ($type) {
            'product:publish:started' => $this->publishProduct($resource['id'] ?? null, $printify, $importer),
            'product:deleted' => $this->unpublishProduct($resource['id'] ?? null),
            'order:updated' => $this->updateOrderStatus($resource, $resource['data']['status'] ?? null, $fulfillment),
            'order:sent-to-production' => $this->updateOrderStatus($resource, 'in-production', $fulfillment),
            'order:shipment:created', 'order:shipment:delivered' => $this->updateShipment($type, $resource, $fulfillment),
            default => null,
        };

        return response('OK', 200);
    }

    /**
     * "Publish" was clicked in Printify: pull the product in and report back.
     */
    protected function publishProduct(?string $productId, PrintifyService $printify, PrintifyProductImporter $importer): void
    {
        if (! $productId) {
            return;
        }

        try {
            $product = $importer->import($printify->product($productId));

            if (! $product) {
                $printify->publishingFailed($productId, 'Product has no enabled variants.');

                return;
            }

            $printify->publishingSucceeded($productId, (string) $product->id, route('shop.show', $product));
        } catch (Throwable $e) {
            Log::error('Printify publish failed', ['product' => $productId, 'error' => $e->getMessage()]);

            rescue(fn () => $printify->publishingFailed($productId, 'Store could not import the product.'), report: false);
        }
    }

    /**
     * Product deleted in Printify: hide it rather than delete, so past orders keep their product.
     */
    protected function unpublishProduct(?string $productId): void
    {
        if ($productId) {
            Product::where('printify_product_id', $productId)->update(['is_active' => false]);
        }
    }

    /**
     * Match the order by the Printify order id or our external_id.
     */
    protected function findOrder(array $resource): ?Order
    {
        $printifyId = $resource['id'] ?? null;
        $externalId = $resource['data']['external_id'] ?? null;

        if (! $printifyId && ! $externalId) {
            return null;
        }

        return Order::query()
            ->when($printifyId, fn ($q) => $q->where('printify_order_id', $printifyId))
            ->when($externalId, fn ($q) => $q->orWhere('order_number', $externalId))
            ->first();
    }

    protected function updateOrderStatus(array $resource, ?string $status, PrintifyFulfillment $fulfillment): void
    {
        $order = $this->findOrder($resource);

        if ($order && $status) {
            $fulfillment->recordStatus($order, $status);
        }
    }

    protected function updateShipment(string $type, array $resource, PrintifyFulfillment $fulfillment): void
    {
        $data = $resource['data'] ?? [];
        $order = $this->findOrder($resource);

        if (! $order) {
            return;
        }

        $fulfillment->recordStatus($order, $type === 'order:shipment:delivered' ? 'delivered' : 'fulfilled');

        $shipment = $data['shipments'][0] ?? $data;

        $order->update([
            'status' => $type === 'order:shipment:delivered' ? 'completed' : 'shipped',
            'tracking_number' => $shipment['number'] ?? $shipment['tracking_number'] ?? $order->tracking_number,
            'tracking_url' => $shipment['url'] ?? $shipment['tracking_url'] ?? $order->tracking_url,
        ]);
    }
}
