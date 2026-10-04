<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PrintifyWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.printify.token' => 'test-token',
            'services.printify.shop_id' => '111',
            'services.printify.webhook_secret' => 'secret',
        ]);
    }

    protected function sendWebhook(array $event)
    {
        $body = json_encode($event);

        return $this->call('POST', '/printify/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PFY_SIGNATURE' => 'sha256='.hash_hmac('sha256', $body, 'secret'),
        ], $body);
    }

    public function test_publishing_in_printify_creates_the_product_and_reports_success()
    {
        Http::fake([
            '*/products/abc123.json' => Http::response([
                'id' => 'abc123',
                'title' => 'Fight Night Tee',
                'description' => '<p>Soft cotton tee</p>',
                'visible' => true,
                'images' => [['src' => 'https://images.printify.com/tee.png', 'is_default' => true]],
                'variants' => [
                    ['id' => 1, 'title' => 'S / Black', 'price' => 2500, 'is_enabled' => true, 'sku' => 'T-S'],
                    ['id' => 2, 'title' => 'M / Black', 'price' => 2700, 'is_enabled' => true, 'sku' => 'T-M'],
                    ['id' => 3, 'title' => 'L / Black', 'price' => 2700, 'is_enabled' => false],
                ],
            ]),
            '*/publishing_succeeded.json' => Http::response([], 200),
        ]);

        $this->sendWebhook([
            'type' => 'product:publish:started',
            'resource' => ['id' => 'abc123', 'type' => 'product'],
        ])->assertOk();

        $product = Product::where('printify_product_id', 'abc123')->firstOrFail();
        $this->assertSame('Fight Night Tee', $product->name);
        $this->assertEquals(25.00, (float) $product->price);
        $this->assertTrue($product->is_active);
        $this->assertTrue($product->is_featured);
        $this->assertSame(['S', 'M'], $product->variants()->orderBy('id')->pluck('size')->all());

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/products/abc123/publishing_succeeded.json')
            && $request['external']['id'] === (string) $product->id);
    }

    public function test_deleting_in_printify_hides_the_product()
    {
        $product = Product::create([
            'printify_product_id' => 'gone1',
            'name' => 'Old Tee',
            'slug' => 'old-tee',
            'price' => 20,
            'stock' => 9999,
            'is_active' => true,
        ]);

        $this->sendWebhook([
            'type' => 'product:deleted',
            'resource' => ['id' => 'gone1', 'type' => 'product'],
        ])->assertOk();

        $this->assertFalse($product->fresh()->is_active);
    }

    public function test_invalid_signature_is_rejected()
    {
        $this->call('POST', '/printify/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PFY_SIGNATURE' => 'sha256=wrong',
        ], '{}')->assertStatus(400);
    }
}
