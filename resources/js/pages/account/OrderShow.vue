<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';
import OrderDetail from '@/components/store/OrderDetail.vue';

defineProps<{ order: Record<string, unknown> & { order_number: string } }>();
</script>

<template>
    <Head :title="`Order ${order.order_number} | Dare To Go Bare`" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-3xl px-4 py-10">
                <Link href="/account/orders" class="mb-3 inline-flex items-center gap-1 font-heading text-xs font-bold uppercase tracking-wide text-white/60 hover:text-d2gb-gold">
                    <ArrowLeft class="h-3 w-3" /> Back to my orders
                </Link>
                <h1 class="font-display text-4xl uppercase md:text-5xl">Order Details</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-3xl px-4 py-12">
                <OrderDetail :order="(order as any)" />
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
