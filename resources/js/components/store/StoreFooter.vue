<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';
import { IconFacebook, IconInstagram, IconTiktok, IconYoutube } from './socialIcons';

const shopLinks = [
    { label: 'All Products', href: '/shop' },
    { label: 'Men', href: '/shop?category=men' },
    { label: 'Women', href: '/shop?category=women' },
    { label: 'Unisex', href: '/shop?category=unisex' },
    { label: 'Fight Gear', href: '/shop?category=fight-gear' },
    { label: 'Sale', href: '/shop' },
];
const companyLinks = ['About Us', 'Our Story', 'Blog', 'Careers', 'Contact Us'];
const careLinks = ['Shipping & Delivery', 'Returns & Exchanges', 'Size Guide', 'FAQ', 'Track Your Order'];
</script>

<template>
    <div>
        <!-- Newsletter -->
        <section class="bg-d2gb-charcoal">
            <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-4 py-12 md:flex-row md:justify-between">
                <div>
                    <h2 class="font-display text-3xl uppercase text-white md:text-4xl">Join The Movement</h2>
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
                        <li v-for="l in shopLinks" :key="l.label"><Link :href="l.href" class="hover:text-d2gb-gold">{{ l.label }}</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-heading text-sm font-bold uppercase text-white">Company</h4>
                    <ul class="space-y-2 text-xs">
                        <li v-for="i in companyLinks" :key="i"><a href="#" class="hover:text-d2gb-gold">{{ i }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-heading text-sm font-bold uppercase text-white">Customer Care</h4>
                    <ul class="space-y-2 text-xs">
                        <li v-for="i in careLinks" :key="i"><a href="#" class="hover:text-d2gb-gold">{{ i }}</a></li>
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
            <div class="border-t border-white/10 py-5 text-center text-xs">© 2024 Dare To Go Bare. All Rights Reserved.</div>
        </footer>

        <Toaster position="top-center" rich-colors />
    </div>
</template>
