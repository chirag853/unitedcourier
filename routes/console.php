<?php

use App\Http\Controllers\ShipmentTrackingController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('shipments:track', function (ShipmentTrackingController $controller) {
    $summary = $controller->sync();

    $this->table(['Processed', 'Updated', 'Skipped', 'Failed'], [[
        $summary['processed'],
        $summary['updated'],
        $summary['skipped'],
        $summary['failed'],
    ]]);

    return $summary['failed'] > 0 ? self::FAILURE : self::SUCCESS;
})->purpose('Synchronize manifested shipment statuses with carrier tracking APIs');

Schedule::useCache('file');

Schedule::command('shipments:track')
    ->hourly()
    ->withoutOverlapping()
    ->name('shipment-tracking-sync');
