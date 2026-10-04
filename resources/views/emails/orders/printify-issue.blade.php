<x-mail::message>
# Printify needs attention

**Order #{{ $order->order_number }}** ({{ $order->first_name }} {{ $order->last_name }}, {{ $order->email }}) was paid but has a fulfilment problem:

<x-mail::panel>
{{ $issue }}
</x-mail::panel>

Open the order in the admin to retry or send it to production once the problem is fixed.

<x-mail::button :url="$adminUrl">
View order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
