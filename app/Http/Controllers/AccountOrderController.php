<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->withCount('items')
            ->latest()
            ->get(['id', 'order_number', 'total', 'status', 'payment_status', 'created_at'])
            ->map(fn (Order $order) => [
                'order_number' => $order->order_number,
                'total' => (float) $order->total,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'items_count' => $order->items_count,
                'placed_at' => $order->created_at?->format('M j, Y'),
            ]);

        return Inertia::render('account/Orders', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, string $orderNumber): Response
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return Inertia::render('account/OrderShow', [
            'order' => $order->toDisplayArray(),
        ]);
    }
}
