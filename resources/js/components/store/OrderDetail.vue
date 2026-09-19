<script setup lang="ts">
interface OrderItem { name: string; size: string | null; price: number; quantity: number; subtotal: number }
interface Order {
    order_number: string;
    placed_at: string | null;
    status: string;
    payment_status: string;
    email: string;
    first_name: string;
    last_name: string;
    phone: string | null;
    shipping: { address: string; city: string; state: string | null; postal_code: string | null; country: string };
    subtotal: number;
    shipping_cost: number;
    tax: number;
    total: number;
    items: OrderItem[];
}

defineProps<{ order: Order }>();

const money = (n: number) => `$${n.toFixed(2)}`;

const statusClass = (status: string) => {
    switch (status) {
        case 'paid':
        case 'completed':
            return 'bg-green-100 text-green-700';
        case 'shipped':
            return 'bg-blue-100 text-blue-700';
        case 'cancelled':
            return 'bg-red-100 text-red-700';
        default:
            return 'bg-amber-100 text-amber-700';
    }
};
</script>

<template>
    <div class="rounded-md border border-neutral-200">
        <!-- Header -->
        <div class="flex flex-col gap-3 border-b border-neutral-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-heading text-sm font-bold uppercase">Order #{{ order.order_number }}</p>
                <p class="mt-1 text-xs text-neutral-500">Placed {{ order.placed_at }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span :class="['rounded px-2.5 py-1 font-heading text-[11px] font-bold uppercase tracking-wide', statusClass(order.status)]">{{ order.status }}</span>
                <span :class="['rounded px-2.5 py-1 font-heading text-[11px] font-bold uppercase tracking-wide', order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-neutral-200 text-neutral-600']">{{ order.payment_status }}</span>
            </div>
        </div>

        <!-- Items -->
        <ul class="divide-y divide-neutral-100">
            <li v-for="(item, i) in order.items" :key="i" class="flex items-center justify-between px-6 py-4">
                <div>
                    <p class="text-sm font-semibold">{{ item.name }}</p>
                    <p class="text-xs text-neutral-500">
                        <span v-if="item.size">Size {{ item.size }} · </span>Qty {{ item.quantity }} × {{ money(item.price) }}
                    </p>
                </div>
                <span class="text-sm font-semibold">{{ money(item.subtotal) }}</span>
            </li>
        </ul>

        <!-- Totals -->
        <div class="space-y-2 border-t border-neutral-200 px-6 py-4 text-sm">
            <div class="flex justify-between"><span class="text-neutral-500">Subtotal</span><span>{{ money(order.subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-neutral-500">Shipping</span><span>{{ order.shipping_cost === 0 ? 'FREE' : money(order.shipping_cost) }}</span></div>
            <div v-if="order.tax > 0" class="flex justify-between"><span class="text-neutral-500">Tax</span><span>{{ money(order.tax) }}</span></div>
            <div class="flex justify-between border-t border-neutral-200 pt-2 font-heading text-base font-bold uppercase"><span>Total</span><span>{{ money(order.total) }}</span></div>
        </div>

        <!-- Shipping + contact -->
        <div class="grid gap-6 border-t border-neutral-200 px-6 py-4 text-sm sm:grid-cols-2">
            <div>
                <p class="mb-1 font-heading text-xs font-bold uppercase text-neutral-500">Ship to</p>
                <p class="font-semibold">{{ order.first_name }} {{ order.last_name }}</p>
                <p class="text-neutral-600">{{ order.shipping.address }}</p>
                <p class="text-neutral-600">
                    {{ order.shipping.city }}<template v-if="order.shipping.state">, {{ order.shipping.state }}</template>
                    <template v-if="order.shipping.postal_code"> {{ order.shipping.postal_code }}</template>
                </p>
                <p class="text-neutral-600">{{ order.shipping.country }}</p>
            </div>
            <div>
                <p class="mb-1 font-heading text-xs font-bold uppercase text-neutral-500">Contact</p>
                <p class="text-neutral-600">{{ order.email }}</p>
                <p v-if="order.phone" class="text-neutral-600">{{ order.phone }}</p>
            </div>
        </div>
    </div>
</template>
