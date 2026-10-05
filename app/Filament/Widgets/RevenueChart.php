<?php

namespace App\Filament\Widgets;

use App\Services\SalesAnalytics;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['md' => 2];

    protected ?string $heading = 'Revenue';

    protected ?string $description = 'Paid orders';

    protected ?string $maxHeight = '280px';

    public ?string $filter = '30';

    protected ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
            'year' => 'Last 12 months',
        ];
    }

    protected function getData(): array
    {
        $monthly = $this->filter === 'year';
        $series = app(SalesAnalytics::class)->series($monthly ? 365 : (int) $this->filter, $monthly);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $series->pluck('revenue')->map(fn ($v) => round($v, 2))->values()->all(),
                    'fill' => 'start',
                    'tension' => 0.3,
                    'pointRadius' => $series->count() > 31 ? 0 : 3,
                ],
            ],
            'labels' => $series->pluck('label')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => ' $' + ctx.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 }) } },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (value) => '$' + value.toLocaleString() } },
                    x: { ticks: { maxTicksLimit: 10 } },
                },
            }
        JS);
    }
}
