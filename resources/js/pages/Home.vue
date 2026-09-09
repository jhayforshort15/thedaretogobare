<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import {
    ShoppingCart,
    ChevronLeft,
    ChevronRight,
    ArrowRight,
    ShieldCheck,
    CreditCard,
    Truck,
    RefreshCw,
} from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Category { id: number; name: string; slug: string }
interface Product { id: number; name: string; price: number; slug: string; image: string | null }
interface Fight { title: string; date: string; location: string }
interface Post { title: string; date: string; slug: string }

defineProps<{
    categories: Category[];
    bestSellers: Product[];
    brands: string[];
    fights: Fight[];
    posts: Post[];
}>();

function addToCart(p: Product) {
    router.post('/cart', { product_id: p.id, quantity: 1 }, {
        preserveScroll: true,
        onSuccess: () => toast.success(`${p.name} added to your cart.`),
    });
}

const slides = [
    { kicker: 'No Gloves. No Excuses.', title: 'DARE TO\nGO BARE', sub: 'Fightwear for warriors.\nBuilt for the bare knuckle lifestyle.' },
    { kicker: 'Train Like a Champion.', title: 'OWN THE\nRING', sub: 'Gear engineered for the grind.\nStep in and prove it.' },
    { kicker: 'Heart Over Everything.', title: 'BUILT\nDIFFERENT', sub: 'More than fightwear.\nA way of life.' },
];
const current = ref(0);
const prev = () => (current.value = (current.value - 1 + slides.length) % slides.length);
const next = () => (current.value = (current.value + 1) % slides.length);

const money = (n: number) => `$${n.toFixed(2)}`;
</script>

