<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('report:daily')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('logs:cleanup')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('analytics:sync')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('horizon:snapshot')
    ->everyFiveMinutes();
