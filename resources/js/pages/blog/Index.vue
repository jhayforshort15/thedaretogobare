<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Post { title: string; slug: string; excerpt: string | null; image: string | null; date: string | null }

defineProps<{ posts: Post[] }>();
</script>

<template>
    <Head title="Blog | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Latest News</p>
                <h1 class="font-display text-5xl uppercase md:text-6xl">From The D2GB Blog</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div v-if="posts.length" class="grid gap-8 md:grid-cols-3">
                    <Link v-for="post in posts" :key="post.slug" :href="`/blog/${post.slug}`" class="group">
                        <div class="mb-4 h-48 overflow-hidden rounded-md bg-gradient-to-br from-neutral-300 to-neutral-500">
                            <img v-if="post.image" :src="post.image" :alt="post.title" class="h-full w-full object-cover" />
                        </div>
                        <p class="text-xs uppercase tracking-wide text-neutral-500">{{ post.date }}</p>
                        <h2 class="mt-1 font-heading text-lg font-semibold uppercase leading-snug transition group-hover:text-d2gb-gold">{{ post.title }}</h2>
                        <p v-if="post.excerpt" class="mt-2 text-sm text-neutral-600">{{ post.excerpt }}</p>
                    </Link>
                </div>
                <div v-else class="py-20 text-center">
                    <p class="font-display text-3xl uppercase text-neutral-400">No articles yet</p>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
