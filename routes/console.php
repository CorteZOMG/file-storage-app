<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('files:cleanup-expired')->everyMinute();

// uncomment to set less aggressive deletion
// Schedule::command('files:cleanup-expired')->hourly();
