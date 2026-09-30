<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { MapPin, Phone, Mail, Clock } from '@lucide/vue';
import StoreHeader from '@/components/store/StoreHeader.vue';
import StoreFooter from '@/components/store/StoreFooter.vue';

interface Contact { address: string; phone: string; email: string; hours: string }
defineProps<{ contact: Contact }>();

const page = usePage();
const flashSuccess = computed<string | null>(() => (page.props.flash as { success?: string } | undefined)?.success ?? null);

const form = useForm({ name: '', email: '', message: '' });
const submit = () => form.post('/contact-us', { preserveScroll: true, onSuccess: () => form.reset() });
</script>

<template>
    <Head title="Contact Us | Dare To Go Bare" />

    <div class="min-h-screen bg-d2gb-dark font-sans text-white">
        <StoreHeader />

        <section class="border-b border-white/10 bg-black">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <p class="font-heading text-xs font-bold uppercase tracking-widest text-d2gb-gold">Get In Touch</p>
                <h1 class="font-display text-5xl uppercase md:text-6xl">Contact Us</h1>
            </div>
        </section>

        <section class="bg-white text-neutral-900">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-2">
                <!-- Details -->
                <div>
                    <h2 class="font-display text-3xl uppercase">Reach The Team</h2>
                    <p class="mt-2 text-neutral-600">Questions about your order, sizing, or the brand? We're here 24/7.</p>
                    <ul class="mt-8 space-y-5">
                        <li class="flex items-start gap-3"><MapPin class="mt-0.5 h-5 w-5 text-d2gb-gold" /><div><p class="font-heading text-sm font-bold uppercase">Address</p><p class="text-neutral-600">{{ contact.address }}</p></div></li>
                        <li class="flex items-start gap-3"><Phone class="mt-0.5 h-5 w-5 text-d2gb-gold" /><div><p class="font-heading text-sm font-bold uppercase">Phone</p><a :href="`tel:${contact.phone}`" class="text-neutral-600 hover:text-d2gb-gold">{{ contact.phone }}</a></div></li>
                        <li class="flex items-start gap-3"><Mail class="mt-0.5 h-5 w-5 text-d2gb-gold" /><div><p class="font-heading text-sm font-bold uppercase">Email</p><a :href="`mailto:${contact.email}`" class="text-neutral-600 hover:text-d2gb-gold">{{ contact.email }}</a></div></li>
                        <li class="flex items-start gap-3"><Clock class="mt-0.5 h-5 w-5 text-d2gb-gold" /><div><p class="font-heading text-sm font-bold uppercase">Hours</p><p class="text-neutral-600">{{ contact.hours }}</p></div></li>
                    </ul>
                </div>

                <!-- Form -->
                <div class="rounded-md border border-neutral-200 p-6">
                    <div v-if="flashSuccess" class="mb-4 rounded bg-green-100 px-4 py-3 text-sm text-green-700">{{ flashSuccess }}</div>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Name</label>
                            <input v-model="form.name" type="text" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Email</label>
                            <input v-model="form.email" type="email" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900" />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase text-neutral-600">Message</label>
                            <textarea v-model="form.message" rows="5" class="w-full border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-neutral-900"></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-xs text-red-500">{{ form.errors.message }}</p>
                        </div>
                        <button type="submit" :disabled="form.processing" class="w-full bg-neutral-900 py-3.5 font-heading text-sm font-bold uppercase tracking-wider text-white transition hover:bg-d2gb-gold hover:text-black disabled:opacity-50">
                            {{ form.processing ? 'Sending…' : 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <StoreFooter />
    </div>
</template>
