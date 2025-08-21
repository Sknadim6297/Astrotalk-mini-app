<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule wallet deductions every minute
Schedule::command('wallet:process-deductions')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
