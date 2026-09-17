<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
