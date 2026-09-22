@php
    $money = fn ($n) => '$' . number_format((float) $n, 2);
@endphp

<x-mail::message>
# 🥊 New Order Received

**Order #{{ $order->order_number }}** was just placed.

**Total: {{ $money($order->total) }}**
Payment: {{ ucfirst($order->payment_status) }} · Status: {{ ucfirst($order->status) }}
Placed {{ $order->created_at?->format('M j, Y g:i A') }}

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->name }}{{ $item->size ? ' (Size ' . $item->size . ')' : '' }} | {{ $item->quantity }} | {{ $money($item->price) }} |
@endforeach
</x-mail::table>

**Subtotal:** {{ $money($order->subtotal) }} · **Shipping:** {{ (float) $order->shipping_cost === 0.0 ? 'FREE' : $money($order->shipping_cost) }}

<x-mail::panel>
**Customer**
{{ $order->first_name }} {{ $order->last_name }} — {{ $order->email }}@if ($order->phone) — {{ $order->phone }}@endif

**Ship to**
{{ $order->shipping_address }}, {{ $order->shipping_city }}@if ($order->shipping_state), {{ $order->shipping_state }}@endif @if ($order->shipping_postal_code) {{ $order->shipping_postal_code }}@endif, {{ $order->shipping_country }}
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/admin/orders'">
View in Admin
</x-mail::button>

**{{ config('app.name') }}** — admin notification
</x-mail::message>
