<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule auto export command
Schedule::command('app:auto-export')->daily();

// Schedule data cleanup command
Schedule::command('app:data-cleanup')->weekly();
