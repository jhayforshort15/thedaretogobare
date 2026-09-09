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
    public function __construct(protected CartService $cart)
    {
    }

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
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::where('is_active', true)->findOrFail($data['product_id']);

        $this->cart->add($product, $data['size'] ?? null, $data['quantity'] ?? 1);

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
