<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Sizes & stock';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('size')
                    ->required()
                    ->maxLength(50)
                    ->helperText('e.g. S, M, L, XL, or "One Size".'),
                TextInput::make('color')
                    ->maxLength(50),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(255),
                TextInput::make('price_override')
                    ->numeric()
                    ->prefix('$')
                    ->helperText('Optional — only if this size costs a different price.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('size')
            ->columns([
                TextColumn::make('size')->weight('bold'),
                TextColumn::make('color')->placeholder('—'),
                TextColumn::make('stock')->numeric()->sortable(),
                TextColumn::make('sku')->label('SKU')->placeholder('—')->toggleable(),
                TextColumn::make('price_override')->money('usd')->placeholder('—')->toggleable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
