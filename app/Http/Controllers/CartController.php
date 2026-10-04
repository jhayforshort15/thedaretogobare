<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(): Response
    {
        return Inertia::render('shop/Cart', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::where('is_active', true)->findOrFail($data['product_id']);
        $quantity = $data['quantity'] ?? 1;

        // Require a valid size/colour when the product has variants.
        $variant = $product->resolveVariant($data['size'] ?? null, $data['color'] ?? null);
        if (is_string($variant)) {
            return back()->withErrors(['variant' => $variant]);
        }

        // Block adding out-of-stock items (accounts for what's already in the cart).
        $alreadyInCart = collect($this->cart->items())
            ->firstWhere('row_id', $this->cart->rowId($product->id, $variant))['quantity'] ?? 0;

        if ($product->stock < ($alreadyInCart + $quantity)) {
            return back()->withErrors(['stock' => "Sorry, only {$product->stock} of {$product->name} left in stock."]);
        }

        $this->cart->add($product, $variant, $quantity);

        return back(fallback: '/cart')->with('success', "{$product->name} added to your cart.");
    }

    public function update(Request $request, string $rowId): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($rowId, $data['quantity']);

        return back();
    }

    public function destroy(string $rowId): RedirectResponse
    {
        $this->cart->remove($rowId);

        return back()->with('success', 'Item removed from your cart.');
    }
}
