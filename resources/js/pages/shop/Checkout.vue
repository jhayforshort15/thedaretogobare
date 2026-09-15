<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Item { row_id: string; name: string; slug: string; image: string | null; size: string | null; price: number; quantity: number; subtotal: number }
interface Summary { subtotal: number; shipping: number; tax: number; total: number; free_shipping_threshold: number }
interface Prefill { email: string; first_name: string; last_name: string }

const props = defineProps<{ items: Item[]; summary: Summary; prefill: Prefill | null }>();

const money = (n: number) => `$${n.toFixed(2)}`;

const form = useForm({
    email: props.prefill?.email ?? '',
    first_name: props.prefill?.first_name ?? '',
    last_name: props.prefill?.last_name ?? '',
    phone: '',
    shipping_address: '',
    shipping_city: '',
    shipping_state: '',
    shipping_postal_code: '',
    shipping_country: 'United States',
    notes: '',
});

const submit = () => form.post('/checkout');
</script>

<template>
    <Head title="Checkout | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-8">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Secure Checkout</p>
                <h1 class="font-display text-4xl uppercase md:text-5xl">Checkout</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <form class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-3" @submit.prevent="submit">
                <!-- Form fields -->
                <div class="space-y-8 lg:col-span-2">
                    <!-- Contact -->
                    <div>
                        <h2 class="mb-4 font-heading text-lg font-bold uppercase">Contact</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Email</label>
                                <input v-model="form.email" type="email" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">First name</label>
                                <input v-model="form.first_name" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-500">{{ form.errors.first_name }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Last name</label>
                                <input v-model="form.last_name" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-500">{{ form.errors.last_name }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Phone (optional)</label>
                                <input v-model="form.phone" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                            </div>
                        </div>
                    </div>

                    <!-- Shipping -->
                    <div>
                        <h2 class="mb-4 font-heading text-lg font-bold uppercase">Shipping Address</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Address</label>
                                <input v-model="form.shipping_address" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.shipping_address" class="mt-1 text-xs text-red-500">{{ form.errors.shipping_address }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">City</label>
                                <input v-model="form.shipping_city" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.shipping_city" class="mt-1 text-xs text-red-500">{{ form.errors.shipping_city }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">State / Province</label>
                                <input v-model="form.shipping_state" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Postal code</label>
                                <input v-model="form.shipping_postal_code" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Country</label>
                                <input v-model="form.shipping_country" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                                <p v-if="form.errors.shipping_country" class="mt-1 text-xs text-red-500">{{ form.errors.shipping_country }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Order notes (optional)</label>
                                <textarea v-model="form.notes" rows="3" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 rounded-md border border-neutral-200 bg-neutral-50 p-6">
                        <h2 class="mb-4 font-heading text-lg font-bold uppercase">Order Summary</h2>

                        <ul class="mb-4 space-y-3 border-b border-neutral-200 pb-4">
                            <li v-for="item in items" :key="item.row_id" class="flex items-center gap-3">
                                <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded bg-neutral-100">
                                    <img v-if="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                                    <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-neutral-900 text-[10px] font-bold text-white">{{ item.quantity }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold">{{ item.name }}</p>
                                    <p v-if="item.size" class="text-xs text-neutral-500">Size: {{ item.size }}</p>
                                </div>
                                <span class="text-sm font-semibold">{{ money(item.subtotal) }}</span>
                            </li>
                        </ul>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-neutral-500">Subtotal</dt><dd>{{ money(summary.subtotal) }}</dd></div>
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">Shipping</dt>
                                <dd>{{ summary.shipping === 0 ? 'FREE' : money(summary.shipping) }}</dd>
                            </div>
                            <div v-if="summary.tax > 0" class="flex justify-between"><dt class="text-neutral-500">Tax</dt><dd>{{ money(summary.tax) }}</dd></div>
                        </dl>

                        <div class="mt-4 flex items-center justify-between border-t border-neutral-200 pt-4">
                            <span class="font-heading text-sm font-bold uppercase">Total</span>
                            <span class="font-display text-2xl">{{ money(summary.total) }}</span>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-6 w-full bg-neutral-900 py-3.5 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black disabled:opacity-50"
                        >
                            {{ form.processing ? 'Placing order…' : 'Place Order' }}
                        </button>

                        <p class="mt-3 text-center text-[11px] text-neutral-400">
                            Online card payment is being set up. Your order is placed as <strong>pending</strong> and our team will confirm payment.
                        </p>

                        <Link href="/cart" class="mt-3 block text-center text-xs font-semibold uppercase text-neutral-500 hover:text-neutral-900">← Back to cart</Link>
                    </div>
                </div>
            </form>
        </section>

        <StoreFooter />
    </div>
</template>
