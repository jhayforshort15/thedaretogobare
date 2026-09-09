<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Search, User, ShoppingCart, Menu } from '@lucide/vue';
import { IconFacebook, IconInstagram, IconTiktok, IconYoutube } from './socialIcons';

const page = usePage();
const cartCount = computed<number>(() => (page.props.cart as { count?: number } | undefined)?.count ?? 0);

const navLinks = [
    { label: 'Shop', href: '/shop' },
    { label: 'Men', href: '/shop?category=men' },
    { label: 'Women', href: '/shop?category=women' },
    { label: 'Unisex', href: '/shop?category=unisex' },
    { label: 'Fightwear', href: '/shop?category=fight-gear' },
    { label: 'Upcoming Fights', href: '/#fights' },
    { label: 'About', href: '/#about' },
];
</script>

<template>
    <div>
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
                <Link href="/" class="flex items-center gap-2">
                    <span class="grid h-11 w-11 place-items-center rounded-full border-2 border-d2gb-gold text-d2gb-gold">
                        <span class="font-display text-lg leading-none">D2</span>
                    </span>
                    <span class="font-display text-lg leading-none tracking-wide text-white">DARE TO<br />GO BARE</span>
                </Link>

                <nav class="hidden items-center gap-6 lg:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="font-heading text-sm font-semibold uppercase tracking-wide text-white/80 transition hover:text-d2gb-gold"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-4 text-white/80">
                    <button class="transition hover:text-d2gb-gold" aria-label="Search"><Search class="h-5 w-5" /></button>
                    <Link href="/login" class="transition hover:text-d2gb-gold" aria-label="Account"><User class="h-5 w-5" /></Link>
                    <Link href="/cart" class="relative transition hover:text-d2gb-gold" aria-label="Cart">
                        <ShoppingCart class="h-5 w-5" />
                        <span v-if="cartCount > 0" class="absolute -right-2 -top-2 grid h-4 w-4 place-items-center rounded-full bg-d2gb-gold text-[10px] font-bold text-black">{{ cartCount }}</span>
                    </Link>
                    <button class="lg:hidden" aria-label="Menu"><Menu class="h-6 w-6" /></button>
                </div>
            </div>
        </header>
    </div>
</template>
