<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpressCheckoutController extends Controller
{
    public function __construct(protected StripeService $stripe)
    {
    }

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

        $subtotal = round((float) $product->price * $data['quantity'], 2);
        $shipping = $subtotal >= CartService::FREE_SHIPPING_THRESHOLD ? 0.0 : CartService::FLAT_SHIPPING;
        $total = round($subtotal + $shipping, 2);

        try {
            [$order, $clientSecret] = DB::transaction(function () use ($data, $product, $subtotal, $shipping, $total, $request) {
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
                    'name' => $product->name,
                    'size' => $data['size'] ?? null,
                    'price' => (float) $product->price,
                    'quantity' => $data['quantity'],
                    'subtotal' => $subtotal,
                ]);

                $intent = $this->stripe->createPaymentIntent($order);
                $order->update(['payment_reference' => $intent->id]);

                return [$order, $intent->client_secret];
            });
        } catch (\Throwable $e) {
            Log::error('Express checkout intent failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Could not start payment.'], 500);
        }

        return response()->json([
            'client_secret' => $clientSecret,
            'order_number' => $order->order_number,
            'amount' => $total,
            'return_url' => route('checkout.confirmation', $order->order_number),
        ]);
    }
}
