<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ShoppingCart, Minus, Plus, ShieldCheck, Truck, RefreshCw } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Related { id: number; name: string; slug: string; price: number; image: string | null }
interface Product {
    id: number; name: string; slug: string; sku: string | null;
    price: number; compare_at_price: number | null;
    short_description: string | null; description: string | null;
    image: string | null; stock: number;
    category: { name: string; slug: string } | null;
    brand: { name: string; slug: string } | null;
    sizes: string[];
    images: { path: string; alt: string | null }[];
}

const props = defineProps<{ product: Product; related: Related[] }>();

const cartCount = ref(0);
const selectedSize = ref<string | null>(props.product.sizes.length ? props.product.sizes[0] : null);
const quantity = ref(1);
const money = (n: number) => `$${n.toFixed(2)}`;
const inStock = computed(() => props.product.stock > 0);

function addToCart() {
    cartCount.value += quantity.value;
}
</script>

<template>
    <Head :title="`${product.name} | Dare To Go Bare`" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader :cart-count="cartCount" />

        <!-- Breadcrumb -->
        <div class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-3 text-xs uppercase tracking-wide text-white/50">
                <Link href="/" class="hover:text-d2gb-gold">Home</Link>
                <span class="mx-2">/</span>
                <Link href="/shop" class="hover:text-d2gb-gold">Shop</Link>
                <template v-if="product.category">
                    <span class="mx-2">/</span>
                    <Link :href="`/shop?category=${product.category.slug}`" class="hover:text-d2gb-gold">{{ product.category.name }}</Link>
                </template>
                <span class="mx-2">/</span>
                <span class="text-white/80">{{ product.name }}</span>
            </div>
        </div>

        <!-- Product -->
        <section class="bg-white text-neutral-900">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-2">
                <!-- Gallery -->
                <div>
                    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-md bg-neutral-100">
                        <img v-if="product.image" :src="product.image" :alt="product.name" class="h-full w-full object-cover" />
                        <span v-else class="grid h-40 w-40 place-items-center rounded-full border-4 border-neutral-300 font-display text-3xl text-neutral-400">D2GB</span>
                    </div>
                    <div v-if="product.images.length" class="mt-4 grid grid-cols-4 gap-3">
                        <div v-for="(img, i) in product.images" :key="i" class="aspect-square overflow-hidden rounded bg-neutral-100">
                            <img :src="img.path" :alt="img.alt ?? product.name" class="h-full w-full object-cover" />
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div>
                    <p v-if="product.brand" class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">{{ product.brand.name }}</p>
                    <h1 class="mt-1 font-display text-4xl uppercase leading-tight md:text-5xl">{{ product.name }}</h1>

                    <div class="mt-4 flex items-center gap-3">
                        <span class="font-display text-3xl">{{ money(product.price) }}</span>
                        <span v-if="product.compare_at_price" class="text-lg text-neutral-400 line-through">{{ money(product.compare_at_price) }}</span>
                    </div>

                    <p v-if="product.short_description" class="mt-4 text-sm leading-relaxed text-neutral-600">{{ product.short_description }}</p>

                    <!-- Size picker -->
                    <div v-if="product.sizes.length" class="mt-6">
                        <p class="mb-2 font-heading text-xs font-bold uppercase tracking-wide">Size</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="size in product.sizes"
                                :key="size"
                                @click="selectedSize = size"
                                :class="[
                                    'min-w-11 border px-4 py-2 font-heading text-sm font-semibold uppercase transition',
                                    selectedSize === size ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 hover:border-neutral-900',
                                ]"
                            >
                                {{ size }}
                            </button>
                        </div>
                    </div>

                    <!-- Quantity + add to cart -->
                    <div class="mt-6 flex items-center gap-3">
                        <div class="flex items-center border border-neutral-300">
                            <button @click="quantity = Math.max(1, quantity - 1)" class="grid h-11 w-11 place-items-center hover:bg-neutral-100"><Minus class="h-4 w-4" /></button>
                            <span class="w-10 text-center font-heading font-semibold">{{ quantity }}</span>
                            <button @click="quantity++" class="grid h-11 w-11 place-items-center hover:bg-neutral-100"><Plus class="h-4 w-4" /></button>
                        </div>
                        <button
                            @click="addToCart"
                            :disabled="!inStock"
                            class="flex flex-1 items-center justify-center gap-2 bg-neutral-900 py-3.5 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            {{ inStock ? 'Add To Cart' : 'Out of Stock' }} <ShoppingCart class="h-4 w-4" />
                        </button>
                    </div>

                    <p class="mt-3 text-xs uppercase tracking-wide" :class="inStock ? 'text-green-600' : 'text-red-500'">
                        {{ inStock ? `In stock — ${product.stock} available` : 'Currently unavailable' }}
                    </p>
                    <p v-if="product.sku" class="mt-1 text-xs text-neutral-400">SKU: {{ product.sku }}</p>

                    <div v-if="product.description" class="mt-6 border-t border-neutral-200 pt-6 text-sm leading-relaxed text-neutral-600">
                        {{ product.description }}
                    </div>

                    <!-- Trust -->
                    <div class="mt-6 grid grid-cols-3 gap-3 border-t border-neutral-200 pt-6 text-center">
                        <div class="flex flex-col items-center gap-1"><ShieldCheck class="h-5 w-5 text-d2gb-gold" /><span class="text-[10px] uppercase text-neutral-500">Premium Quality</span></div>
                        <div class="flex flex-col items-center gap-1"><Truck class="h-5 w-5 text-d2gb-gold" /><span class="text-[10px] uppercase text-neutral-500">Fast Shipping</span></div>
                        <div class="flex flex-col items-center gap-1"><RefreshCw class="h-5 w-5 text-d2gb-gold" /><span class="text-[10px] uppercase text-neutral-500">Easy Returns</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related -->
        <section v-if="related.length" class="bg-neutral-100 text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <h2 class="mb-8 font-display text-3xl uppercase md:text-4xl">You May Also Like</h2>
                <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                    <div v-for="p in related" :key="p.id" class="group">
                        <Link :href="`/shop/${p.slug}`" class="relative mb-3 flex aspect-square items-center justify-center overflow-hidden rounded-md bg-white">
                            <img v-if="p.image" :src="p.image" :alt="p.name" class="h-full w-full object-cover" />
                            <span v-else class="grid h-20 w-20 place-items-center rounded-full border-2 border-neutral-300 font-display text-neutral-400">D2GB</span>
                        </Link>
                        <Link :href="`/shop/${p.slug}`" class="font-heading text-sm font-semibold uppercase transition hover:text-d2gb-gold">{{ p.name }}</Link>
                        <p class="mt-1 font-display text-lg">{{ money(p.price) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
