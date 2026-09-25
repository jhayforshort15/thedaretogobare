@php
    $money = fn ($n) => '$' . number_format((float) $n, 2);
@endphp

<x-mail::message>
# {{ $headline }}

Hi {{ $order->first_name }},

{{ $body }}

**Order #{{ $order->order_number }}**
Status: **{{ ucfirst($order->status) }}** · Total: {{ $money($order->total) }}

<x-mail::button :url="config('app.url') . '/order-lookup'">
Track Your Order
</x-mail::button>

Questions? Just reply to this email.

No gloves. No excuses.
**{{ config('app.name') }}**
</x-mail::message>
