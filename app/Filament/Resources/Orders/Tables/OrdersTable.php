<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\PrintifyActions;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->state(fn ($record) => trim($record->first_name.' '.$record->last_name))
                    ->description(fn ($record) => $record->email)
                    ->searchable(['first_name', 'last_name', 'email']),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter(),
                TextColumn::make('total')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'refunded' => 'info',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('printify_status')
                    ->label('Printify')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Order::PRINTIFY_STATUSES[$state] ?? $state)
                    ->color(fn (?string $state): string => match (true) {
                        in_array($state, Order::PRINTIFY_PROBLEM_STATUSES, true) => 'danger',
                        $state === 'on-hold' => 'warning',
                        in_array($state, ['fulfilled', 'delivered'], true) => 'success',
                        default => 'info',
                    })
                    ->tooltip(fn ($record) => $record->printify_error)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('printify_attention')
                    ->label('Printify needs attention')
                    ->query(fn ($query) => $query->where(fn ($q) => $q
                        ->whereIn('printify_status', [...Order::PRINTIFY_PROBLEM_STATUSES, 'on-hold'])
                        ->orWhereNotNull('printify_error'))),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Payment status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->recordActions([
                PrintifyActions::retry()->iconButton(),
                PrintifyActions::sendToProduction()->iconButton(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
