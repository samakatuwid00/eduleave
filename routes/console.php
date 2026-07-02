<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('auth:clear-resets')->everyFifteenMinutes();

Schedule::command('queue:work database --queue=mail --stop-when-empty --max-jobs=5 --tries=3 --backoff=60 --timeout=30')
    ->everyMinute()
    ->withoutOverlapping(5);
