<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderLookupController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('shop/OrderLookup', [
            'order' => null,
        ]);
    }

    public function find(Request $request): Response
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::where('order_number', $data['order_number'])
            ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])
            ->first();

        if (! $order) {
            // Deliberately vague so order numbers can't be probed.
            throw ValidationException::withMessages([
                'order_number' => 'No order found for that order number and email.',
            ]);
        }

        return Inertia::render('shop/OrderLookup', [
            'order' => $order->toDisplayArray(),
        ]);
    }
}
