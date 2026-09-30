<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Post { title: string; excerpt: string | null; body: string | null; image: string | null; date: string | null }

defineProps<{ post: Post }>();
</script>

<template>
    <Head :title="`${post.title} | Dare To Go Bare`" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-3xl px-4 py-10">
                <Link href="/blog" class="mb-3 inline-flex items-center gap-1 font-heading text-xs font-bold uppercase tracking-wide text-white/60 hover:text-d2gb-gold">
                    <ArrowLeft class="h-3 w-3" /> Back to blog
                </Link>
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">{{ post.date }}</p>
                <h1 class="mt-1 font-display text-4xl uppercase leading-tight md:text-5xl">{{ post.title }}</h1>
            </div>
        </section>

        <article class="bg-white text-neutral-900">
            <div class="mx-auto max-w-3xl px-4 py-12">
                <div v-if="post.image" class="mb-8 overflow-hidden rounded-md">
                    <img :src="post.image" :alt="post.title" class="w-full object-cover" />
                </div>
                <div class="whitespace-pre-line text-base leading-relaxed text-neutral-700">{{ post.body }}</div>
            </div>
        </article>

        <StoreFooter />
    </div>
</template>
