<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order status')
                    ->description('Update the fulfilment and payment status of this order.')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'shipped' => 'Shipped',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ]),
                        Select::make('payment_status')
                            ->label('Payment status')
                            ->required()
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'refunded' => 'Refunded',
                            ]),
                    ]),

                Section::make('Printify fulfilment')
                    ->columns(2)
                    ->visible(fn ($record) => $record?->printify_order_id || $record?->printify_status || $record?->printify_error)
                    ->schema([
                        TextInput::make('printify_order_id')->label('Printify order ID')->disabled(),
                        Select::make('printify_status')->label('Printify status')->options(Order::PRINTIFY_STATUSES)->disabled(),
                        Textarea::make('printify_error')->label('Last problem')->disabled()->columnSpanFull()
                            ->visible(fn ($record) => filled($record?->printify_error)),
                    ]),

                Section::make('Tracking')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tracking_number')->maxLength(255),
                        TextInput::make('tracking_url')->label('Tracking link')->url()->maxLength(255),
                    ]),

                Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_number')->disabled(),
                        TextInput::make('email')->label('Email address')->disabled(),
                        TextInput::make('first_name')->disabled(),
                        TextInput::make('last_name')->disabled(),
                        TextInput::make('phone')->disabled(),
                    ]),

                Section::make('Shipping address')
                    ->columns(2)
                    ->schema([
                        TextInput::make('shipping_address')->disabled()->columnSpanFull(),
                        TextInput::make('shipping_city')->disabled(),
                        TextInput::make('shipping_state')->label('State / Province')->disabled(),
                        TextInput::make('shipping_postal_code')->label('Postal code')->disabled(),
                        TextInput::make('shipping_country')->disabled(),
                    ]),

                Section::make('Totals & payment')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subtotal')->disabled()->prefix('$'),
                        TextInput::make('shipping_cost')->disabled()->prefix('$'),
                        TextInput::make('tax')->disabled()->prefix('$'),
                        TextInput::make('total')->disabled()->prefix('$'),
                        TextInput::make('payment_method')->disabled(),
                        TextInput::make('payment_reference')->disabled(),
                        Textarea::make('notes')->disabled()->columnSpanFull(),
                    ]),
            ]);
    }
}
