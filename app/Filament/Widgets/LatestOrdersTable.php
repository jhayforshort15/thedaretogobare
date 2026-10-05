<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrdersTable extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['md' => 3, 'xl' => 2];

    protected static ?string $heading = 'Latest orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(6))
            ->paginated(false)
            ->recordUrl(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('No orders yet')
            ->columns([
                TextColumn::make('order_number')->label('Order #')->weight('bold'),
                TextColumn::make('customer')
                    ->state(fn (Order $record) => trim($record->first_name.' '.$record->last_name)),
                TextColumn::make('total')->money('usd'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid', 'completed' => 'success',
                        'shipped' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'refunded' => 'info',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')->label('Placed')->since()->tooltip(fn (Order $record) => $record->created_at?->format('M j, Y g:i A')),
            ]);
    }
}
