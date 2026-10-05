<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Services\SalesAnalytics;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProductsTable extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['md' => 3, 'xl' => 1];

    protected static ?string $heading = 'Best sellers · last 30 days';

    public function table(Table $table): Table
    {
        return $table
            ->query(app(SalesAnalytics::class)->topProductsQuery(30)->with('product'))
            ->defaultSort('units', 'desc')
            ->paginated(false)
            ->modifyQueryUsing(fn ($query) => $query->limit(5))
            ->emptyStateHeading('No paid orders yet')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->state(fn (OrderItem $record) => $record->product?->image_url)
                    ->square()
                    ->imageSize(40),
                TextColumn::make('name')
                    ->label('Product')
                    ->weight('bold')
                    ->limit(28)
                    ->tooltip(fn (OrderItem $record) => $record->name)
                    ->description(fn (OrderItem $record) => number_format($record->units).' sold · $'.number_format((float) $record->revenue, 2)),
            ]);
    }
}