<template>
    <Head title="Dare To Go Bare — Bare Knuckle Fightwear" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <!-- Hero -->
        <section class="relative overflow-hidden bg-black">
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_40%,rgba(200,164,100,0.18),transparent_60%)]"></div>
            <div class="relative mx-auto grid min-h-[520px] max-w-7xl items-center gap-8 px-4 py-16 md:grid-cols-2">
                <div class="max-w-xl">
                    <p class="mb-4 font-heading text-xl font-semibold uppercase tracking-wide text-white/90 md:text-2xl" style="white-space: pre-line">{{ slides[current].kicker }}</p>
                    <h1 class="font-display text-6xl uppercase leading-[0.92] text-d2gb-gold md:text-8xl" style="white-space: pre-line">{{ slides[current].title }}</h1>
                    <p class="mt-6 text-sm uppercase tracking-wide text-white/70 md:text-base" style="white-space: pre-line">{{ slides[current].sub }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <Link href="/shop" class="bg-d2gb-gold px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-black transition hover:bg-d2gb-gold-light">Shop Now</Link>
                        <a href="#fights" class="border border-white/40 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:border-d2gb-gold hover:text-d2gb-gold">Explore Fights</a>
                    </div>
                </div>
                <div class="relative hidden h-[420px] items-end justify-center md:flex">
                    <div class="absolute inset-0 rounded-lg bg-gradient-to-b from-neutral-800 to-neutral-950 opacity-70"></div>
                    <div class="relative grid h-40 w-40 place-items-center rounded-full border-4 border-d2gb-gold/40 font-display text-4xl text-d2gb-gold/50">D2GB</div>
                </div>
            </div>

            <div class="absolute right-6 top-1/2 hidden -translate-y-1/2 flex-col items-center gap-3 text-white/70 md:flex">
                <span class="font-display text-lg">0{{ current + 1 }}</span>
                <span class="h-16 w-px bg-white/30"></span>
                <span class="font-display text-lg">0{{ slides.length }}</span>
            </div>
            <div class="absolute bottom-6 right-6 flex gap-2">
                <button @click="prev" class="grid h-9 w-9 place-items-center rounded-full border border-white/30 text-white transition hover:border-d2gb-gold hover:text-d2gb-gold"><ChevronLeft class="h-4 w-4" /></button>
                <button @click="next" class="grid h-9 w-9 place-items-center rounded-full border border-white/30 text-white transition hover:border-d2gb-gold hover:text-d2gb-gold"><ChevronRight class="h-4 w-4" /></button>
            </div>
        </section>

        <!-- Category grid -->
        <section class="bg-d2gb-charcoal">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-3 px-4 py-4 lg:grid-cols-4">
                <Link
                    v-for="cat in categories"
                    :key="cat.slug"
                    :href="`/shop?category=${cat.slug}`"
                    class="group relative flex h-56 items-end overflow-hidden rounded-md bg-gradient-to-br from-neutral-700 to-neutral-950"
                >
                    <div class="absolute inset-0 bg-black/30 transition group-hover:bg-black/10"></div>
                    <div class="relative p-5">
                        <h3 class="font-display text-3xl uppercase leading-none">{{ cat.name }}</h3>
                        <span class="mt-1 flex items-center gap-1 font-heading text-xs font-semibold uppercase tracking-wide text-d2gb-gold">
                            Shop Now <ArrowRight class="h-3 w-3" />
                        </span>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Best sellers -->
        <section id="best-sellers" class="bg-white text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-14">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Featured</p>
                        <h2 class="font-display text-4xl uppercase md:text-5xl">Best Sellers</h2>
                    </div>
                    <Link href="/shop" class="hidden items-center gap-2 border border-neutral-300 px-5 py-2.5 font-heading text-xs font-bold uppercase tracking-wide transition hover:border-d2gb-gold hover:text-d2gb-gold sm:flex">
                        View All Products <ArrowRight class="h-3 w-3" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-5 lg:grid-cols-4">
                    <div v-for="p in bestSellers" :key="p.slug" class="group">
                        <Link :href="`/shop/${p.slug}`" class="relative mb-4 flex aspect-square items-center justify-center overflow-hidden rounded-md bg-neutral-100">
                            <img v-if="p.image" :src="p.image" :alt="p.name" class="h-full w-full object-cover" />
                            <span v-else class="grid h-24 w-24 place-items-center rounded-full border-2 border-neutral-300 font-display text-xl text-neutral-400">D2GB</span>
                        </Link>
                        <Link :href="`/shop/${p.slug}`" class="font-heading text-sm font-semibold uppercase transition hover:text-d2gb-gold">{{ p.name }}</Link>
                        <p class="mt-1 font-display text-lg">{{ money(p.price) }}</p>
                        <button
                            @click="addToCart(p)"
                            class="mt-3 flex w-full items-center justify-center gap-2 bg-neutral-900 py-3 font-heading text-xs font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black"
                        >
                            Add To Cart <ShoppingCart class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <div class="mt-12 grid grid-cols-2 gap-6 rounded-md bg-neutral-50 p-6 md:grid-cols-4">
                    <div class="flex items-center gap-3">
                        <ShieldCheck class="h-7 w-7 text-d2gb-gold" />
                        <div><p class="font-heading text-sm font-bold uppercase">Premium Quality</p><p class="text-xs text-neutral-500">Built to last</p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <CreditCard class="h-7 w-7 text-d2gb-gold" />
                        <div><p class="font-heading text-sm font-bold uppercase">Secure Payments</p><p class="text-xs text-neutral-500">100% safe checkout</p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <Truck class="h-7 w-7 text-d2gb-gold" />
                        <div><p class="font-heading text-sm font-bold uppercase">Fast Shipping</p><p class="text-xs text-neutral-500">Quick worldwide delivery</p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <RefreshCw class="h-7 w-7 text-d2gb-gold" />
                        <div><p class="font-heading text-sm font-bold uppercase">Easy Returns</p><p class="text-xs text-neutral-500">14-day return policy</p></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top brands -->
        <section class="bg-black">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <div class="mb-6 flex items-center justify-between">
                    <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Trusted By Warriors</p>
                    <a href="#" class="flex items-center gap-1 font-heading text-xs font-bold uppercase tracking-wide text-white/70 hover:text-d2gb-gold">View All Brands <ArrowRight class="h-3 w-3" /></a>
                </div>
                <div class="flex flex-wrap items-center gap-x-12 gap-y-6">
                    <span class="font-display text-2xl uppercase tracking-wide">Top Brands</span>
                    <span v-for="b in brands" :key="b" class="font-heading text-xl font-bold uppercase tracking-wide text-white/70">{{ b }}</span>
                </div>
            </div>
        </section>

        <!-- Upcoming fights -->
        <section id="fights" class="bg-neutral-100 text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-14">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">The Fight</p>
                        <h2 class="font-display text-4xl uppercase md:text-5xl">Upcoming Fights</h2>
                    </div>
                    <a href="#" class="hidden items-center gap-1 font-heading text-xs font-bold uppercase tracking-wide hover:text-d2gb-gold sm:flex">View All Events <ArrowRight class="h-3 w-3" /></a>
                </div>
                <div class="grid gap-5 md:grid-cols-3">
                    <div v-for="f in fights" :key="f.title" class="relative flex h-56 flex-col justify-center overflow-hidden rounded-md bg-gradient-to-br from-neutral-800 to-black p-6 text-center text-white">
                        <p v-if="f.date" class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">{{ f.date }}</p>
                        <h3 class="mt-2 font-display text-2xl uppercase leading-tight">{{ f.title }}</h3>
                        <p class="mt-1 text-xs uppercase tracking-wide text-white/60">{{ f.location }}</p>
                        <a href="#" class="mx-auto mt-4 bg-d2gb-gold px-5 py-2 font-heading text-xs font-bold uppercase tracking-wider text-black transition hover:bg-d2gb-gold-light">View Event</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- More than fightwear -->
        <section id="about" class="relative overflow-hidden bg-black">
            <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-16 md:grid-cols-2">
                <div class="max-w-md">
                    <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Our Movement</p>
                    <h2 class="mt-2 font-display text-5xl uppercase leading-[0.95]">More Than<br />Fightwear</h2>
                    <p class="mt-5 text-sm leading-relaxed text-white/70">
                        Dare To Go Bare is a lifestyle. It's about heart, discipline, and the courage to show up—every single day.
                    </p>
                    <a href="#" class="mt-7 inline-block bg-d2gb-gold px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-black transition hover:bg-d2gb-gold-light">Learn More</a>
                </div>
                <div class="relative flex h-72 items-center justify-center rounded-md bg-gradient-to-br from-neutral-800 to-neutral-950">
                    <span class="grid h-40 w-40 place-items-center rounded-full border-4 border-white/20 font-display text-4xl text-white/40">D2GB</span>
                </div>
            </div>
        </section>

        <!-- Blog -->
        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-14">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Latest News</p>
                        <h2 class="font-display text-4xl uppercase md:text-5xl">From The D2GB Blog</h2>
                    </div>
                    <a href="#" class="hidden items-center gap-1 font-heading text-xs font-bold uppercase tracking-wide hover:text-d2gb-gold sm:flex">View All Articles <ArrowRight class="h-3 w-3" /></a>
                </div>
                <div class="grid gap-6 md:grid-cols-3">
                    <a v-for="post in posts" :key="post.slug" href="#" class="group">
                        <div class="mb-4 h-48 overflow-hidden rounded-md bg-gradient-to-br from-neutral-300 to-neutral-500"></div>
                        <h3 class="font-heading text-base font-semibold uppercase leading-snug transition group-hover:text-d2gb-gold">{{ post.title }}</h3>
                        <p class="mt-1 text-xs uppercase tracking-wide text-neutral-500">{{ post.date }}</p>
                    </a>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
