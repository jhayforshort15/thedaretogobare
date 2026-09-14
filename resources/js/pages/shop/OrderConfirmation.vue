<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface OrderItem { name: string; size: string | null; price: number; quantity: number; subtotal: number }
interface Order {
    order_number: string; email: string; first_name: string;
    status: string; payment_status: string;
    subtotal: number; shipping_cost: number; tax: number; total: number;
    items: OrderItem[];
}

defineProps<{ order: Order }>();

const money = (n: number) => `$${n.toFixed(2)}`;
</script>

<template>
    <Head :title="`Order ${order.order_number} | Dare To Go Bare`" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-3xl px-4 py-14">
                <div class="text-center">
                    <CheckCircle2 class="mx-auto h-14 w-14 text-green-600" />
                    <h1 class="mt-4 font-display text-4xl uppercase md:text-5xl">Thank You, {{ order.first_name }}!</h1>
                    <p class="mt-2 text-neutral-600">Your order has been placed. A confirmation was sent to <strong>{{ order.email }}</strong>.</p>
                    <p class="mt-4 inline-block rounded bg-neutral-100 px-4 py-2 font-heading text-sm font-bold uppercase tracking-wide">
                        Order #{{ order.order_number }}
                    </p>
                    <p class="mt-3 text-xs uppercase tracking-wide text-amber-600">
                        Status: {{ order.status }} · Payment: {{ order.payment_status }}
                    </p>
                </div>

                <div class="mt-10 rounded-md border border-neutral-200">
                    <div class="border-b border-neutral-200 px-6 py-4">
                        <h2 class="font-heading text-sm font-bold uppercase">Order Details</h2>
                    </div>
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
                    <div class="space-y-2 border-t border-neutral-200 px-6 py-4 text-sm">
                        <div class="flex justify-between"><span class="text-neutral-500">Subtotal</span><span>{{ money(order.subtotal) }}</span></div>
                        <div class="flex justify-between"><span class="text-neutral-500">Shipping</span><span>{{ order.shipping_cost === 0 ? 'FREE' : money(order.shipping_cost) }}</span></div>
                        <div v-if="order.tax > 0" class="flex justify-between"><span class="text-neutral-500">Tax</span><span>{{ money(order.tax) }}</span></div>
                        <div class="flex justify-between border-t border-neutral-200 pt-2 font-heading text-base font-bold uppercase">
                            <span>Total</span><span>{{ money(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <Link href="/shop" class="inline-block bg-neutral-900 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black">
                        Continue Shopping
                    </Link>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
