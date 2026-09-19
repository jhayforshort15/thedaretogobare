<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';
import OrderDetail from '@/components/store/OrderDetail.vue';

defineProps<{ order: (Record<string, unknown> & { order_number: string }) | null }>();

const form = useForm({
    order_number: '',
    email: '',
});

const submit = () => form.post('/order-lookup', { preserveScroll: true });
</script>

<template>
    <Head title="Track Your Order | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-3xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Order Status</p>
                <h1 class="font-display text-4xl uppercase md:text-5xl">Track Your Order</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-3xl px-4 py-12">
                <form class="rounded-md border border-neutral-200 p-6" @submit.prevent="submit">
                    <p class="mb-4 text-sm text-neutral-600">Enter your order number and the email used at checkout.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Order number</label>
                            <input v-model="form.order_number" type="text" placeholder="D2GB-XXXXXXXX" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Email</label>
                            <input v-model="form.email" type="email" placeholder="you@example.com" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                        </div>
                    </div>
                    <p v-if="form.errors.order_number" class="mt-3 text-xs text-red-500">{{ form.errors.order_number }}</p>
                    <button type="submit" :disabled="form.processing" class="mt-4 flex items-center justify-center gap-2 bg-neutral-900 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black disabled:opacity-50">
                        <Search class="h-4 w-4" /> {{ form.processing ? 'Searching…' : 'Find Order' }}
                    </button>
                </form>

                <div v-if="order" class="mt-8">
                    <OrderDetail :order="(order as any)" />
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
