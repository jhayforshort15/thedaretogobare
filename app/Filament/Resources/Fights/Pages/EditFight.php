<?php

namespace App\Filament\Resources\Fights\Pages;

use App\Filament\Resources\Fights\FightResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFight extends EditRecord
{
    protected static string $resource = FightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
