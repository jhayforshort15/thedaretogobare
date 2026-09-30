<?php

namespace App\Filament\Resources\Fights\Pages;

use App\Filament\Resources\Fights\FightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFights extends ListRecords
{
    protected static string $resource = FightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
