<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\PrintifyService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncPrintify')
                ->label('Sync from Printify')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => app(PrintifyService::class)->enabled())
                ->requiresConfirmation()
                ->modalDescription('This will import/update products, sizes and images from your Printify shop.')
                ->action(function () {
                    Artisan::call('printify:import');

                    Notification::make()
                        ->title('Printify sync complete')
                        ->body(trim(Artisan::output()))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
