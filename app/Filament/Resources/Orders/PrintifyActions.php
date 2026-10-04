<?php

namespace App\Filament\Resources\Orders;

use App\Models\Order;
use App\Services\PrintifyFulfillment;
use App\Services\PrintifyService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PrintifyActions
{
    /**
     * Send (or re-send) a paid order to Printify.
     */
    public static function retry(): Action
    {
        return Action::make('printifyRetry')
            ->label('Send to Printify')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(fn (Order $record) => app(PrintifyService::class)->enabled()
                && $record->payment_status === 'paid'
                && empty($record->printify_order_id))
            ->requiresConfirmation()
            ->modalDescription('Create this order in Printify for printing and shipping.')
            ->action(function (Order $record) {
                app(PrintifyFulfillment::class)->fulfil($record);
                $record->refresh();

                static::notify($record, 'Order sent to Printify');
            });
    }

    /**
     * Approve an on-hold Printify order for printing.
     */
    public static function sendToProduction(): Action
    {
        return Action::make('printifyProduction')
            ->label('Send to production')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->visible(fn (Order $record) => app(PrintifyService::class)->enabled()
                && ! empty($record->printify_order_id)
                && $record->printify_status === 'on-hold')
            ->requiresConfirmation()
            ->modalDescription('Printify will start printing this order and charge your Printify account.')
            ->action(function (Order $record) {
                app(PrintifyFulfillment::class)->sendToProduction($record);
                $record->refresh();

                static::notify($record, 'Order sent to production');
            });
    }

    protected static function notify(Order $order, string $successTitle): void
    {
        if ($order->printify_error) {
            Notification::make()->title('Printify problem')->body($order->printify_error)->danger()->persistent()->send();

            return;
        }

        if (empty($order->printify_order_id)) {
            Notification::make()->title('Nothing to send')->body('This order has no Printify products.')->warning()->send();

            return;
        }

        Notification::make()->title($successTitle)->success()->send();
    }
}
