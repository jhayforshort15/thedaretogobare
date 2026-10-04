<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\PrintifyActions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PrintifyActions::retry()->after(fn () => $this->refreshFormData(['printify_order_id', 'printify_status', 'printify_error'])),
            PrintifyActions::sendToProduction()->after(fn () => $this->refreshFormData(['printify_status', 'printify_error'])),
            DeleteAction::make(),
        ];
    }
}
