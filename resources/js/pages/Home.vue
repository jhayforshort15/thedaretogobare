<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Search,
    User,
    ShoppingCart,
    ChevronLeft,
    ChevronRight,
    ArrowRight,
    ShieldCheck,
    CreditCard,
    Truck,
    RefreshCw,
    Menu,
} from '@lucide/vue';

// Brand social icons (lucide removed brand glyphs) — inline SVG components.
import { h } from 'vue';
const svg = (paths: string) => () =>
    h('svg', { viewBox: '0 0 24 24', fill: 'currentColor', class: 'h-full w-full' }, [h('path', { d: paths })]);
const IconFacebook = svg('M9 8H6v4h3v12h5V12h3.6L18 8h-4V6.3c0-1 .2-1.3 1.1-1.3H18V0h-3.8C10.6 0 9 1.6 9 4.6V8z');
const IconInstagram = svg('M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2zm0 3.2A6.6 6.6 0 1 0 18.6 12 6.6 6.6 0 0 0 12 5.4zm0 10.9A4.3 4.3 0 1 1 16.3 12 4.3 4.3 0 0 1 12 16.3zm6.9-11.1a1.5 1.5 0 1 1-1.5-1.5 1.5 1.5 0 0 1 1.5 1.5z');
const IconTiktok = svg('M16.6 5.8a4.8 4.8 0 0 1-1-1.8h-2.7v11.2a2.4 2.4 0 1 1-1.7-2.3V10a5 5 0 1 0 4.1 4.9V9.4a7.1 7.1 0 0 0 4 1.3V8a4.8 4.8 0 0 1-2.7-.8 5 5 0 0 1-.3-.4z');
const IconYoutube = svg('M23 7.5a3 3 0 0 0-2.1-2.1C19 4.9 12 4.9 12 4.9s-7 0-8.9.5A3 3 0 0 0 1 7.5 31 31 0 0 0 .5 12 31 31 0 0 0 1 16.5a3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.5 31 31 0 0 0-.5-4.5zM9.8 15.3V8.7l5.7 3.3z');

interface Category { name: string; slug: string }
interface Product { name: string; price: number; slug: string }
interface Brand { }
interface Fight { title: string; date: string; location: string }
interface Post { title: string; date: string; slug: string }

const props = defineProps<{
    categories: Category[];
    bestSellers: Product[];
    brands: string[];
    fights: Fight[];
    posts: Post[];
}>();

const navLinks = ['Shop', 'Men', 'Women', 'Unisex', 'Fightwear', 'Upcoming Fights', 'About'];

const cartCount = ref(3);
function addToCart(_p: Product) {
    cartCount.value++;
}

