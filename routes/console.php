<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('publish:scheduled')->everyMinute();
Schedule::command('ops:metrics')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('ops:health-check')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('updates:check')->daily()->withoutOverlapping();
