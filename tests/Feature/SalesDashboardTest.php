<?php

namespace Tests\Feature;

use App\Filament\Widgets\LatestOrdersTable;
use App\Filament\Widgets\OrdersByStatusChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\SalesStatsOverview;
use App\Filament\Widgets\TopProductsTable;
use App\Models\Order;
use App\Models\User;
use App\Services\SalesAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SalesDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function order(float $total, string $payment, string $placed, string $status = 'paid', string $item = 'Fight Tee', int $qty = 1): Order
    {
        $order = Order::create([
            'order_number' => 'D2GB-'.uniqid(),
            'email' => 'fan@example.com', 'first_name' => 'Sam', 'last_name' => 'Cruz',
            'shipping_address' => '1 Main St', 'shipping_city' => 'Manila', 'shipping_country' => 'PH',
            'subtotal' => $total, 'shipping_cost' => 0, 'tax' => 0, 'total' => $total,
            'status' => $status, 'payment_status' => $payment,
        ]);
        $order->items()->create(['name' => $item, 'price' => $total / $qty, 'quantity' => $qty, 'subtotal' => $total]);
        $order->forceFill(['created_at' => Carbon::parse($placed)])->saveQuietly();

        return $order;
    }

    public function test_summary_counts_only_paid_orders_and_compares_with_previous_period()
    {
        Carbon::setTestNow('2026-10-06 12:00:00');

        $this->order(100, 'paid', '2026-10-01');
        $this->order(50, 'paid', '2026-09-20');
        $this->order(999, 'unpaid', '2026-10-02');     // ignored: not paid
        $this->order(999, 'refunded', '2026-10-03');   // ignored: refunded
        $this->order(75, 'paid', '2026-08-20');        // previous 30-day period

        $summary = app(SalesAnalytics::class)->summary(30);

        $this->assertEquals(150.0, $summary['revenue']);
        $this->assertSame(2, $summary['orders']);
        $this->assertEquals(75.0, $summary['average']);
        $this->assertEquals(100.0, $summary['revenue_change']); // 75 -> 150
    }

    public function test_series_buckets_revenue_by_day_and_top_products_rank_by_units()
    {
        Carbon::setTestNow('2026-10-06 12:00:00');

        $this->order(60, 'paid', '2026-10-05 09:00', item: 'Hoodie', qty: 1);
        $this->order(40, 'paid', '2026-10-05 18:00', item: 'Tee', qty: 2);
        $this->order(20, 'paid', '2026-10-06 08:00', item: 'Tee', qty: 1);

        $series = app(SalesAnalytics::class)->series(7);
        $this->assertCount(7, $series);
        $this->assertEquals(100.0, $series['2026-10-05']['revenue']);
        $this->assertSame(2, $series['2026-10-05']['orders']);

        $top = app(SalesAnalytics::class)->topProductsQuery(30)->orderByDesc('units')->get();
        $this->assertSame('Tee', $top->first()->name);
        $this->assertEquals(3, $top->first()->units);
    }

    public function test_admin_dashboard_and_orders_page_show_the_analytics()
    {
        $this->order(120, 'paid', now()->subDay()->toDateTimeString(), item: 'Fight Night Tee');
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $this->get('/admin')->assertOk()->assertSee('Sales overview');
        $this->get('/admin/orders')->assertOk();

        // Widgets load lazily, so render each one directly.
        Livewire::test(SalesStatsOverview::class)->assertSee('$120.00')->assertSee('Average order');
        Livewire::test(RevenueChart::class)->assertOk();
        Livewire::test(OrdersByStatusChart::class)->assertOk();
        Livewire::test(TopProductsTable::class)->assertSee('Fight Night Tee');
        Livewire::test(LatestOrdersTable::class)->assertSee('Sam Cruz');
    }
}
