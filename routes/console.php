<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Safety net in case a Printify webhook is missed (price/image edits, downtime).
Schedule::command('printify:import')->hourly()->withoutOverlapping()
    ->when(fn () => app(\App\Services\PrintifyService::class)->enabled());

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
