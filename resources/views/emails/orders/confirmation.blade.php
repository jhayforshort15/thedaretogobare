@php
    $money = fn ($n) => '$' . number_format((float) $n, 2);
@endphp

<x-mail::message>
# Thanks for your order, {{ $order->first_name }}!

We've received your order and it's now being processed. Here's a summary:

**Order #{{ $order->order_number }}**
Placed {{ $order->created_at?->format('M j, Y') }}

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->name }}{{ $item->size ? ' (Size ' . $item->size . ')' : '' }} | {{ $item->quantity }} | {{ $money($item->price) }} |
@endforeach
</x-mail::table>

**Subtotal:** {{ $money($order->subtotal) }}
**Shipping:** {{ (float) $order->shipping_cost === 0.0 ? 'FREE' : $money($order->shipping_cost) }}
@if ((float) $order->tax > 0)
**Tax:** {{ $money($order->tax) }}
@endif
**Total: {{ $money($order->total) }}**

<x-mail::panel>
**Shipping to:**
{{ $order->first_name }} {{ $order->last_name }}
{{ $order->shipping_address }}
{{ $order->shipping_city }}@if ($order->shipping_state), {{ $order->shipping_state }}@endif @if ($order->shipping_postal_code) {{ $order->shipping_postal_code }}@endif
{{ $order->shipping_country }}
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/order-lookup'">
Track Your Order
</x-mail::button>

Questions? Just reply to this email.

No gloves. No excuses.
**{{ config('app.name') }}**
</x-mail::message>
