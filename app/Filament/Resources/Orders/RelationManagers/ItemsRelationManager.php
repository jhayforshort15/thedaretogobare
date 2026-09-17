<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order items';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->weight('bold')
                    ->description(fn ($record) => $record->size ? "Size: {$record->size}" : null),
                TextColumn::make('price')
                    ->money('usd'),
                TextColumn::make('quantity')
                    ->alignCenter(),
                TextColumn::make('subtotal')
                    ->money('usd'),
            ])
            // Order items are a fixed record of the purchase — read only.
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
