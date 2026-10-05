<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Sales overview';

    public function getColumns(): int|array
    {
        return ['md' => 3];
    }
}
