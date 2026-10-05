<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Sales numbers for the admin dashboard. "Sales" = orders with payment_status "paid"
 * (refunded and unpaid orders are left out), dated by when the order was placed.
 */
class SalesAnalytics
{
    public function paidOrders(?CarbonImmutable $from = null, ?CarbonImmutable $to = null): Builder
    {
        return Order::query()
            ->where('payment_status', 'paid')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<', $to));
    }

    /**
     * Revenue, order count and average order value for the last $days days,
     * plus the same figures for the $days before that (for the % change).
     */
    public function summary(int $days): array
    {
        $now = CarbonImmutable::now();
        $start = $now->subDays($days)->startOfDay();
        $prevStart = $start->subDays($days);

        $current = $this->totals($start, $now);
        $previous = $this->totals($prevStart, $start);

        return [
            'revenue' => $current['revenue'],
            'orders' => $current['orders'],
            'average' => $current['orders'] ? $current['revenue'] / $current['orders'] : 0.0,
            'revenue_change' => $this->percentChange($previous['revenue'], $current['revenue']),
            'orders_change' => $this->percentChange($previous['orders'], $current['orders']),
            'average_change' => $this->percentChange(
                $previous['orders'] ? $previous['revenue'] / $previous['orders'] : 0.0,
                $current['orders'] ? $current['revenue'] / $current['orders'] : 0.0,
            ),
        ];
    }

    /**
     * Paid revenue and order count per day (or per month) — grouped in PHP so it works on MySQL and SQLite.
     *
     * @return Collection<string, array{label:string, revenue:float, orders:int}>
     */
    public function series(int $days, bool $monthly = false): Collection
    {
        $now = CarbonImmutable::now();
        $start = $monthly ? $now->subMonths(11)->startOfMonth() : $now->subDays($days - 1)->startOfDay();
        $keyFormat = $monthly ? 'Y-m' : 'Y-m-d';

        $buckets = collect(CarbonPeriod::create($start, $monthly ? '1 month' : '1 day', $now))
            ->mapWithKeys(fn ($date) => [$date->format($keyFormat) => [
                'label' => $date->format($monthly ? 'M Y' : 'M j'),
                'revenue' => 0.0,
                'orders' => 0,
            ]]);

        $this->paidOrders($start)->get(['total', 'created_at'])->each(function (Order $order) use ($buckets, $keyFormat) {
            $key = $order->created_at->format($keyFormat);
            if ($buckets->has($key)) {
                $bucket = $buckets->get($key);
                $bucket['revenue'] += (float) $order->total;
                $bucket['orders']++;
                $buckets->put($key, $bucket);
            }
        });

        return $buckets;
    }

    /**
     * Count of all orders by fulfilment status.
     *
     * @return array<string, int>
     */
    public function ordersByStatus(): array
    {
        return Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /**
     * Paid orders that still need to ship.
     */
    public function toFulfil(): int
    {
        return $this->paidOrders()->whereIn('status', ['paid', 'pending'])->count();
    }

    public function printifyProblems(): int
    {
        return Order::query()
            ->where(fn ($q) => $q
                ->whereIn('printify_status', Order::PRINTIFY_PROBLEM_STATUSES)
                ->orWhereNotNull('printify_error'))
            ->count();
    }

    /**
     * Best sellers by units sold in paid orders over the last $days days.
     */
    public function topProductsQuery(int $days): Builder
    {
        $paidIds = $this->paidOrders(CarbonImmutable::now()->subDays($days)->startOfDay())->select('id');

        return OrderItem::query()
            ->selectRaw('MIN(id) as id, product_id, name, SUM(quantity) as units, SUM(subtotal) as revenue')
            ->whereIn('order_id', $paidIds)
            ->groupBy('product_id', 'name');
    }

    /**
     * % change from $previous to $current; null when there's nothing to compare against.
     */
    protected function percentChange(float|int $previous, float|int $current): ?float
    {
        if ($previous == 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /** @return array{revenue: float, orders: int} */
    protected function totals(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $row = $this->paidOrders($from, $to)->selectRaw('COALESCE(SUM(total), 0) as revenue, COUNT(*) as orders')->first();

        return ['revenue' => (float) $row->revenue, 'orders' => (int) $row->orders];
    }
}
