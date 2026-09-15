<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cart, protected StripeService $stripe)
    {
    }

    public function index(Request $request): Response|RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()->route('cart.index');
        }

        $user = $request->user();

        return Inertia::render('shop/Checkout', [
            'items' => $this->cart->items(),
            'summary' => [
                'subtotal' => $this->cart->subtotal(),
                'shipping' => $this->cart->shipping(),
                'tax' => $this->cart->tax(),
                'total' => $this->cart->total(),
                'free_shipping_threshold' => CartService::FREE_SHIPPING_THRESHOLD,
            ],
            'prefill' => $user ? [
                'email' => $user->email,
                'first_name' => Str::before($user->name, ' '),
                'last_name' => Str::contains($user->name, ' ') ? Str::after($user->name, ' ') : '',
            ] : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_state' => ['nullable', 'string', 'max:255'],
            'shipping_postal_code' => ['nullable', 'string', 'max:50'],
            'shipping_country' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = $this->cart->items();

        $order = DB::transaction(function () use ($data, $items, $request) {
            $order = Order::create([
                ...$data,
                'order_number' => 'D2GB-'.strtoupper(Str::random(8)),
                'user_id' => $request->user()?->id,
                'subtotal' => $this->cart->subtotal(),
                'shipping_cost' => $this->cart->shipping(),
                'tax' => $this->cart->tax(),
                'total' => $this->cart->total(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        // If Stripe is configured, send the customer to hosted Checkout to pay.
        // The cart is cleared once payment is confirmed (success page + webhook).
        if ($this->stripe->enabled()) {
            try {
                $order->load('items');
                $session = $this->stripe->createCheckoutSession($order);
                $order->update([
                    'payment_method' => 'stripe',
                    'payment_reference' => $session->id,
                ]);

                return Inertia::location($session->url);
            } catch (\Throwable $e) {
                Log::error('Stripe checkout session failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);

                return back()->withErrors(['payment' => 'We could not start the payment. Please try again.']);
            }
        }

        // No payment gateway configured yet: place as pending/unpaid.
        $this->cart->clear();

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(Request $request, string $orderNumber): Response
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        // Reconcile with Stripe when returning from Checkout.
        // (The webhook is authoritative; this makes the success page instant.)
        $sessionId = $request->query('session_id');
        $paymentIntentId = $request->query('payment_intent');

        if ($this->stripe->enabled() && $order->payment_status !== 'paid') {
            try {
                // Hosted Checkout redirect
                if ($sessionId) {
                    $session = $this->stripe->retrieveSession($sessionId);
                    if (($session->metadata->order_id ?? null) == (string) $order->id && $session->payment_status === 'paid') {
                        $order->update(['status' => 'paid', 'payment_status' => 'paid']);
                        $this->cart->clear();
                        $order->refresh();
                    }
                }

                // Embedded Express Checkout Element (PaymentIntent)
                if ($paymentIntentId && $order->payment_status !== 'paid') {
                    $intent = $this->stripe->retrievePaymentIntent($paymentIntentId);
                    if (($intent->metadata->order_id ?? null) == (string) $order->id && $intent->status === 'succeeded') {
                        $order->update(['status' => 'paid', 'payment_status' => 'paid']);
                        $order->refresh();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Stripe reconcile failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            }
        }

        return Inertia::render('shop/OrderConfirmation', [
            'order' => [
                'order_number' => $order->order_number,
                'email' => $order->email,
                'first_name' => $order->first_name,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'tax' => (float) $order->tax,
                'total' => (float) $order->total,
                'items' => $order->items->map(fn ($i) => [
                    'name' => $i->name,
                    'size' => $i->size,
                    'price' => (float) $i->price,
                    'quantity' => $i->quantity,
                    'subtotal' => (float) $i->subtotal,
                ]),
            ],
        ]);
    }
}
