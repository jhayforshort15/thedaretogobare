<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Minus, Plus, Trash2, ArrowRight, ShoppingBag } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface CartItem {
    row_id: string;
    product_id: number;
    name: string;
    slug: string;
    image: string | null;
    size: string | null;
    price: number;
    quantity: number;
    subtotal: number;
}

defineProps<{ items: CartItem[]; subtotal: number }>();

const money = (n: number) => `$${n.toFixed(2)}`;

function setQty(item: CartItem, qty: number) {
    if (qty < 1) return;
    router.patch(`/cart/${item.row_id}`, { quantity: qty }, { preserveScroll: true });
}

function remove(item: CartItem) {
    router.delete(`/cart/${item.row_id}`, {
        preserveScroll: true,
        onSuccess: () => toast.success('Item removed from your cart.'),
    });
}

const SHIPPING_THRESHOLD = 150;
</script>

<template>
    <Head title="Your Cart | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <!-- Page header -->
        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Your Cart</p>
                <h1 class="font-display text-5xl uppercase md:text-6xl">Shopping Cart</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <!-- Empty state -->
                <div v-if="!items.length" class="py-20 text-center">
                    <ShoppingBag class="mx-auto h-12 w-12 text-neutral-300" />
                    <p class="mt-4 font-display text-3xl uppercase text-neutral-400">Your cart is empty</p>
                    <p class="mt-2 text-sm text-neutral-500">Add some gear and come back to check out.</p>
                    <Link href="/shop" class="mt-6 inline-flex items-center gap-2 bg-neutral-900 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black">
                        Start Shopping <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>

                <!-- Cart with items -->
                <div v-else class="grid gap-10 lg:grid-cols-3">
                    <!-- Items -->
                    <div class="lg:col-span-2">
                        <div v-for="item in items" :key="item.row_id" class="flex gap-4 border-b border-neutral-200 py-5">
                            <Link :href="`/shop/${item.slug}`" class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md bg-neutral-100">
                                <img v-if="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                                <span v-else class="grid h-14 w-14 place-items-center rounded-full border border-neutral-300 font-display text-xs text-neutral-400">D2GB</span>
                            </Link>

                            <div class="flex flex-1 flex-col">
                                <div class="flex justify-between gap-2">
                                    <div>
                                        <Link :href="`/shop/${item.slug}`" class="font-heading text-sm font-semibold uppercase hover:text-d2gb-gold">{{ item.name }}</Link>
                                        <p v-if="item.size" class="mt-0.5 text-xs uppercase tracking-wide text-neutral-500">Size: {{ item.size }}</p>
                                        <p class="mt-0.5 text-xs text-neutral-500">{{ money(item.price) }} each</p>
                                    </div>
                                    <button @click="remove(item)" class="self-start text-neutral-400 transition hover:text-red-500" aria-label="Remove"><Trash2 class="h-4 w-4" /></button>
                                </div>

                                <div class="mt-auto flex items-center justify-between pt-3">
                                    <div class="flex items-center border border-neutral-300">
                                        <button @click="setQty(item, item.quantity - 1)" class="grid h-9 w-9 place-items-center hover:bg-neutral-100"><Minus class="h-3.5 w-3.5" /></button>
                                        <span class="w-9 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                        <button @click="setQty(item, item.quantity + 1)" class="grid h-9 w-9 place-items-center hover:bg-neutral-100"><Plus class="h-3.5 w-3.5" /></button>
                                    </div>
                                    <span class="font-display text-lg">{{ money(item.subtotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <Link href="/shop" class="mt-6 inline-flex items-center gap-2 font-heading text-xs font-bold uppercase tracking-wide text-neutral-600 hover:text-d2gb-gold">
                            <ArrowRight class="h-3 w-3 rotate-180" /> Continue Shopping
                        </Link>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-md bg-neutral-50 p-6">
                            <h2 class="font-display text-2xl uppercase">Order Summary</h2>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-neutral-500">Subtotal</dt>
                                    <dd class="font-semibold">{{ money(subtotal) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-neutral-500">Shipping</dt>
                                    <dd class="font-semibold">{{ subtotal >= SHIPPING_THRESHOLD ? 'FREE' : 'Calculated at checkout' }}</dd>
                                </div>
                                <p v-if="subtotal < SHIPPING_THRESHOLD" class="text-xs text-d2gb-gold">
                                    Add {{ money(SHIPPING_THRESHOLD - subtotal) }} more for free shipping.
                                </p>
                            </dl>
                            <div class="mt-4 flex justify-between border-t border-neutral-200 pt-4">
                                <span class="font-heading font-bold uppercase">Total</span>
                                <span class="font-display text-2xl">{{ money(subtotal) }}</span>
                            </div>
                            <button
                                @click="toast.info('Checkout with Stripe is coming in the next phase!')"
                                class="mt-6 flex w-full items-center justify-center gap-2 bg-neutral-900 py-4 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black"
                            >
                                Proceed To Checkout <ArrowRight class="h-4 w-4" />
                            </button>
                            <p class="mt-3 text-center text-xs text-neutral-400">Taxes calculated at checkout</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
