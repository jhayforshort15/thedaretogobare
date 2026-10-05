<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\SalesAnalytics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Last 30 days';

    protected ?string $description = 'Paid orders only, compared with the 30 days before.';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $analytics = app(SalesAnalytics::class);
        $summary = $analytics->summary(30);
        $daily = $analytics->series(30);
        $toFulfil = $analytics->toFulfil();
        $problems = $analytics->printifyProblems();

        return [
            $this->trendStat('Revenue', '$'.number_format($summary['revenue'], 2), $summary['revenue_change'])
                ->chart($daily->pluck('revenue')->values()->all()),

            $this->trendStat('Orders', number_format($summary['orders']), $summary['orders_change'])
                ->chart($daily->pluck('orders')->values()->all()),

            $this->trendStat('Average order', '$'.number_format($summary['average'], 2), $summary['average_change']),

            Stat::make('To ship', number_format($toFulfil))
                ->description($problems
                    ? "{$problems} with a Printify problem"
                    : 'Paid, not shipped yet')
                ->descriptionIcon($problems ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-truck')
                ->color($problems ? 'danger' : ($toFulfil ? 'warning' : 'success'))
                ->url($problems
                    ? OrderResource::getUrl('index', ['filters' => ['printify_attention' => ['isActive' => true]]])
                    : OrderResource::getUrl('index', ['filters' => ['status' => ['value' => 'paid']]])),
        ];
    }

    protected function trendStat(string $label, string $value, ?float $change): Stat
    {
        $stat = Stat::make($label, $value);

        if ($change === null) {
            return $stat->description('No sales in the previous period')->color('gray');
        }

        $up = $change >= 0;

        return $stat
            ->description(($up ? '+' : '').$change.'% vs previous 30 days')
            ->descriptionIcon($up ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($up ? 'success' : 'danger');
    }
}
