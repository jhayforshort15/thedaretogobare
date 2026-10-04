<?php

namespace Tests\Feature;

use App\Mail\PrintifyIssueMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\PrintifyFulfillment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PrintifyFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.printify.token' => 'test-token',
            'services.printify.shop_id' => '111',
            'services.printify.webhook_secret' => null,
            'services.printify.auto_production' => true,
            'services.store.order_notification_email' => 'owner@example.com',
        ]);

        Mail::fake();
    }

    protected function pendingOrder(): Order
    {
        $product = Product::create([
            'printify_product_id' => 'pfy-prod',
            'name' => 'Fight Night Tee',
            'slug' => 'fight-night-tee',
            'price' => 25,
            'stock' => 9999,
            'is_active' => true,
        ]);
        $variant = $product->variants()->create([
            'printify_variant_id' => '555', 'size' => 'M', 'color' => 'Black', 'stock' => 9999,
        ]);

        $order = Order::create([
            'order_number' => 'D2GB-TEST0001',
            'email' => 'fan@example.com',
            'first_name' => 'Sam',
            'last_name' => 'Cruz',
            'shipping_address' => '1 Main St',
            'shipping_city' => 'Manila',
            'shipping_country' => 'PH',
            'subtotal' => 25, 'shipping_cost' => 10, 'tax' => 0, 'total' => 35,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'product_variant_id' => $variant->id,
            'name' => 'Fight Night Tee', 'size' => 'M', 'color' => 'Black',
            'price' => 25, 'quantity' => 1, 'subtotal' => 25,
        ]);

        return $order;
    }

    public function test_paid_order_is_created_in_printify_and_sent_to_production()
    {
        Http::fake([
            '*/orders.json' => Http::response(['id' => 'pfy-1']),
            '*/orders/pfy-1/send_to_production.json' => Http::response([]),
        ]);

        $order = $this->pendingOrder();
        $order->update(['status' => 'paid', 'payment_status' => 'paid']);

        $order->refresh();
        $this->assertSame('pfy-1', $order->printify_order_id);
        $this->assertSame('sending-to-production', $order->printify_status);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/orders/pfy-1/send_to_production.json'));
    }

    public function test_orders_stay_on_hold_when_auto_production_is_off()
    {
        config(['services.printify.auto_production' => false]);
        Http::fake(['*/orders.json' => Http::response(['id' => 'pfy-1'])]);

        $order = $this->pendingOrder();
        $order->update(['status' => 'paid', 'payment_status' => 'paid']);

        $this->assertSame('on-hold', $order->fresh()->printify_status);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'send_to_production'));

        // Admin approves it later.
        Http::fake(['*/send_to_production.json' => Http::response([])]);
        app(PrintifyFulfillment::class)->sendToProduction($order->fresh());

        $this->assertSame('sending-to-production', $order->fresh()->printify_status);
    }

    public function test_failed_submission_is_recorded_and_the_admin_is_alerted_then_retry_succeeds()
    {
        Http::fakeSequence('*/orders.json')
            ->push(['message' => 'Validation failed.', 'errors' => ['reason' => 'address_to.zip is required']], 400)
            ->push(['id' => 'pfy-2']);
        Http::fake(['*/send_to_production.json' => Http::response([])]);

        $order = $this->pendingOrder();
        $order->update(['status' => 'paid', 'payment_status' => 'paid']);

        $order->refresh();
        $this->assertSame('failed', $order->printify_status);
        $this->assertStringContainsString('address_to.zip is required', $order->printify_error);
        Mail::assertSent(PrintifyIssueMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));

        // Retry from the admin once the problem is fixed.
        app(PrintifyFulfillment::class)->fulfil($order);

        $order->refresh();
        $this->assertSame('pfy-2', $order->printify_order_id);
        $this->assertSame('sending-to-production', $order->printify_status);
        $this->assertNull($order->printify_error);
    }

    public function test_printify_status_webhooks_update_the_order()
    {
        $order = $this->pendingOrder();
        $order->updateQuietly(['status' => 'paid', 'printify_order_id' => 'pfy-3', 'printify_status' => 'sending-to-production']);

        $this->postJson('/printify/webhook', [
            'type' => 'order:sent-to-production',
            'resource' => ['id' => 'pfy-3', 'type' => 'order', 'data' => []],
        ])->assertOk();
        $this->assertSame('in-production', $order->fresh()->printify_status);

        $this->postJson('/printify/webhook', [
            'type' => 'order:shipment:created',
            'resource' => ['id' => 'pfy-3', 'type' => 'order', 'data' => [
                'shipments' => [['carrier' => 'usps', 'number' => '9400111', 'url' => 'https://track.example/9400111']],
            ]],
        ])->assertOk();

        $order->refresh();
        $this->assertSame('fulfilled', $order->printify_status);
        $this->assertSame('shipped', $order->status);
        $this->assertSame('9400111', $order->tracking_number);
        Mail::assertNotSent(PrintifyIssueMail::class);
    }

    public function test_admin_order_pages_show_printify_status_and_actions()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->pendingOrder();
        $order->updateQuietly([
            'status' => 'paid', 'payment_status' => 'paid',
            'printify_status' => 'failed', 'printify_error' => 'address_to.zip is required',
        ]);

        $this->actingAs($admin)->get('/admin/orders')
            ->assertOk()
            ->assertSee('Failed to send');

        $this->actingAs($admin)->get("/admin/orders/{$order->id}/edit")
            ->assertOk()
            ->assertSee('Printify fulfilment')
            ->assertSee('address_to.zip is required')
            ->assertSee('Send to Printify');
    }

    public function test_canceled_by_printify_alerts_the_admin()
    {
        $order = $this->pendingOrder();
        $order->updateQuietly(['status' => 'paid', 'printify_order_id' => 'pfy-4', 'printify_status' => 'in-production']);

        $this->postJson('/printify/webhook', [
            'type' => 'order:updated',
            'resource' => ['id' => 'pfy-4', 'type' => 'order', 'data' => ['status' => 'canceled']],
        ])->assertOk();

        $this->assertSame('canceled', $order->fresh()->printify_status);
        Mail::assertSent(PrintifyIssueMail::class);
    }
}
