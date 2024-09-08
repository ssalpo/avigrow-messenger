<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:update-expired-tokens')->everyMinute();
Schedule::command('app:send-scheduled-reviews')->dailyAt('19:05');
Schedule::command('app:change-account-status')->everyFiveMinutes();