// Hero slider
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
        <!-- Announcement bar -->
        <div class="bg-d2gb-gold text-black">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 text-[11px] font-semibold tracking-wide">
                <span class="hidden sm:inline">🚚 FREE SHIPPING ON ORDERS OVER $150</span>
                <span class="uppercase">Up to 50% off selected items</span>
                <div class="hidden items-center gap-3 sm:flex">
                    <span class="h-4 w-4"><IconFacebook /></span>
                    <span class="h-4 w-4"><IconInstagram /></span>
                    <span class="h-4 w-4"><IconTiktok /></span>
                    <span class="h-4 w-4"><IconYoutube /></span>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-white/10 bg-d2gb-dark/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2">
                    <span class="grid h-11 w-11 place-items-center rounded-full border-2 border-d2gb-gold text-d2gb-gold">
                        <span class="font-display text-lg leading-none">D2</span>
                    </span>
                    <span class="font-display text-lg leading-none tracking-wide">
                        DARE TO<br />GO BARE
                    </span>
                </a>

                <!-- Nav -->
                <nav class="hidden items-center gap-6 lg:flex">
                    <a
                        v-for="link in navLinks"
                        :key="link"
                        href="#"
                        class="font-heading text-sm font-semibold uppercase tracking-wide text-white/80 transition hover:text-d2gb-gold"
                    >
                        {{ link }}
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <button class="text-white/80 transition hover:text-d2gb-gold" aria-label="Search"><Search class="h-5 w-5" /></button>
                    <button class="text-white/80 transition hover:text-d2gb-gold" aria-label="Account"><User class="h-5 w-5" /></button>
                    <button class="relative text-white/80 transition hover:text-d2gb-gold" aria-label="Cart">
                        <ShoppingCart class="h-5 w-5" />
                        <span class="absolute -right-2 -top-2 grid h-4 w-4 place-items-center rounded-full bg-d2gb-gold text-[10px] font-bold text-black">{{ cartCount }}</span>
                    </button>
                    <button class="text-white/80 lg:hidden" aria-label="Menu"><Menu class="h-6 w-6" /></button>
                </div>
            </div>
        </header>

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
                        <a href="#best-sellers" class="bg-d2gb-gold px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-black transition hover:bg-d2gb-gold-light">Shop Now</a>
                        <a href="#fights" class="border border-white/40 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:border-d2gb-gold hover:text-d2gb-gold">Explore Fights</a>
                    </div>
                </div>
                <!-- Hero image placeholder -->
                <div class="relative hidden h-[420px] items-end justify-center md:flex">
                    <div class="absolute inset-0 rounded-lg bg-gradient-to-b from-neutral-800 to-neutral-950 opacity-70"></div>
                    <div class="relative grid h-40 w-40 place-items-center rounded-full border-4 border-d2gb-gold/40 font-display text-4xl text-d2gb-gold/50">D2GB</div>
                </div>
            </div>

            <!-- Slide controls -->
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
                <a
                    v-for="cat in categories"
                    :key="cat.slug"
                    href="#"
                    class="group relative flex h-56 items-end overflow-hidden rounded-md bg-gradient-to-br from-neutral-700 to-neutral-950"
                >
                    <div class="absolute inset-0 bg-black/30 transition group-hover:bg-black/10"></div>
                    <div class="relative p-5">
                        <h3 class="font-display text-3xl uppercase leading-none">{{ cat.name }}</h3>
                        <span class="mt-1 flex items-center gap-1 font-heading text-xs font-semibold uppercase tracking-wide text-d2gb-gold">
                            Shop Now <ArrowRight class="h-3 w-3" />
                        </span>
                    </div>
                </a>
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
                    <a href="#" class="hidden items-center gap-2 border border-neutral-300 px-5 py-2.5 font-heading text-xs font-bold uppercase tracking-wide transition hover:border-d2gb-gold hover:text-d2gb-gold sm:flex">
                        View All Products <ArrowRight class="h-3 w-3" />
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-5 lg:grid-cols-4">
                    <div v-for="p in bestSellers" :key="p.slug" class="group">
                        <div class="relative mb-4 flex aspect-square items-center justify-center overflow-hidden rounded-md bg-neutral-100">
                            <span class="grid h-24 w-24 place-items-center rounded-full border-2 border-neutral-300 font-display text-xl text-neutral-400">D2GB</span>
                        </div>
                        <h3 class="font-heading text-sm font-semibold uppercase">{{ p.name }}</h3>
                        <p class="mt-1 font-display text-lg">{{ money(p.price) }}</p>
                        <button
                            @click="addToCart(p)"
                            class="mt-3 flex w-full items-center justify-center gap-2 bg-neutral-900 py-3 font-heading text-xs font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black"
                        >
                            Add To Cart <ShoppingCart class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Trust badges -->
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
        <section class="relative overflow-hidden bg-black">
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

        <!-- Newsletter -->
        <section class="bg-d2gb-charcoal">
            <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-4 py-12 md:flex-row md:justify-between">
                <div>
                    <h2 class="font-display text-3xl uppercase md:text-4xl">Join The Movement</h2>
                    <p class="mt-1 text-sm text-white/60">Get exclusive drops, fight updates, and special offers.</p>
                </div>
                <form class="flex w-full max-w-md gap-2" @submit.prevent>
                    <input type="email" required placeholder="Enter your email address" class="w-full rounded-sm bg-white px-4 py-3 text-sm text-neutral-900 outline-none placeholder:text-neutral-400" />
                    <button type="submit" class="whitespace-nowrap bg-d2gb-gold px-6 py-3 font-heading text-sm font-bold uppercase tracking-wider text-black transition hover:bg-d2gb-gold-light">Subscribe</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-black text-white/70">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-14 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        <span class="grid h-10 w-10 place-items-center rounded-full border-2 border-d2gb-gold font-display text-d2gb-gold">D2</span>
                        <span class="font-display leading-none text-white">DARE TO<br />GO BARE</span>
                    </div>
                    <p class="text-xs leading-relaxed">Premium fightwear for warriors. No gloves. No excuses.</p>
                    <div class="mt-4 flex gap-3 text-white/70">
                        <span class="h-4 w-4 transition hover:text-d2gb-gold"><IconFacebook /></span>
                        <span class="h-4 w-4 transition hover:text-d2gb-gold"><IconInstagram /></span>
                        <span class="h-4 w-4 transition hover:text-d2gb-gold"><IconTiktok /></span>
                        <span class="h-4 w-4 transition hover:text-d2gb-gold"><IconYoutube /></span>
                    </div>
                </div>
                <div>
                    <h4 class="mb-4 font-heading text-sm font-bold uppercase text-white">Shop</h4>
                    <ul class="space-y-2 text-xs">
                        <li v-for="i in ['All Products','Men','Women','Unisex','Fight Gear','Sale']" :key="i"><a href="#" class="hover:text-d2gb-gold">{{ i }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-heading text-sm font-bold uppercase text-white">Company</h4>
                    <ul class="space-y-2 text-xs">
                        <li v-for="i in ['About Us','Our Story','Blog','Careers','Contact Us']" :key="i"><a href="#" class="hover:text-d2gb-gold">{{ i }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-heading text-sm font-bold uppercase text-white">Customer Care</h4>
                    <ul class="space-y-2 text-xs">
                        <li v-for="i in ['Shipping & Delivery','Returns & Exchanges','Size Guide','FAQ','Track Your Order']" :key="i"><a href="#" class="hover:text-d2gb-gold">{{ i }}</a></li>
                    </ul>
                    <p class="mt-5 font-heading text-xs font-bold uppercase text-white">We Accept</p>
                    <div class="mt-2 flex gap-2 text-[10px] font-bold">
                        <span class="rounded bg-white px-2 py-1 text-neutral-900">VISA</span>
                        <span class="rounded bg-white px-2 py-1 text-neutral-900">MC</span>
                        <span class="rounded bg-white px-2 py-1 text-neutral-900">PayPal</span>
                        <span class="rounded bg-white px-2 py-1 text-neutral-900">Pay</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 py-5 text-center text-xs">
                © 2024 Dare To Go Bare. All Rights Reserved.
            </div>
        </footer>
    </div>
</template>
