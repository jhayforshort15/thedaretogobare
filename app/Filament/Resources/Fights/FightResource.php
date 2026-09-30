<?php

namespace App\Filament\Resources\Fights;

use App\Filament\Resources\Fights\Pages\CreateFight;
use App\Filament\Resources\Fights\Pages\EditFight;
use App\Filament\Resources\Fights\Pages\ListFights;
use App\Filament\Resources\Fights\Schemas\FightForm;
use App\Filament\Resources\Fights\Tables\FightsTable;
use App\Models\Fight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FightResource extends Resource
{
    protected static ?string $model = Fight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FightForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FightsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFights::route('/'),
            'create' => CreateFight::route('/create'),
            'edit' => EditFight::route('/{record}/edit'),
        ];
    }
}
