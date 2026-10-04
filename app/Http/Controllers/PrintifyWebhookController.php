<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PrintifyProductImporter;
use App\Services\PrintifyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class PrintifyWebhookController extends Controller
{
    public function handle(Request $request, PrintifyService $printify, PrintifyProductImporter $importer): Response
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
            'order:shipment:created', 'order:shipment:delivered' => $this->updateShipment($type, $resource),
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

    protected function updateShipment(string $type, array $resource): void
    {
        $data = $resource['data'] ?? [];

        // Match the order by the Printify order id or our external_id.
        $order = Order::where('printify_order_id', $resource['id'] ?? null)
            ->orWhere('order_number', $data['external_id'] ?? null)
            ->first();

        if (! $order) {
            return;
        }

        $shipment = $data['shipments'][0] ?? $data;

        $order->update([
            'status' => $type === 'order:shipment:delivered' ? 'completed' : 'shipped',
            'tracking_number' => $shipment['number'] ?? $shipment['tracking_number'] ?? $order->tracking_number,
            'tracking_url' => $shipment['url'] ?? $shipment['tracking_url'] ?? $order->tracking_url,
        ]);
    }
}
