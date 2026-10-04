<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderNotifier;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpressCheckoutController extends Controller
{
    public function __construct(protected StripeService $stripe, protected OrderNotifier $notifier) {}

    /**
     * Create a pending order + PaymentIntent for a single-product express purchase.
     * Called by the Express Checkout Element (Apple Pay / Google Pay / Link).
     */
    public function intent(Request $request): JsonResponse
    {
        if (! $this->stripe->enabled()) {
            return response()->json(['message' => 'Payments are not configured.'], 422);
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_state' => ['nullable', 'string', 'max:255'],
            'shipping_postal_code' => ['nullable', 'string', 'max:50'],
            'shipping_country' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::where('is_active', true)->findOrFail($data['product_id']);

        $variant = $product->resolveVariant($data['size'] ?? null, $data['color'] ?? null);
        if (is_string($variant)) {
            return response()->json(['message' => $variant], 422);
        }

        if ($product->stock < $data['quantity']) {
            return response()->json(['message' => "Only {$product->stock} left in stock."], 422);
        }

        $unitPrice = $variant ? $variant->priceFor($product) : (float) $product->price;
        $subtotal = round($unitPrice * $data['quantity'], 2);
        $shipping = $subtotal >= CartService::FREE_SHIPPING_THRESHOLD ? 0.0 : CartService::FLAT_SHIPPING;
        $total = round($subtotal + $shipping, 2);

        try {
            [$order, $clientSecret] = DB::transaction(function () use ($data, $product, $variant, $unitPrice, $subtotal, $shipping, $total, $request) {
                $order = Order::create([
                    'order_number' => 'D2GB-'.strtoupper(Str::random(8)),
                    'user_id' => $request->user()?->id,
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'] ?? '',
                    'phone' => $data['phone'] ?? null,
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city' => $data['shipping_city'],
                    'shipping_state' => $data['shipping_state'] ?? null,
                    'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                    'shipping_country' => $data['shipping_country'],
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'tax' => 0,
                    'total' => $total,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => 'stripe',
                ]);

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $product->name,
                    'size' => $variant?->size,
                    'color' => $variant?->color,
                    'price' => $unitPrice,
                    'quantity' => $data['quantity'],
                    'subtotal' => $subtotal,
                ]);

                $product->decrementStock($variant, $data['quantity']);

                $intent = $this->stripe->createPaymentIntent($order);
                $order->update(['payment_reference' => $intent->id]);

                return [$order, $intent->client_secret];
            });
        } catch (\Throwable $e) {
            Log::error('Express checkout intent failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Could not start payment.'], 500);
        }

        $this->notifier->notifyPlaced($order);

        return response()->json([
            'client_secret' => $clientSecret,
            'order_number' => $order->order_number,
            'amount' => $total,
            'return_url' => route('checkout.confirmation', $order->order_number),
        ]);
    }
}
