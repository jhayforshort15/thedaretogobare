<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PrintifyService
{
    public function enabled(): bool
    {
        return ! empty(config('services.printify.token')) && ! empty(config('services.printify.shop_id'));
    }

    protected function client(): PendingRequest
    {
        return Http::withToken(config('services.printify.token'))
            ->acceptJson()
            ->baseUrl(rtrim(config('services.printify.base_url'), '/'));
    }

    protected function shopId(): string
    {
        return (string) config('services.printify.shop_id');
    }

    /**
     * Fetch one page of products from the connected shop.
     */
    public function products(int $page = 1, int $limit = 50): array
    {
        $response = $this->client()->get("/shops/{$this->shopId()}/products.json", [
            'page' => $page,
            'limit' => $limit,
        ])->throw();

        return $response->json();
    }

    /**
     * Iterate every product across all pages.
     */
    public function eachProduct(callable $callback): void
    {
        $page = 1;

        do {
            $payload = $this->products($page);
            $data = $payload['data'] ?? [];

            foreach ($data as $product) {
                $callback($product);
            }

            $hasMore = ! empty($payload['next_page_url'] ?? null) || count($data) >= 50;
            $page++;
        } while ($hasMore && ! empty($data));
    }

    /**
     * Submit an order to Printify for fulfilment.
     * Returns the Printify order id, or null if it could not be created.
     */
    public function submitOrder(Order $order): ?string
    {
        $order->loadMissing('items.product', 'items.variant');

        $lineItems = [];
        foreach ($order->items as $item) {
            // Prefer the exact variant chosen; fall back to matching size + colour.
            $variantId = $item->variant?->printify_variant_id
                ?? $item->product?->variants()
                    ->where('size', $item->size)
                    ->where('color', $item->color)
                    ->value('printify_variant_id');

            $printifyProductId = $item->product?->printify_product_id;

            // Skip items that aren't Printify-backed.
            if (! $variantId || ! $printifyProductId) {
                continue;
            }

            $lineItems[] = [
                'product_id' => $printifyProductId,
                'variant_id' => (int) $variantId,
                'quantity' => $item->quantity,
            ];
        }

        if (empty($lineItems)) {
            return null;
        }

        $payload = [
            'external_id' => $order->order_number,
            'label' => $order->order_number,
            'line_items' => $lineItems,
            'shipping_method' => 1, // 1 = standard
            'send_shipping_notification' => false,
            'address_to' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone ?? '',
                'country' => $order->shipping_country,
                'region' => $order->shipping_state ?? '',
                'address1' => $order->shipping_address,
                'city' => $order->shipping_city,
                'zip' => $order->shipping_postal_code ?? '',
            ],
        ];

        $response = $this->client()->post("/shops/{$this->shopId()}/orders.json", $payload)->throw();

        return $response->json('id');
    }

    /**
     * Fetch a single product from the connected shop.
     */
    public function product(string $productId): array
    {
        return $this->client()->get("/shops/{$this->shopId()}/products/{$productId}.json")->throw()->json();
    }

    /**
     * Tell Printify the product is live on our site (clears its "Publishing" state).
     */
    public function publishingSucceeded(string $productId, string $externalId, string $handle): void
    {
        $this->client()->post("/shops/{$this->shopId()}/products/{$productId}/publishing_succeeded.json", [
            'external' => ['id' => $externalId, 'handle' => $handle],
        ])->throw();
    }

    public function publishingFailed(string $productId, string $reason): void
    {
        $this->client()->post("/shops/{$this->shopId()}/products/{$productId}/publishing_failed.json", [
            'reason' => $reason,
        ])->throw();
    }

    /**
     * List shops available to the API token.
     */
    public function shops(): array
    {
        return $this->client()->get('/shops.json')->throw()->json();
    }

    /**
     * List webhooks registered for the connected shop.
     */
    public function webhooks(): array
    {
        return $this->client()->get("/shops/{$this->shopId()}/webhooks.json")->throw()->json();
    }

    /**
     * Register a webhook for the given topic.
     */
    public function createWebhook(string $topic, string $url, ?string $secret = null): array
    {
        $payload = array_filter([
            'topic' => $topic,
            'url' => $url,
            'secret' => $secret,
        ]);

        return $this->client()->post("/shops/{$this->shopId()}/webhooks.json", $payload)->throw()->json();
    }

    /**
     * Delete a webhook. Printify requires the host of the webhook URL.
     */
    public function deleteWebhook(string $webhookId, string $url): void
    {
        $host = urlencode((string) parse_url($url, PHP_URL_HOST));

        $this->client()->delete("/shops/{$this->shopId()}/webhooks/{$webhookId}.json?host={$host}")->throw();
    }
}
