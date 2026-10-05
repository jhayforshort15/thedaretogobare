<?php

namespace App\Filament\Widgets;

use App\Services\SalesAnalytics;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Orders by status';

    protected ?string $description = 'All orders';

    protected ?string $maxHeight = '280px';

    protected ?string $pollingInterval = null;

    // Same order as the orders table; colours match its status badges where they're distinct.
    protected const STATUSES = [
        'pending' => ['Pending', '#f59e0b'],
        'paid' => ['Paid', '#22c55e'],
        'shipped' => ['Shipped', '#3b82f6'],
        'completed' => ['Completed', '#64748b'],
        'cancelled' => ['Cancelled', '#ef4444'],
    ];

    protected function getData(): array
    {
        $counts = app(SalesAnalytics::class)->ordersByStatus();
        $statuses = collect(self::STATUSES)->filter(fn ($_, $status) => ($counts[$status] ?? 0) > 0);

        return [
            'datasets' => [
                [
                    'data' => $statuses->keys()->map(fn ($status) => $counts[$status])->all(),
                    'backgroundColor' => $statuses->pluck(1)->values()->all(),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $statuses->pluck(0)->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                cutout: '65%',
                plugins: { legend: { position: 'bottom' } },
                scales: { x: { display: false }, y: { display: false } },
            }
        JS);
    }
}
