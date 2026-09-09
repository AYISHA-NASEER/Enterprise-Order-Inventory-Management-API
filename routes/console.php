<?php

use App\Jobs\DailyReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncSupplierProductsJob;
use App\Jobs\ExpireInventoryReservationsJob;
use App\Jobs\CheckLowStockJob;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncSupplierProductsJob)->everyThirtyMinutes();

Schedule::job(new ExpireInventoryReservationsJob)
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::job(new CheckLowStockJob)->hourly();

Schedule::job(new DailyReportJob)->daily();