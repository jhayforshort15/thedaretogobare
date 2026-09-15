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
    <div v-if="stripe.enabled" class="mt-4">
        <div v-show="available">
            <div class="mb-2 flex items-center gap-3">
                <span class="h-px flex-1 bg-neutral-200"></span>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Express Checkout</span>
                <span class="h-px flex-1 bg-neutral-200"></span>
            </div>
            <p v-if="errorMsg" class="mt-2 text-xs text-red-500">{{ errorMsg }}</p>
        </div>
        <div ref="container"></div>
    </div>
</template>
