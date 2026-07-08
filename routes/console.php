<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('auth:clear-resets')->everyFifteenMinutes();

Schedule::command('imports:cleanup')->hourly()->withoutOverlapping();

Schedule::command('governance:prune')->dailyAt('03:00')->withoutOverlapping();

Schedule::command('automation:run action_center_evaluation')
    ->dailyAt(config('automation.evaluation_time'))
    ->timezone(config('automation.timezone'))
    ->withoutOverlapping();

Schedule::command('automation:run daily_admin_digest')
    ->dailyAt(config('automation.daily_digest_time'))
    ->timezone(config('automation.timezone'))
    ->withoutOverlapping();

Schedule::command('automation:run weekly_admin_summary')
    ->mondays()
    ->at(config('automation.weekly_summary_time'))
    ->timezone(config('automation.timezone'))
    ->withoutOverlapping();

Schedule::command('queue:work database --queue=mail --stop-when-empty --max-jobs=5 --tries=3 --backoff=60 --timeout=30')
    ->everyMinute()
    ->withoutOverlapping(5);
