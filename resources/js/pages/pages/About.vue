<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';
</script>

<template>
    <Head title="About | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <!-- Hero -->
        <section class="relative overflow-hidden bg-black">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_40%,rgba(200,164,100,0.18),transparent_60%)]"></div>
            <div class="relative mx-auto max-w-4xl px-4 py-20 text-center">
                <p class="font-heading text-sm font-bold uppercase tracking-widest text-d2gb-gold">Our Story</p>
                <h1 class="mt-2 font-display text-5xl uppercase leading-none md:text-7xl">More Than Fightwear</h1>
                <p class="mx-auto mt-6 max-w-2xl text-white/70">
                    Dare To Go Bare is a lifestyle. It's about heart, discipline, and the courage to show up — every single day.
                </p>
            </div>
        </section>

        <!-- Body -->
        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-3xl px-4 py-16">
                <div class="space-y-6 text-base leading-relaxed text-neutral-700">
                    <p>Born from the raw world of bare knuckle fighting, D2GB was built for warriors who refuse to hide behind gloves or excuses. Every piece we make is engineered for the grind — tough, honest, and unmistakably bold.</p>
                    <p>We don't just sell apparel. We stand for a mindset: train hard, stay humble, and dare to step into the ring when others step back. Whether you're a fighter, a fan, or someone chasing a harder version of yourself, this is your gear.</p>
                    <p>No gloves. No excuses. <strong>Dare To Go Bare.</strong></p>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="rounded-md bg-neutral-50 p-6 text-center">
                        <p class="font-display text-3xl text-d2gb-gold">Heart</p>
                        <p class="mt-1 text-sm text-neutral-600">Show up every day.</p>
                    </div>
                    <div class="rounded-md bg-neutral-50 p-6 text-center">
                        <p class="font-display text-3xl text-d2gb-gold">Discipline</p>
                        <p class="mt-1 text-sm text-neutral-600">Earn every win.</p>
                    </div>
                    <div class="rounded-md bg-neutral-50 p-6 text-center">
                        <p class="font-display text-3xl text-d2gb-gold">Courage</p>
                        <p class="mt-1 text-sm text-neutral-600">Dare to go bare.</p>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <Link href="/shop" class="inline-block bg-neutral-900 px-8 py-3 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black">Shop The Collection</Link>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
