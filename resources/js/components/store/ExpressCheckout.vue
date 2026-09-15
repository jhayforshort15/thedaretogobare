<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    productId: number;
    quantity: number;
    size: string | null;
    unitPrice: number;
}>();

const page = usePage();
const stripe = (page.props.stripe as { key?: string; enabled?: boolean } | undefined) ?? {};

const container = ref<HTMLElement | null>(null);
const errorMsg = ref('');
const available = ref(false); // becomes true only if wallet buttons actually render

// Free shipping over threshold, else flat rate (mirrors the backend CartService).
const FREE_SHIPPING_THRESHOLD = 150;
const FLAT_SHIPPING = 10;

const lineSubtotal = () => Math.round(props.unitPrice * props.quantity * 100); // cents
const shippingCents = () => (props.unitPrice * props.quantity >= FREE_SHIPPING_THRESHOLD ? 0 : FLAT_SHIPPING * 100);
const totalCents = () => lineSubtotal() + shippingCents();

function csrfToken(): string {
    const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return m ? decodeURIComponent(m[1]) : '';
}

let stripeJsPromise: Promise<any> | null = null;
function loadStripeJs(): Promise<any> {
    if ((window as any).Stripe) return Promise.resolve((window as any).Stripe);
    if (!stripeJsPromise) {
        stripeJsPromise = new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = 'https://js.stripe.com/v3/';
            s.onload = () => resolve((window as any).Stripe);
            s.onerror = () => reject(new Error('Failed to load Stripe.js'));
            document.head.appendChild(s);
        });
    }
    return stripeJsPromise;
}

onMounted(async () => {
    if (!stripe.enabled || !stripe.key || !container.value) return;

    try {
        const StripeCtor = await loadStripeJs();
        const stripeClient = StripeCtor(stripe.key);

        const elements = stripeClient.elements({
            mode: 'payment',
            amount: totalCents(),
            currency: 'usd',
        });

        const expressCheckout = elements.create('expressCheckout');
        expressCheckout.mount(container.value);

        // Keep the amount in sync if the customer changes quantity.
        watch(
            () => props.quantity,
            () => elements.update({ amount: totalCents() }),
        );

        // Show the wrapper only when at least one wallet is available.
        expressCheckout.on('ready', (event: any) => {
            available.value = !!(event?.availablePaymentMethods);
        });

        expressCheckout.on('click', (event: any) => {
            event.resolve({
                emailRequired: true,
                phoneNumberRequired: false,
                shippingAddressRequired: true,
                shippingRates: [
                    {
                        id: 'standard',
                        displayName: shippingCents() === 0 ? 'Free shipping' : 'Standard shipping',
                        amount: shippingCents(),
                    },
                ],
                lineItems: [{ name: 'Order subtotal', amount: lineSubtotal() }],
            });
        });

        expressCheckout.on('confirm', async (event: any) => {
            errorMsg.value = '';

            const { error: submitError } = await elements.submit();
            if (submitError) {
                errorMsg.value = submitError.message ?? 'Payment could not be started.';
                return;
            }

            const ship = event.shippingAddress ?? {};
            const addr = ship.address ?? {};
            const billing = event.billingDetails ?? {};
            const fullName: string = ship.name ?? billing.name ?? '';
            const [firstName, ...rest] = fullName.trim().split(' ');

            try {
                const res = await fetch('/express/intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-XSRF-TOKEN': csrfToken(),
                    },
                    body: JSON.stringify({
                        product_id: props.productId,
                        size: props.size,
                        quantity: props.quantity,
                        email: billing.email ?? '',
                        first_name: firstName || 'Customer',
                        last_name: rest.join(' '),
                        phone: billing.phone ?? null,
                        shipping_address: [addr.line1, addr.line2].filter(Boolean).join(', '),
                        shipping_city: addr.city ?? '',
                        shipping_state: addr.state ?? null,
                        shipping_postal_code: addr.postal_code ?? null,
                        shipping_country: addr.country ?? 'United States',
                    }),
                });

                if (!res.ok) {
                    errorMsg.value = 'We could not process the payment. Please use the cart checkout.';
                    return;
                }

                const data = await res.json();
                const { error } = await stripeClient.confirmPayment({
                    elements,
                    clientSecret: data.client_secret,
                    confirmParams: { return_url: window.location.origin + data.return_url },
                });

                if (error) {
                    errorMsg.value = error.message ?? 'Payment failed.';
                }
            } catch {
                errorMsg.value = 'Something went wrong starting the payment.';
            }
        });
    } catch {
        // Stripe.js failed to load — silently hide (cart checkout still works).
    }
});
</script>

