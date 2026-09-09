<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ShoppingCart } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Category { id: number; name: string; slug: string }
interface Product { id: number; name: string; slug: string; price: number; compare_at_price: number | null; image: string | null }

const props = defineProps<{
    products: Product[];
    categories: Category[];
    activeCategory: string | null;
}>();

const addToCart = (p: Product) =>
    router.post('/cart', { product_id: p.id, quantity: 1 }, {
        preserveScroll: true,
        onSuccess: () => toast.success(`${p.name} added to your cart.`),
    });
const money = (n: number) => `$${n.toFixed(2)}`;

const activeName = props.categories.find((c) => c.slug === props.activeCategory)?.name;
</script>

<template>
    <Head :title="`Shop${activeName ? ' — ' + activeName : ''} | Dare To Go Bare`" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <!-- Page header -->
        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Shop</p>
                <h1 class="font-display text-5xl uppercase md:text-6xl">{{ activeName ?? 'All Products' }}</h1>
            </div>
        </section>

        <!-- Category filters -->
        <div class="border-b border-white/10 bg-d2gb-charcoal">
            <div class="mx-auto flex max-w-7xl flex-wrap gap-2 px-4 py-4">
                <Link
                    href="/shop"
                    :class="[
                        'px-4 py-2 font-heading text-xs font-bold uppercase tracking-wide transition',
                        !activeCategory ? 'bg-d2gb-gold text-black' : 'border border-white/20 text-white/70 hover:border-d2gb-gold hover:text-d2gb-gold',
                    ]"
                >
                    All
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/shop?category=${cat.slug}`"
                    :class="[
                        'px-4 py-2 font-heading text-xs font-bold uppercase tracking-wide transition',
                        activeCategory === cat.slug ? 'bg-d2gb-gold text-black' : 'border border-white/20 text-white/70 hover:border-d2gb-gold hover:text-d2gb-gold',
                    ]"
                >
                    {{ cat.name }}
                </Link>
            </div>
        </div>

        <!-- Product grid -->
        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <p class="mb-6 text-sm text-neutral-500">{{ products.length }} product{{ products.length === 1 ? '' : 's' }}</p>

                <div v-if="products.length" class="grid grid-cols-2 gap-5 md:grid-cols-3 lg:grid-cols-4">
                    <div v-for="p in products" :key="p.id" class="group">
                        <Link :href="`/shop/${p.slug}`" class="relative mb-4 flex aspect-square items-center justify-center overflow-hidden rounded-md bg-neutral-100">
                            <img v-if="p.image" :src="p.image" :alt="p.name" class="h-full w-full object-cover" />
                            <span v-else class="grid h-24 w-24 place-items-center rounded-full border-2 border-neutral-300 font-display text-xl text-neutral-400">D2GB</span>
                            <span v-if="p.compare_at_price" class="absolute left-3 top-3 bg-d2gb-gold px-2 py-1 font-heading text-[10px] font-bold uppercase text-black">Sale</span>
                        </Link>
                        <Link :href="`/shop/${p.slug}`" class="font-heading text-sm font-semibold uppercase transition hover:text-d2gb-gold">{{ p.name }}</Link>
                        <p class="mt-1 flex items-center gap-2">
                            <span class="font-display text-lg">{{ money(p.price) }}</span>
                            <span v-if="p.compare_at_price" class="text-sm text-neutral-400 line-through">{{ money(p.compare_at_price) }}</span>
                        </p>
                        <button
                            @click="addToCart(p)"
                            class="mt-3 flex w-full items-center justify-center gap-2 bg-neutral-900 py-3 font-heading text-xs font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black"
                        >
                            Add To Cart <ShoppingCart class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <div v-else class="py-20 text-center">
                    <p class="font-display text-3xl uppercase text-neutral-400">No products found</p>
                    <Link href="/shop" class="mt-4 inline-block font-heading text-sm font-bold uppercase tracking-wide text-d2gb-gold hover:underline">View all products</Link>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
