<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\PrintifyProductImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductColorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.stripe.secret' => null,
            'services.stripe.key' => null,
            'services.printify.token' => 'test-token',
            'services.printify.shop_id' => '111',
        ]);
    }

    /** A Printify product in the real API shape: "Color / Size" titles plus structured options. */
    protected function importTee(): Product
    {
        return app(PrintifyProductImporter::class)->import([
            'id' => 'tee1',
            'title' => 'Fight Night Tee',
            'description' => 'Soft tee',
            'visible' => true,
            'options' => [
                ['name' => 'Colors', 'type' => 'color', 'values' => [
                    ['id' => 10, 'title' => 'Black', 'colors' => ['#000000']],
                    ['id' => 11, 'title' => 'White', 'colors' => ['#FFFFFF']],
                ]],
                ['name' => 'Sizes', 'type' => 'size', 'values' => [
                    ['id' => 1, 'title' => 'M'],
                    ['id' => 2, 'title' => '2XL'],
                ]],
            ],
            'variants' => [
                ['id' => 101, 'title' => 'Black / M', 'options' => [10, 1], 'price' => 2500, 'is_enabled' => true],
                ['id' => 102, 'title' => 'Black / 2XL', 'options' => [10, 2], 'price' => 2900, 'is_enabled' => true],
                ['id' => 201, 'title' => 'White / M', 'options' => [11, 1], 'price' => 2500, 'is_enabled' => true],
            ],
            'images' => [
                ['src' => 'https://img/black.png', 'variant_ids' => [101, 102], 'is_default' => true],
                ['src' => 'https://img/white.png', 'variant_ids' => [201], 'is_default' => false],
            ],
        ]);
    }

    public function test_import_reads_size_and_color_from_printify_options()
    {
        $product = $this->importTee();

        $white = $product->variants()->where('printify_variant_id', 201)->first();
        $this->assertSame('M', $white->size);
        $this->assertSame('White', $white->color);
        $this->assertSame('#FFFFFF', $white->color_hex);
        $this->assertSame('https://img/white.png', $white->image);
    }

    public function test_color_is_required_when_the_product_has_colors()
    {
        $product = $this->importTee();

        $this->post('/cart', ['product_id' => $product->id, 'size' => 'M'])
            ->assertSessionHasErrors(['variant' => 'Please choose a color first.']);

        $this->post('/cart', ['product_id' => $product->id, 'size' => '2XL', 'color' => 'White'])
            ->assertSessionHasErrors(['variant' => 'That size and color combination is not available.']);
    }

    public function test_chosen_color_and_variant_price_flow_through_to_the_printify_order()
    {
        Http::fake(['*/orders.json' => Http::response(['id' => 'pfy-order-1'])]);

        $product = $this->importTee();

        $this->post('/cart', ['product_id' => $product->id, 'size' => '2XL', 'color' => 'Black'])
            ->assertSessionHasNoErrors();

        $this->post('/checkout', [
            'email' => 'fan@example.com',
            'first_name' => 'Sam',
            'last_name' => 'Cruz',
            'shipping_address' => '1 Main St',
            'shipping_city' => 'Manila',
            'shipping_country' => 'PH',
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $item = $order->items->first();
        $this->assertSame('2XL', $item->size);
        $this->assertSame('Black', $item->color);
        $this->assertEquals(29.00, (float) $item->price); // 2XL price, not the base price

        $order->update(['status' => 'paid', 'payment_status' => 'paid']);

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/orders.json')
            && $request['line_items'][0]['variant_id'] === 102);
        $this->assertSame('pfy-order-1', $order->fresh()->printify_order_id);
    }
}