<template>
    <div class="mt-4">
        <!-- Real Stripe Express Checkout Element (renders when keys are set + on a supported device) -->
        <template v-if="stripe.enabled">
            <div v-show="available" class="mb-2 flex items-center gap-3">
                <span class="h-px flex-1 bg-neutral-200"></span>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Express Checkout</span>
                <span class="h-px flex-1 bg-neutral-200"></span>
            </div>
            <div ref="container"></div>
            <p v-if="errorMsg" class="mt-2 text-xs text-red-500">{{ errorMsg }}</p>
        </template>

        <!-- Preview (shown until Stripe keys are configured) -->
        <div v-else class="space-y-2.5">
            <div class="mb-1 flex items-center gap-3">
                <span class="h-px flex-1 bg-neutral-200"></span>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Express Checkout</span>
                <span class="h-px flex-1 bg-neutral-200"></span>
            </div>

            <!-- Apple Pay -->
            <button type="button" class="flex h-12 w-full items-center justify-center rounded-lg bg-black text-white" aria-label="Apple Pay">
                <svg viewBox="0 0 24 24" class="mr-1 h-5 w-5" fill="currentColor"><path d="M17.05 12.5c-.03-2.6 2.13-3.85 2.22-3.91-1.21-1.77-3.1-2.02-3.77-2.05-1.6-.16-3.13.94-3.94.94-.81 0-2.07-.92-3.4-.9-1.75.03-3.37 1.02-4.27 2.58-1.82 3.16-.47 7.84 1.31 10.41.87 1.26 1.9 2.67 3.26 2.62 1.31-.05 1.8-.85 3.38-.85s2.03.85 3.42.82c1.41-.02 2.31-1.28 3.17-2.55.99-1.46 1.4-2.87 1.42-2.94-.03-.01-2.72-1.05-2.75-4.16zM14.6 4.87c.72-.87 1.2-2.08 1.07-3.29-1.03.04-2.28.69-3.02 1.56-.66.76-1.24 1.99-1.09 3.16 1.15.09 2.32-.58 3.04-1.43z"/></svg>
                <span class="text-lg font-semibold">Pay</span>
            </button>

            <!-- Google Pay -->
            <button type="button" class="flex h-12 w-full items-center justify-center gap-1 rounded-lg bg-black text-white" aria-label="Google Pay">
                <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="#4285F4" d="M22.5 12.2c0-.7-.06-1.4-.18-2.06H12v3.9h5.9a5.05 5.05 0 0 1-2.19 3.31v2.75h3.54c2.07-1.9 3.25-4.72 3.25-7.9z"/><path fill="#34A853" d="M12 23c2.95 0 5.43-.98 7.24-2.65l-3.54-2.75c-.98.66-2.24 1.05-3.7 1.05-2.85 0-5.26-1.92-6.12-4.5H2.23v2.84A11 11 0 0 0 12 23z"/><path fill="#FBBC04" d="M5.88 14.15a6.6 6.6 0 0 1 0-4.3V7.01H2.23a11 11 0 0 0 0 9.98l3.65-2.84z"/><path fill="#EA4335" d="M12 5.35c1.6 0 3.05.55 4.19 1.64l3.13-3.13C17.43 2.09 14.95 1 12 1A11 11 0 0 0 2.23 7.01l3.65 2.84C6.74 7.27 9.15 5.35 12 5.35z"/></svg>
                <span class="text-lg font-semibold">Pay</span>
            </button>

            <!-- Link -->
            <button type="button" class="flex h-12 w-full items-center justify-center gap-1.5 rounded-lg bg-[#33ddb3] text-black" aria-label="Pay with Link">
                <span class="text-base font-medium">Pay securely with</span>
                <span class="grid h-5 w-5 place-items-center rounded-full bg-black">
                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="#33ddb3" stroke-width="3"><path d="M8 5l7 7-7 7"/></svg>
                </span>
                <span class="text-lg font-bold">link</span>
            </button>

            <p class="pt-1 text-center text-[11px] text-neutral-400">Preview — activates once Stripe keys are added.</p>
        </div>
    </div>
</template>
