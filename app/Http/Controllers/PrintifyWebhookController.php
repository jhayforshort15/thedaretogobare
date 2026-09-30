<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PrintifyWebhookController extends Controller
{
    public function handle(Request $request): Response
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
        $data = $resource['data'] ?? [];

        // Match the order by the Printify order id or our external_id.
        $order = Order::where('printify_order_id', $resource['id'] ?? null)
            ->orWhere('order_number', $data['external_id'] ?? null)
            ->first();

        if (! $order) {
            return response('OK', 200);
        }

        if (in_array($type, ['order:shipment:created', 'order:shipment:delivered'], true)) {
            $shipment = $data['shipments'][0] ?? $data;

            $order->update([
                'status' => $type === 'order:shipment:delivered' ? 'completed' : 'shipped',
                'tracking_number' => $shipment['number'] ?? $shipment['tracking_number'] ?? $order->tracking_number,
                'tracking_url' => $shipment['url'] ?? $shipment['tracking_url'] ?? $order->tracking_url,
            ]);
        }

        return response('OK', 200);
    }
}
