<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Package } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Order {
    order_number: string;
    total: number;
    status: string;
    payment_status: string;
    items_count: number;
    placed_at: string | null;
}

defineProps<{ orders: Order[] }>();

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
    <Head title="My Orders | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-5xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">My Account</p>
                <h1 class="font-display text-4xl uppercase md:text-5xl">My Orders</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-5xl px-4 py-12">
                <!-- Orders list -->
                <div v-if="orders.length" class="space-y-4">
                    <Link
                        v-for="order in orders"
                        :key="order.order_number"
                        :href="`/account/orders/${order.order_number}`"
                        class="flex flex-col gap-4 rounded-md border border-neutral-200 p-5 transition hover:border-neutral-900 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="font-heading text-sm font-bold uppercase">Order #{{ order.order_number }}</p>
                            <p class="mt-1 text-xs text-neutral-500">
                                {{ order.placed_at }} · {{ order.items_count }} item{{ order.items_count === 1 ? '' : 's' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span :class="['rounded px-2.5 py-1 font-heading text-[11px] font-bold uppercase tracking-wide', statusClass(order.status)]">
                                {{ order.status }}
                            </span>
                            <span class="font-display text-xl">{{ money(order.total) }}</span>
                        </div>
                    </Link>
                </div>

                <!-- Empty state -->
                <div v-else class="py-20 text-center">
                    <Package class="mx-auto h-12 w-12 text-neutral-300" />
                    <p class="mt-4 font-display text-3xl uppercase text-neutral-400">No orders yet</p>
                    <p class="mt-1 text-sm text-neutral-500">When you place an order, it'll show up here.</p>
                    <Link href="/shop" class="mt-6 inline-block bg-neutral-900 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black">
                        Start Shopping
                    </Link>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
